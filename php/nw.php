<?php


function nw($s1, $s2, $op=["G"=>1.8, "P"=>2, "M"=>-4]) {
  $UP   = 1;
  $LEFT = 2;
  $UL   = 4;

  $op = $op ?: []; // Initialize $op as an empty array if it's not already set
  $G = isset($op["G"]) ? $op["G"] : 1.8; // Set $G to the value of $op["G"] if it's set, otherwise use 1.8
  $P = isset($op["P"]) ? $op["P"] : 2; // Set $P to the value of $op["P"] if it's set, otherwise use 2
  $M = isset($op["M"]) ? $op["M"] : -4; // Set $M to the value of $op["M"] if it's set, otherwise use -4

  $mat   = [];
  $direc = [];

  // initialization
  for ($i=0; $i<count($s1)+1; $i++) {
    $mat[$i] = [0 => 0];
    $direc[$i] = [0 => []];
    for($j=1; $j<count($s2)+1; $j++) {
      $mat[$i][$j] = ($i == 0) 
          ? 0
          : (($s1[$i - 1] == $s2[$j - 1]) ? $P : $M);

    $direc[$i][$j] = [];
    }
  }

  // calculate each value
  for($i=0; $i<count($s1)+1; $i++) {
    for($j=0; $j<count($s2)+1; $j++) {
      $newval = ($i == 0 || $j == 0)
        ? -$G * ($i + $j)
        : max($mat[$i - 1][$j] - $G, $mat[$i - 1][$j - 1] + $mat[$i][$j], $mat[$i][$j - 1] - $G);


      if ($i > 0 && $j > 0) {
        if ($newval == $mat[$i - 1][$j] - $G) {
            $direc[$i][$j][] = $UP;
        }

        if ($newval == $mat[$i][$j - 1] - $G) {
            $direc[$i][$j][] = $LEFT;
        }

        if ($newval == $mat[$i - 1][$j - 1] + $mat[$i][$j]) {
            $direc[$i][$j][] = $UL;
        }
      }
      else {
        $direc[$i][$j][] = ($j == 0) ? $UP : $LEFT;
      }
      $mat[$i][$j] = $newval;
    }
  }

  // get result
  $chars = [[],[]];
  $I = count($s1);
  $J = count($s2);
  $max = max($I, $J);
  while ($I > 0 || $J > 0) {
    switch ($direc[$I][$J][0]) {
        case $UP:
            $I--;
            array_unshift($chars[0], $s1[$I]);
            array_unshift($chars[1], '-');
            break;
        case $LEFT:
            $J--;
            array_unshift($chars[0], '-');
            array_unshift($chars[1], $s2[$J]);
            break;
        case $UL:
            $I--;
            $J--;
            array_unshift($chars[0], $s1[$I]);
            array_unshift($chars[1], $s2[$J]);
            break;
        default:
            break;
    }
  }

  return [implode('', $chars[0]), implode('', $chars[1])];
}


function main() {
  global $argv; // Needed to access command line arguments in PHP

  if (count($argv) < 3) {
    return;
  }

  $w1 = $argv[1];
  $w2 = $argv[2];

  $r = nw($w1, $w2);
  echo $r[0];
  echo $r[1];
}

$seq1 = "a b cd edc fed";
$seq2 = "h aG ah aG G Gh d a b cd edc fed c ch ah 'h aG G F a ccd c ded c h ahcG G F Ga ah a a(G)'";

// Convert strings to arrays
$s1 = str_split($seq1);
$s2 = str_split($seq2);

$res = nw($s1, $s2);

echo "<div style='font-family:courier'>";
foreach ($res as $key => $value) {
  echo $value;
  echo "<br>";
}
echo "</div>";