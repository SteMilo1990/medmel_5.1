<?php
// Turn off all error reporting
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
error_reporting(0);

// Set custom error handler
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    $message = "PHP ERROR [$errno] $errstr in $errfile on line $errline";
    logMessage($message, 'ERROR');

    // Optional: also continue with PHP's default error logging
    return false; // return false to allow normal error handling too
});

// Optional: catch fatal errors
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        $message = "FATAL ERROR [{$error['type']}] {$error['message']} in {$error['file']} on line {$error['line']}";
        logMessage($message, 'FATAL');
    }
});

include '../../php/functions_shared.php';
include '../../php/modern_paths.php';
include '../../php/functions_printStaves.php';

function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

$notes = $text_string = $title = $id = $author = $language = $ms = $f_input= $annotations = $use_manual_RS = $manual_RS = "";
$b_in_key = $noteCount = $totalElementsCount = 0;
$repetition_pattern = [[null, null]];

// Space for ms SIGLUM

$titles = $ids = $authors = [];
$line_number = 1;

$number_staves = 1;

$number_staves = test_input($_POST["number_staves"]);
$ms_staves = [];
$ms_array = json_decode($_POST["ms_staves"] ?? '[]');
$notes = test_input($_POST["notes"]);
$text_string = test_input($_POST["text_string"]);
$titles = json_decode($_POST["titles"]);
$ids = json_decode($_POST["ids"]);
$authors = json_decode($_POST["authors"]);
$folios = json_decode($_POST["folios"]);
$annotations = json_decode($_POST["annotations"]);
$set_of_lines = json_decode($_POST["setOfLines"]);
$use_id_as_staff_label = filter_var($_POST["useIdAsStaffLabel"], FILTER_VALIDATE_BOOLEAN);

$staff_label_space = 40;
if ($use_id_as_staff_label){
  $staff_label_space = get_width_of_longest_id($ids);
}

if (isset($_COOKIE['plicaType'])){
  if($_COOKIE['plicaType'] == 1) {
    $plica_type = 1;
  } else {
    $plica_type = 0;
  }
}

if (isset($_COOKIE['textFont'])){
	if($_COOKIE['textFont'] == 0){
		$textFont = "Times New Roman";
	}else{
		if ($_COOKIE['textFont'] == "1"){
			$textFont = "Times New Roman";
		}elseif ($_COOKIE['textFont'] == "2"){
			$textFont = "EB Garamond";
		}elseif ($_COOKIE['textFont'] == "3"){
			$textFont = "Courier";
		}elseif ($_COOKIE['textFont'] == "4"){
			$textFont = "Roboto";
		}
	}
}else{
	$textFont = "Times New Roman";
}

if (isset($_COOKIE['fontSize'])){
	if($_COOKIE['fontSize'] == 0){
		$fontSize = 18;
	}else{
		$fontSize = $_COOKIE['fontSize'];
	}
}else{
	$fontSize = 18;
}

//clean note input
$notes_bar_separator = preg_replace( '/\n/', '|', $notes);
$notes_bar_separator = str_replace(" |", "|", $notes_bar_separator);
$notes_bar_separator = str_replace("| ", "|", $notes_bar_separator);
$notes_bar_separator = preg_replace( '/\n/', '', $notes_bar_separator);
$notes_bar_separator = str_replace( '+a', 'u', $notes_bar_separator);
$notes_bar_separator = str_replace( '+b', 'p', $notes_bar_separator);
$notes_bar_separator = str_replace( '+h', 'q', $notes_bar_separator);
$notes_bar_separator = str_replace( '+c', 'r', $notes_bar_separator);
$notes_bar_separator = str_replace("*G", "J", $notes_bar_separator);
$notes_bar_separator = str_replace("%}", "h}", $notes_bar_separator);

$notes_bar_separator = str_replace("/\[?[CFGDA][0-9]\]? ?/gmi", "",$notes_bar_separator); 

$notes_for_apostrophe = str_replace(array("(",")", "%", "+", "#", "/", "-", "[", "]", "b} ", "h} "),"", $notes_bar_separator);
$notes_for_b_in_key = str_replace(array("(",")", "%", "+", "#", "/", "-", "[", "]","'"),"", $notes_bar_separator);
$notes_in_Brackets_left = str_replace(array("(", ")", "#", "+", "_", "%", "/", "-", "]", "b} ", "b}", "h} ", "h}","'"), "", $notes_bar_separator);
$notes_in_Brackets_right = str_replace(array("(", ")", "#", "+", "_", "%", "/", "-", "[", "b} ", "b}", "h} ", "h}","'"), "", $notes_bar_separator);
$notes_for_mid_bar = str_replace(array("(", ")", "#", "+", "_", "%", "-", "[", "]", "b} ", "b}", "%} ", "h}","'"), "", $notes_bar_separator);
$notes_for_plica = str_replace(array(")", "%", "#", "+", "_", "/", "[", "]", "b} ", "b}", "h} ", "h}","'"),"", $notes_bar_separator);
$notes_for_natural = str_replace(array("(", ")", "#", "+", "_", "/", "[", "]", "b} ", "b}", "h} ", "h}","'"), "", $notes_bar_separator);
$notes_for_sharp = str_replace(array("(",")", "%", "+", "_", "/", "[", "]", "b} ", "b}", "h} ", "h}","'"),"", $notes_bar_separator);
$notes_for_flat = str_replace(array("(",")", "%", "+", "#", "/", "[", "]", "'", "b} ", "b}", "h} ", "h}"),"", $notes_bar_separator);
$notes_clean = str_replace(array("%", "(", ")", "#", "+", "_", "/","[", "]", "b} ", "b}", "h} ", "h}", "'"),"", $notes_bar_separator);

// String for Tone.js
$notes_for_js = str_replace(array("?", "-"),"", $notes_clean);
$duration_pattern = str_replace(array("J|", "A|","B|","H|","C|","D|","E|", "F|", "G|", "a|", "b|","h|", "c|", "d|", "e|", "f|", "g|", "u|", "p|", "q|"), "4", $notes_for_js);
$duration_pattern = str_replace(array("J ", "A ","B ","H ","C ","D ","E ", "F ", "G ", "a ", "b ", "h ","c ", "d ", "e ", "f ", "g ", "u ", "p ", "q "), "2", $duration_pattern);
$duration_pattern = str_replace(array("J", "A","B", "H", "C","D","E","F","G","a","b","h","c","d","e","f","g","u","p","q"), "1", $duration_pattern);
$notes_for_js = str_replace(array(" ","|"), "",$notes_for_js);

// create array to check apostrophe
$apostrophe_string = str_replace(array("'?","'-","'J", "'A", "'H", "'B", "'C", "'D", "'E", "'F", "'G", "'a", "'b", "'h", "'c", "'d", "'e", "'f", "'g", "'u", "'p", "'q"), "1", $notes_for_apostrophe);
$apostrophe_string = str_replace(array("'J", "'A", "'H", "'B", "'C", "'D", "'E", "'F", "'G", "'a", "'b", "'h", "'c", "'d", "'e", "'f", "'g", "'u", "'p", "'q"), "1", $apostrophe_string);
// Change all other notes to value "0"
$apostrophe_string = preg_replace("[a|b|c|d|e|f|g|h|u|p|q|J|A|B|C|D|E|F|G|H]", "0", $apostrophe_string);
// Separate LINES
$apostrophe_string_lines[] = explode("|", $apostrophe_string);
// separate GROUPS
foreach ($apostrophe_string_lines as $apostrophe_string_line) {
	foreach ($apostrophe_string_line as $apostrophe_string_group) {
		$apostrophe_string_groups_ar[] = explode(" ", $apostrophe_string_group);
  }
}

// create array to check b in key
$b_in_key_string = str_replace(array("b} J", "b} A", "b} H", "b} B", "b} C", "b} D", "b} E", "b} F", "b} G", "b} a", "b} b", "b} h", "b} c", "b} d", "b} e", "b} f", "b} g", "b} u", "b} p", "b} q"), "1", $notes_for_b_in_key);
$b_in_key_string = str_replace(array("b}J", "b}A", "b}H", "b}B", "b}C", "b}D", "b}E", "b}F", "b}G", "b}a", "b}b", "b}h", "b}c", "b}d", "b}e", "b}f", "b}g", "b}u", "b}p", "b}q"), "1", $b_in_key_string);
$b_in_key_string = str_replace(array("h} J", "h}A", "h}H", "h}B", "h}C", "h}D", "h}E", "h}F", "h}G", "h}a", "h}b", "h}h", "h}c", "h}d", "h}e", "h}f", "h}g", "h}u", "h}p", "h}q"), "1", $b_in_key_string);
$b_in_key_string = str_replace(array("h} J", "h} A", "h} H", "h} B", "h} C", "h} D", "h} E", "h} F", "h} G", "h} a", "h} b", "h} h", "h} c", "h} d", "h} e", "h} f", "h} g", "h} u", "h} p", "h} q"), "1", $b_in_key_string);

// Change all other notes to value "0"
$b_in_key_string = preg_replace("[a|b|c|d|e|f|g|h|u|p|q|J|A|B|C|D|E|F|G|H]", "0", $b_in_key_string);
// Separate LINES
$b_in_key_string_lines[] = explode("|", $b_in_key_string);
// separate GROUPS
foreach ($b_in_key_string_lines as $b_in_key_line) {
	foreach ($b_in_key_line as $b_in_key_group) {
		$b_in_key_notes_groups_ar[] = explode(" ", $b_in_key_group);
  }
}

// Calculate position of plicas
// Change notes preceded by parentesis to value "1"
$Plica_string = str_replace(array('(a', '(b', '(c', '(d', '(e', '(f', '(g', '(h', '(J', '(A', '(B', '(C', '(D', '(E', '(F', '(G', '(H', '(u', '(p', '(q'),   "1", $notes_for_plica);
// Change all other notes to value "0"
$Plica_string = preg_replace("[a|b|c|d|e|f|g|h|u|p|q|J|A|B|C|D|E|F|G|H]", "0", $Plica_string);
// Separate LINES
$Plica_string_lines[] = explode("|", $Plica_string);
// separate GROUPS
foreach ($Plica_string_lines as $Plica_line) {
	foreach ($Plica_line as $Plica_group) {
		$Plica_notes_groups_ar[] = explode(" ", $Plica_group);
  }
}

// Calculate position of natural alteration
// Change notes preceded by "%"" to value "1"
$Natural_string = str_replace(array( '%a', '%b', '%c', '%d', '%e', '%f', '%g', '%h', '%J', '%A', '%B', '%C', '%D', '%E', '%F', '%G', '%H', '%u', '%p', '%q'), "1", $notes_for_natural);
// Change all other notes to value "0"
$Natural_string = preg_replace("[a|b|c|d|e|f|g|h|u|p|q|J|A|B|C|D|E|F|G|H]", "0", $Natural_string);
// Separate LINES
$Natural_string_lines[] = explode("|", $Natural_string);
// separate GROUPS
foreach ($Natural_string_lines as $Natural_line) {
	foreach ($Natural_line as $Natural_group) {
		$Natural_notes_groups_ar[] = explode(" ", $Natural_group);
  }
}

// Calculate position of sharp alteration
// Change notes preceded by "#"" to value "1"
$Sharp_string = str_replace(array('#a', '#b', '#c', '#d', '#e', '#f', '#g', '#h', '#J', '#A', '#B', '#C', '#D', '#E', '#F', '#G', '#H', '#u', '#p', "#q"), "1", $notes_for_sharp);
// Change all other notes to value "0"
$Sharp_string = preg_replace("[a|b|c|d|e|f|g|h|J|A|B|C|D|E|F|G|H|u|p|q]", "0", $Sharp_string);
// Separate LINES
$Sharp_string_lines[] = explode("|", $Sharp_string);
// separate GROUPS
foreach ($Sharp_string_lines as $Sharp_line) {
	foreach ($Sharp_line as $Sharp_group) {
		$Sharp_notes_groups_ar[] = explode(" ", $Sharp_group);
  }
}

// Calculate position of flat alteration
// Change notes preceded by "_"" to value "1"
$Flat_string = str_replace(array( '_a', '_b', '_c', '_d', '_e', '_f', '_g', '_h', '_J', '_A', '_B', '_C', '_D', '_E', '_F', '_G', '_H', '_u', '_p', "_q"), "1", $notes_for_flat);
// Change all other notes to value "0"
$Flat_string = preg_replace("[a|b|c|d|e|f|g|h|J|A|B|C|D|E|F|G|H|u|p|q]", "0", $Flat_string);
// Separate LINES
$Flat_string_lines[] = explode("|", $Flat_string);
// separate GROUPS
foreach ($Flat_string_lines as $Flat_line) {
	foreach ($Flat_line as $Flat_group) {
		$Flat_notes_groups_ar[] = explode(" ", $Flat_group);
  }
}

// Calculate position of Middle bars
// Separate LINES
$Mid_bar_string_lines[] = explode("|", $notes_for_mid_bar);
// separate GROUPS
foreach ($Mid_bar_string_lines as $Mid_bar_line) {
	foreach ($Mid_bar_line as $Mid_bar_group) {
		$Mid_bar_notes_groups_ar[] = explode(" ", $Mid_bar_group);
  }
}

// Get melodic lines
$lines_ar = explode("|", $notes_clean);

// Count melodic lines
$n_of_lines = count($lines_ar);

// Clean text
$text_string = str_replace("- ","-", $text_string);
$text_string_clean = str_replace("-","- ", $text_string);
$text_string_clean = str_replace("  "," ", $text_string_clean);
$text_string_newline_to_bar_separator = str_replace("\n","|", $text_string_clean); // new line as text separator

// Divide textual lines
$text_line_ar = explode('|', $text_string_newline_to_bar_separator);

// Establish text height
if (strpos($notes, '*G') !== false) {
   $text_height = 146;
} else if (strpos($notes, 'A') !== false) {
   $text_height = 139;
} else if (strpos($notes, 'B') !== false) {
   $text_height = 133;
} else if (strpos($notes, 'H') !== false) { 
   $text_height = 133;
} else {
	$text_height = 129;
}

// Get postiton of notes_in_Brackets_left
// Change notes preceded by parentesis to value "1"
$Brackets_left_string = str_replace(array('[a', '[b', '[c', '[d', '[e', '[f', '[g', '[h', '[J', '[A', '[B', '[C', '[D', '[E', '[F', '[G', '[H', '[u', '[p', '[q', '[r]'),   "1", $notes_in_Brackets_left);
// Change all other notes to value "0"
$Brackets_left_string = preg_replace("[a|b|c|d|e|f|g|h|u|p|q|J|A|B|C|D|E|F|G|H]", "0", $Brackets_left_string);
// Separate LINES
$Brackets_left_lines[] = explode("|", $Brackets_left_string);
// separate GROUPS
foreach ($Brackets_left_lines as $Brackets_left_line) {
	foreach ($Brackets_left_line as $Brackets_left_group) {
		$Brackets_left_lines_groups_ar[] = explode(" ", $Brackets_left_group);
  	}
}

// Get postiton of notes_in_Brackets_right
// Change notes preceded by parentesis to value "1"
$Brackets_right_string = str_replace(array('a]', 'b]', 'c]', 'd]', 'e]', 'f]', 'g]', 'h]', 'J]', 'A]', 'B]', 'C]', 'D]', 'E]', 'F]', 'G]', 'H]', 'u]', 'p]', 'q]'),   "1", $notes_in_Brackets_right);
// Change all other notes to value "0"
$Brackets_right_string = preg_replace("[a|b|c|d|e|f|g|h|u|p|q|J|A|B|C|D|E|F|G|H]", "0", $Brackets_right_string);
// Separate LINES
$Brackets_right_lines[] = explode("|", $Brackets_right_string);
// separate GROUPS
foreach ($Brackets_right_lines as $Brackets_right_line) {
	foreach ($Brackets_right_line as $Brackets_right_group) {
		$Brackets_right_lines_groups_ar[] = explode(" ", $Brackets_right_group);
  }
}

// Get bar width for column
// Get number of syllable of the longest musical line
// for each line
for ($q = 0; $q < $n_of_lines; $q++) {
	//clean line array (necessary!)
	$q_this_line_ar = rtrim($lines_ar[$q], " \|");
	$q_this_line_ar = trim($q_this_line_ar,"\|");
	$q_this_line_ar = ltrim($q_this_line_ar," ");

	// Create a array of all note groups in the musical line
	$q_notes_ar = explode(' ', $q_this_line_ar);

	// Count note groups in line
	$q_n_of_note_groups[] = count($q_notes_ar);

	// Get number of syllables in longest line
	$longest_number_of_syl_in_line = max($q_n_of_note_groups);
}

// For each column ($qq) look at the line ($qqq)
for ($qq = 0; $qq < $n_of_lines; $qq++) {
	// Clean (necessary!)
	$qq_this_line_ar = rtrim($lines_ar[$qq] ?? '', " \|");
	$qq_this_line_ar = trim($qq_this_line_ar,"\|");
	$qq_this_line_ar = ltrim($qq_this_line_ar," ");
  
	// Create an array of all note gorups in the line
	$qq_notes_ar[] = explode(' ', $qq_this_line_ar); /**text*/
	$qq_this_text_line_ar = rtrim($text_line_ar[$qq] ?? '', " \|");
	$qq_this_text_line_ar = trim($qq_this_text_line_ar,"\|");
	$qq_this_text_line_ar = ltrim($qq_this_text_line_ar," ");
	$qq_text_ar[] = explode(' ', $qq_this_text_line_ar);
}

for ($yy = 0; $yy < $longest_number_of_syl_in_line; $yy++){
	for ($yyy = 0; $yyy < $n_of_lines; $yyy++) {
     // $column[] = strlen($qq_notes_ar[$yyy][$yy]); // to get the note names (and not the number of notes) omit strlen()
     $column[] = isset($qq_notes_ar[$yyy][$yy]) ? strlen($qq_notes_ar[$yyy][$yy]) : 0;

     /**text**/
     $text_column[] = isset($qq_text_ar[$yyy][$yy])
         ? mb_strlen(html_entity_decode($qq_text_ar[$yyy][$yy]))
         : 0;
	 }
}

$column_subar = array_chunk($column, $n_of_lines);
/**text**/
$text_column_subar = array_chunk($text_column, $n_of_lines);

for ($yyyy = 0; $yyyy < $longest_number_of_syl_in_line; $yyyy++) {

	$max_n_of_notes_in_column[] = max($column_subar[$yyyy]);

	/**text**/
	$max_n_of_letters_in_column[] = max($text_column_subar[$yyyy]);
	$thisDiffNotes_Text = max($text_column_subar[$yyyy])*($fontSize/1.6) - max($column_subar[$yyyy])*5;
	
  if ($thisDiffNotes_Text > 50) {
    $diffNotes_Text[] = $thisDiffNotes_Text-45;
  }

	else {$diffNotes_Text[] = 0;}
}

// Check if extra space is needed in clef
if (count($lines_ar)/100 >= 1){
  $extra_space = 12;
} else {
  $extra_space = 0;
}

// Calculate line width
$col_widths_ar = [];
// Get widtg of first slot (clef) (89, usually)
$clef_width = 70 + $extra_space + $fontSize + $staff_label_space;
// initialize x translate
$g_x_translate_columns = [$clef_width];
$line_width = $clef_width;
for ($column = 0; $column < count($max_n_of_notes_in_column); $column++) {
  $col_width = round(34 + 20 * $max_n_of_notes_in_column[$column] + $diffNotes_Text[$column]);
  $line_width += $col_width;
  array_push($g_x_translate_columns, $line_width);
  array_push($col_widths_ar, $col_width);
}
$line_width += 20; // add a bit of space at the end
// Calculate Melodic structure
// $apost = [];
// $SM = [];
// $SM[0] = 0;
// 
// $lines_ar_per_SM = str_replace(" ", "", $lines_ar);
// for ($ln = 0; $ln < $n_of_lines; $ln++) { // every line
// 	$lns_with_apostr = [];
// 	$apost_count = 0;
// 	$just_set_baby = array_key_exists($ln, $SM); // Does the line have a number assigned?
// 
// 	if ($just_set_baby != 1) { // Well, if not
// 			$SMmax = $SMmax +1; // Ajourn the number to be assigned
// 			$SM[$ln] = $SMmax;
//   } // and assign it
// 
// 	for ($ln2 = $ln+1; $ln2 < $n_of_lines; $ln2++) { // per every line
// 
// 		if (!array_key_exists($ln2, $SM)) {// if second line does not already have a letter
// 			$sym = 0; // reset the "coefficient of symilarity"
// 			$nln = count($qq_notes_ar[$ln]); // how many syl in line 1
// 			$nln2 = count($qq_notes_ar[$ln2]); // how many syl in line 2
// 
// 			if ($nln > $nln2) { // if line 1 in longer than line 2
// 				$syl_n = $nln; // compare for the longer comparable number of syl
//       } 
// 			else {
//         $syl_n = $nln2;
//       }
// 
// 			for ($syl = 0; $syl < $syl_n; $syl++) { // for every syl
// 
//         // if the two syl correspond set this variable to 1
//         if ($qq_notes_ar[$ln][$syl] == $qq_notes_ar[$ln2][$syl]){ 
// 					$symil = 1;
//         }
// 
//         //shift?
// 				else if (($qq_notes_ar[$ln][$syl] == $qq_notes_ar[$ln2][$syl+1]) && ($qq_notes_ar[$ln][$syl+1] == $qq_notes_ar[$ln2][$syl+2])) { 
//           // set to a value > 0 to count shift
// 					$symil = 0;
//         } 
//         // No correspondence
// 				else {
// 					$symil = 0;
//         }
// 
//         // keep count of how many syll are equal
// 				$sym += $symil;
//       } 
// 
//       // If last note correspondes: bonus
//       if (substr($lines_ar_per_SM[$ln], -1) == substr($lines_ar_per_SM[$ln2], -1)) {
//         $final_note_matches = 1;
//         $end_bonus = 0.1;
//       }else{
//         $final_note_matches = 0;
//         $end_bonus = 0;
//       };
// 			$sym_perc = ($sym + $end_bonus) / $syl_n; // find the percentage of similarity
// 			if ($sym_perc > 0.8 and $final_note_matches == 1) // if high percentage
// 				{$apost[$ln2] = $apost[$ln];
// 				$SM[$ln2] = $SM[$ln]; // the two lines have a same letter
// 			} // and no apostrophe
// 			else if (($sym_perc > 0.8 and $final_note_matches == 0) or ($sym_perc > 0.65 and $syl_n < 4) or ($sym_perc > 0.45 and $syl_n >= 4)) // if less percentage
// 				{$SM[$ln2] = $SM[$ln];// the two lins have a same letter
// 				 $apost[$ln2] = $apost[$ln] + $apost_count + 1;
// 				 for ($ln3 = $ln2; $ln3 < $n_of_lines; $ln3++){
// 				 	if ($lines_ar_per_SM[$ln2] == $lines_ar_per_SM[$ln3]){
// 				 		$SM[$ln3] = $SM[$ln2];
// 				 		$apost[$ln3] = $apost[$ln2];
// 				 	}
// 				 }
// 				$apost_count = $apost_count + 1;
// 				array_push($lns_with_apostr, $ln2);
//       } // and an apostrophe
// 		}//print_r($lns_with_apostr);
// 	}
// }
// 
// // Change the numeric value to alphabetic
// $alphaL = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X ','Y','Z');
// 
// $alphaG = array('α','β','γ','δ','ε','ζ','η','θ','ι','κ','λ','μ','ν','ξ','ο','π','ρ','σ','τ','υ','φ','χ','ψ','ω');
// 
// if($_COOKIE['alpha'] == 0) {
//   $alpha = $alphaL; // Chose which alphabet to use
// } else {
//   $alpha = $alphaG; // Chose which alphabet to use
// }
// 
// for ($i = 0; $i < $n_of_lines; $i++) {
// 	$x = $SM[$i];
// 	if ($x < count($alpha)){
// 		$thisSM = $alpha[$x];
// 	}
// 	// If the letters in the alphabet are not enough, start using AA, AB, AC ...
// 	else{
// 		$var1 = floor($x / count($alpha));
// 		$var2 = $x%count($alpha);
// 		$var1 = $alpha[$var1-1];
// 		$var2 = $alpha[$var2-1];
// 		$thisSM = $var1.$var2;
// 	}
// 
// 	$ap = "";
// 	for ($a = 0; $a < $apost[$i]; $a++) {
// 		$ap = $ap."'";
// 	}
// 	$SMalpha[] = $thisSM.$ap; // create an array with a value for each line
// }
  
// ************************
// start printing the music
// ************************

// for each musical line
$line_counter = 0;
$svg_translate = 0;
$distance_between_lines = 20;
$stave_height = 155;
$y_stave = getHeaderSpace($ids, $fontSize);

$svg_height = $stave_height * $n_of_lines + $y_stave + 100;
echo "<div id='staves'>";
echo "<svg xmlns=\"http://www.w3.org/2000/svg\" id='mainSVG' height='$svg_height' width='$line_width'>";

printHeader($titles, $ids, $authors, $ms_array, $folios, $textFont, $fontSize);

for ($line = 0; $line < $n_of_lines; $line++) {
	// reset this_line_width for each line
	$this_line_width = 0;

	// Clean line array
	$this_line_ar = rtrim($lines_ar[$line], " \|");
	$this_line_ar = trim($this_line_ar,"\|");
	$this_line_ar = ltrim($this_line_ar," ");

	// Create an array of all note groups in line
	$notes_ar = explode(' ', $this_line_ar);

	// Count note groups in line
	$n_of_note_groups = count($notes_ar);
  
  $svg_heigth = 140;
  $g_x_translate = 0;
  $text_distance = 40 + $fontSize * 0.2;
  
  // make space if range is too wide
  if (strpos($notes, '+c') !== false || strpos($notes, '+d') !== false) {
    $svg_heigth = 175;
    $svg_translate = 35;
    if (strpos($notes, '*G') !== false) {
      $svg_heigth = 190;
    }
  }
  
  // Add space between lines (=> increase height on last ms compared in line)
  if ($number_staves - $line_counter == 1 && $number_staves > 1){
    $svg_heigth += $distance_between_lines;
  }
  
  // If first line of comparison add the class that prevents it's break when printed
  if ($line_counter == 0 && $number_staves > 1){
    echo "<g class='stavesGroup'>";
  }
  
  // NEW LINE
  echo "<g class='singleStave' transform='translate(0, $y_stave)'>";
  $y_stave += $stave_height;

  // STAFF LABELS
  if ($use_id_as_staff_label){
    echo "<text x='0' y='67' class='ms_siglum' style=\"font-family:'$textFont'; font-size:".$fontSize."px\">".$ids[$line_counter]."</text>";
  }
  // Use ms as staff label
  else {
    echo "<text x='0' y='65' class='ms_siglum' style=\"font-family:'$textFont'; font-size:".$fontSize."px\">".$ms_array[$line_counter]."</text>";
  }
  
  if (($line_counter < $number_staves && $number_staves > 1) || $number_staves == 1){
      // first 
      if ($line_counter == 0 && $number_staves > 1) {
          echo "<line x1='$staff_label_space' 
                      y1='".(31 + $svg_translate)."' 
                      x2='$staff_label_space' 
                      y2='".(150 + $svg_translate)."' 
                      style='stroke:black;stroke-width:1'/>";
      }
      // last
      else if($number_staves - $line_counter == 1 && $number_staves > 1){
        $svg_heigth += 100;
          echo "<line x1='$staff_label_space' 
                      y1='".(-5 + $svg_translate)."' 
                      x2='$staff_label_space' 
                      y2='".(89 + $svg_translate)."' 
                      style='stroke:black;stroke-width:1' />";
      }
      // middle
      else if ($number_staves > 1) {
          echo "<line x1='$staff_label_space' 
                      y1='".(-5 + $svg_translate)."' 
                      x2='$staff_label_space' 
                      y2='".(150 + $svg_translate)."' 
                      style='stroke:black;stroke-width:1' />";
      }
      $line_counter = $line_counter + 1;
  }
        
  printStaffLines($svg_translate, $staff_label_space, $line_width-$staff_label_space);
  
  print_G_clef($svg_translate, $staff_label_space, $line_width-$staff_label_space);
  
  if($_COOKIE['octaveClef'] == 1) {
    echo "<text class='eight hidden' x='20' y='123' style=\"font-size:17px !important; font-family:'Times New Roman'; font-style:italic; visibility:".$octaveClef_visible."; font-weight: bold\">8</text>";
	}

	// Print b in clef if necessary
  if (isset($b_in_key_notes_groups_ar[$line][0][0]) &&
      $b_in_key_notes_groups_ar[$line][0][0] == 1) {
        print_b_flat($line, 50);
		    $b_in_key = 1;
  }
	// else
	// 	{$b_in_key = 0;};

	// Print Melodic Structure
	if($_COOKIE['melodicStructure'] == 1) {
	  $visible = "visible";
	} else {
	  $visible = "hidden";
	}
  
  // Put a cap on font size
	if ($fontSize > 30){
    $fontSizeMS = 25;
  } else {
    $fontSizeMS = $fontSize * 0.8;
  }
	
  // Show line Number
  if ($_COOKIE['lineNumber'] == 1) {
    $line_number_text = $line_number;

    if (is_array($set_of_lines) && count($set_of_lines) > 0) {
      $line_number_text = $set_of_lines[$line_number-1][$line_counter-1];
    }
    
	   echo "<text x='45' y='$text_height' style=\"font-family:'$textFont'; font-size:".$fontSize."px\" class='line_number'>".$line_number_text.".</text>";
	}

  echo "</g>";
  
  if ($line > 0){
    $totalElementsCount++;
  }

	// for each note group
	for ($i = 0; $i < $n_of_note_groups; $i++) {
		// Count notes in note group
		$n_of_notes_in_note_group[$i] = strlen($notes_ar[$i]);

    // Print syl's g
    echo "<g transform='translate($g_x_translate_columns[$i], $svg_translate) rotate(0)'>";
    
		if ($i > 0) {
      $totalElementsCount++;
    }

		// for each note of note group, create a subarray
		for ($x = 0; $x < $n_of_notes_in_note_group[$i]; $x++){
      $noteCount++;
      $totalElementsCount++;

 			if ($Plica_notes_groups_ar[$line][$i][$x] == 1)   $totalElementsCount += 1;
			if ($Natural_notes_groups_ar[$line][$i][$x] == 1) $totalElementsCount += 1;
			if ($Sharp_notes_groups_ar[$line][$i][$x] == 1)   $totalElementsCount += 1;
			if ($Flat_notes_groups_ar[$line][$i][$x] == 1)    $totalElementsCount += 1;
      if (!empty($Mid_bar_notes_groups_ar[$line][$i][$x+1]) 
      && substr($Mid_bar_notes_groups_ar[$line][$i][$x+1], -1) === "/") {
          $totalElementsCount += 1;
      }
      if (isset($apostrophe_string_groups_ar[$line][$i][$x]) 
      && $apostrophe_string_groups_ar[$line][$i][$x] == 1) {
          $totalElementsCount += 1;
      }
			if ($notes_ar[$i][$x] == "u" 
      || $notes_ar[$i][$x] == "p" 
      || $notes_ar[$i][$x] == "q" 
      || $notes_ar[$i][$x] == "r" 
      || $notes_ar[$i][$x] == "s" 
      ||$notes_ar[$i][$x] == "J"){
          $totalElementsCount += 1;
      }



			$note_group_ar = str_split($notes_ar[$i], 1);

			// b in key (middle
      if (isset($b_in_key_notes_groups_ar[$line][$i][$x]) &&
          $b_in_key_notes_groups_ar[$line][$i][$x] == 1 &&
          $i != 0 &&
          $b_always_flat_at_line_beginning != 1) {	
            
            print_b_flat($line, 2);        
            $b_in_key = 1;

      } elseif (isset($b_in_key_notes_groups_ar[$line][$i][$x]) &&
                $b_in_key_notes_groups_ar[$line][$i][$x] == 2 &&
                $i > 0) {          
            
            print_h_natural($line, 0);
            $b_in_key = 0;
      }

			// Natural accidental check
			$Natural = $Natural_notes_groups_ar[$line][$i][$x];
      if ($Natural == 1) {
        $ty = get_natural_ty($note_group_ar[$x]);
        print_h_natural($line, 0, $ty);
      }

			// Sharp check
      $Sharp = $Sharp_notes_groups_ar[$line][$i][$x];
      if ($Sharp == 1) {
        print_sharp($note_group_ar[$x]);
      }      

			if ($note_group_ar[$x] !== "?" and $note_group_ar[$x] !== "-"){

			// Plica check
		  $plica = $Plica_notes_groups_ar[$line][$i][$x];

      if ($plica == 0) {
        $tx = 390 + 20 * $x;
        $ty = getNoteTy($note_group_ar[$x]);
        $d1 = -1.19;
        $d2 = -1.19;
        $class = "single_note";
        $ledg_lines = 1;
        print_note($tx, $ty, $d1, $d2, $noteCount, $class, $note_path, $note_group_ar[$x], $ledg_lines);
      
      } elseif ($plica == 1) {
      
        if ($plica_type != 1) {
          $tx = 240 + 20 * $x;
          $ty = get_plica_1_type_0($note_group_ar[$x]);
          $d1 = -0.7;
          $d2 = -0.7;
          $class = "single_note plica";
          $ledg_lines = 1;
          print_note($tx, $ty, $d1, $d2, $noteCount, $class, $note_path, $note_group_ar[$x], $ledg_lines);
                        
        } elseif ($plica_type == 1) {
            // check plica direction
            $ledg_lines = 0;
            $plica_direction = note_number($note_group_ar[$x]) - note_number($note_group_ar[$x-1]);
            $class = "single_note plica_comma";
            $d1 = 1;

            if ($plica_direction <= 0){
              $tx = 20 * $x;
              $ty = get_plica_1_type_1_descendant($note_group_ar[$x-1]);
              $d2 = 1;
              print_note($tx, $ty, $d1, $d2, $noteCount, $class, $plica_comma_path, $note_group_ar[$x], $ledg_lines);

            } elseif ($plica_direction > 0) {
              // print plica asc height
              $tx = 8 * $x;
              $ty = get_plica_1_type_1_ascendant($note_group_ar[$x-1]);
              $d2 = -1;
              $r2 = 0.11;
              print_note($tx, $ty, $d1, $d2, $noteCount, $class, $plica_comma_path, $note_group_ar[$x], $ledg_lines, $r2);
            }
          }
      }
    } 
    // if ? or -
    else {
      echo "<g class='hypen'></g>";
    }

    // Square bracket check
    if (isset($Brackets_left_lines_groups_ar[$line][$i][$x])) {
        $Bracket_left = $Brackets_left_lines_groups_ar[$line][$i][$x];
    }else{
      $Bracket_left = null;
      }
    
    
    if (isset($Brackets_left_lines_groups_ar[$line][$i][0]) 
        && $Brackets_left_lines_groups_ar[$line][$i][0] == 1) {
        $first_in_brackets = 1;
    } else {
        $first_in_brackets = 0;
    }
    
    if (isset($Brackets_right_lines_groups_ar[$line][$i]) &&
        isset($n_of_notes_in_note_group[$i]) &&
        $n_of_notes_in_note_group[$i] > 0 && // Ensure the index is valid
        isset($Brackets_right_lines_groups_ar[$line][$i][$n_of_notes_in_note_group[$i] - 1]) &&
        $Brackets_right_lines_groups_ar[$line][$i][$n_of_notes_in_note_group[$i] - 1] == 1) {
        $last_in_brackets = 1;
    } else {
        $last_in_brackets = 0;
    }

    if ($first_in_brackets == 1 and $last_in_brackets == 1) {
      $all_syl_in_brackets = 1;
    }else{
      $all_syl_in_brackets = 0;
    }

    // careful: if there are multiple groups of note in brakects in one syl, this will not work, and will need more precise calculation
    if (strpos($Brackets_right_lines_groups_ar[$line][$i], "1") == true){
      $bracket_closes_within_syl = 1;
    }else{
      $bracket_closes_within_syl = 0;
    }

    if (isset($Brackets_left_lines_groups_ar[$line][$i]) && 
        strpos((string)$Brackets_left_lines_groups_ar[$line][$i], "1") !== false) {
      $bracket_opens_within_syl = 1;
    }else{
      $bracket_opens_within_syl = 0;
    }

    if ($Bracket_left == 1) {
      echo "<path transform='matrix(0.3, 0, 0, 0.5, -4, 35)'";
      echo " id='bracket_$n_of_notes_in_note_group[$i]'";
      echo " class='bracket_left' d='$bracket_left_path'/>";
    }

    if (isset($Brackets_right_lines_groups_ar[$line][$i][$x])) {
        $Bracket_right = $Brackets_right_lines_groups_ar[$line][$i][$x];
    } else {
        $Bracket_right = null; // Assign a default value or handle the error
    }
    
    if ($Bracket_right == 1) {
      echo "<path class='$all_syl_in_brackets $n_of_notes_in_note_group[$i] $bracket_opens_within_syl'";
      if (
        1 or // passa sempre
        ($all_syl_in_brackets == 1 and $n_of_notes_in_note_group[$i] > 1)
        or $bracket_opens_within_syl == 0
        or !preg_match('/[a-zA-Z]/', $note_group_ar[$x])) {
        echo " transform='matrix(0.3, 0, 0, 0.5,";
        echo $col_widths_ar[$i]-12;
        echo ", 35)'";
      } 
      // Small bracket enclosing single note
      // This never passes for now, it will need more work
      else {
        echo " transform='matrix(0.2, 0, 0, 0.21,";
        echo 30 * $x +30;
        echo ", ";
        brackets_height($note_group_ar[$x]);
        echo ")'";
      }

      echo " class='bracket_right' d='$bracket_right_path'/>";
    }
    
    // Print pointy brackets
    if (isset($Pointy_brackets_lines_groups_ar[$line][$i][$x])) {
        $Pointy_bracket = $Pointy_brackets_lines_groups_ar[$line][$i][$x];
    } else {
        $Pointy_bracket = null; // Assign a default value or handle missing data appropriately
    }
    
    // Print pointy brackets
    if ($Pointy_bracket == 1) {
      if ($Pointy_bracket_open_or_closed == "open") {
        print_closing_pointy_bracket($width_of_column);
        $Pointy_bracket_open_or_closed = "closed";
      } else {
        print_opening_pointy_bracket();
        $Pointy_bracket_open_or_closed = "open";
      }
    } else if ($Pointy_bracket == 2) {
      print_opening_pointy_bracket();
      print_closing_pointy_bracket($width_of_column);
    }	

    // Flat check
    $Flat = $Flat_notes_groups_ar[$line][$i][$x];

    if ($Flat == 1) {
      if ($note_group_ar[$x] === "H" or $note_group_ar[$x] === "h"){
        // Parameters for editorial flat (small flat on top of note)
        $d1 = "0.025";
        $d2 = "-0.025";
        $tx = 3+20*($x+1);
      } else {
        // Parameters for actual flat accidental
        $d1 = "0.04";
        $d2 = "-0.04";
        $tx = "4";
      }
      
      $ty = get_flat_ty($note_group_ar[$x]);
      
      print_b_flat($line, $tx, $ty, $d1, $d2);
    }
    // End Flat accidental

    // Flat on middle b
    if ($note_group_ar[$x] === "b" and $b_in_key !== 1){
      print_b_flat($line, 4, 62);
    }

    // Flat on lower B
    if ($note_group_ar[$x] === "B" and $b_in_key !== 1){
      print_b_flat($line, 4, 110);
    }

    // Flat on higher b (+b)
    if ($note_group_ar[$x] === "p" and $b_in_key !==1){
      print_b_flat($line, 4, 12);
    }

    // Middle bar check
    if (isset($Mid_bar_notes_groups_ar[$line][$i][$x+1])) {
        $Mid_bar = substr($Mid_bar_notes_groups_ar[$line][$i][$x+1], -1);
    } else {
        $Mid_bar = null; // Assign a default value if the index does not exist
    }
    
    if ($Mid_bar == "/") {
      print_mid_bar($width_of_column);
    }

	} // end operation for single note in note group
	  // Continuation of operations for the whole syllable (for $i)

    // ********** SLUR ********** //
    if ($n_of_notes_in_note_group[$i] < 2){
        /*no slur*/
    } elseif ($Plica_notes_groups_ar[$line][$i][$n_of_notes_in_note_group[$i]-1] == 1
      and $plica_type == 1
      and $n_of_notes_in_note_group[$i] == 2) {
      /*no slur*/

    } elseif ($n_of_notes_in_note_group[$i] > 1){
      $notes_divided_hyphens = explode("-", $notes_ar[$i]);
      $n_of_groups_divided_hyphens = count($notes_divided_hyphens);
      $cumulate_subgroup_count = 0;

      for ($z = 0; $z < $n_of_groups_divided_hyphens; $z++){
        $group = $notes_divided_hyphens[$z];
        $group_len = strlen($group);
        if ($group_len > 1) {
          // set variable for last note of the group
          if ($Plica_notes_groups_ar[$line][$i][$x-1] == 1 and $plica_type == 1){
            $last_note_of_note_group = $group[$group_len-2];
          } else {
            $last_note_of_note_group = $group[$group_len-1];
          };

          // set variable for first note of the group
          $first_note_of_the_group = $group[0];

          // get pitch of first note for positioning beginning of slur (%My); set note weigth
          [$My, $first_note_height] = get_slur_info_for_first_note($first_note_of_the_group);

          $n = $n_of_notes_in_note_group[$i];

          if ($Plica_notes_groups_ar[$line][$i][$n-1] == 1 and $plica_type == 1){
            $group_len = $n-1;
          }

          // last note weight
          $last_note_height = get_last_note_height($last_note_of_note_group);

          // Max height within note group
          $note_to_weight = get_note_to_weigth($group);
          
          // Calculate the maximum height and position
          $max_height_in_note_group = max($note_to_weight);
          $max_height_in_note_group_position = array_search($max_height_in_note_group, $note_to_weight);
          $max_height_in_note_group_position_ratio = $max_height_in_note_group_position / $n_of_notes_in_note_group[$i];
          
          // End calculating max height of slur

          // Height difference between first and last note of note group (determins height of slur ending)
          $height_difference = $first_note_height - $last_note_height;
          $Zx = ($group_len -1) * 20 ;
          $Zy = $height_difference * 7;

          // Calculate difference between first or last note (whichever is the highest) and the highest note in note group
          if ($height_difference > 0) {
            $max_difference = $max_height_in_note_group - $first_note_height;
          }	else {
            $max_difference = $max_height_in_note_group - $last_note_height;
          }

          if ($height_difference < 0) { // if first note is lower than last one
            $q1 = $Zx/2;
            $q2 = -14 + ($height_difference*7) - ($max_difference*14) ;
            $q3 = -18  - ($max_difference*7)  - ($max_difference*7);
          } else {
            $q1 = $Zx/2 ; // if first note is higher to last one
            $q2 = -18 - ($max_difference*7) - ($max_difference*7);
            $q3 = -14  - ($height_difference*7) - ($max_difference*14);
          }

          // Print slur
          $Mx = 28+($cumulate_subgroup_count)*20;
          
          echo "<path id='slur$noteCount'
          d='m $Mx $My q $q1 $q2 $Zx $Zy q ".(-$q1)." $q3 ".(-$Zx)." ".(-$Zy)."' 
          stroke='black' stroke-width='0.5' fill='black'/>";
          // End slur
        } // end if group > 1
        
        $cumulate_subgroup_count = $cumulate_subgroup_count + $group_len + 1;
      } //end slur loop
    } // End operations note group (??)

    // ********** PRINT TEXT ********** //
  	// Create and array of syllabes
   	$text_ar = explode(' ', $text_line_ar[$line] ?? '');

		// Calculate x position of syl
		$syl_len = mb_strlen(html_entity_decode($text_ar[$i]));
    
		if ($syl_len > 4 and $n_of_notes_in_note_group[$i] == 1) {
      $text_position = 10 - $syl_len;
      
    } else{
      $text_position = 18;
    };

    // Print syl
		echo "<text x='$text_position' y='$text_height' style=\"font-family:'$textFont'; font-size:".$fontSize."px\">";

		$this_syl = str_replace("_", " ", $text_ar[$i]);
    $this_syl = parse_pseudo_html($this_syl);
    $this_syl = str_replace("'", "’", $this_syl);

		echo $this_syl;

		echo "</text>";

    // ADDED LATER... could verify if it works
    if ($_COOKIE['showMsLineBreaks'] != 2
        && isset($apostrophe_string_groups_ar[$line][$i+1][0])
        && ($apostrophe_string_groups_ar[$line][$i+1][0] == "'"
            || $apostrophe_string_groups_ar[$line][$i+1][0] == 1)) {
        echo "<text style='font-family:$textFont; font-size:15px;' x='".($width_of_column-10)."' y='30' fill='gray'>&#9662;</text>";
    }

		// Close bar
    echo "</g>"; //*
    
    //*** ADD? 
    /*The following code may be added to update the b_in_key feature but don't know it works with multiple melodies
    
    if (isset($apostrophe_string_groups_ar[$line][$i+1]) && 
       ($apostrophe_string_groups_ar[$line][$i+1] == 1 
        || $apostrophe_string_groups_ar[$line][$i+1] == "'")
      ) {
      $b_in_key = 0;
    }
    
    */
	
  } // End line
  
  drawVerticalLineAtEndOfLine($svg_translate, $line_width);
  
  $End_bar = substr(end($Mid_bar_notes_groups_ar[$line]), -1) == "/";
  if ($End_bar) {
    $End_bar_double_slash = substr(end($Mid_bar_notes_groups_ar[$line]), -2) == "//";
    if ($End_bar_double_slash) {
      drawVerticalLineAtEndOfLine_midbar_double($svg_translate, $line_width);
    }else{
      drawVerticalLineAtEndOfLine_midbar_single($svg_translate, $line_width);
    }
  }
  echo "</g>"; //* Close line svg **+ changed to <g>
  
  // If first line of comparison add the class that prevents it's break when printed
  if ($line_counter == $number_staves && $number_staves > 1){
    echo "</g>"; //  close 'stavesGroup'
  }
		
	// Push this line width in the array of lines widths
  if (($line_counter == $number_staves && $number_staves > 1) 
  || $number_staves == 1)
      {        
        $line_counter = 0;
        $line_number = $line_number + 1;
  }
} // End analyse string

echo "</svg>";

// Width of largest line
$largest_line_width = $line_width;

echo "<input type='hidden' id='largest_line' class='largest_line' value='".$largest_line_width."'>";

echo "<div style='bottom:10px;' id='annotationsSection'>";

if ($_COOKIE['showAnnotations'] == 1) {
  for ($i=0; $i < count($ids); $i++) {
    $ann_arr = json_decode($annotations[$i]);
    $ann = $ann_arr[0];
    if ($ann != "") {
      echo "<div class='annotation' style=\"font-family:'$textFont';font-size:".$fontSize."px\">$ids[$i] $ms_array[$i]. ";
      $ann = str_replace("\n", "<br/>", $ann);
      echo html_entity_decode($ann);
      echo "</div>";
    }
  }
}

echo "</div>";
echo "</div>";

function logMessage($message, $level = 'INFO', $logFile = 'compare.log') {
    // Define the format of the log entry: [YYYY-MM-DD HH:MM:SS] [LEVEL] Message
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] $message" . PHP_EOL;
    
    // Append the log entry to the file
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}
function logArray($array, $level = 'INFO', $logFile = 'compare.log') {
    // Convert array to a readable string
    $message = print_r($array, true);
    
    // Define the format of the log entry: [YYYY-MM-DD HH:MM:SS] [LEVEL] Message
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] " . PHP_EOL . $message . PHP_EOL;
    
    // Append the log entry to the file
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

function getHeaderSpace($ids, $fontSize) {
  $initial_space = 70;
  $slot_height = 100 + $fontSize * 3;
  return count(array_unique($ids)) * $slot_height;
}

function printHeader($titles, $ids, $authors, $ms_array, $folios, $textFont, $fontSize){
  $printed_titles_id = [];
  $slot_height = 60 + $fontSize*1.1;
  $initial_space = 70;
  $y_slot = $initial_space;
  
  echo "<g id='titleHeader'>";
  if ($titles) {
    for ($i=0; $i < count($ids); $i++) {
      // If title not already printed
      if (!isset($printed_titles_id[$ids[$i]])) {
        // If first 
        if ($i == 0 || $ids[$i] != $ids[$i-1]) {
          echo "<text x='50%' y=$y_slot text-anchor='middle' class='title' style=\"font-family:'$textFont'; font-size:".($fontSize+10)."\">";

          echo str_replace("'", "’", $titles[$i]);

          $y_slot += 20 + $fontSize;
          echo "</text>";
          if ($authors[$i]){
            echo "<text x='50%' y=$y_slot text-anchor='middle' class='author' style=\"font-family:'$textFont'; font-size:".($fontSize+10)."\">";
            echo $authors[$i];
            echo "</text>";
          }
          // Set ids
          $id_mss_fs = "";
          for ($j=0; $j < count($ids); $j++) {
            // Check if we are dealing with the same song of the title
            if ($ids[$i] == $ids[$j]) {
              // Set first item
              if ($j == 0 || $ids[$j] != $ids[$j-1]){
                $id_mss_fs = $ids[$j] . ": " . $ms_array[$j] . " " . $folios[$j];  
              } else if ($ids[$j] == $ids[$j-1]){
                $id_mss_fs .= ", " . $ms_array[$j] . " " . $folios[$j];
              }
            }
          }
          $y_slot += 20 + $fontSize;
          echo "<text x='50%' y=$y_slot text-anchor='middle' class='id_mss_fs' style=\"font-family:'$textFont'; font-size:".($fontSize+4)."\">";
          echo $id_mss_fs;
          echo "</text>";
            
          $printed_titles_id[$ids[$i]] = true;
          $y_slot += 50 + ($fontSize);
        }
      }
    }
  }
  echo "</g>"; // Close #titleHeader
}

function get_width_of_longest_id($ids){
  $longest_id = 0;
  for ($i=0; $i < count($ids); $i++) {
    $id_lenght = strlen($ids[$i]);
    if ($id_lenght > $longest_id){
      $longest_id = $id_lenght;
    }
  }
  return 17.5 * $longest_id;
}


?>
<style>
@media print {
  g.stavesGroup {
    display: inline-block;
    page-break-inside: avoid;
  }
}

</style>