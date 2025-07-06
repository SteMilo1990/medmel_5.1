<?php

function nw_intervals($seq1, $seq2, $match_score = 1, $mismatch = -1, $gap = 0) {
  global $consider_plica;

  $seq1 = interval_string_to_array($seq1, $consider_plica);
  $seq2 = interval_string_to_array($seq2, $consider_plica);
  $len1 = count($seq1);
  $len2 = count($seq2);

  // Initialize the first row and column
  for ($i = 0; $i <= $len1; $i++) {
      $matrix[$i][0] = $i * $gap;
  }
  for ($j = 0; $j <= $len2; $j++) {
      $matrix[0][$j] = $j * $gap;
  }

  // Fill the matrix
  for ($i = 1; $i <= $len1; $i++) {
      for ($j = 1; $j <= $len2; $j++) {
          $match_mismatch = ($seq1[$i-1] === $seq2[$j-1]) ? $match_score : $mismatch;
          $match = $matrix[$i-1][$j-1] + $match_mismatch;
          $hgap = $matrix[$i-1][$j] + $gap;
          $vgap = $matrix[$i][$j-1] + $gap;
          $max = max($match, $hgap, $vgap);
          $matrix[$i][$j] = $max;
          $matrix_p[$i][$j] = 'W';
          if ($max === $hgap) {
              $matrix_p[$i][$j] = 'U';
          } else if ($max === $vgap) {
              $matrix_p[$i][$j] = 'L';
          }
      }
  }

  // Trace back to find the optimal alignment
  $i = $len1;
  $j = $len2;

  $aligned_seq1 = [];
  $aligned_seq2 = [];

  while ($i > 0 && $j > 0) {
      $pointer = $matrix_p[$i][$j];
      if ($pointer === 'W') {
          array_unshift($aligned_seq1, $seq1[$i-1]);
          array_unshift($aligned_seq2, $seq2[$j-1]);
          $i--;
          $j--;
      } elseif ($pointer === 'U') {
          array_unshift($aligned_seq1, $seq1[$i-1]);
          array_unshift($aligned_seq2, '||');
          $i--;
      } elseif ($pointer === 'L') {
          array_unshift($aligned_seq1, '||');
          array_unshift($aligned_seq2, $seq2[$j-1]);
          $j--;
      }
  }

  while ($i > 0) {
      array_unshift($aligned_seq1, $seq1[$i-1]);
      array_unshift($aligned_seq2, '||');
      $i--;
  }
  while ($j > 0) {
      array_unshift($aligned_seq1, '||');
      array_unshift($aligned_seq2, $seq2[$j-1]);
      $j--;
  }

  $aligned_seq1 = implode('', $aligned_seq1);
  $aligned_seq2 = implode('', $aligned_seq2);
  $score = $matrix[$len1][$len2];

  return [
      $aligned_seq1,
      $aligned_seq2,
      $score
  ];
}

function nw_intervals_chatGPTNEW($seq1, $seq2, $match_score = 1, $mismatch = 0, $gap = -1) {
    global $consider_plica;
    $transpose = 1; // Keep this as 0 if you want to compare characters; set to 1 for elements/words.

    if ($transpose) {
        $seq1 = interval_string_to_array($seq1);
        $seq2 = interval_string_to_array($seq2);
        $len1 = count($seq1);
        $len2 = count($seq2);
        $gap_sign = "||";
    } else {
        $len1 = strlen($seq1);
        $len2 = strlen($seq2);
        $gap_sign = "|";
    }

    // Initialize the scoring and pointer matrices
    $matrix = array_fill(0, $len1 + 1, array_fill(0, $len2 + 1, 0));
    $matrix_p = array_fill(0, $len1 + 1, array_fill(0, $len2 + 1, '0'));

    $max_i = 0;
    $max_j = 0;
    $max_score = 0;

    // Fill the scoring matrix with local alignment
    for ($i = 1; $i <= $len1; $i++) {
        for ($j = 1; $j <= $len2; $j++) {
            $char1 = $transpose ? $seq1[$i - 1] : $seq1[$i - 1];
            $char2 = $transpose ? $seq2[$j - 1] : $seq2[$j - 1];

            $match_mismatch = ($char1 === $char2) ? $match_score : $mismatch;
            $diag = $matrix[$i - 1][$j - 1] + $match_mismatch;
            $up = $matrix[$i - 1][$j] + $gap;
            $left = $matrix[$i][$j - 1] + $gap;
            $max = max(0, $diag, $up, $left); // Set negative scores to zero

            $matrix[$i][$j] = $max;

            if ($max === 0) {
                $matrix_p[$i][$j] = '0';
            } elseif ($max === $diag) {
                $matrix_p[$i][$j] = 'D'; // Diagonal
            } elseif ($max === $up) {
                $matrix_p[$i][$j] = 'U'; // Up
            } else {
                $matrix_p[$i][$j] = 'L'; // Left
            }

            // Keep track of the maximum score position
            if ($max > $max_score) {
                $max_score = $max;
                $max_i = $i;
                $max_j = $j;
            }
        }
    }

    // Trace back from the cell with the maximum score
    $i = $max_i;
    $j = $max_j;
    $aligned_seq1 = [];
    $aligned_seq2 = [];

    while ($matrix[$i][$j] > 0) {
        $pointer = $matrix_p[$i][$j];

        if ($pointer === 'D') {
            array_unshift($aligned_seq1, $transpose ? $seq1[$i - 1] : $seq1[$i - 1]);
            array_unshift($aligned_seq2, $transpose ? $seq2[$j - 1] : $seq2[$j - 1]);
            $i--;
            $j--;
        } elseif ($pointer === 'U') {
            array_unshift($aligned_seq1, $transpose ? $seq1[$i - 1] : $seq1[$i - 1]);
            array_unshift($aligned_seq2, $gap_sign);
            $i--;
        } elseif ($pointer === 'L') {
            array_unshift($aligned_seq1, $gap_sign);
            array_unshift($aligned_seq2, $transpose ? $seq2[$j - 1] : $seq2[$j - 1]);
            $j--;
        } else {
            break; // Stop when score is zero
        }
    }

    $aligned_seq1 = $transpose ? implode('', $aligned_seq1) : implode('', $aligned_seq1);
    $aligned_seq2 = $transpose ? implode('', $aligned_seq2) : implode('', $aligned_seq2);
    $score = $max_score;

    return [
        $aligned_seq1,
        $aligned_seq2,
        $score
    ];
}

function interval_string_to_array($seq) {
  $new_arr = [];
  $el = "";
  // return $seq = str_replace(["+", '='], "", $seq);
  for ($i=0; $i < strlen($seq); $i++) {
    
    if ($seq[$i] == "+" || $seq[$i] == "-" || $seq[$i] == "=" || $seq[$i] == " " || $seq[$i] == "(") {
        // If first el
        if ($i == 0) {
          if ($seq[$i] == " ") {
            $el = "  ";
          } else {
            $el = $seq[$i];
          }
        }
          
        else {
          // Space
          if ($seq[$i] == " ") {
            array_push($new_arr, $el);
            $el = "  ";
          } 
          // everything else
          else {
            // if previous not plica symbol, push and begin a new el
            if ($seq[$i-1] !== "(") {
              array_push($new_arr, $el);
              $el = $seq[$i];
            } 
            // If previous is plica symbol
            else {
              $el .= $seq[$i]; 
            }
          }
      }
    }else{
      $el .= $seq[$i]; 
    }
  }
  
  array_push($new_arr, $el);
  return $new_arr;
}


function trimSequences($seq1, $seq2) {  
  $seq1;
  if ($seq1[0] != "|") {
        $firstNonGapIndex = 0;
  } else {
      for ($i = 0; $i < strlen($seq1)-1; $i++) {
          // If the character is not "-", return its index
          if ($seq1[$i] !== '|') {
              $firstNonGapIndex = $i;
              break;
          }
      }
  }
  
  if($seq1[strlen($seq1)-1] != "|"){
      $lastNonGapIndex = strlen($seq1)-1;
  } else {
      for ($i = strlen($seq1) - 1; $i >= 0; $i--) {
          // If the character is not "-", return its index
          if ($seq1[$i] !== '|') {
              $lastNonGapIndex = $i;
              break;
          }
      }
  }
    
    // If no gap found, return original sequences
    if ($firstNonGapIndex === false || $lastNonGapIndex === false) {
        return [$seq1, $seq2];
    }

    // Extract substrings between first and last non-gap indices
    $trimmedSeq1 = substr($seq1, $firstNonGapIndex, $lastNonGapIndex - $firstNonGapIndex + 1);
    $trimmedSeq2 = substr($seq2, $firstNonGapIndex, $lastNonGapIndex - $firstNonGapIndex + 1);

    return [$trimmedSeq1, $trimmedSeq2];
}

function compare_aligned_lines($line_start, $line_compare) {
    global $consider_spaces;
    global $consider_plica;
    $matches = [];
    $compared_elements = 0;

    if (!$consider_plica) {
      $line_start = str_replace(["(",")"], "", $line_start);
      $line_compare = str_replace(["(",")"], "", $line_compare);
    }
    
    for ($char_n = 0; $char_n < strlen($line_start); $char_n++){
        $char_start = $line_start[$char_n];
        $char_compare = $line_compare[$char_n];
      
        // Match!
        if ($char_start == $char_compare and $char_start != "|") {
            // False match, just two spaces allined
            if ($char_start == " ") {
              if ($consider_spaces) {
                $matches[$char_n] = 1;
                $compared_elements += 1;
              }else{
                $matches[$char_n] = " ";
              }
            }
            // Actual match
            else {
                $matches[$char_n] = 1;
                $compared_elements += 1;
            }
        // Mismatch but between a separation and a space
        } elseif (($char_start == "|" or $char_start == "-") and $char_compare == " ") {
          if ($consider_spaces) {
            $matches[$char_n] = " ";
            $compared_elements += 1;
          }else{
            $matches[$char_n] = " ";
          }        
        } 
        // Mismatch, note against "|", or against a " "
        elseif ($char_compare == "|" or $char_compare == "-" or $char_compare == " ") {
          // skip, when used for colors, 
          // but take into consideration for the ratio of matches/mismatches  
          $matches[$char_n] = " ";
          $compared_elements += 1;
        }
        // Mismatch, but may be due to interpretation (note that _h is treated as b)
        elseif (($char_start == "h" and $char_compare == "b") or
                ($char_start == "b" and $char_compare == "h")) {
          $matches[$char_n] = 0.5;
          $compared_elements += 1;
        } else {
          $matches[$char_n] = 0;
          $compared_elements += 1;
        }
    }
  
    return array(sum_numeric_elements($matches) / $compared_elements, $matches);
}

function sum_numeric_elements($arr) {
    $total = 0;

    foreach ($arr as $element) {
        if (is_numeric($element)) {
            $total += $element;
        }
    }
    return $total;
}
