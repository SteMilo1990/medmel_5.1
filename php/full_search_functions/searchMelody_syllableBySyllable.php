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

$search_intervals = filter_var(test_input($_POST['transpose'] ?? ""), FILTER_VALIDATE_BOOLEAN); 
$id_staves    = test_input($_POST['id_staves'] ?? "");
$repertory    = test_input($_POST['repertory'] ?? "");
$batch        = test_input($_POST['batch'] ?? "");
$exact_search = filter_var(test_input($_POST['exactSearch'] ?? ""), FILTER_VALIDATE_BOOLEAN); 
$consider_spaces = filter_var(test_input($_POST['considerSpaces'] ?? ""), FILTER_VALIDATE_BOOLEAN); 
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
  $id_user = $user_info[0];

  $data = getDataFromDB($pdo, $id_user);
}

if ($id_user != 10) {
  storeAccess($id_user, $id_staves, $start_melody_id, $repertory, $pdo);
}

$line_setup = array_fill(0, count($lines_start), 0);

$start_time = microtime(true);
$checked_melodic_sequences = [];

/************
* Analysis *
************/
include "../calculateIntervals.php";

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
    
    if ($search_intervals == 1) {
      $line_start = notesToIntervals($line_start);
    }
    
    if (in_array($line_start, $checked_melodic_sequences)) {
      continue;
    }else{
        $checked_melodic_sequences[] = $line_start;
    };

    // Store line_start before needlemanWunsch
    // It will need to be restored at every compare_line loop
    $line_start_reset = $line_start;
    // Divide start line into syllables
    if (!$consider_spaces) {
      $line_start = str_replace(' ', '', $line_start);
      // divide into notes (there are no more syllables)
      $syls_start = str_split($line_start);
    }else{
      // divide into syllables
      $syls_start = explode(" ", $line_start);
    }
    
    // Start syllable count    
    $nln = count($syls_start);

    if ($ln == $startLine) {
      $startData = test_input($_POST['startCompareData'] ?? "");
    }else{
      $startData = 0;
    }
    // Loop through melodies to compare
    for ($i = $startData; $i < count($data); $i++) {

      // Avoid comparing the start melody with itself
      if ($data[$i][0] == $start_melody_medmel_id) continue;
      
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
          
          // Always consider spaces, for now-otherwise it'ìs a mess
          if (!$consider_spaces) {
            $line_compare = str_replace(' ', '', $line_compare);
            // divide into notes (there are no more syllables)
            $syls_strLine = str_split($line_compare);
          }else{
            // divide into syllables
            $syls_strLine = explode(" ", $line_compare);
          }
          $nln2 = count($syls_strLine);

          // restore start_line
          $line_start = $line_start_reset;
          // Reset matching score at each line
          $score = 100;
          // Reset values (not used)
          $transposed = "";
          $equivalence = "";
          $shift = "";
          
          // Transform compared line into intervals if necessary
          if ($search_intervals == 1) $line_compare = notesToIntervals($line_compare);
          // Filter through Levenshtein to limit the number of Needleman-Wunsch comparisons
          $preliminary_symil = 1;
          
          //$preliminary_distance = levenshtein($line_start, $line_compare);
          // $preliminary_simil = 1 - ($preliminary_distance / max(strlen($line_start), strlen($line_compare)));

          if ($preliminary_symil < 0.6) continue;
          $equivalence = $preliminary_symil;
          $syls_strLine = explode(" ", $line_compare);
          $nln2 = count($syls_strLine); 
          // always prevent this for now; 
          // $search_intervals = false;
          if ($search_intervals) {
              // $line_start_directionless = str_replace(['+', '-', '='], '', $line_start);
              // $line_compare_directionless = str_replace(['+', '-', '='], '', $line_compare);
              // $line_start_direction = preg_replace('/[^+\-=]/', '', $line_start);
              // $line_compare_direction = preg_replace('/[^+\-=]/', '', $line_compare);
              // 
              // // INCOMPLETE
              // $score = sylBySyl($line_start_directionless, $line_compare_directionless);
              // $equivalence = $preliminary_symil. " <br>$line_start<br>$line_compare";
              // if (!$exact_search) {
              //   list($line_start, $line_compare) = trimSequences($line_start, $line_compare);
              // }
              // // Get coefficient of similarity and a list of correspondences
              // list($sym, $matches) = compare_aligned_lines_intervals($line_start, $line_compare, $line_start_direction, $line_compare_direction);
              list($score, $matches) = sylBySyl($syls_start, $syls_strLine, $nln, $nln2);

          } else {
            list($score, $matches) = sylBySyl($syls_start, $syls_strLine, $nln, $nln2);
            // echo json_encode($matches);
          }
          
      
            // Print melodies if the score is above the tolerance threshold
            if ($score >= $tolerance) {
              // list($line_start, $line_compare) = needlemanWunsch($line_start, $line_compare);
              // $syls_start = explode(" ", $line_start);
              // $syls_strLine = explode(" ", $line_compare);
              // $matches = compare_aligned_lines($syls_start, $syls_strLine,  $nln, $nln2);

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
                          
              // Caculate progress
              $updateProgressBar = (($i + 1) + ($ln * count($data))) * 100 / (count($data) * count($lines_start));

              // Print match (compared melody)
              echo json_encode([$getLineStavesUI_startLine, $line_compare_original, $data[$i][0], $data[$i][2], $data[$i][3], $title, $author, $data[$i][6], $txtLine, $j, $score, $transposed, $shift, $equivalence, json_encode($matches), $ln, $i, $updateProgressBar]);

              ob_flush();
              flush();
      		  }
          }
          if ($i % 500 == 0) {
            $updateProgressBar = (($i + 1) + ($ln * count($data))) * 100 / (count($data) * count($lines_start));
            echo json_encode(["progress", $updateProgressBar, $ln, $i]);
            ob_flush();
            flush();   
          }
      }    
      $updateProgressBar = (($i + 1) + ($ln * count($data))) * 100 / (count($data) * count($lines_start));
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

function compare_aligned_lines_intervals($line_start, $line_compare, $direction_start, $direction_compare) {
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

        // Match
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
          if (is_numeric($char_compare)) {$compare_note_count++;
          }
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
    WHERE (visibility = 2 OR id_user = $id_user) $where
    ORDER BY id
  ";

  $check = $pdo->prepare($query);
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

function sylBySyl($syls_start, $syls_strLine, $nln, $nln2) {
  $symilarity = 0;
  $matches_ar = [];
  

  
  // Select longest line as start line
  if ($nln < $nln2){ 
    $syl_n = $nln; 
    $max_syl_n = $nln2; 
  } else {
    $syl_n = $nln2;
    $max_syl_n = $nln; 
  }
  
  // Loop through syllables
  for ($syl = 0; $syl < $syl_n; $syl++) {
    $syl_start = $syls_start[$syl];
    $syl_strLine = $syls_strLine[$syl];
    $max_len_syl = max(strlen($syl_start), strlen($syl_strLine));
    // The two syl correspond
    if ($syl_start == $syl_strLine) {
      $match = 1;
    }
    // simple mismatch if comparing 1 agaist 1 note (to avoid unnecessary Levenshtein calculations)
    else if ($max_len_syl == 1) {
      $match = 0;
    } 
    // Check Levenstein if mismatch if at least one of the syllables is a ligature
    else {
      $match = 1 - (levenshtein($syl_start, $syl_strLine) / $max_len_syl);
    }

    // Keep count of how many syllables are equal
    $symilarity += $match;
    for ($i = 0; $i < strlen($syl_strLine); $i++){
      array_push($matches_ar, round($match, 2));
    }
  } 
      
  // Assign bonus if last note matches
  if ($syls_start[$nln-1] == $syls_strLine[$nln2-1]) $symilarity += 0.1;

  // Get percentage of similarity 
  $score = $symilarity / $max_syl_n * 100;

  // Limit to 100%
  if ($score > 100) $score = 100;
  
  // Round to two decimal places  
  $score = round($score, 2);
  return [$score, $matches_ar];
}

function logMessage($message, $level = 'INFO', $logFile = 'app.log') {
    // Define the format of the log entry: [YYYY-MM-DD HH:MM:SS] [LEVEL] Message
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] [$level] $message" . PHP_EOL;
    
    // Append the log entry to the file
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

function compare_aligned_lines($syls_start, $syls_compare, $nln, $nln2) {
  if ($nln < $nln2){ 
    $syl_n = $nln; 
  } else {
    $syl_n = $nln2;
  }

  $matches = [];

  for ($i = 0; $i < $syl_n; $i++){
      $s_start = $syls_start[$i];
      $s_compare = $syls_compare[$i];

      for ($j = 0; $j < strlen($s_compare); $j++) {
        if ($j < strlen($s_start)) {
          if ($s_start[$j] == $s_compare[$j]){
            array_push($matches, 1);
          } elseif (($s_start[$j]== "h" and $s_compare[$j] == "b") or
                  ($s_start[$j] == "b" and $s_compare[$j] == "h")){
            array_push($matches, 0.5);
          } else {
            array_push($matches, 0);
          }
        } else {
          array_push($matches, 0);
        }
      }
      if ($i != $syl_n-1) array_push($matches, " ");
  }

  return $matches;
}

function needlemanWunsch($seq1, $seq2, $match_score = 1, $mismatch = 0, $gap = -1) {
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


function storeAccess($id_user, $id_staves, $start_melody_id, $repertory, $pdo) {
    // Get the IP address of the user
    $ip = $_SERVER['REMOTE_ADDR'];
    
    // Get the current date
    $date = date('Y-m-d H:i:s');
    
    // Prepare the SQL statement
    $sql = "INSERT INTO mm_access (id_user, ip, date, id_staves, start_melody_id, repertory) VALUES (:id_user, :ip, :date, :id_staves, :start_melody_id, :repertory)";
    
    // Prepare and execute the statement
    $stmt = $pdo->prepare($sql);
    
    // Bind the parameters
    $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
    $stmt->bindParam(':ip', $ip, PDO::PARAM_STR);
    $stmt->bindParam(':date', $date, PDO::PARAM_STR);
    $stmt->bindParam(':id_staves', $id_staves, PDO::PARAM_INT);
    $stmt->bindParam(':start_melody_id', $start_melody_id, PDO::PARAM_INT);
    $stmt->bindParam(':repertory', $repertory, PDO::PARAM_STR);
    
    // Execute the query
    if ($stmt->execute()) {
        return "Access stored successfully.";
    } else {
        return "Error storing access: " . implode(", ", $stmt->errorInfo());
    }
}


?>