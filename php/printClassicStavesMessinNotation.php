<?php
// Turn off all error reporting
// error_reporting(0);
ini_set('display_errors', 1);
error_reporting(1);

include 'functions_shared.php';
include 'functions_medieval_shared.php';
include 'paths.php';


$notes = $text_string = $title = $id = $author = $ms = "";

$notes = $_POST["notes"];
$text_string = $_POST["text_string"];
$title = $_POST["title"];
$id = $_POST["id"];
$author = $_POST["author"];
$ms = $_POST["ms"];
$ms_page = $_POST["f_input"];
$repetition_sequence = $_POST["repetitionPattern"];
$repetition_pattern = fromSequenceToRepetitionPattern($repetition_sequence);
$bar = json_decode(test_input($_POST["bar"]));
$custos = json_decode(test_input($_POST["custos"]));
$notation_type = $_POST["notationType"];
$annotations = test_input($_POST["annotations"]);

if ($notation_type == 2){ // kolmarer
  include "kolmarer_paths.php";
}

//group sequence number
$groupSequence = 0;

// number of lines in staff (4 if nothing)
$n_of_lines_in_staff_array = [];
$n_of_lines_in_staff_array =  json_decode(test_input($_POST["linesInLine"]));

// shape of note groups
$shapes_group = [];
$shapes_group = json_decode(test_input($_POST["shapeGroupNote"]));
// Single note shape

$note_shape = [];
$note_shape = json_decode(test_input($_POST["shapeSingleNote"]));

// STEM DIRECTION NOT USED
$stem_direction = [];
$stem_direction = json_decode(test_input($_POST["stemSingleNote"]));
// $connecting_line_param NOT USED
$connecting_line_param = [];
$connecting_line_param = json_decode(test_input($_POST["connectGroupNote"]));

//--------------PARAMETERS-------------------------------------------------
// pes param
$pes_post = test_input($_POST['pes'] ?? 1);
// clivis param
$clivis_post = test_input($_POST['clivis'] ?? 1);
//echo("clivis ".$clivis_post);
// climacus param
$climacus_post = test_input($_POST['climacus'] ?? 1);

// porrectus param
$porrectus_post = test_input($_POST['porrectus'] ?? 1);

// plica param
$plica_post = test_input($_POST['plica'] ?? 1);

// scandicus param
$scandicus_post = test_input($_POST['scandicus'] ?? 1);

//-------------- END PARAMETERS-------------------------

$textFontOld = get_font_family($_COOKIE['textFontOld']);
$fontSizeOld = get_font_size($_COOKIE['fontSizeOld']);

$stavesDistance = 0; // can be used as setting

print_element("title", $title);
print_element("author", $author);
print_element("id", $id);
print_element("id", "Ms: $ms $ms_page");


$notes_bar_separator = clean_note_input($notes);


$notes_in_Brackets_left = str_replace(array("(", ")", "#", "+", "_", "%", "/", "-", "]", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0"), "", $notes_bar_separator);
$notes_in_Brackets_right = str_replace(array("(", ")", "#", "+", "_", "%", "/", "-", "[", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0"), "", $notes_bar_separator);
$notes_for_mid_bar = str_replace(array("(", ")", "#", "+", "_", "%", "-", "[", "]",  "1", "2", "3", "4", "5", "6", "7", "8", "9"), "", $notes_bar_separator);
$notes_for_plica = str_replace(array(")", "%", "#", "+", "_", "/", "-", "[", "]", "1 ", "2 ", "3 ", "4 ", "5 ", "6 ", "7 ", "8 ", "9 ", "0 ", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0"),"", $notes_bar_separator);
$notes_for_natural = str_replace(array("(", ")", "#", "+", "_", "/", "-", "[", "]", "1", "2", "3", "4", "5", "6", "7", "8", "9"), "", $notes_bar_separator);
$notes_for_sharp = str_replace(array("(",")", "%", "+", "_", "/", "-", "[", "]", "1", "2", "3", "4", "5", "6", "7", "8", "9"),"", $notes_bar_separator);
$notes_for_flat = str_replace(array("(",")", "%", "+", "#", "/", "-", "[", "]", "b}", "h}", "B}", "1", "2", "3", "4", "5", "6", "7", "8", "9"),"", $notes_bar_separator);
$notes_clean = str_replace(array("%", "(", ")", "#", "+", "_", "/","[", "]", "b}", "h}", "B}"),"", $notes_bar_separator);


// Calculate position of plicas
// Change notes preceded by parentesis to value "1"
$notes_for_plica = ltrim($notes_for_plica, " ");
$Plica_string = str_replace(array('(a', '(b', '(c', '(d', '(e', '(f', '(g', '(h', '(J', '(A', '(B', '(C', '(D', '(E', '(F', '(G', '(H', '(u', '(p', '(q', '(r'), "1", $notes_for_plica);
// Change all other notes to value "0"
$Plica_string = preg_replace("[a|b|c|d|e|f|g|h|u|p|q|J|A|B|C|D|E|F|G|H]", "0", $Plica_string);
$Plica_notes_groups_ar = format_string($Plica_string);


// Calculate position of natural alteration
// Change notes preceded by "%"" to value "1"
$Natural_string = str_replace(array('%', '%b', '%c', '%d', '%e', '%f', '%g', '%h', '%J', '%A', '%B', '%C', '%D', '%E', '%F', '%G', '%H', '%u', '%p', '%q', '%r'), "%", $notes_for_natural);
// Change all other notes to value "0"
$Natural_string = preg_replace("[a|b|c|d|e|f|g|h|u|p|q|J|A|B|C|D|E|F|G|H|1|2|3|4|5|6|7|8|9]", "0", $Natural_string);
$Natural_string = str_replace("%", "1", $Natural_string);
// Separate LINES
$Natural_notes_groups_ar = format_string($Natural_string);

// Calculate position of sharp alteration
// Change notes preceded by "#"" to value "1"
$Sharp_string = str_replace(array('#a', '#b', '#c', '#d', '#e', '#f', '#g', '#h', '#J', '#A', '#B', '#C', '#D', '#E', '#F', '#G', '#H', '#u', '#p', "#q", "#r"), "#", $notes_for_sharp);
// Change all other notes to value "0"
$Sharp_string = preg_replace("[a|b|c|d|e|f|g|h|u|p|q|r|J|A|B|C|D|E|F|G|H|u|p|q|1|2|3|4|5|6|7|8]", "0", $Sharp_string);
$Sharp_string = str_replace("#", "1", $Sharp_string);
$Sharp_notes_groups_ar = format_string($Sharp_string);


// Calculate position of flat alteration
// Change notes preceded by "_"" to value "1"
$Flat_string = str_replace(array( '_a', '_b', '_c', '_d', '_e', '_f', '_g', /*'_h',*/ '_J', '_A', '_B', '_C', '_D', '_E', '_F', '_G', '_H', '_u', '_p', "_q", "_r"), "_", $notes_for_flat);
// Change all other notes to value "0"
$Flat_string = str_replace('_h',"0", $Flat_string);
$Flat_string = preg_replace("[a|b|c|d|e|f|g|h|u|p|q|r|J|A|B|C|D|E|F|G|H|u|p|q|1|2|3|4|5|6|7|8|9]", "0", $Flat_string);
$Flat_string = str_replace("_", "1",$Flat_string);
$Flat_notes_groups_ar = format_string($Flat_string);


// Calculate position of Middle bars
$Mid_bar_notes_groups_ar = format_string($notes_for_mid_bar);

// Get position of notes in Brackets
// Get position of notes_in_Brackets_left
$Brackets_left_string = str_replace(array('[a', '[b', '[c', '[d', '[e', '[f', '[g', '[h', '[J', '[A', '[B', '[C', '[D', '[E', '[F', '[G', '[H', '[u', '[p', '[q', '[r]'),   "[", $notes_in_Brackets_left);
// Change all other notes to value "0"
$Brackets_left_string = preg_replace("[a|b|c|d|e|f|g|h|u|p|q|r|J|A|B|C|D|E|F|G|H|1|2|3|4|5|6|7|8|9]", "0", $Brackets_left_string);
$Brackets_left_string = str_replace("[", "1", $Brackets_left_string);
// Separate LINES
$Brackets_left_lines[] = explode("|", $Brackets_left_string);
// separate GROUPS
foreach ($Brackets_left_lines as $Brackets_left_line) {
	foreach ($Brackets_left_line as $Brackets_left_group) {
		$Brackets_left_lines_groups_ar[] = explode(" ", $Brackets_left_group);
	}
}

// Get position of notes_in_Brackets_right
$Brackets_right_string = str_replace(array('a]', 'b]', 'c]', 'd]', 'e]', 'f]', 'g]', 'h]', 'J]', 'A]', 'B]', 'C]', 'D]', 'E]', 'F]', 'G]', 'H]', 'u]', 'p]', 'q]', 'r]'),   "]", $notes_in_Brackets_right);
// Change all other notes to value "0"
$Brackets_right_string = preg_replace("[a|b|c|d|e|f|g|h|u|p|q|r|J|A|B|C|D|E|F|G|H|1|2|3|4|5|6|7|8|9]", "0", $Brackets_right_string);
$Brackets_right_string = str_replace("]", "1",$Brackets_right_string);
// Separate LINES
$Brackets_right_lines[] = explode("|", $Brackets_right_string);
// separate GROUPS
foreach ($Brackets_right_lines as $Brackets_right_line) {
	foreach ($Brackets_right_line as $Brackets_right_group) {
		$Brackets_right_lines_groups_ar[] = explode(" ", $Brackets_right_group);
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


// Establish text height
if (strpos($notes, '*G') !== false) {$text_height = 126;}
elseif (strpos($notes, 'A') !== false) {$text_height = 119;}
elseif ((strpos($notes, 'B') !== false) or (strpos($notes, 'H') !== false)) {$text_height = 113;}
else {$text_height = 109;}

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
	$qq_this_line_ar = rtrim($lines_ar[$qq], " \|");
	$qq_this_line_ar = trim($qq_this_line_ar,"\|");
	$qq_this_line_ar = ltrim($qq_this_line_ar," ");

	// Create an array of all note gorups in the line
	$qq_notes_ar[] = explode(' ', $qq_this_line_ar);
}


for ($yy = 0; $yy < $longest_number_of_syl_in_line; $yy++){
	for ($yyy = 0; $yyy < $n_of_lines; $yyy++) {
		 $column[] = strlen($qq_notes_ar[$yyy][$yy]);
	 }
}

$column_subar = array_chunk($column, $n_of_lines);


for ($yyyy = 0; $yyyy < $longest_number_of_syl_in_line; $yyyy++) {
	$max_n_of_notes_in_column[] = max($column_subar[$yyyy]);
}

// Reduce text to those which are not repeated lines, and then reput them into a string
$text_ = preg_replace(array('/\r/', '/\n/'), "|", $text_string_clean);
$text_lines_ = explode('|', $text_);
[$text_line_ar, $repetitions, $repeated_text_lines] = parse_for_repetition($text_lines_, $repetition_pattern);
$indexes_of_syllables_starting_new_line =  get_indexes_of_syllables_starting_new_line($text_line_ar);

$text_string_clean = implode(" ", $text_line_ar);

// do other thing
if ($repetition_sequence != "") {
  $count_text_syl_in_succession = get_array_of_text_syl_corresponding_to_music_repetitions($qq_notes_ar, $text_line_ar, $repetitions);
}else{
  $count_text_syl_in_succession = get_array_of_text_syl_corresponding_to_music($qq_notes_ar, $text_string_clean);
}
// Pair text (char count) and music
$pair_notes_and_text = get_pair_notes_and_text($notes_bar_separator, $count_text_syl_in_succession);


[$syl_widths, $line_width] = get_width_of_syls($pair_notes_and_text); // dimension [line[syl_x, syl_width]]


$metrical_line = 0;
$syl_rep_index = 0;

// ************************
// start printing the music
// ************************

echo "<div id='staves' style='margin-top:30px'>";

// for each musical line
for ($line = 0; $line < $n_of_lines; $line++){
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

	// Check number of lines in staff
	if ($n_of_lines_in_staff_array[$line] == ""){
		$n_of_lines_in_staff = 4;
	} else {
		$n_of_lines_in_staff = $n_of_lines_in_staff_array[$line];
	}

  $staff_height = 112 + $n_of_lines_in_staff*7;

	if ($n_of_lines_in_staff > 4) {
		$text_height_coeff = ($n_of_lines_in_staff-4) * 14;
	}else{
		$text_height_coeff = 0;
	}

	//reset count of keys
	$n_of_keys_in_line = 0;

	// reset b in key
	$b_in_key = 0;

	$index_group = 0;

  // line break for every new line apart from the first one
	if ($line > 0) {
		echo "<br>";
	}
  
  // Get number of lines 
  $n_linesCurrent = 4;
  if ($n_of_lines_in_staff_array[$line] != null) $n_linesCurrent = $n_of_lines_in_staff_array[$line];
  
  // Create more space if there are text repetitions
  if (array_key_exists($metrical_line, $repetitions)){
    $number_of_repetitions_for_this_line = count($repetitions[$metrical_line]);
    $staff_height = $staff_height + 17 * $number_of_repetitions_for_this_line;
  }
  
  echo "<svg  class='stave-line'  height='".$staff_height."' width='$line_width' onclick='openLineOptionsMenu($line)' oncontextmenu='showLineNumber(this, $line, event)' id='line_$line' data-lines='".$n_linesCurrent."' data-line='".$line."'>";
  
  print_n_gram($n_of_lines_in_staff, $staff_height, $line);
  
	// for each note group
	for ($i = 0; $i < $n_of_note_groups; $i++) {
		$npos = [];
		$coeff = 0;
		$coeff_x = 0;
		$tot_of_i += 1;
    $b_in_syl = $b_in_key;
  
      
		// Keep count of how many keys in line so far
		if (is_numeric($qq_notes_ar[$line][$i])){
			$n_of_keys_in_line += 1;
			$n_of_keys_total += 1;
			$n_of_keys_total_for_selection += strlen($qq_notes_ar[$line][$i]);
		}
    
    // Get metrical line (= if the new syllable is the first of a new metrical line, increment metrical line)
    
		// Count notes in note group
		$n_of_notes_in_note_group[$i] = strlen($notes_ar[$i]);
    
    $function = "";

    $groupNoteSequence = 0;
    $stripped_qq_notes = str_replace("[", "", $qq_notes_ar[$line][$i]);
    $stripped_qq_notes = str_replace("]", "", $stripped_qq_notes);

   	if ($n_of_notes_in_note_group[$i] > 0  && !is_numeric($qq_notes_ar[$line][$i])) {
	    $function = "onclick='extractGroupFromClick(".$groupSequence.", ".$line.")'";
		  $groupNoteSequence = $groupSequence;
	  	$groupSequence = $groupSequence + 1;
	  }
    
    $syl_x = $syl_widths[$line][$i][0] + $start_x;
    $syl_width = $syl_widths[$line][$i][1] + $start_x;

    echo "<g class='groupNote'";
    echo " id='group_".$line."_$groupSequence'"; // this created id duplicates. I don't think it was ever used but let's not delete it just yet
    echo " transform='translate($syl_x, 0)' style='margin-top: ".$stavesDistance."px;'>\n";
    
    // KEEP COUNT OF METRICAL LINES. VERY IMPORTANT!
    if ($tot_of_i - 1 - $n_of_keys_total > 0 && in_array($tot_of_i - 1 - $n_of_keys_total, $indexes_of_syllables_starting_new_line)){
      // logMessage("c1: ".$tot_of_i - 1 - $n_of_keys_total, $indexes_of_syllables_starting_new_line);
      // logArray($indexes_of_syllables_starting_new_line);
      array_shift($indexes_of_syllables_starting_new_line); // Necessary
      $metrical_line++;
      $syl_rep_index = 0;
      if ($_COOKIE['showMsLineBreaks'] == 1){
        echo "<text class='metrical-unit-sign' x=0 y=12 style='font-family:".$textFontOld."; font-size:$fontSizeOld' fill='lightgray'>&#9662;</text>";
      }
    }
    
    print_rect($syl_width, $staff_height, $function);
    
    // Print accidents before notes
    for ($x = 0; $x < $n_of_notes_in_note_group[$i]; $x++) {
      // Flat check
      $Flat = $Flat_notes_groups_ar[$line][$i][$x];
      if ($Flat == 1 or (is_b_flat($notes_ar[$i][$x]) and $b_in_syl != 1)) {
        print_flat($notes_ar[$i][$x], $pos, $coeff_x);
        $coeff_x += 8;
        $b_in_syl = 1;
      }

      // Natural accidental check
      $Natural = $Natural_notes_groups_ar[$line][$i][$x];
      if ($Natural == 1) {
        print_natural($notes_ar[$i][$x], $pos, $coeff_x);
        $coeff_x += 8;
      }

      // Sharp check
      $Sharp = $Sharp_notes_groups_ar[$line][$i][$x];
      if ($Sharp == 1) {
        print_sharp($notes_ar[$i][$x], $pos, $coeff_x);
        $coeff_x += 10;
      }
    }
    
    
		// for each note of note group, create a subarray
		for ($x = 0; $x < $n_of_notes_in_note_group[$i]; $x++){
			$noteCount1++;
			$noteCount = $noteCount1 - $n_of_keys_total_for_selection;
			$note_group_ar = str_split($notes_ar[$i], 1);

			/*****
	    * KEY
	    ******/
			// $pos = set_pos($key);
      
			/*****
	    * CLEF
	    ******/
			if (is_numeric(implode($note_group_ar))) {
				// The code has 3 numbers, but we only need to pass it once, hence $x == 0 (otherwise it would process it three times)
				if ($x == 0){
					print_clef($note_group_ar);
			}
			// Notes
			} elseif ($note_group_ar[$x] !== "?" and $note_group_ar[$x] !== "-") {

			/*****
			* Calculate note height
			******/

			$npos[$x] = note_height($note_group_ar[$x]);
			$npos[$x] = $npos[$x] + $pos;

	   	/*****
	   	* Preprocess: get the type of note group
	   	******/

		  // numeric value of first 7 notes
		  $nn0 = note_number($note_group_ar[0]);
			$nn1 = note_number($note_group_ar[1]);
			$nn2 = note_number($note_group_ar[2]);
			$nn3 = note_number($note_group_ar[3]);
			$nn4 = note_number($note_group_ar[4]);
			$nn5 = note_number($note_group_ar[5]);
			$nn6 = note_number($note_group_ar[6]);
			$nn7 = note_number($note_group_ar[7]);
			$this_nn = note_number($note_group_ar[$x]);
			$next_nn = note_number($note_group_ar[$x+1]);
			$previous_nn = note_number($note_group_ar[$x-1]);
			$two_before_nn = note_number($note_group_ar[$x-2]);
			$two_after_nn = note_number($note_group_ar[$x-2]);
			$diff_this_next = $this_nn-$next_nn;
			$diff_previous_this = $previous_nn-$this_nn;
			$plica = $Plica_notes_groups_ar[$line][$i-$n_of_keys_in_line][$x];
			$plica_next = $Plica_notes_groups_ar[$line][$i-$n_of_keys_in_line][$x+1];


		 	// REPEATED PITCH
	    if ($this_nn == $previous_nn) {
	    		$coeff_x += 3;
	    }

		 	// DETERMINE CLIVIS SHAPE
		 	if ($diff_previous_this == 1) {
		 		if ($two_before_nn > $previous_nn) {
					$clivis_shape = $clivis_1_grade_preceeded_by_clivis;
				}	else {
					$clivis_shape = $clivis_1_grade;
				}
		 	} elseif ($diff_previous_this == 2) {
				$clivis_shape = $clivis_2_grades;
			}	elseif ($diff_previous_this == 3) {
				$clivis_shape = $clivis_3_grades;
			}	elseif ($diff_previous_this == 4) {
				$clivis_shape = $clivis_4_grades;
			} else {
				$clivis_shape = $uncinus;
			}


			// DETERMINE PES SHAPE
		  if ($diff_previous_this >= -1) {
		 		if ($two_before_nn > $previous_nn) { // switched this from < to > ... don't know if it's right
					$pes_shape = $pes_of_scandicus_x2;
				}	else {
					$pes_shape = $pes_1_grade;
				}
		 	} elseif ($diff_previous_this == -2) {
				$pes_shape = $pes_2_grades;
			} elseif ($diff_previous_this < -2) {
				$pes_shape = $uncinus;
			}


	    /*****
	    * PARAMETERS
	    ******/

			// clivis param
			$united_clivis = 1;

 			// porrectus param
 			$porrectus_type = 0;

		 	// plica param
		 	$plica_type = 4;

			$group_type = $clivis_type = $scandicus_type = $torculus_type = $climacus_type = $aGG_type =  $aaGF_type = $aGGF_type = $ahch_type = $aGFE_type = $aGaG_type = $ahchPlica_type = $ahaG_type = $aGGFE_type = $aaG_type = $aGah_type = $aGFG_type = $aGFGF_type = $ahaGa_type = $ahcha_type = $aGFEF_type = $aaGFG_type = $ahha_type = $ahcdc_type = $aGFGa_type = $ah_type = 0;
      

			// note group param from external file
			if ($shapes_group[$line][$i-$n_of_keys_in_line] == 1) {
				$porrectus_type = 1;
        $torculus_type = 1;
				$scandicus_type = 1;
				$clivis_type = 1;
				$climacus_type = 1;
        $aGG_type = 1;
			 	$aGGF_type = 1;
			 	$aaGF_type = 1;
			 	$ahch_type = 1;
 				$ahaG_type = 1;
 				$aGGFE_type = 1;
				$ahchPlica_type = 1;
				$aGah_type = 1;
				$aGFG_type = 1;
				$aGFGF_type = 1;
				$ahaGa_type = 1;
				$ahcha_type = 1;
				$aGFEF_type = 1;
				$aGFE_type = 1;
				$aGaG_type = 1;
				$aaGFG_type = 1;
				$ahha_type = 1;
				$ahcdc_type = 1;
				$aGFGa_type = 1;
				$ah_type = 1;
        $group_type = 1;
			}

			if ($shapes_group[$line][$i-$n_of_keys_in_line] == 2){
				$aaG_type = 2;
        $aGG_type = 2;
				$climacus_type = 0;
        $clivis_type = 2;
				$scandicus_type = 2;
				$ahaG_type = 2;
				$ahcha_type = 2;
				$aGFG_type = 2;
				$ahchPlica_type = 2;
				$ahch_type = 2;
        $group_type = 2;
        $ah_type = 2;
			}

			if ($shapes_group[$line][$i-$n_of_keys_in_line] == 3){
				$scandicus_type = 3;
				$ahaG_type = 3;
				$ahcha_type = 3;
				$climacus_type = 3;
				$aGFG_type = 3;
        $group_type = 3;
			}

			if ($shapes_group[$line][$i-$n_of_keys_in_line] == 4){
				$ahcha_type = 4;
        $climacus_type = 4;
        $group_type = 4;
			}
			if ($shapes_group[$line][$i-$n_of_keys_in_line] == 5){
				$ahcha_type = 5;
        $climacus_type = 5;
        $group_type = 5;
			}
			if ($shapes_group[$line][$i-$n_of_keys_in_line] == 6){
				$ahcha_type = 6;
        $group_type = 6;
			}

			// get shape parameters from external file
			$single_note_shape = $note_shape[$line][$i-$n_of_keys_in_line][$x];
			$single_note_shape_next = $note_shape[$line][$i-$n_of_keys_in_line][$x+1];
			$note_shape_previous = $note_shape[$line][$i-$n_of_keys_in_line][$x-1];
			$note_shape_minus2 = $note_shape[$line][$i-$n_of_keys_in_line][$x-2];

			/***************************************/
      if ($notation_type == 2){
        include "print_kolmarer_notation.php";
      }else{
        include "print_messine_notation.php";
      }
		}

		/********************************* END NOTES *********************************/

		// Square bracket check
		$Bracket_left = $Brackets_left_lines_groups_ar[$line][$i][$x];

		if ($Brackets_left_lines_groups_ar[$line][$i][0] == 1){
			$first_in_brackets = 1;
		}else{
			$first_in_brackets = 0;
		}

		if ($Brackets_right_lines_groups_ar[$line][$i][($n_of_notes_in_note_group[$i]-1)] == 1) {
			$last_in_brackets = 1;
		}else{
			$last_in_brackets = 0;
		}

		if ($first_in_brackets == 1 and $last_in_brackets == 1) {
			$all_syl_in_brackets = 1;}else{$all_syl_in_brackets = 0;
		}

		// careful: if there are multiple groups of note in brakects in one syl, this will not work, and will need more precise calculation
		if (strpos($Brackets_right_lines_groups_ar[$line][$i], "1") == true){
			$bracket_closes_within_syl = 1;
		} else {
			$bracket_closes_within_syl = 0;
		}

		if (strpos($Brackets_left_lines_groups_ar[$line][$i], "1") == true){
			$bracket_opens_within_syl = 1;
		}else{
			$bracket_opens_within_syl = 0;
		}

		/*** Bar ***/
		$index_group = $i-$n_of_keys_in_line;
		$rientro_bar = 0.5;
		$y1 = $bar[$line][$index_group][0];
		$y2 = $bar[$line][$index_group][1];

		if ($custos[$line] != null && $i == $longest_number_of_syl_in_line-1) {
			$rientro_bar = 0;
		}

		$stripped_qq_notes = str_replace("[", "", $qq_notes_ar[$line][$i]);
		$stripped_qq_notes = str_replace("]", "", $stripped_qq_notes);

		if ($bar[$line][$index_group][1] != 0 && !is_numeric($stripped_qq_notes)) {
			// point on bar
			if($bar[$line][$index_group][2] == 2
			|| $bar[$line][$index_group][2] == 3
			&& !is_numeric($qq_notes_ar[$line][$index_group])){
				$rientro_bar = 10;
				echo '<circle cx="'.($syl_width-2).'" cy="';
				echo ((14*($bar[$line][$index_group][0]+1)+17.5)+ 14*($bar[$line][$index_group][1]+1)+17.5)/2;
				echo '" r="1" stroke="black" stroke-width="1" fill="black" />';
			}
			echo "  <line x1='";
			echo $syl_width-$rientro_bar;
			echo "' y1='";
			echo (14*($bar[$line][$index_group][0]+1)+17.5);
			echo "' x2='";
			echo $syl_width-$rientro_bar;
			echo "' y2='";
			echo (14*($bar[$line][$index_group][1]+1)+17.5);
			echo "' style='stroke:black;stroke-width:1'></line>";

			if ($bar[$line][$index_group][2] == 1 || $bar[$line][$index_group][2] == 3){
				echo "  <line x1='";
				echo $syl_width-$rientro_bar-3.5;
				echo "' y1='";
				echo (14*($bar[$line][$index_group][0]+1)+17.5);
				echo "' x2='";
				echo $syl_width-$rientro_bar-3.5;
				echo "' y2='";
				echo (14*($bar[$line][$index_group][1]+1)+17.5);
				echo "' style='stroke:black;stroke-width:1'></line>";
			}
		}

		// Brackets
		if ($Bracket_left == 1) {
			echo "<path transform='matrix(0.3, 0, 0, 0.5, -9, 27)' id='$n_of_notes_in_note_group[$i]' name='bracket_left' d='$bracket_left_path'/>";
		}

		$Bracket_right = $Brackets_right_lines_groups_ar[$line][$i][$x];
		if ($Bracket_right == 1) {
			echo "<path";

			if (($all_syl_in_brackets == 1 and $n_of_notes_in_note_group[$i] > 1) or $bracket_opens_within_syl == 0 or !preg_match('/[a-zA-Z]/', $note_group_ar[$x])){
				echo " transform='matrix(0.3, 0, 0, 0.5,";
				echo $syl_width-12;
				echo ", 27)'";

			} else {
				echo " transform='matrix(0.2, 0, 0, 0.21, ";
				echo 30*$x;
				echo ", ";
				brackets_height($note_group_ar[$x]);
        echo ")' ";
			}

			echo " name='bracket_right' d='$bracket_right_path'/>";
		}

		// Middle bar // MIDBAR DISABLED
		// $Mid_bar = substr($Mid_bar_notes_groups_ar[$line][$i][$x+1], -1);
		// if ($Mid_bar == "/") {
		// 	echo "  <line x1='";
		// 	echo $syl_width;
		// 	echo "' y1='";
		// 	echo (14*$n_of_lines_in_staff+17.5);
		// 	echo "' x2='".$syl_width;
		// 	echo "' y2='30.68' style='stroke:black;stroke-width:1	' />";
		// }
	} // end operation for single note in note group

	// End Print music

	// PRINT TEXT
	if (!is_numeric($qq_notes_ar[$line][$i]) and $qq_notes_ar[$line][$i] != "§"){
		$i_text = $i_text + 1;
		// Print text
		// Create and array of syllabes
		$text_ar = preg_replace(array('/\r/', '/\n/'), " ", $text_string_clean);
		$text_ar = explode(' ', $text_ar);
		// Calculate x position of syl
		$syl_len = strlen($text_ar[$i_text-1]);

		if ($syl_len > 3 and $n_of_notes_in_note_group[$i] == 1) {
			$text_position = 3 - $syl_len*2.5;
      if ($text_position < 0){
        $text_position = 0;
      }
		}	else{
	    $text_position = 3;
	  }

		echo "<text x='$text_position' y='".($text_height+$text_height_coeff)."' style='font-family:".$textFontOld."; font-size:$fontSizeOld'>";
    
		// Print syl
		$this_syl = str_replace("_", " ", $text_ar[$tot_of_i-1-$n_of_keys_total]);
		echo graces(parse_pseudo_html($this_syl));
		echo "</text>\n";
    
    // Print text of repetition
    if (array_key_exists($metrical_line, $repetitions)){

      $rep_count = 1;
      foreach ($repetitions[$metrical_line] as $index => $this_text_line_str) {
        $this_text_line_ar = explode(" ", $this_text_line_str);
        // NEW
        $rep_syl = graces(parse_pseudo_html($this_text_line_ar[$syl_rep_index]));

        echo "          <text class='re' style='font-family:$textFontOld; font-size:".$fontSizeOld."px;' x='$text_position' y='".($text_height + $text_height_coeff + 22 * $rep_count)."'>";
        echo $rep_syl;
        echo "</text>\n";
        
        $rep_count++;
      }
      $syl_rep_index++;
    }
	}

	// Close bar
  echo "</g>\n";

} // End line


  if ($custos[$line] !== null) {
    print_custos($line_width, $custos[$line]);
  }
  // print_midbar($Mid_bar_notes_groups_ar[$line], $n_of_lines_in_staff, $staff_height); // IF YOU WANT THIS, make sure to use 4 arguments or modify function so that it expects 3 arguments.

  echo "</svg>\n";

} // End analyse string
print_annotation($annotations, $textFontOld, $fontSizeOld);
// Width of largest line (needed for automatic zoom)
echo "<input type='hidden' id='largest_line' name='largest_line' value='$line_width'>
			</div>
		</div>
	</div>";
  
  echo "<style>";
  echo ".groupClickableRect {fill: transparent}\n";
  echo ".staffLines line {stroke:black; stroke-width:1}\n";
  echo "#staves {display: flex; flex-direction: column;}";
  echo "</style>";
  
  
  function logMessage($message, $level = 'INFO', $logFile = 'messineNotation.log') {
      // Define the format of the log entry: [YYYY-MM-DD HH:MM:SS] [LEVEL] Message
      $timestamp = date('Y-m-d H:i:s');
      $logEntry = "[$timestamp] $message" . PHP_EOL;
      
      // Append the log entry to the file
      file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
  }
  function logArray($array, $level = 'INFO', $logFile = 'messineNotation.log') {
      // Convert array to a readable string
      $message = print_r($array, true);
      
      // Define the format of the log entry: [YYYY-MM-DD HH:MM:SS] [LEVEL] Message
      $timestamp = date('Y-m-d H:i:s');
      $logEntry = "[$timestamp] " . PHP_EOL . $message . PHP_EOL;
      
      // Append the log entry to the file
      file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
  }