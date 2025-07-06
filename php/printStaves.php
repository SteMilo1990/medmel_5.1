<?php
ini_set('display_errors', 0);
// Turn off all error reporting
ini_set('display_errors', 1);
error_reporting(1);

$c_active = true;

include 'functions_shared.php';
include 'modern_paths.php';
include 'functions_printStaves.php';

$pos = 1;

function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

$notes = $text_string = $title = $id = $author = $language = $ms = $f_input= $annotations = $use_manual_RS = $manual_RS = "";
$b_in_key = $noteCount = $totalElementsCount = 0;
$repetition_pattern = false;

$notes = test_input($_POST["notes"]);
$text_string = test_input($_POST["text_string"]);
$title = test_input($_POST["title"]);
$id = test_input($_POST["id"]);
$author = test_input($_POST["author"]);
$language = test_input($_POST["language"]);
$ms = test_input($_POST["ms"]);
$f_input = test_input($_POST["f_input"]);
$annotations= test_input($_POST["annotations"]);
$text_mode = test_input($_POST["text_mode"]);
$calling_from = test_input($_POST["callingFrom"]);
$alphabet_melodic_structure = test_input($_POST["alphabetMelodicStructure"]);
$use_manual_RS = test_input($_POST["useManualMelodicStructure"]);
$manual_RS = test_input($_POST["manualMelodicStructure"]);
$b_always_flat_at_line_beginning = test_input($_POST["bFlatAlwaysInKeySignature"]);
$repetition_sequence = test_input($_POST["repetitionPattern"] ?? -1);
$repetition_pattern = fromSequenceToRepetitionPattern($repetition_sequence);
$repetitionSigns = calculateRepetitionSigns($repetition_sequence);
$matches = test_input($_POST["matches"] ?? -1);
$matches = json_decode($matches);
$matches_individual_notes = test_input($_POST["matchesIndividualNotes"] ?? -1);
$matches_individual_notes = json_decode($matches_individual_notes, true);

if ($_COOKIE['plicaType'] == 1) {
  $plica_type = 1;
} else {
  $plica_type = 0;
}

$textFont = get_font_family($_COOKIE['textFont']);
$fontSize = get_font_size($_COOKIE['fontSize']);

$notes = preg_replace("/&lt;|&gt;/", "^", $notes);

// Clean note input
$replacements = [
    "\n" => "|",
    " |" => "|",
    "| " => "|",
    "+a" => "u",
    "+b" => "p",
    "+h" => "q",
    "+c" => "r",
    "+d" => "s",
    "*G" => "J",
    "%}" => "h}",
    "B}" => "b}",
    "&#039;" => "'"
];

foreach ($replacements as $search => $replace) {
    $notes = str_replace($search, $replace, $notes);
}
// Specify single quote in php 8
$sq = "'";

$notes_for_plica = str_replace(        [$sq,      ")", "%", "#", "+", "_", "/",      "[", "]", "b} ", "b}", "h} ", "h}", "^" ], "", $notes);
$notes_for_natural = str_replace(      [$sq, "(", ")",      "#", "+", "_", "/",      "[", "]", "b} ", "b}", "h} ", "h}", "^"], "", $notes);
$notes_for_sharp = str_replace(        [$sq, "(", ")", "%",      "+", "_", "/",      "[", "]", "b} ", "b}", "h} ", "h}", "^"], "", $notes);
$notes_for_flat = str_replace(         [$sq, "(", ")", "%", "#", "+",      "/",      "[", "]", "b} ", "b}", "h} ", "h}", "^"], "", $notes);
$notes_clean = str_replace(            [$sq, "(", ")", "%", "#", "+", "_", "/",      "[", "]", "b} ", "b}", "h} ", "h}", "^"], "", $notes);
$notes_in_Brackets_left = str_replace( [$sq, "(", ")", "%", "#", "+", "_", "/", "-", "]",      "b} ", "b}", "h} ", "h}", "^"], "", $notes);
$notes_in_Brackets_right = str_replace([$sq, "(", ")", "%", "#", "+", "_", "/", "-", "[",      "b} ", "b}", "h} ", "h}", "^"], "", $notes);
$notes_in_Pointy_brack = str_replace(  [$sq, "(", ")", "%", "#", "+", "_", "/", "-", "[", "]", "b} ", "b}", "h} ", "h}"     ], "", $notes);
$notes_for_mid_bar = str_replace(      [$sq, "(", ")", "%", "#", "+", "_",      "-", "[", "]", "b} ", "b}", "h} ", "h}", "^"], "", $notes);
$notes_for_b_in_key = str_replace(     [$sq, "(", ")", "%", "#", "+",      "/", "-", "[", "]",                           "^"], "", $notes);
$notes_for_apostrophe = str_replace(   [     "(", ")", "%", "#", "+",      "/", "-", "[", "]", "b} ", "b}", "h} ", "h}", "^"], "", $notes);

// create array to check apostrophe
$apostrophe_string = str_replace(array($sq."?",$sq."-",$sq."b}",$sq."h}",$sq."J", $sq."A", $sq."H", $sq."B", $sq."C", $sq."D", $sq."E", $sq."F", $sq."G", $sq."a", $sq."b", $sq."h", $sq."c", $sq."d", $sq."e", $sq."f", $sq."g", $sq."u", $sq."p", $sq."q", $sq."r", $sq), 1, $notes_for_apostrophe);
// Change all other notes to value "0"
$apostrophe_string = preg_replace('/[a-zA-Z-]/', "0", $apostrophe_string);
// Separate LINES
$apostrophe_string_lines[] = explode("|", $apostrophe_string);
// separate GROUPS
foreach ($apostrophe_string_lines as $apostrophe_string_line) {
	foreach ($apostrophe_string_line as $apostrophe_string_group) {
		$apostrophe_string_groups_ar[] = explode(" ", $apostrophe_string_group);
  	}
}

// create array to check b in key
$b_in_key_string = preg_replace("/[bB]} ?[a-hA-HJupqrs]/", "1", $notes_for_b_in_key);
$b_in_key_string = preg_replace("/h} ?[a-hA-HJupqrs]/", "2", $b_in_key_string );
$b_in_key_string = preg_replace	("/[a-hA-HJupqrs\-]/",   "0", $b_in_key_string );
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
$Plica_string = preg_replace("/\([a-hA-HJupqrs]/", "1", $notes_for_plica);
$Plica_string = preg_replace("/[a-hA-HJupqrs\-]/", "0", $Plica_string);

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
$Natural_string = preg_replace("/%[a-hA-HJupqrs]/", "1", $notes_for_natural);
$Natural_string = preg_replace("/[a-hA-HJupqrs\-]/", "0", $Natural_string);

// Separate LINES
$Natural_string_lines[] = explode("|", $Natural_string);
// separate GROUPS
foreach ($Natural_string_lines as $Natural_line) {
	foreach ($Natural_line as $Natural_group) {
		$Natural_notes_groups_ar[] = explode(" ", $Natural_group);
  	}
}

// Calculate position of sharp alteration
// Change notes preceded by "#" to value "1"
$Sharp_string = preg_replace("/#[a-hA-HJupqrs]/", "1", $notes_for_sharp);
$Sharp_string = preg_replace("/[a-hA-HJupqrs\-]/", "0", $Sharp_string);
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
$Flat_string = preg_replace("/_[a-hA-HJupqrs]/", "1", $notes_for_flat);
$Flat_string = preg_replace("/[a-hA-HJupqrs\-]/", "0", $Flat_string);

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
// End Calculate position of Flat alteration

// Get melodic lines
$lines_ar = explode("|", $notes_clean);

// Count melodic lines
$n_of_lines = count($lines_ar);

// Clean text
$text_string_clean = str_replace("- ","-", $text_string);
$text_string_clean = str_replace("-","- ", $text_string_clean);
$text_string_clean = str_replace("__","_", $text_string_clean);
$text_string_clean = str_replace("  "," ", $text_string_clean);
$text_string_newline_to_bar_separator = preg_replace("[\n\s*\n]","\$", $text_string_clean);
$text_string_newline_to_bar_separator = str_replace("\n","|", $text_string_newline_to_bar_separator); // new line as text separator
$text_stanza_ar = explode("\$", $text_string_newline_to_bar_separator);
$text_line_ar = explode("\n", $text_string_clean);


foreach ($text_stanza_ar as $text_stanza) {
	$text_line_ar2[] = explode('|', $text_stanza);
}
for ($stanza = 0; $stanza < count($text_stanza_ar); $stanza++) {
	for ($linea = 0; $linea < count($text_line_ar2[$stanza]); $linea++) {
		$text_line_ar2[$stanza][$linea] = trim($text_line_ar2[$stanza][$linea]);
	}
}

$text_stanzas_line_ar = [];
for ($stanza = 0; $stanza < count($text_stanza_ar); $stanza++) {
	array_push($text_stanzas_line_ar, []);
	for ($linea = 0; $linea < count($text_line_ar2[$stanza]); $linea++) {
		$text_stanzas_line_ar[$stanza][$linea] = explode(" ", $text_line_ar2[$stanza][$linea]);
	}
}

// Establish text height
if (strpos($notes, '*G') !== false) {
	$text_height = 146;
} else if (strpos($notes, 'A') !== false) {
	$text_height = 139;
}	else if (strpos($notes, 'B') !== false) {
	$text_height = 133;
} else if (strpos($notes, 'H') !== false) {
	$text_height = 133;
} else {
	$text_height = 129;
}


// Get postiton of notes_in_Brackets_left
// Change notes preceded by parentesis to value "1"
$Brackets_left_string = preg_replace("/\[[a-hA-HJupqrs]/", "1", $notes_in_Brackets_left);

// Change all other notes to value "0"
$Brackets_left_string = preg_replace("/[a-hA-HJupqrs\-]/", "0", $Brackets_left_string);
// Separate LINES
$Brackets_left_lines[] = explode("|", $Brackets_left_string);
// separate GROUPS
foreach ($Brackets_left_lines as $Brackets_left_line) {
	foreach ($Brackets_left_line as $Brackets_left_group) {

		$Brackets_left_lines_groups_ar[] = explode(" ", $Brackets_left_group);
  }
}

// Get postiton of notes_in_Brackets_left
// Change notes preceded by parentesis to value "1"
$Brackets_right_string = preg_replace("/[a-hA-HJupqrs]\]/", "1", $notes_in_Brackets_right);
$Brackets_right_string = preg_replace("/[a-hA-HJupqrs\-]/", "0", $Brackets_right_string);

// Separate LINES
$Brackets_right_lines[] = explode("|", $Brackets_right_string);
// separate GROUPS
foreach ($Brackets_right_lines as $Brackets_right_line) {
	foreach ($Brackets_right_line as $Brackets_right_group) {
		$Brackets_right_lines_groups_ar[] = explode(" ", $Brackets_right_group);
  }
}

// Change notes preceded by parentesis to value "1"
$Pointy_brackets_string = preg_replace("/\^[a-hA-HJupqr]\^/", "§", $notes_in_Pointy_brack);
$Pointy_brackets_string = preg_replace("/\^[a-hA-HJupqr]/", "^", $Pointy_brackets_string);
$Pointy_brackets_string = preg_replace("/[a-h|A-H|J|u|p|q|r]\^/", "^", $Pointy_brackets_string);
// Change all other notes to value "0"
$Pointy_brackets_string = str_replace('^',"1",$Pointy_brackets_string);
$Pointy_brackets_string = str_replace('§',"2",$Pointy_brackets_string);
// Separate LINES
$Pointy_brackets_lines[] = explode("|", $Pointy_brackets_string);
// separate GROUPS
foreach ($Pointy_brackets_lines as $Pointy_brackets_line) {
	foreach ($Pointy_brackets_line as $Pointy_brackets_group) {
		$Pointy_brackets_lines_groups_ar[] = explode(" ", $Pointy_brackets_group);
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

	// Create an array of all note groups in the line
	$qq_notes_ar[] = explode(' ', $q_this_line_ar);
	// text
	$qq_this_text_line_ar = rtrim($text_line_ar[$q], " \|");
	$qq_this_text_line_ar = str_replace("<i>", "", html_entity_decode($qq_this_text_line_ar, ENT_QUOTES, 'UTF-8'));
	$qq_this_text_line_ar = str_replace("</i>", "", $qq_this_text_line_ar);
	$qq_this_text_line_ar = str_replace("<strong>", "", $qq_this_text_line_ar);
	$qq_this_text_line_ar = str_replace("</strong>", "", $qq_this_text_line_ar);

	$qq_this_text_line_ar = trim($qq_this_text_line_ar,"\|");
	$qq_this_text_line_ar = ltrim($qq_this_text_line_ar," ");
	$qq_text_ar[] = explode(' ', $qq_this_text_line_ar);
}

for ($yy = 0; $yy < $longest_number_of_syl_in_line; $yy++) {
    for ($yyy = 0; $yyy < $n_of_lines; $yyy++) {
        if (isset($qq_notes_ar[$yyy][$yy])) {
            $column[] = strlen($qq_notes_ar[$yyy][$yy]);
        } else {
            $column[] = 0; // Default value if the index does not exist
        }

        if (isset($qq_text_ar[$yyy][$yy])) {
            $text_column[] = strlen($qq_text_ar[$yyy][$yy]);
        } else {
            $text_column[] = 0; // Default value if the index does not exist
        }
    }
}

$column_subar = array_chunk($column, $n_of_lines);

// text
$text_column_subar = array_chunk($text_column, $n_of_lines);

for ($yyyy = 0; $yyyy < $longest_number_of_syl_in_line; $yyyy++) {
	$max_n_of_notes_in_column[] = max($column_subar[$yyyy]);

	// text
 	$max_n_of_letters_in_column[] = max($text_column_subar[$yyyy]);
	$thisDiffNotes_Text = max($text_column_subar[$yyyy])*($fontSize/1.5) - max($column_subar[$yyyy])*6;

	if ($thisDiffNotes_Text > 50) {
		$diffNotes_Text[] = $thisDiffNotes_Text-45;
	}else {
		$diffNotes_Text[] = 0;
	}
}

if (count($lines_ar)/100 >= 1){
  $extra_space = 12;
} else {
  $extra_space = 0;
}

// Calculate line width
$col_widths_ar = [];
// Get widtg of first slot (clef) (89, usually)
$clef_width = 70 + $extra_space + $fontSize;
// initialize x translate
$g_x_translate_columns = [$clef_width];
$line_width = $clef_width;
for ($column = 0; $column < count($max_n_of_notes_in_column); $column++) {
  $col_width = round(34 + 20 * $max_n_of_notes_in_column[$column] + $diffNotes_Text[$column]);
  $line_width += $col_width;
  array_push($g_x_translate_columns, $line_width);
  array_push($col_widths_ar, $col_width);
}



// override if manual melodic scheme
if ($use_manual_RS) {
  // Preserve the original string to store in DB
	$melodic_structure_to_store = $manual_RS;
  if ($alphabet_melodic_structure == 1){
    // Convert entered string into array
    $SMalpha = explode(" ", $manual_RS);
  }else{
    // Convert to greek if necessary
    $SMalpha = convert_manual_melodic_structure_to_greek($manual_RS);
  }
}else{
  [$SMalpha, $melodic_structure_to_store] = get_melodic_structure($n_of_lines, $qq_notes_ar, $lines_ar);
}


if ($title){
	echo "\n  <br/>\n";
	echo "  <div id='title' style='font-family:$textFont'>";
	echo graces($title);
	echo "</div>\n";
}

if ($author){
	echo "  <div id='author' style='font-family:$textFont'>";
	echo $author;
	echo "</div>\n";
}


if ($id){
	echo "  <div id='id' style='font-family:$textFont'>";
	echo $id;
	echo "</div>\n";
}

if ($ms) {
	echo "  <div id='ms' style='font-family:$textFont'>";
	echo $ms;
	if ($f_input) {
	  echo " ".$f_input;
	}
	echo "</div>\n";
}

echo "  <br/>\n";

// ************************
// start printing the music
// ************************

// fixed parameter
$show_stanza_number = true;

echo "  <div id='staves'>\n";

$lineCount = 0;
$lineCount_within_stanza = 0;
$line_text_count = 0;

for ($stanzaN = 0; $stanzaN < count($text_stanza_ar); $stanzaN++) {
  [$text_stanzas_line_ar[$stanzaN], $repetitions, $repeated_text_lines] = parse_for_repetition($text_stanzas_line_ar[$stanzaN], $repetition_pattern);

	if($stanzaN == 0){
		if ($text_mode == 2 and $show_stanza_number == true){
      echo "    <div style='font-family:\"$textFont\"; font-size:".($fontSize*1.5)."px'><br>I</div>\n";
		}
	} elseif ($text_mode == 2){
    $lineCount_within_stanza = 0;
		if ($show_stanza_number == true){
      echo "    <div style='font-family:\"$textFont\"; font-size:".($fontSize*1.5)."px'><br>".numberToRomanRepresentation(($stanzaN + 1))."</div>\n";

		}
	}else{
		break;
	}


	// for each musical line
	for ($line = 0; $line < $n_of_lines; $line++) {
		// Resetting match count (used in color class handles)
		$match_count = 0;
		$lineCount++;
    $lineCount_within_stanza++;
    $line_text_count++;
		// uncomment to annull effect of b} at each metrical line
		//	$b_in_key = 0;
	

		$n_of_textual_lines_in_stanza = count($text_stanzas_line_ar[$stanzaN]);
    
		if ($n_of_lines - $n_of_textual_lines_in_stanza > 0){
			$is_tornada = true;
		}else{
		 	$is_tornada = false;
		}
		$diff_stanza_tornada = $n_of_lines - $n_of_textual_lines_in_stanza;


		if ($text_mode == 2 and $stanzaN > 0){
			$diff_stanza_tornada = $n_of_lines - $n_of_textual_lines_in_stanza;
      
			if ($n_of_lines - $n_of_textual_lines_in_stanza > 0){
				 $is_tornada = true;
			}else{
		 		$is_tornada = false;
		 	}

			if ($is_tornada == true){
			 	$line = $diff_stanza_tornada + $tornada_counter;
			 	$tornada_counter = $tornada_counter + 1;
			}
		}else{
			$diff_stanza_tornada = 0;
		}

		// reset $this_line_width for each line
		$this_line_width = 0;

		// line break for every new line apart from the first one
		if ($line > 0) {
			echo "    <br/>\n";
		}

		// Clean line array
		$this_line_ar = rtrim($lines_ar[$line], " \|");
		$this_line_ar = trim($this_line_ar,"\|");
		$this_line_ar = ltrim($this_line_ar," ");
    
		// Create an array of all note groups in line
		$notes_ar = explode(' ', $this_line_ar);

		// Count note groups in line
		$n_of_note_groups = count($notes_ar);

		$svg_heigth = 160;
    $svg_translate = 10;
    $g_x_translate = 0;
		$text_distance = 40+$fontSize*0.2;
		
		// make space if range is too wide
		if (strpos($notes, '+c') !== false || strpos($notes, '+d') !== false) {
			$svg_heigth = 175;
			$svg_translate = 35;
			if (strpos($notes, '*G') !== false) {
				$svg_heigth = 190;
			}
		}

		if ($text_mode == 1) {
      $svg_heigth = $svg_heigth + ((count($text_stanzas_line_ar)-1) * $text_distance);
    }
    if (array_key_exists($lineCount-1, $repetitions)){
      $number_of_repetitions_for_this_line = count($repetitions[$lineCount-1]);
      $svg_heigth = $svg_heigth + 22 * $number_of_repetitions_for_this_line;
    }

    // Print svg container for line
		echo "      <svg  class='stave-line' height='".$svg_heigth."' width='$line_width'>\n";
    
    printStaffLines($svg_translate);
    
    // Print G-Clef
    print_G_clef($svg_translate);
    

		if($_COOKIE['octaveClef'] == 1) {
      print_octave_clef();
		}
    if (array_key_exists($line+1, $repetitionSigns)) {
      if ($repetitionSigns[$line+1]["start"] == 1){
        print_start_repetition_sign($svg_translate);
      }
      if ($repetitionSigns[$line+1]["end"] == 1){
        print_end_repetition_sign($svg_translate, $line_width);
      }
    }
    
		// Print b in clef if necessary
		$this_line_with_apostr = explode("|", $notes_for_apostrophe);
		$pos_of_first_apostr = strpos($this_line_with_apostr[$line], "'");

		if ($pos_of_first_apostr > 0) {
			$this_line_substr = substr($this_line_with_apostr[$line], 0, $pos_of_first_apostr);
			$isMsLineOver = true;
		} else {
			$this_line_substr = $this_line_with_apostr[$line];
			$isMsLineOver = false;
		}
		$beginning_of_line_contains_b = strpos($this_line_substr, "b");

		if ($b_in_key_notes_groups_ar[$line][0][0] == 1
		or	(
				$b_in_key == 1
				and $_COOKIE['showCarryBFlat'] != 2
				and ($beginning_of_line_contains_b > 0 or $beginning_of_line_contains_b === 0)
				and $b_in_key_notes_groups_ar[$line][0][0] != 2
			)
		or $b_always_flat_at_line_beginning == 1 // Always print if param "always_flat_at_line_beginning" is true
		) {
  			if ($b_in_key == 1
  			and $b_in_key_notes_groups_ar[$line][0][0] != 1
  			and $_COOKIE['showCarriedBFlatBrackets'] != 2) {
  				echo "          <text x='41.5' y='65' style='font-size:20px; font-family:\"Times New Roman\"'>[&nbsp;&nbsp;]</text>\n	";
      }

      print_b_flat($line, 50);

  		$b_in_key = 1;

		} elseif ($b_in_key_notes_groups_ar[$line][0][0] == 2){
			// natural in clef
			print_h_natural($line, 50);
			$b_in_key = 0;
		} elseif ($isMsLineOver == true){
			$b_in_key = 0;
		} elseif($_COOKIE['showCarryBFlat'] == 2) {
			$b_in_key = 0;
		}


		// Print Melodic Structure
		if ($_COOKIE['melodicStructure'] == 1 and $calling_from != "searchTool") {
      print_melodic_structure($SMalpha[$line], $textFont, $fontSize);
		} 


		if ($_COOKIE['lineNumber'] == 1 && $calling_from != "searchTool") {
      while (in_array($line_text_count - 1, $repeated_text_lines, true)) {
          $line_text_count++;
      }
		 
      // Print line number
  		echo "<text x='45' y='$text_height' style='font-family:$textFont; font-size: ".$fontSize."px; visibility:".$visible.";' class='line_number'>".$line_text_count.".</text>\n";
      
      // Print eventual repeated line number
      if (array_key_exists($lineCount-1, $repetitions)){
        $rep_count = 1;
        foreach ($repetitions[$lineCount-1] as $key => $value) {
          echo "          <text x='45' y='".($text_height + 30 * $rep_count)."' style='font-family:$textFont; font-size: ".$fontSize."px; visibility:".$visible.";' class='line_number'>".($key + 1).".</text>\n";
          
          $rep_count++;
        }
      }
      
      // print line number in special text mode (not compatible with repetition)
      if ($text_mode == 1) {
        for ($index_for_stanza_number_text_mode_1 = 1; $index_for_stanza_number_text_mode_1 < count($text_stanza_ar); $index_for_stanza_number_text_mode_1++){
          
          $lineCount_subsequent = (($index_for_stanza_number_text_mode_1)* $n_of_lines) + $line + 1;
          
          echo "          <text x='45' y='".($text_height+(($index_for_stanza_number_text_mode_1)*$text_distance))."' style='font-family:$textFont; font-size: ".$fontSize."px; visibility:".$visible.";' class='line_number'>".$lineCount_subsequent.".</text>\n";
        }
      }
    }
    
		echo "        </g>\n"; // close Clef <g>


		if ($line > 0){
			$totalElementsCount++;
		}

		// for each note group
		for ($i = 0; $i < $n_of_note_groups; $i++) {
			
			// Count notes in note group
			$n_of_notes_in_note_group[$i] = strlen($notes_ar[$i]);
      
      // Print note's g
			echo "        <g transform='translate($g_x_translate_columns[$i], $svg_translate) rotate(0)'>\n";
			
			if ($i > 0){
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
        if (isset($Mid_bar_notes_groups_ar[$line][$i][$x+1]) &&
            substr($Mid_bar_notes_groups_ar[$line][$i][$x+1], -1) == "/") {
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
				||$notes_ar[$i][$x] == "J") {
					$totalElementsCount += 1;
				}

				$note_group_ar = str_split($notes_ar[$i], 1);

				// b in key (middle)
        if (isset($b_in_key_notes_groups_ar[$line][$i][$x]) && // if there is something in the note-position
            $b_in_key_notes_groups_ar[$line][$i][$x] == 1 &&   // and it is a b}
            $i != 0 &&                                         // if it's not the first group of line (cause already printed)
            $b_always_flat_at_line_beginning != 1) {           // and b-flat NOT fixed
              
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
        
        // Start printing notes
				if ($note_group_ar[$x] !== "?" and $note_group_ar[$x] !== "-") {
					
					// Class handles for colors
          if ($calling_from == "searchTool") {
            // printing classes and updating the match count
					  [$match_count, $color_class] = get_search_tool_color_classes($matches, $i, $matches_individual_notes, $match_count);
          }else{
            $color_class = "";
          }

					$plica = $Plica_notes_groups_ar[$line][$i][$x];
          
					if ($plica == 0) {
            // c($note_group_ar[$x]);
            $tx = 390 + 20 * $x;
            $ty = getNoteTy($note_group_ar[$x]);
            $d1 = -1.19;
            $d2 = -1.19;
            $class = "single_note".$color_class;
            $ledg_lines = 1;
            print_note($tx, $ty, $d1, $d2, $noteCount, $class, $note_path, $note_group_ar[$x], $ledg_lines);
					
          } elseif ($plica == 1) {
          
            if ($plica_type != 1) {
              $tx = 240 + 20 * $x;
              $ty = get_plica_1_type_0($note_group_ar[$x]);
              $d1 = -0.7;
              $d2 = -0.7;
              $class = "single_note plica".$color_class;
              $ledg_lines = 1;
              print_note($tx, $ty, $d1, $d2, $noteCount, $color_class, $note_path, $note_group_ar[$x], $ledg_lines);
                            
  					} elseif ($plica_type == 1) {
              // check plica direction
              $ledg_lines = 0;
              $plica_direction = note_number($note_group_ar[$x]) - note_number($note_group_ar[$x-1]);
              $class = "single_note plica_comma".$color_class;
              $d1 = 1;

              if ($plica_direction <= 0) {
                $tx = 20 * $x;
                $ty = get_plica_1_type_1_descendant($note_group_ar[$x-1]);
                $d2 = $d1 = 1;
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
					echo "            <g class='hypen'></g>\n";
				}
        // end note

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
					echo "<path";
					echo " transform='matrix(0.3, 0, 0, 0.5, -4, 35)'";
					echo "id='$n_of_notes_in_note_group[$i]'";
					echo " name='bracket_left' d='$bracket_left_path'/>";
				}

        if (isset($Brackets_right_lines_groups_ar[$line][$i][$x])) {
            $Bracket_right = $Brackets_right_lines_groups_ar[$line][$i][$x];
        } else {
            $Bracket_right = null; // Assign a default value or handle the error
        }
        
				if ($Bracket_right == 1) {
					echo "<path";
          echo " class='$all_syl_in_brackets $n_of_notes_in_note_group[$i] $bracket_opens_within_syl'";
					if (
            1 or // passa sempre
            ($all_syl_in_brackets == 1 and $n_of_notes_in_note_group[$i] > 1)
  					or $bracket_opens_within_syl == 0
  					or !preg_match('/[a-zA-Z]/', $note_group_ar[$x])) {
						echo " transform='matrix(0.3, 0, 0, 0.5,";
						echo $col_widths_ar[$i] - 12;
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

					echo " name='bracket_right' d='$bracket_right_path'/>";
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
						print_closing_pointy_bracket($col_widths_ar[$i]);
						$Pointy_bracket_open_or_closed = "closed";
					} else {
            print_opening_pointy_bracket();
						$Pointy_bracket_open_or_closed = "open";
					}
				} else if ($Pointy_bracket == 2) {
          print_opening_pointy_bracket();
          print_closing_pointy_bracket($col_widths_ar[$i]);
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
          print_b_flat($line, 4, 61);
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
					print_mid_bar($col_widths_ar[$i]);
				}
			}
      // end operation for single note in note group

			// Continuation of operations for the whole syllable (for $i)

			// ********** SLUR ********** //
			if ($n_of_notes_in_note_group[$i] < 2) {
					/*no slur*/
			} elseif ($Plica_notes_groups_ar[$line][$i][$n_of_notes_in_note_group[$i]-1] == 1
				and $plica_type == 1
				and $n_of_notes_in_note_group[$i] == 2) {
				/*no slur*/
			} elseif ($n_of_notes_in_note_group[$i] > 1){
				print_slur($notes_ar[$i], $Plica_notes_groups_ar[$line][$i], $plica_type, $x, $n_of_notes_in_note_group[$i]);
			} // End operations note group (??)

			// End Print music

			// ********** PRINT TEXT ********** //
			// Create and array of syllabes
    
			$text_ar = explode(' ', $text_line_ar2[$stanzaN][$lineCount_within_stanza-1]); // Sobstitutes $line with $lineCount, so that text does not repeat with repetition

			$text_height2 = $text_height;

			for ($stanzaOther = 0; $stanzaOther < count($text_stanzas_line_ar); $stanzaOther++) {

				// Calculate x position of syl
        if (isset($text_stanzas_line_ar[$stanzaN][$line-$diff_stanza_tornada][$i])) {
            $syl_len = mb_strlen($text_stanzas_line_ar[$stanzaN][$line-$diff_stanza_tornada][$i], 'utf-8');
        } else {
            $syl_len = 0;
        }
				if ($syl_len > 5 and $n_of_notes_in_note_group[$i] == 1) {
					$text_position = 18 - $syl_len/3;
				}else{
					$text_position = 18;
				}
								
				$this_syl = "";

				// Print syl
				if ($text_mode == 2){
          
					$this_syl = $this_syl." ".str_replace("_", " ", $text_stanzas_line_ar[$stanzaN][$lineCount_within_stanza-1-$diff_stanza_tornada][$i]);

				} else if ($text_mode == 1 and $stanzaOther > 0){
					$diff_stanza_tornada = $n_of_lines - count($text_stanzas_line_ar[$stanzaOther]);
					$this_syl = $this_syl." ".str_replace("_", " ", $text_stanzas_line_ar[$stanzaOther][$lineCount-1-$diff_stanza_tornada][$i]);

				}	else { // if text_mode == 0
          if (isset($text_stanzas_line_ar[$stanzaOther][$lineCount-1][$i])) {
              $this_syl .= " " . str_replace("_", " ", $text_stanzas_line_ar[$stanzaOther][$lineCount-1][$i]);
          }
        }

				$this_syl = graces(parse_pseudo_html($this_syl));
        
				echo "          <text style='font-family:$textFont; font-size:".$fontSize."px;' x='$text_position' y='$text_height2'>";
				echo $this_syl;
				echo "</text>\n";
        // if a line is meant to be repeated under this, print it
        if (array_key_exists($lineCount-1, $repetitions)) {
          $rep_count = 1;
          foreach ($repetitions[$lineCount-1] as $index => $this_text_line_ar) {
            
            $rep_syl = graces(parse_pseudo_html($this_text_line_ar[$i]));

            echo "          <text class='re' style='font-family:$textFont; font-size:".$fontSize."px;' x='$text_position' y='".($text_height2+30*$rep_count)."'>";
            echo $rep_syl;
            echo "</text>\n";
            
            $rep_count++;
          }
        }

				$text_height2 = $text_height2 + $text_distance;
				if ($text_mode == 0) $stanzaOther = count($text_stanzas_line_ar);
			}

      // Print ms lb
      // $_COOKIE['showMsLineBreaks'] != 2    => User setting not disabling ms lb view
      // isset($apostrophe_string_groups_ar[$line][$i+1][0]) 
      if ($_COOKIE['showMsLineBreaks'] != 2
          && isset($apostrophe_string_groups_ar[$line][$i+1][0])
          && ($apostrophe_string_groups_ar[$line][$i+1][0] == "'"
              || $apostrophe_string_groups_ar[$line][$i+1][0] == 1)) {
          echo "          <text style='font-family:$textFont; font-size:15px;' x='".($col_widths_ar[$i]-10)."' y='30' fill='gray'>&#9662;</text>\n";
      }
      
			// Close bar
			echo "        </g>\n";

      if (!$b_always_flat_at_line_beginning // ADDED 28-2-2025:       // Unless there is a b-flat for the whole score 
            && (isset($apostrophe_string_groups_ar[$line][$i+1])      // If there is something in the note position
                && ($apostrophe_string_groups_ar[$line][$i+1] == 1      // and that something is a 1
                || $apostrophe_string_groups_ar[$line][$i+1] == "'")    // Or a prime sign (why two ways?)
            )
           ) 
      {
        $b_in_key = 0; // reset b_in_key
      }

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
    
    echo "      </svg>\n"; // Close line svg

	} // End analyse string
}// end stanza
// Width of largest line
echo "    <input type='hidden' id='largest_line' name='largest_line' value='".$line_width."'/>\n";
echo "    <input type='hidden' id='melodic_structure_to_store' value='".$melodic_structure_to_store."'/>\n";


// Annotations
if ($calling_from != "searchTool") {
  print_annotations($annotations);
}

// close divs // which divs?
echo "    </div>\n";
echo "  </div>";

function logMessage($message, $level = 'INFO', $logFile = 'modern.log') {
    // Define the format of the log entry: [YYYY-MM-DD HH:MM:SS] [LEVEL] Message
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] $message" . PHP_EOL;
    
    // Append the log entry to the file
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}
function logArray($array, $level = 'INFO', $logFile = 'modern.log') {
    // Convert array to a readable string
    $message = print_r($array, true);
    
    // Define the format of the log entry: [YYYY-MM-DD HH:MM:SS] [LEVEL] Message
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] " . PHP_EOL . $message . PHP_EOL;
    
    // Append the log entry to the file
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

?>

	<style>
		polygon {
			fill: transparent;
			z-index: 1;
		}

		line {
			stroke: black !important;
			z-index: 10;
		}
	</style>