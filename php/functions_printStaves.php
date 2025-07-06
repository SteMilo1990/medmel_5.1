<?php

function print_note($tx, $ty, $d1, $d2, $noteCount, $class, $path, $note, $ledg_lines, $r2=0) {
  echo "<g onclick='selectNote($noteCount)'";
  echo " id='note$noteCount'";
  echo " class='$class note$noteCount'";
  echo " transform='matrix($d1, 0, $r2, $d2, $tx, $ty)'>";
  echo "<path d='$path'/>";
  if ($ledg_lines) {
    print_ledger_lines($note);
  }
  echo "</g>";
}

function print_G_clef($svg_translate, $tx=0) {
  global $G_clef_path;
  echo "<g transform='translate($tx,".$svg_translate.")' >";
  echo "<path class='G-clef' stroke='#000' d='$G_clef_path'/>";
}

function print_ledger_lines($note) {
  //  C ledger line
    if ($note === "C") {
      $path = "M 317,304 295,304";
      print_a_ledger_line($path);
    }

    // lower B/H ledger line.
    if ($note === "H" || $note === "B" || $note === "J") {
      $path = 'M 317,309.9 295,309.9';
      print_a_ledger_line($path);
      
      // lower G (*G) ledger line
      if ($note === "J") {
        $path = 'M 317,321.65 295,321.65';
        print_a_ledger_line($path);
      }
    }
    // Higher b/h (+b/+h) ledger line
    else if ($note === "p" || $note === "q" || $note === "s") {
      $path = 'M 317,298 295,298';
      print_a_ledger_line($path);
      
      if ($note === "s") {
        $path = 'M 317,286.2 295,286.2';
        print_a_ledger_line($path);
      }
    }
    // Higher b/h (+b/+h) ledger line
    else if ($note === "A") {
      // higher A ledger line
      $path ='M 317,315.7 295,315.7';
      print_a_ledger_line($path);

      // Lower A ledger line
      $path = 'M 317,304 295,304';
      print_a_ledger_line($path);
    }

    else if ($note === "u" || $note === "r") {
      $path = 'M 317,303.9 295,303.9';
      print_a_ledger_line($path);
      
      if ($note === "r") {
        $path = 'M 317,292.1 295,292.1';
        print_a_ledger_line($path);
      }
    }
    
}
function print_a_ledger_line($path) {
  echo "<path d='$path' style='fill:none;stroke:#000000;stroke-width:1px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1'/>";
}

function printStaffLines($ty, $tx=0, $width="100%") {
  echo "<g transform='translate($tx, $ty)'>";
  echo "<line x1='0' y1='31.2' x2='$width' y2='31.2' style='stroke:black;stroke-width:1'/>";
  echo "<line x1='0' y1='45.5' x2='$width' y2='45.5' style='stroke:black;stroke-width:1'/>";
  echo "<line x1='0' y1='59.8' x2='$width' y2='59.8' style='stroke:black;stroke-width:1'/>";
  echo "<line x1='0' y1='74.1' x2='$width' y2='74.1' style='stroke:black;stroke-width:1'/>";
  echo "<line x1='0' y1='88.4' x2='$width' y2='88.4' style='stroke:black;stroke-width:1'/>";
  echo "</g>";  
}

function numberToRomanRepresentation($number) {
	$map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
	$returnValue = '';
	while ($number > 0) {
		foreach ($map as $roman => $int) {
			if($number >= $int) {
				$number -= $int;
				$returnValue .= $roman;
				break;
			}
		}
	}
	return $returnValue;
}

function drawVerticalLineAtEndOfLine($svg_translate, $width_of_column) {
  // Vertical bar at end of line
  echo "<g transform='translate(0,".$svg_translate.") rotate(0)'>";
  echo "<line class='endVerticalLine' x1='".($width_of_column-0.5)."' y1='88.92' x2='".($width_of_column-0.5)."' y2='30.68' style='stroke:black;stroke-width:1'/>";
  echo "</g>";
}
function drawVerticalLineAtEndOfLine_midbar_single($svg_translate, $width_of_column) {
  // Vertical bar at end of line
  echo "<g transform='translate(0,".$svg_translate.") rotate(0)'>\n";
  echo "<line class='midbar2' x1='".($width_of_column-10)."' y1='88.92' x2='".($width_of_column-10)."' y2='30.68' style='stroke:black;stroke-width:1' />";
  echo "</g>\n";
}
function drawVerticalLineAtEndOfLine_midbar_double($svg_translate, $width_of_column) {
  // Vertical bar at end of line
  echo "<g transform='translate(0,".$svg_translate.") rotate(0)'>";
  echo "<line class='midbar2' x1='".($width_of_column-10)."' y1='88.92' x2='".($width_of_column-10)."' y2='30.68' style='stroke:black;stroke-width:1'/>";
  echo "<line class='midbar2' x1='".($width_of_column-0.5)."' y1='88.92' x2='".($width_of_column-.5)."' y2='30.68' style='stroke:black;stroke-width:10'/>";
  echo "</g>";
}
function print_start_repetition_sign($svg_translate) {
  // Vertical bar at end of line
  echo "<g transform='translate(0, $svg_translate)'>\n";
  echo  "<line class='start_repetition_line_1' x1='73' y1='21' x2='73' y2='79' style='stroke:black;stroke-width:7' />";
  echo "<line class='start_repetition_line_1' x1='80' y1='21' x2=80 y2='79' style='stroke:black;stroke-width:1' />";
  print_circle(86, 43, 1.5);
  print_circle(86, 57, 1.5);
  echo "</g>\n";

}
function print_end_repetition_sign($svg_translate, $line_width) {
  // Vertical bar at end of line
  echo "<g transform='translate(0,".$svg_translate.") rotate(0)'>";
  echo "<line class='midbar2' x1='".($line_width-10)."' y1='21' x2='".($line_width-10)."' y2='79' style='stroke:black;stroke-width:1'/>";
  echo "<line class='midbar2' x1='".($line_width-0.5)."' y1='21' x2='".($line_width-.5)."' y2='79' style='stroke:black;stroke-width:10'/>";
  print_circle($line_width-16, 43, 1.5);
  print_circle($line_width-16, 57, 1.5);
  echo "</g>\n";
}
function print_circle($cx, $cy, $r){
  echo "<circle cx=$cx cy=$cy r=$r stroke='black' stroke-width='1' fill='black'/>";
}

function c($t){
	global $c_active;
	if ($c_active){
    echo "<div id='ms'>";
		if (gettype($t) == "array") {
			echo "<pre>";
			print_r($t);
			echo "</pre>";
			// error_log("--Start--");
			// error_log($t);
			// error_log("--End--");
		}else{
			echo $t."\n";
			error_log("--Start--");
			error_log($t);
			error_log("--End--");
		}
    echo "</div>";
	}
}

function print_b_flat($line, $tx, $ty=61, $d1=0.04, $d2=-0.04) {
  global $flat_path;
  echo "<path class='b_in_key' id='b_key_".$line."'";
  echo " transform='matrix($d1, 0,  0, $d2, $tx, $ty)'";
  echo " d='$flat_path'";
  echo "/>";
}

function print_h_natural($line, $tx, $ty=32) {
  global $natural_path;
  echo "<path class='natural' id='h_key_".$line."' transform='matrix(3, 0,  0, 3, $tx, $ty)' d='$natural_path' />";
}

function print_sharp($note) {
  global $sharp_path;
  $ty = get_sharp_ty($note);
  echo "<g transform='matrix(1.5, 0, 0, 1.5, -122, $ty)'>";
  echo "<path d='$sharp_path'/>";
  echo "</g>";
}

function get_sharp_ty($note) {
  $noteValues = [
      "J" => "-544",
      "A" => "-551",
      "H" => "-558",
      "C" => "-565",
      "D" => "-572",
      "E" => "-579",
      "F" => "-586",
      "G" => "-593",
      "a" => "-600",
      "h" => "-607",
      "c" => "-616",
      "d" => "-621",
      "e" => "-628",
      "f" => "-635",
      "g" => "-642",
      "u" => "-649",
      "p" => "-656",
      "q" => "-656",
      "r" => "-664",
      "s" => "-664",
  ];

  return $noteValues[$note];
}  

function get_natural_ty($note) {
    $noteValues = [
        "J" => "95",
        "A" => "88",
        "H" => "81",
        "C" => "74",
        "D" => "67",
        "E" => "60",
        "F" => "53",
        "G" => "46",
        "a" => "39",
        "h" => "32",
        "b" => "32", // Same value as 'h'
        "c" => "25",
        "d" => "17",
        "e" => "10",
        "f" => "3",
        "g" => "-4",
        "u" => "-11",
        "p" => "-18",
        "q" => "-18", // Same value as 'p'
        "r" => "-25",
        "s" => "-32",
    ];

    return $noteValues[$note] ?? null; // Return null if the note is not found
}

function getNoteTy($note) {
    $noteValues = [
      "J" => "485",
      "A" => "478",
      "H" => "471", "B" => "471",
      "C" => "464",
      "D" => "457",
      "E" => "450",
      "F" => "443.3",
      "G" => "436",
      "a" => "429",
      "h" => "422", "b" => "422",
      "c" => "414.5",
      "d" => "407",
      "e" => "400.3",
      "f" => "393",
      "g" => "386",
      "u" => "379",
      "p" => "372", "q" => "372",
      "r" => "365",
      "s" => "358"
  ];

  if (isset($note, $noteValues[$note])) {
      return $noteValues[$note];
  } elseif ($note === "9") {
      // Do nothing
  }
}

function get_flat_ty($note) {
    $noteMapping = [
      "J" => "124",
      "A" => "117",
      "H" => "22", "h" => "22",
      "B" => "110",
      "C" => "103",
      "D" => "96",
      "E" => "89",
      "F" => "82",
      "G" => "75",
      "a" => "67",
      "b" => "61",
      "c" => "53",
      "d" => "47",
      "e" => "39",
      "f" => "33",
      "g" => "25",
      "u" => "18",
      "p" => "11", "q" => "11",
      "r" => "4",
      "s" => "-3",
  ];

  return $noteMapping[$note];
}

function get_plica_1_type_0($note) {
  $noteToValue = [
    "J" => "320.3", 
    "A" => "327.3",
    "H" => "320.3", "B" => "320.3",
    "C" => "315.3",
    "D" => "306",
    "E" => "301.3",
    "F" => "294.3",
    "G" => "287",
    "a" => "280",
    "h" => "273", "b" => "273",
    "c" => "265.5",
    "d" => "258",
    "e" => "251",
    "f" => "244",
    "g" => "239",
    "u" => "230",
    "p" => "221", "q" => "221",
    "r" => "212",
    "s" => "203"
  ];

  // Output the value if the note exists in the mapping
  if (isset($noteToValue[$note])) {
      return $noteToValue[$note];
  }
}

function get_plica_1_type_1_descendant($note) {

  $noteToValue = [
      "J" => "21", 
      "A" => "14",
      "H" => "7", "B" => "7",
      "C" => "0",
      "D" => "-7",
      "E" => "-14",
      "F" => "-21",
      "G" => "-28",
      "a" => "-35",
      "h" => "-42", "b" => "-42",
      "c" => "-49",
      "d" => "-56",
      "e" => "-63",
      "f" => "-70",
      "g" => "-77",
      "u" => "-84",
      "p" => "-91", "q" => "-91",
      "r" => "-98",
      "s" => "-105"
  ];

  // Output the value if the note exists in the mapping
  if (isset($noteToValue[$note])) {
    return $noteToValue[$note];
  }
}

function get_plica_1_type_1_ascendant($note) {
  $noteToValue = [
    "J" => 224, 
    "A" => 217,
    "H" => 210, "B" => 210,
    "C" => 203,
    "D" => 196,
    "E" => 189,
    "F" => 182,
    "G" => 175,//Correct
    "a" => 168,
    "h" => 161, "b" => 161,
    "c" => 154,
    "d" => 147,
    "e" => 140,
    "f" => 133,
    "g" => 126,
    "u" => 119,
    "p" => 112, "q" => 112,
    "r" => 105,
    "s" => 98
  ];

  // Output the value if the note exists in the mapping
  if (isset($noteToValue[$note])) {
      return $noteToValue[$note];
  }
}

function print_mid_bar($width_of_column) {
  echo "<line class='midbar' x1='".($width_of_column-5)."' y1='88.92' x2='".($width_of_column-5)."' y2='30.68' style='stroke:black;stroke-width:1'/>";
}

function get_slur_info_for_first_note($first_note_of_the_group){
    $noteMapping = [
      "J" => [102, 0],
      "A" => [102, 0],
      "H" => [95, 1], "B" => [95, 1],
      "C" => [87, 2],
      "D" => [80, 3],
      "E" => [73, 4],
      "F" => [66.3, 5],
      "G" => [59, 6],
      "a" => [52, 7],
      "h" => [45, 8], "b" => [45, 8],
      "c" => [37.5, 9],
      "d" => [25, 10],
      "e" => [23.3, 11],
      "f" => [16, 12],
      "g" => [9, 13],
      "u" => [5, 14],
      "p" => [2, 15], "q" => [2, 15],
      "r" => [-4, 16], "s" => [-4, 16],
  ];

  // Assign $My and $first_note_height based on the mapping
  if (isset($noteMapping[$first_note_of_the_group])) {
      return $noteMapping[$first_note_of_the_group];
  }
}

function get_last_note_height($last_note_of_note_group) {
  $noteHeightMapping = [
    "J" => 0, 
    "A" => 0,
    "B" => 1, "H" => 1,
    "C" => 2,
    "D" => 3,
    "E" => 4,
    "F" => 5,
    "G" => 6,
    "a" => 7,
    "b" => 8, "h" => 8,
    "c" => 9,
    "d" => 10,
    "e" => 11,
    "f" => 12,
    "g" => 13,
    "u" => 14,
    "p" => 15, "q" => 15,
    "r" => 16, "s" => 16,
    ];

  // Assign $last_note_height based on the mapping
  return $noteHeightMapping[$last_note_of_note_group] ?? null; // Default to null if not found
}

function get_note_to_weigth($group) {
  $noteToWeightMap = [
      "J" => -1, "A" => 0, "B" => 1, "H" => 1,
      "C" => 2, "D" => 3, "E" => 4, "F" => 5,
      "G" => 6, "a" => 7, "b" => 8, "h" => 8,
      "c" => 9, "d" => 10, "e" => 11, "f" => 12,
      "g" => 13, "u" => 14, "p" => 15, "q" => 15,
      "r" => 16, "s" => 17,
  ];

  // Process the group
  $note_to_weight = array_map(
      function($note) use ($noteToWeightMap) {
          return $noteToWeightMap[$note] ?? null; // Assign null if the note is invalid
      },
      str_split($group)
  );

  // Ensure all weights are valid
  $note_to_weight = array_filter($note_to_weight, fn($weight) => $weight !== null);
  return $note_to_weight;
}

function print_opening_pointy_bracket() {
  echo "<line x1='10' y1='28' x2='1' y2='52.5' style='stroke:black; stroke-width:2; stroke-linecap: round;'/>
     <line x1='1' y1='52.5' x2='10' y2='77' style='stroke:black; stroke-width:2; stroke-linecap: round;'/>";
}

function print_closing_pointy_bracket ($width_of_column) {
  echo "<line x1='".($width_of_column - 10)."' y1='28' x2='".($width_of_column-1)."' y2='52.5' style='stroke:black; stroke-width:2; stroke-linecap: round;'/>
      <line x1='".($width_of_column - 1)."' y1='52.5' x2='".($width_of_column-10)."' y2='77' style='stroke:black; stroke-width:2; stroke-linecap: round;'/>";
}

function get_search_tool_color_classes($matches, $i, $matches_individual_notes, $match_count) {
    // If color given to syllables
    if ($matches != -1) {
      if ($matches[$i] == 10) {
        $cl = " s10";
      } elseif ($matches[$i] >= 5) {
        $cl = " s5-9";
      }
      elseif ($matches[$i] > 3) {
        $cl = " s3-5";
      }
      elseif ($matches[$i] <= 3) {
        $cl = " s0-3";
      }
    } 
    // If color given to notes
    elseif ($matches_individual_notes != -1) {
      $note_color_class = $matches_individual_notes[$match_count];

      if ($note_color_class == 1) {
        $cl = ' s10';
      } elseif ($note_color_class >= 0.5) {
        $cl = " s5-9";
      }
      elseif ($note_color_class > 0.3) {
        $cl = " s3-5";
      }
      elseif ($note_color_class <= 0.3) {
        $cl = " s0-3";
      }
      $match_count += 1;
  }
  return [$match_count, $cl];
}


function get_melodic_structure($n_of_lines, $qq_notes_ar, $lines_ar) {
  global $alphabet_melodic_structure;

  $apost = [];
  $SM = [0];
  $SMmax = 0;
  
  // Calculate Melodic structure
  $lines_ar_per_SM = str_replace(" ", "", $lines_ar);
  
  // Loop through lines
  for ($ln = 0; $ln < $n_of_lines; $ln++) {
  	$lns_with_apostr = [];
  	$apost_count = 0;
  	$melodic_scheme_set_values = array_key_exists($ln, $SM);

  	// If a value has not yet been assigned
  	if ($melodic_scheme_set_values != 1) {
  		// Ajourn the number to be assigned
  		$SMmax += 1; 
  		$SM[$ln] = $SMmax;
  	} // and assign it

  	// Loop through lines
  	for ($ln2 = $ln + 1; $ln2 < $n_of_lines; $ln2++) {
  		// If new compared line does not already have a letter start assignement
  		if (!array_key_exists($ln2, $SM)) {
  			// reset the "coefficient of symilarity"
  			$sym = 0; 
  			// Count syl in line 1
  			$nln = count($qq_notes_ar[$ln]); 
  			// Count syl in line 2
  			$nln2 = count($qq_notes_ar[$ln2]); 

  			// Compare the two lines for the longest comparable number of syl
  			if ($nln > $nln2){
  				$syl_n = $nln; 
  			} else {
  				$syl_n = $nln2;
  			}

        // Loop through syllables
        for ($syl = 0; $syl < $syl_n; $syl++) {

            // Check if the current syllables correspond
            if (isset($qq_notes_ar[$ln][$syl], $qq_notes_ar[$ln2][$syl]) &&
                $qq_notes_ar[$ln][$syl] == $qq_notes_ar[$ln2][$syl]) {
                $symil = 1;

            // Attempt to calculate shift. Not used.
            } elseif (isset($qq_notes_ar[$ln][$syl], $qq_notes_ar[$ln2][$syl+1], $qq_notes_ar[$ln][$syl+1], $qq_notes_ar[$ln2][$syl+2]) &&
                      $qq_notes_ar[$ln][$syl] == $qq_notes_ar[$ln2][$syl+1] &&
                      $qq_notes_ar[$ln][$syl+1] == $qq_notes_ar[$ln2][$syl+2]) {
                $symil = 0; // set to a value > 0 to count shift

            } else {
                $symil = 0; // or else to 0
            }

            // Keep count of how many syllables are equal
            $sym += $symil;
        }

  			// Assign 10% bonus if final note matches
  			if (substr($lines_ar_per_SM[$ln], -1) == substr($lines_ar_per_SM[$ln2], -1)) {
  				$end_bonus = 0.1;
  			}
  			else{
  				$end_bonus = 0;
  			}

  			// Get percentage of similarity
  			$sym_perc = ($sym + $end_bonus) / $syl_n;
  			
  			// If high percentage
  			if ($sym_perc > 0.8 and $end_bonus == 0.1) {
  				$apost[$ln2] = $apost[$ln];
  				// assign the same letter to line 2, and no apostrophe
  				$SM[$ln2] = $SM[$ln];
  			} 
  			// If medium percentage, assign same letter with apostrophe(s)
  			else if (($sym_perc > 0.8 and $end_bonus == 0)
  						or ($sym_perc > 0.65 and $syl_n < 4)
  						or ($sym_perc > 0.45 and $syl_n >= 4)) { 
  				$SM[$ln2] = $SM[$ln];
  	 			$apost[$ln2] = $apost[$ln] + $apost_count + 1;

  				for ($ln3 = $ln2; $ln3 < $n_of_lines; $ln3++) {
  				 	if ($lines_ar_per_SM[$ln2] == $lines_ar_per_SM[$ln3]) {
  				 		$SM[$ln3] = $SM[$ln2];
  				 		$apost[$ln3] = $apost[$ln2];
  			 		}
  			 	}

  				$apost_count = $apost_count + 1;
  				array_push($lns_with_apostr, $ln2);
  			}
  		}
  	}
  }

  // Change the numeric value to alphabetic
  $SM_latin = convert_melodic_structure_to_alphabet($SM, $apost, 1);
  $melodic_structure_to_store = implode($SM_latin);

  // Chose which alphabet to use
  if ($alphabet_melodic_structure == 0) {
    $SM_greek = convert_melodic_structure_to_alphabet($SM, $apost, 0);
    return [$SM_greek, $melodic_structure_to_store];
  }else{
    return [$SM_latin, $melodic_structure_to_store];
  }
}

function convert_melodic_structure_to_alphabet($SM, $apost, $alphabet_melodic_structure) {
  $alphaLatin = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
  $alphaGreek = array('α','β','γ','δ','ε','ζ','η','θ','ι','κ','λ','μ','ν','ξ','ο','π','ρ','σ','τ','υ','φ','χ','ψ','ω');
  
  if ($alphabet_melodic_structure == 0){
    $alpha = $alphaGreek;
  }else{
    $alpha = $alphaLatin;
  }

  // Metrical structure for storage (should always be in Latin alphabet)
  for ($i = 0; $i < count($SM); $i++) {
  	$x = $SM[$i];
  	if ($x < count($alpha)){
  		$thisSM = $alpha[$x]; // If the letters in the alphabet are not enough, start using AA, AB, AC ...
  	}else{
  		$var1 = floor($x / count($alpha));
  		$var2 = $x % count($alpha);
  		$var1 = $alpha[$var1-1];
  		$var2 = $alpha[$var2-1];
  		$thisSM = $var1.$var2;
  	}
  
  	$ap = "";
    if (count($apost) > 0) {
    	for ($a = 0; $a < $apost[$i]; $a++) {
    		$ap = $ap."'";
    	}
    }
  	$melodic_structure[] = $thisSM.$ap." "; // create an array with a value for each line
  }
  
  return $melodic_structure;
}

function convert_manual_melodic_structure_to_greek($manual_MS) {
    // Define Latin and Greek alphabets
    $alphaL = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
    $alphaG = array('α','β','γ','δ','ε','ζ','η','θ','ι','κ','λ','μ','ν','ξ','ο','π','ρ','σ','τ','υ','φ','χ','ψ','ω','Υ','Ζ');

    $SMalpha = []; // Output array
    $manual_MS = trim($manual_MS);
    $manual_MS_ar = explode(" ", $manual_MS); // Split input by spaces

    foreach ($manual_MS_ar as $this_man_el) {
        // Extract only letters
        $manual_MS_el_pos = array_search(preg_replace('/[^A-Z]/', '', strtoupper($this_man_el)), $alphaL);

        if ($manual_MS_el_pos !== false) {
            $greek_letter = $alphaG[$manual_MS_el_pos]; // Get Greek equivalent
        } else {
            $greek_letter = $this_man_el; // If no match, keep as is
        }

        // Preserve apostrophes and other characters
        $this_manual_apostrophes = preg_replace('/[A-Z]/i', '', $this_man_el);
        $SMalpha[] = $greek_letter . $this_manual_apostrophes;
    }

    return $SMalpha;
}

function print_octave_clef() {
  echo "<text class='eight hidden' x='20' y='123' style='font-size:17px !important; font-family:\"Times New Roman\"; font-style:italic; font-weight: bold'>8</text>";
}

function print_melodic_structure($melodic_structure_char, $textFont, $fontSize) {
  if ($fontSize > 30) {
    $fontSizeMS = 25;
  } else {
    $fontSizeMS = $fontSize * 0.8;
  }
  
  echo "<text class='melodicStructureText' x='0' y='20' style='font-family:$textFont; font-size: ".$fontSizeMS."pt'>".$melodic_structure_char."</text>";
}

function print_annotations($annotations) {
  global $textFont;
  global $fontSize;
  
  echo "<br/>\n";
  echo "<div style='bottom:10px; margin-top:30px;' class='annotationsContainer'>\n";
  echo "<br/>\n";
  echo "<br/>\n";
  echo "<div id='annotationsSection' style='font-family:$textFont; font-size:".$fontSize."px;";

  if ($_COOKIE['showAnnotations'] != 2 and $annotations != "") {
   	echo "display:block;";
  }else{
   	echo "display:none;";
  }
  echo "'>";

  $annotations = str_replace("\n", "<br/>", $annotations);
  echo graces(html_entity_decode($annotations))."<br/>";
  echo "</div>";
}


function get_font_family($font) {
  if ($font == 0) {
  	return "Times New Roman";
  } else {
  	if ($font == "1") {
  		return "Times New Roman";
  	} elseif ($font == "2") {
  		return "EB Garamond";
  	} elseif ($font == "3") {
  		return "Courier";
  	} elseif ($font == "4") {
  		return "Roboto";
  	}elseif ($font == "5"){
  		return "Junicode";
  	}
  }
}
function get_font_size($size){
  if ($size == 0) {
  	return 18;
  } else {
  	return $size;
  }
}

function print_slur($note_group, $Plica_group, $plica_type, $x, $n_of_notes_in_this_note_group){
  $notes_divided_hyphens = explode("-", $note_group);
  $n_of_groups_divided_hyphens = count($notes_divided_hyphens);
  $cumulate_subgroup_count = 0;

  for ($z = 0; $z < $n_of_groups_divided_hyphens; $z++) {
    $group = $notes_divided_hyphens[$z];
    $group_len = strlen($group);
    if ($group_len > 1) {
      // set variable for last note of the group
      if ($Plica_group[$x-1] == 1 and $plica_type == 1){
        $last_note_of_note_group = $group[$group_len-2];
      } else {
        $last_note_of_note_group = $group[$group_len-1];
      };

      // set variable for first note of the group
      $first_note_of_the_group = $group[0];

      // get pitch of first note for positioning beginning of slur (%My); set note weigth
      [$My, $first_note_height] = get_slur_info_for_first_note($first_note_of_the_group);

      $n = $n_of_notes_in_this_note_group;

      if ($Plica_group[$n-1] == 1 and $plica_type == 1){
        $group_len = $n-1;
      }
      // last note weight
      $last_note_height = get_last_note_height($last_note_of_note_group);

      // Max height within note group
      $note_to_weight = get_note_to_weigth($group);

      // Calculate the maximum height and position
      $max_height_in_note_group = max($note_to_weight);
      $max_height_in_note_group_position = array_search($max_height_in_note_group, $note_to_weight);
      $max_height_in_note_group_position_ratio = $max_height_in_note_group_position / $n_of_notes_in_this_note_group;
      
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

      echo "<path
      d='m ".$Mx." ".$My." q ".$q1." ".$q2." ".$Zx." ".$Zy." q ".(-$q1)." ".$q3." ".(-$Zx)." ".(-$Zy)."' stroke='black' stroke-width='0.5' fill='black'/>";
      

      // End slur
    } // end if group > 1
    
    $cumulate_subgroup_count = $cumulate_subgroup_count + $group_len + 1;
  } //end slur loop
}


// function calculateRepetitionSigns($sequence) {
//     // Step 1: Map music sections to music line numbers
//     $uniqueSections = array_unique(str_split($sequence));
//     $sectionToLine = [];
//     $lineNumber = 1;
//     foreach ($uniqueSections as $section) {
//         $sectionToLine[$section] = $lineNumber;
//         $lineNumber++;
//     }
// 
//     // Step 2: Build the sequence of music line numbers
//     $sequenceLines = [];
//     $sequenceSections = str_split($sequence);
//     foreach ($sequenceSections as $section) {
//         $sequenceLines[] = $sectionToLine[$section];
//     }
// 
//     // Step 3: Initialize the result array
//     $repetitionSigns = [];
//     foreach ($sectionToLine as $line) {
//         $repetitionSigns[$line] = ['start' => 0, 'end' => 0];
//     }
// 
//     // Step 4: Detect repeating patterns
//     $n = count($sequenceLines);
//     $patternFound = false;
// 
//     // Try patterns of different lengths
//     for ($patternLength = 1; $patternLength <= $n / 2; $patternLength++) {
//         if ($n % $patternLength != 0) {
//             continue; // The pattern must divide the sequence length evenly
//         }
// 
//         $pattern = array_slice($sequenceLines, 0, $patternLength);
//         $isRepeating = true;
// 
//         // Check if the entire sequence is made up of the pattern
//         for ($i = 0; $i < $n; $i++) {
//             if ($sequenceLines[$i] != $pattern[$i % $patternLength]) {
//                 $isRepeating = false;
//                 break;
//             }
//         }
// 
//         if ($isRepeating) {
//             // Repeating pattern found
//             $firstMusicLine = $pattern[0];
//             $lastMusicLine = $pattern[$patternLength - 1];
//             $repetitionSigns[$firstMusicLine]['start'] = 1;
//             $repetitionSigns[$lastMusicLine]['end'] = 1;
//             $patternFound = true;
//             break;
//         }
//     }
// 
//     // If no repeating pattern was found, check for immediate repetitions
//     if (!$patternFound) {
//         $i = 0;
//         while ($i < $n) {
//             $currentLine = $sequenceLines[$i];
//             $j = $i + 1;
// 
//             while ($j < $n && $sequenceLines[$j] == $currentLine) {
//                 $j++;
//             }
// 
//             if ($j - $i > 1) {
//                 // Immediate repetition detected
//                 $repetitionSigns[$currentLine]['start'] = 1;
//                 $repetitionSigns[$currentLine]['end'] = 1;
//             }
//             $i = $j;
//         }
//     }
// 
//     return $repetitionSigns;
// }

// SEEMS TO BE WORKING BETTER than the one commented above, whic couldn't handle cases such as "ABCDEABCDEGFGF"
function calculateRepetitionSigns($sequence) {
    // Step 1: Map music sections to music line numbers
    $uniqueSections = array_unique(str_split($sequence));
    $sectionToLine = [];
    $lineNumber = 1;
    foreach ($uniqueSections as $section) {
        $sectionToLine[$section] = $lineNumber++;
    }

    // Step 2: Convert sequence to music line numbers
    $sequenceLines = array_map(fn($s) => $sectionToLine[$s], str_split($sequence));

    // Step 3: Initialize the repetition signs array
    $repetitionSigns = [];
    foreach ($sectionToLine as $line) {
        $repetitionSigns[$line] = ['start' => 0, 'end' => 0];
    }

    $n = count($sequenceLines);

    // Step 4: Detect repeating patterns
    for ($patternLength = 1; $patternLength <= $n / 2; $patternLength++) {
        for ($start = 0; $start <= $n - 2 * $patternLength; $start++) {
            $pattern = array_slice($sequenceLines, $start, $patternLength);
            $isRepeating = true;

            // Check if the next block is identical
            for ($i = 0; $i < $patternLength; $i++) {
                if ($sequenceLines[$start + $patternLength + $i] !== $pattern[$i]) {
                    $isRepeating = false;
                    break;
                }
            }

            if ($isRepeating) {
                $firstMusicLine = $pattern[0];
                $lastMusicLine = end($pattern);
                $repetitionSigns[$firstMusicLine]['start'] = 1;
                $repetitionSigns[$lastMusicLine]['end'] = 1;
            }
        }
    }

    // Step 5: Detect immediate repetitions
    for ($i = 0; $i < $n - 1; $i++) {
        if ($sequenceLines[$i] === $sequenceLines[$i + 1]) {
            $repetitionSigns[$sequenceLines[$i]]['start'] = 1;
            $repetitionSigns[$sequenceLines[$i]]['end'] = 1;
        }
    }

    return $repetitionSigns;
}