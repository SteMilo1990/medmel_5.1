<?php
// Disable PHP's implicit output buffering
ini_set('output_buffering', 'off');
ini_set('zlib.output_compression', 'off');
ini_set('implicit_flush', 'on');
ob_implicit_flush(true);

// Disable Apache's mod_deflate output buffering
if (function_exists('apache_setenv')) {
    apache_setenv('no-gzip', '1');
    apache_setenv('dont-vary', '1');
}

include "../calculateIntervals.php";
include "../store_access.php";


// Start output buffering
//ob_start();


function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
  return $data;
}

// Parameters and filters
$tolerance = test_input($_POST['tolerance'] ?? 100);

$start_melody_medmel_id = test_input($_POST['id_staves'] ?? "");


$id_staves    = test_input($_POST['id_staves'] ?? "");
$repertory    = test_input($_POST['repertory'] ?? "");
$batch        = test_input($_POST['batch'] ?? "");
$exact_search = filter_var(test_input($_POST['exactSearch'] ?? ""), FILTER_VALIDATE_BOOLEAN); 
$consider_spaces = filter_var(test_input($_POST['considerSpaces'] ?? ""), FILTER_VALIDATE_BOOLEAN); 
$search_intervals = filter_var(test_input($_POST['transpose'] ?? ""), FILTER_VALIDATE_BOOLEAN); 
$first_notes = test_input($_POST['firstNotes'] ?? "");

// Fixed param (controllers to be added)
$separator = 1;
$unisons = 0;


// Load start from database
require_once('../serverConfig.php');

$dataStaves = selectStartingStave($pdo, $id_staves);

$title_input = $dataStaves[0];
$start_melody_id = $dataStaves[1];
$author_input = $dataStaves[2];
$ms_input = $dataStaves[3];
$f_input = $dataStaves[4];
$start_melody = json_decode($dataStaves[5])[0];
$lines_start = explode("\n", $start_melody);  
$start_text_string = json_decode($dataStaves[6])[0];
$start_text = explode("\n", $start_text_string);  

// SELECT REPERTORY (as $data)
// Load cantus data
$id_user = -1;

if ($repertory > 0){
  $filepath = "../../json/cantus_{$repertory}_{$batch}.json";
  $cantus_file = file_get_contents($filepath);
  $data = json_decode($cantus_file, true); // Add true to decode as an associative array
} 
// or search in database
else {
  $user_info = checkUserPermission($pdo);
  if ($user_info != null) {
    $id_user = $user_info[0];
  }else{
    $id_user = -1;
  }

  $data = getDataFromDB($pdo, $id_user);
}

$count_data = count($data);

storeAccess($id_user, $id_staves, $start_melody_id, $repertory, "searchSong", $pdo);

$line_setup = array_fill(0, count($lines_start), 0);

$start_time = microtime(true);
$checked_melodic_sequences = [];

/************
* Analysis *
************/

if (!empty($lines_start)) {
  $startLine = test_input($_POST['startLine'] ?? "");
  // Loop through lines of start melody
  for ($ln = $startLine; $ln < count($lines_start); $ln++) {

    // Get start line to analyse
    $line_start = $lines_start[$ln];
    // clean (and keep copy)
    $line_start = str_replace("'", "", $line_start);
    $line_start_original = $line_start;
    $line_start = cleanLine($line_start);
    $line_start = filter_first_notes($line_start, $first_notes);
    logMessage($line_start);
    
    $line_start_directionless = "";
    $line_start_direction = "";
    
    // Trasform source in intervals
    if ($search_intervals) {
      $line_start = notesToIntervals($line_start);
    }
    
    if (!$consider_spaces) {
      $line_start = str_replace(' ', '', $line_start);
    }
    
    if (in_array($line_start, $checked_melodic_sequences)) {
      continue;
    }else{
        $checked_melodic_sequences[] = $line_start;
    };
    
    // Store line_start before needlemanWunsch
    // It will need to be restored at every compare_line loop
    $line_start_reset = $line_start;
    if ($ln == $startLine) {
      $startData = test_input($_POST['startCompareData'] ?? "");
    }else{
      $startData = 0;
    }
    
    // Loop through melodies to compare
    for ($i = $startData; $i < $count_data; $i++) {
      $target_repertory_id = $data[$i][2];
      // Avoid comparing the start melody with itself
      if ($target_repertory_id == $start_melody_id) continue;
      
      // Split compared melody into lines
      if ($repertory == 0) {
        // This splits the melodies in the DB
        $lines = explode("\\n", $data[$i][1]);
      }else{
        // This splits the melodies from Cantus
        $lines = explode("\n", $data[$i][1]);
      }
      
      // Loop through compared melody lines
      for ($j = 0; $j < count($lines); $j++){
          
          $line_compare = preg_replace('/\'|\"/', '', $lines[$j]);
          // Store line before cleaning (possibily needed for display)
          $line_compare_original = $line_compare;
          $line_compare = cleanLine($line_compare);
          
          if (!$consider_spaces) {
            $line_compare = str_replace(' ', '', $line_compare);
          }
          // restore start_line
          $line_start = $line_start_reset;
          // Reset matching score at each line
          $score = 100;
          // Reset values (not used)
          $transposed = "";
          $equivalence = "";
          $shift = "";
          
          // Perform simplest search if exact line and no tolerance
          // if ($tolerance == 100) {
          //   // Convert second line into intervals if required
          //   if ($search_intervals) {
          //     $line_compare = notesToIntervals($line_compare);
          //   }
          // 
          //   // Split process, either euqals or substring
          //   if ($exact_search) {
          //     if ($line_start != $line_compare){
          //       $score = 0;
          //       $matches = [1];
          // 
          //     }
          //   }else if ($line_start == $line_compare 
          //   or strpos($line_start, $line_compare) !== false 
          //   or strpos($line_compare, $line_start) !== false) {
          //       $score = 100;
          //       $matches = [1];
          //   }
          // 
          // }
          // 
          
          // SEARCH INTERVALS
          if ($search_intervals) {
              // Just compare the intervals, without considering their actual direction
              // Many false positives will come out of this, but they will be filtered out
              // by the compare_aligned_lines_intervals function
              $line_start_directionless = str_replace(['+', '-', '='], '', $line_start); // DO NOT MOVE ABOVE
              $line_start_direction = preg_replace('/[^+\-=]/', '', $line_start); // DO NOT MOVE ABOVE
              
              $line_compare = notesToIntervals($line_compare);
              $line_compare_directionless = str_replace(['+', '-', '='], '', $line_compare);
              $line_compare_direction = preg_replace('/[^+\-=]/', '', $line_compare);
              
              list($line_start, $line_compare, $nw_complete) = needlemanWunsch($line_start_directionless, $line_compare_directionless);
              $equivalence = " <br>*$line_start<br>*$line_compare";
              if (!$exact_search) {
                list($line_start, $line_compare) = trimSequences($line_start, $line_compare);
              }
              // Get coefficient of similarity and a list of correspondences
              list($sym, $matches) = compare_aligned_lines_intervals($line_start, $line_compare, $line_start_direction, $line_compare_direction);
              
              // Get percentage of similarity
              $score = $sym * 100;
              // Limit to 100%
              if ($score > 100) $score = 100;
              // Round to two decimal places  
              $score = round($score, 2);
          } 
          // SEARCH PITCHES
          else {
            

              list($line_start, $line_compare, $nw_complete) = needlemanWunsch($line_start, $line_compare);
              
              if ($nw_complete == false) {
                break;
              }

              $equivalence = " <br>$line_start<br>$line_compare";
              $line_start_pre_trim = $line_start;
              $line_compare_pre_trim = $line_compare;
              if (!$exact_search) {
                list($line_start, $line_compare) = trimSequences($line_start, $line_compare);
              }
              // Get coefficient of similarity and a list of correspondences
              $sym = compare_aligned_lines($line_start, $line_compare)[0];
              if ($data[$i][0] == "1481" && $ln == 0 && $j == 2){
                logMessage("*$line_start*");
                logMessage("*$line_compare*");
                logMessage($nw_complete);
                logMessage($sym);
                logMessage("------");
              }
              // Get percentage of similarity
          		$score = $sym * 100;
              // Limit to 100%
              if ($score > 100) $score = 100;
              // Round to two decimal places  
              $score = round($score, 2);
              
              if ($score >= $tolerance) {
                $matches = compare_aligned_lines($line_start_pre_trim, $line_compare_pre_trim)[1];
              }
          }
          
          // Assign bonus if last note matches
          if (substr($line_start, -1) == substr($line_compare, -1)) {
            $sym += 0.1;
          }
          
          // Print melodies if the score is above the tolerance threshold
          if ($score >= $tolerance) {
              
            // If there is at least one match for the start line
            if ($line_setup[$ln] == 0) {
              // Setup table to accomodate results and print start line$text_ln = isset($start_text[$ln]) ? $start_text[$ln] : "";
              $text_ln = isset($start_text[$ln]) ? $start_text[$ln] : "";
              $getLineStavesUI_startLine = [$line_start_original, $start_melody_medmel_id, $start_melody_id, $ms_input, $f_input, $title_input, $author_input, $text_ln, $ln];
              $line_setup[$ln] = -1;
            }
            else{
              $getLineStavesUI_startLine = -1;
            }
              
            // Process text line of the match (compared melody)
            $textLines = explode('\n', $data[$i][7]);
            $txtLine = '';
            try{
              if(isset($textLines[$j])){
                // $txtLine contains text in staves i line j
                $txtLine = preg_replace('/\"/', '', $textLines[$j]);
              }
            } catch (Exception $e) { $txtLine = '';}
              
            // Clean attributes of the match (compared melody)
            $title = str_replace("'","&#39;", $data[$i][4]);
            $author = str_replace("'","&#39;", $data[$i][5]);
            $target_medmel_id = $data[$i][0];
            $target_ms = $data[$i][3];
        
                        
            // Caculate progress
            $updateProgressBar = (($i + 1) + ($ln * $count_data)) * 100 / ($count_data * count($lines_start));

            // Print match (compared melody)
            echo json_encode([$getLineStavesUI_startLine, $line_compare_original, $target_medmel_id, $target_repertory_id, $target_ms, $title, $author, $data[$i][6], $txtLine, $j, $score, $transposed, $shift, $equivalence, json_encode($matches), $ln, $i, $updateProgressBar]);

            ob_flush();
            flush();
    		  }
        }
        if ($i % 500 == 0) {
          $updateProgressBar = (($i + 1) + ($ln * $count_data)) * 100 / ($count_data * count($lines_start));
          echo json_encode(["progress", $updateProgressBar, $ln, $i]);
          ob_flush();
          flush();   
        }
      }  
      
      $updateProgressBar = (($i + 1) + ($ln * $count_data)) * 100 / ($count_data * count($lines_start));
      echo json_encode(["progress", $updateProgressBar, $ln, $i]);
      ob_flush();
      flush();      
    }
    
    echo json_encode(["end_progress"]);
    ob_flush();
    flush();
}


// Performance check
$end_time = microtime(true);
$execution_time = $end_time - $start_time;

echo json_encode(["end", number_format($execution_time, 6)]);
ob_flush();
flush();
ob_end_flush();

/************
* Utilities *
*************/

function cleanLine($str, $ignore_plicas = true) {
  $str = str_replace('_h', 'b', $str);
  $str = str_replace('+a', 'u', $str);
  $str = str_replace('+b', 'p', $str);
  $str = str_replace('+h', 'q', $str);
  $str = str_replace('+c', 'r', $str);
  $str = str_replace('+d', 's', $str);
  $str = str_replace("*G", "J", $str);
  $str = str_replace(array("%", "/", "-", "[", "]", "'", "&039;"),"", $str);
  $str = preg_replace("/[CFGDA]\d ?|[bh]} ?/i", "", $str);

  // Plicas are always treated as simple notes (a controller will need to be added)
  if ($ignore_plicas == true) {
    $str = str_replace("(", "", $str);
    $str = str_replace(")", "", $str);
  }
  $str = trim($str);
  return $str;
}

function calculateSum($expression) {
    // Use regular expression to find all numbers (positive or negative)
    preg_match_all('/-?\d+/', $expression, $matches);

    // Extracted numbers are stored in $matches[0]
    $numbers = $matches[0];

    // Convert the strings to integers and sum them
    $totalSum = array_sum(array_map('intval', $numbers));

    return $totalSum;
}

// *** FAST BUT PROBLEMATIC
// function needlemanWunsch($sequence1, $sequence2, $match = 1, $mismatch = -1, $gap = -1) {
//     $len1 = strlen($sequence1) + 1;
//     $len2 = strlen($sequence2) + 1;
// 
//     $maxConsecutiveMismatch = 8;
// 
//     // Initialize the scoring matrix
//     $matrix = array_fill(0, $len1, array_fill(0, $len2, 0));
// 
//     // Initialize the first row and column
//     for ($i = 0; $i < $len1; $i++) {
//         $matrix[$i][0] = $i * $gap;
//     }
// 
//     for ($j = 0; $j < $len2; $j++) {
//         $matrix[0][$j] = $j * $gap;
//     }
// 
//     // Fill in the scoring matrix
//     for ($i = 1; $i < $len1; $i++) {
//         for ($j = 1; $j < $len2; $j++) {
//             $matchValue = ($sequence1[$i - 1] == $sequence2[$j - 1]) ? $match : $mismatch;
//             $matrix[$i][$j] = max(
//                 $matrix[$i - 1][$j - 1] + $matchValue,
//                 $matrix[$i - 1][$j] + $gap,
//                 $matrix[$i][$j - 1] + $gap
//             );
//         }
//     }
// 
//     // Traceback to find the aligned sequences
//     $alignedSeq1 = "";
//     $alignedSeq2 = "";
//     $i = $len1 - 1;
//     $j = $len2 - 1;
// 
//     $consecutiveMismatch = 0; // Counter for consecutive mismatches
//     $tracebackCompleted = false;
// 
//     while (($i > 0 || $j > 0) && !$tracebackCompleted) {
//       if ($i > 0 && $j > 0 && $matrix[$i][$j] == $matrix[$i - 1][$j - 1] + (($sequence1[$i - 1] == $sequence2[$j - 1]) ? $match : $mismatch)) {
//           $alignedSeq1 = $sequence1[$i - 1] . $alignedSeq1;
//           $alignedSeq2 = $sequence2[$j - 1] . $alignedSeq2;
//           $i--;
//           $j--;
//           $consecutiveMismatch = 0; // Reset consecutive mismatch counter
//         } elseif ($i > 0 && $j > 0 && isset($matrix[$i - 1][$j]) && $matrix[$i][$j] == $matrix[$i - 1][$j] + $gap) {
//           $alignedSeq1 = $sequence1[$i - 1] . $alignedSeq1;
//           $alignedSeq2 = "|" . $alignedSeq2;
//           $i--;
//           $consecutiveMismatch++;
//       } else {
//           if ($j > 0) { // Add this condition to ensure $j doesn't become negative
//               $alignedSeq1 = "|" . $alignedSeq1;
//               $alignedSeq2 = $sequence2[$j - 1] . $alignedSeq2;
//           }
//           $j--;
//           $consecutiveMismatch++;
//       }
//       // Check if consecutive mismatches exceed the threshold
//       if ($consecutiveMismatch > $maxConsecutiveMismatch && ($i <= 0 || $j <= 0)) {
//           $tracebackCompleted = true;
//       }
//    }
// 
//     // Trim the aligned sequences if necessary
//     if ($tracebackCompleted) {
//         return array("", "", false);
//     }
// 
//     return array($alignedSeq1, $alignedSeq2, true);
// }


// *** SLOW BUT PRECISE.. BUT SLOOOOOW
function needlemanWunsch($seq1, $seq2, $match_score = 1, $mismatch = -1, $gap = -1) {
  $len1 = strlen($seq1);
  $len2 = strlen($seq2);

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
          array_unshift($aligned_seq2, '|');
          $i--;
      } elseif ($pointer === 'L') {
          array_unshift($aligned_seq1, '|');
          array_unshift($aligned_seq2, $seq2[$j-1]);
          $j--;
      }
  }

  while ($i > 0) {
      array_unshift($aligned_seq1, $seq1[$i-1]);
      array_unshift($aligned_seq2, '|');
      $i--;
  }
  while ($j > 0) {
      array_unshift($aligned_seq1, '|');
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

function compare_aligned_lines($line_start, $line_compare) {
    global $consider_spaces;
    $matches = [];
    $compared_elements = 0;

    for ($char_n = 0; $char_n < strlen($line_start); $char_n++){
        $char_start = $line_start[$char_n];
        $char_compare = $line_compare[$char_n];
        // Match!
        if ($char_start == $char_compare and $char_start != "|") {
            // False match, just two spaces allined
            if ($char_start == " ") {
                $matches[$char_n] = " ";
            }
            // Actual match
            else {
                $matches[$char_n] = 1;
                $compared_elements += 1;
            }
        // Mismatch but between a separation and a space
        } elseif (($char_start == "|" or $char_start == "-") and $char_compare == " ") {
            $matches[$char_n] = " ";
            if ($consider_spaces) {
              $compared_elements += 0.2;
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

function compare_aligned_lines_intervals($line_start, $line_compare, $direction_start, $direction_compare) {
    global $consider_spaces;
    $matches = [];
    $compared_elements = 0;
    $start_note_count = 0;
    $compare_note_count = 0;
    
    for ($char_n = 0; $char_n < strlen($line_start); $char_n++){
        $char_start = $line_start[$char_n];
        $char_compare = $line_compare[$char_n];
        if ($start_note_count < strlen($direction_start)){
          $dir_start = $direction_start[$start_note_count];
        }
        if ($compare_note_count < strlen($direction_compare)){
          $dir_compare = $direction_compare[$compare_note_count];
        }
        
        // Match!
        if ($char_start == $char_compare and $char_start != "|" and $dir_start == $dir_compare) {
            // False match, just two spaces allined
            if ($char_start == " ") {
                $matches[$char_n] = " ";
            }
            // Actual match
            else {
                $matches[$char_n] = 1;
                $compared_elements += 1;
            }
            if (is_numeric($char_start)) {$start_note_count++;}
            if (is_numeric($char_compare)) {$compare_note_count++;}

        // Mismatch but between a separation and a space
        } elseif (($char_start == "|" or $char_start == "-") and $char_compare == " ") {
            $matches[$char_n] = " ";
            if ($consider_spaces) {
              $compared_elements += 0.5;
            }
        } 
      
        // Mismatch, note against "|", or against a " "
        elseif ($char_compare == "|" or $char_compare == "-" or $char_compare == " ") {
          // skip, when used for colors, 
          // but take into consideration for the ratio of matches/mismatches  
          $matches[$char_n] = " ";
          $compared_elements += 1;
          if (is_numeric($char_start)) {$start_note_count++;}
        }
        
        // Mismatch, but may be due to interpretation (note that _h is treated as b)
        elseif ((($char_start == "1" and $char_compare == "2") or
                 ($char_start == "2" and $char_compare == "1")) and 
                $dir_start == $dir_compare) {
          $matches[$char_n] = 0.5;
          $compared_elements += 1;
          $start_note_count++;
          $compare_note_count++;
        } else {
          $matches[$char_n] = 0;
          $compared_elements += 1;   
          if (is_numeric($char_start)) {$start_note_count++;}
          if (is_numeric($char_compare)) {$compare_note_count++;}
        }
    }
    array_unshift($matches, 1);
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

function trimSequences($seq1, $seq2) {
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

function checkUserPermission($pdo) {
  
    // Check user credentials
    $email = test_input($_POST['email'] ?? '');
    $password = test_input($_POST['password'] ?? '');
    $id_user = '1000';
    $user_permission = 4;

    if ($email != "") {
      $query = "
        SELECT id_user, user_permission
        FROM mm_users
        WHERE email = :email
        AND password = :password
      ";

      $check = $pdo->prepare($query);
      $check->bindParam(':email', $email, PDO::PARAM_STR);
      $check->bindParam(':password', $password, PDO::PARAM_STR);
      $check->execute();
      
      while ($row1 = $check->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
        // 0:id_user 1:user_permission
        $id_user = $row1[0];
        $user_permission = $row1[1];
        return [$id_user, $user_permission];
      }
    }
}

function selectStartingStave($pdo, $id_staves) {
  $query = "
      SELECT *
      FROM mm_staves_stored
      WHERE id_staves = :id_staves
  ";

  $check = $pdo->prepare($query);
  $check->bindParam(':id_staves', $id_staves, PDO::PARAM_STR);
  $check->execute();

  $stave = $check->fetch(PDO::FETCH_ASSOC);

  if (!$stave) {
      return;
  }
  else {
      $check->execute();
    while ($row = $check->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {

      //0:id_staves 1:title 2:id 3:author 4:language 5:ms 6:f 7:modernStyle 8:oldStyle 9:staves(JSON) 10:multiple-text 11:multiple-Annotations 12:visibility 13:id_user(publisher)
      $dataStaves = array($row[1],$row[2],$row[3],$row[5],$row[6],$row[9],$row[10]);
      
      return $dataStaves;
    }
  }
}

function getDataFromDB($pdo, $id_user) {  
  $selectLanguage = test_input($_POST['lang'] ?? '');
  if ($selectLanguage != '') {
    $where = "AND language = '".$selectLanguage."'";
  } else {
    $where = "";
  }
    
  // Start searching in the db  
  $query = "
    SELECT id_staves, JSON_EXTRACT(staves, '$[0]'), id, ms, f, title, author, JSON_EXTRACT(text, '$[0]'), id_user
    FROM mm_staves_stored
    WHERE (:id_user1=10 OR visibility = 2 OR id_user = :id_user2) $where
    ORDER BY id
  ";

  $check = $pdo->prepare($query);
  $check->bindParam(':id_user1', $id_user, PDO::PARAM_INT);
  $check->bindParam(':id_user2', $id_user, PDO::PARAM_INT);
  $check->execute();
  $data = array();

  while ($row = $check->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT))
    {
      // 0:id_staves, 1:JSON_EXTRACT(staves, '$[0]'), 2:id, 3:ms, 4:f, 5:title, 6:author, 7:JSON_EXTRACT(text, '$[0]'), 
      $a = array($row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7]);
      array_push($data, $a);
    }
  return $data;
}

function filter_first_notes($line_start, $first_notes){
  if ($first_notes == "all") {
    logMessage("first_notes all..." . $line_start);
    return $line_start;
  } else {
    // 1. explode into parts
    $parts = explode(" ", $line_start);
    if ($first_notes > count($parts)){
      logMessage("Failed: $first_notes - ". count($parts));
      return $line_start;
    }
    logMessage("Only $first_notes syls");

    // 2. take the first $X items
    $firstX = array_slice($parts, 0, $first_notes);

    // 3. implode back into a string
    $result = implode(" ", $firstX);

    return $result;
  }
}

function logMessage($message, $level = 'INFO', $logFile = 'searchSong.log') {
    // Define the format of the log entry: [YYYY-MM-DD HH:MM:SS] [LEVEL] Message
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] $message" . PHP_EOL;
    
    // Append the log entry to the file
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}
function logArray($array, $level = 'INFO', $logFile = 'searchSong.log') {
    // Convert array to a readable string
    $message = print_r($array, true);
    
    // Define the format of the log entry: [YYYY-MM-DD HH:MM:SS] [LEVEL] Message
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] " . PHP_EOL . $message . PHP_EOL;
    
    // Append the log entry to the file
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}
?>