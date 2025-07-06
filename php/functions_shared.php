<?php

function brackets_height($br) {
	if ($br  === "J") {
	$bracket_height =  "113.3";
	}elseif($br  === "A") {
	$bracket_height =  "106.3";
	}elseif ($br  === "H" || $br  === "B") {
	$bracket_height =  "99.3";
	}elseif ($br  === "C") {
	$bracket_height =  "91.3";
	}elseif($br  === "D") {
	$bracket_height =  "84";
	}elseif($br  === "E") {
	$bracket_height =  "77.5";
	}elseif($br  === "F") {
	$bracket_height =  "70.7";
	}elseif($br  === "G") {
	$bracket_height =  "63";
	}elseif($br  === "a") {
	$bracket_height =  "57";
	}elseif($br  === "h" || $br  === "b") {
	$bracket_height =  "50";
	}elseif($br  === "c") {
	$bracket_height =  "42";
	}elseif($br  === "d") {
	$bracket_height =  "34";
	}elseif($br  === "e") {
	$bracket_height =  "27.55";
	}elseif($br  === "f") {
	$bracket_height =  "20";
	}elseif($br  === "g") {
	$bracket_height =  "13";
	}elseif($br  === "u") {
	$bracket_height =  "5";
	}elseif($br  === "p") {
	$bracket_height =  "-2";
	}elseif($br  === "q") {
	$bracket_height =  "-2";
	}elseif($br  === "r") {
	$bracket_height =  "-10";
  }elseif($br  === "s") {
	$bracket_height =  "-10";
	}
	echo $bracket_height;
}

function natural_acc($x) {
	if ($x === "J") {
		return 95;
	}elseif($x === "A") {
		return 88;
	}elseif ($x === "H") {
		return 81;
	}elseif ($x === "C") {
		return 74;
	}elseif($x === "D") {
		return 67;
	}elseif($x === "E") {
		return 60;
	}elseif($x === "F") {
		return 53;
	}elseif($x === "G") {
		return 46;
	}elseif($x === "a") {
		return 39;
	}elseif($x === "h" || $x === "b") {
		return 32;
	}elseif($x === "c") {
		return 25;
	}elseif($x === "d") {
		return 17;
	}elseif($x === "e") {
		return 10;
	}elseif($x === "f") {
		return 3;
	}elseif($x === "g") {
		return -4;
	}elseif($x === "u") {
		return -11;
	}elseif($x === "p") {
		return -18;
	}elseif($x === "q") {
		-18;
	}elseif($x === "r") {
		-25;
	}elseif($x === "s") {
		-32;
	}
}

function flat_acc($x) {
	if ($x === "J") {
		return 101;
	}elseif ($x === "A") {
		return 94;
	}elseif($x === "H" or $x === "h") {
		return 22;
	}elseif ($x === "B"){
		return 88;
	}elseif ($x === "C") {
		return 80;
	}elseif($x === "D") {
		return 74;
	}elseif($x === "E") {
		return 67;
	}elseif($x === "F") {
		return 60;
	}elseif($x === "G") {
		return 53;
	}elseif($x === "a") {
		return 45;
	}elseif($x === "b") {
		return 38;
	}elseif($x === "c") {
		return 32;
	}elseif($x === "d") {
		return 26;
	}elseif($x === "e") {
		return 19;
	}elseif($x === "f") {
		return 11;
	}elseif($x === "g") {
		return 4;
	}elseif($x === "u") {
		return -4;
	}elseif($x === "p") {
		return -11;
	}elseif($x === "q") {
		return -11;
	}elseif($x === "r") {
		return -18;
	}elseif($x === "s") {
		return -25;
	}
}

function note_height($x) {
	if ($x === "J") {
		return 90;
	}elseif($x === "A") {
		return 83;
	}elseif ($x === "H" || $x === "B") {
		return 76;
	}elseif ($x === "C") {
		return 69;
	}elseif($x === "D") {
		return 62;
	}elseif($x === "E") {
		return 55;
	}elseif($x === "F") {
		return 48;
	}elseif($x === "G") {
		return 41;
	}elseif($x === "a") {
		return 34;
	}elseif($x === "h" || $x === "b") {
		return 27;
	}elseif($x === "c") {
		return 20;
	}elseif($x === "d") {
		return 13;
	}elseif($x === "e") {
		return 6;
	}elseif($x === "f") {
		return -1;
	}elseif($x === "g") {
		return -8;
	}elseif($x === "u") {
		return -15;
	}elseif($x === "p") {
		return -22;
	}elseif($x === "q") {
		return  -22;
	}elseif($x === "r") {
		return  -29;
	}elseif($x === "s") {
		return  -36;
	}
}

function sharp_acc($x){
	if ($x === "J") {
		return -500;
	}elseif($x === "A") {
		return -507;
	}elseif ($x === "H") {
		return -514;
	}elseif ($x === "C") {
		return -521;
	}elseif($x === "D") {
		return -528;
	}elseif($x === "E") {
		return -535;
	}elseif($x === "F") {
		return -542;
	}elseif($x === "G") {
		return -549;
	}elseif($x === "a") {
		return -556;
	}elseif($x === "h") {
		return -563;
	}elseif($x === "c") {
		return -570;
	}elseif($x === "d") {
		return -577;
	}elseif($x === "e") {
		return -584;
	}elseif($x === "f") {
		return -591;
	}elseif($x === "g") {
		return -599;
	}elseif($x === "u") {
		return -606;
	}elseif($x === "p") {
		return -623;
	}elseif($x === "q") {
		return -630;
	}elseif($x === "r") {
		return -636;
	}elseif($x === "s") {
		return -643;
	}
}

function note_number($note)	{
	if ($note === "J"){
		return -1;
	}elseif($note === "A"){
		return 0;
	}elseif($note === "H"){
		return 1;
	}elseif ($note === "B"){
		return 1;
	}elseif ($note === "C"){
		return 2;
	}elseif ($note === "D"){
		return 3;
	}elseif ($note === "E"){
		return 4;
	}elseif ($note === "F"){
		return 5;
	}elseif ($note === "G"){
		return 6;
	}elseif ($note === "a"){
		return 7;
	}elseif ($note === "h"){
		return 8;
	}elseif ($note === "b"){
		return 8;
	}elseif ($note === "c"){
		return 9;
	}elseif ($note === "d"){
		return 10;
	}elseif ($note === "e"){
		return 11;
	}elseif ($note === "f"){
		return 12;
	}elseif ($note === "g"){
		return 13;
	}elseif ($note === "u") {
		return 14;
	}elseif ($note === "p") {
		return 15;
	}elseif ($note === "q") {
		return 15;
	}elseif ($note === "r") {
		return 16;
	}elseif ($note === "s") {
		return 17;
	}
}
	
	// square notation only
function flag_note2number($note)	{
	if ($note === "J"){
		return 18;
	}elseif($note === "A"){
		return 17;
	}elseif($note === "H"){
		return 16;
	}elseif ($note === "B"){
		return 16;
	}elseif ($note === "C"){
		return 15;
	}elseif ($note === "D"){
		return 14;
	}elseif ($note === "E"){
		return 13;
	}elseif ($note === "F"){
		return 12;
	}elseif ($note === "G"){
		return 11;
	}elseif ($note === "a"){
		return 10;
	}elseif ($note === "h"){
		return 9;
	}elseif ($note === "b"){
		return 9;
	}elseif ($note === "c"){
		return 8;
	}elseif ($note === "d"){
		return 7;
	}elseif ($note === "e"){
		return 6;
	}elseif ($note === "f"){
		return 5;
	}elseif ($note === "g"){
		return 4;
	}elseif ($note === "u") {
		return 3;
	}elseif ($note === "p") {
		return 2;
	}elseif ($note === "q") {
		return 1;
	}elseif ($note === "r") {
		return 0;
	}elseif ($note === "s") {
		return -1;
	}
}
		
// Modern only
function slur_height($note)	{
	if ($note === "J"){
		$My = 102;
		$note_height = 0;
	}elseif($note === "A"){
		$My = 102;
		$note_height = 0;
	}elseif($note === "H"){
		$My = 95;
		$note_height = 1;
	}elseif ($note === "B"){
		$My = 95;
		$note_height = 1;
	}elseif ($note === "C"){
		$My = 87;
		$note_height = 2;
	}elseif ($note === "D"){
		$My = 80;
		$note_height = 3;
	}elseif ($note === "E"){
		$My = 73;
		$note_height = 4;
	}elseif ($note === "F"){
		$My = 66.3;
		$note_height = 5;
	}elseif ($note === "G"){
		$My = 59;
		$note_height = 6;
	}elseif ($note === "a"){
		$My = 52;
		$note_height = 7;
	}elseif ($note === "h"){
		$My = 45;
		$note_height = 8;
	}elseif ($note === "b"){
		$My = 45;
		$note_height = 8;
	}elseif ($note === "c"){
		$My = 37.5;
		$note_height = 9;
	}elseif ($note === "d"){
		$My = 25;
		$note_height = 10;
	}elseif ($note === "e"){
		$My = 23.3;
		$note_height = 11;
	}elseif ($note === "f"){
		$My = 16;
		$note_height = 12;
	}elseif ($note === "g"){
		$My = 9;
		$note_height = 13;
	}elseif ($note === "u") {
		$My = 6;
		$note_height = 14;
	}elseif ($note === "p") {
		$My = 6;
		$note_height = 15;
	}elseif ($note === "q") {
		$My = 6;
		$note_height = 15;
	}elseif ($note === "r") {
		$My = 6;
		$note_height = 16;
	}elseif ($note === "s") {
		$My = 0;
		$note_height = 17;
	}
}

function fromSequenceToRepetitionPattern($sequence) {
    $sequenceArray = str_split($sequence);
    $letterToMusicLine = [];
    $pairs = [];
    $musicLineNumber = 0;

    foreach ($sequenceArray as $position => $letter) {
        $currentPosition = $position; // Positions start from 1
        if (!isset($letterToMusicLine[$letter])) {
            // First occurrence of the letter
            $letterToMusicLine[$letter] = $musicLineNumber;
            $musicLineNumber++;
        } else {
            // Letter repeats, create a pair
            $firstOccurrenceMusicLine = $letterToMusicLine[$letter];
            $pairs[] = [$currentPosition, $firstOccurrenceMusicLine];
        }
    }

    return $pairs;
}  

function parse_pseudo_html($txt) {
  $txt = html_entity_decode($txt, ENT_QUOTES, 'UTF-8');

  $txt = str_replace("<i>", "<tspan class=\"italic\">", $txt);
  $txt = str_replace("</i>", "</tspan>", $txt);
  $txt = str_replace("<strong>", "<tspan class=\"bold\">", $txt);
  $txt = str_replace("</strong>", "</tspan>", $txt);
  $txt = str_replace("<b>", "<tspan class=\"bold\">", $txt);
  $txt = str_replace("</b>", "</tspan>", $txt);

  return $txt;
}

function graces($text){
  // Replace apostrophes inside words
  $text = preg_replace("/\b'\b/", "’", html_entity_decode($text, ENT_QUOTES, 'UTF-8'));
  
  // Replace single quotes used as quotation marks
  $text = preg_replace("/([^']+)'/", "$1’", html_entity_decode($text));
  $text = preg_replace("/'([^']+)/", "‘$1", html_entity_decode($text));

  return $text;
}


function parse_for_repetition($text_line_ar, $repetition_pattern) {
 $repetitions = [];
 $repeated_text_lines = [];
  // Loop through rules of repetition
  foreach ($repetition_pattern as $rule) {
      $source_index = $rule[0]; // Index of the line to repeat
      $target_index = $rule[1]; // Index where it should be repeated
      // Remember which lines need to be repeated
      array_push($repeated_text_lines, $rule[0]);
      // Apply the repetition: copy the line to the target index
      // $repetitions[$target_index] = [$text_line_ar[$source_index], $source_index+1];
      
      // NEW WAY
      if (!isset($repetitions[$target_index])) {
            $repetitions[$target_index] = [];
        }
      
      $repetitions[$target_index][$source_index] = $text_line_ar[$source_index];
      // NEW WAY
      
      // Remove the source line
      unset($text_line_ar[$source_index]);
 }

 // Reindex the array to remove gaps in the keys
 $text_line_ar = array_values($text_line_ar);

 return [$text_line_ar, $repetitions, $repeated_text_lines];
}