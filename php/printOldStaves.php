<?php
  ini_set('display_errors', 1);
  // Turn off all error reporting
  error_reporting(1);

  // import external php files
  include 'functions_shared.php';
  include 'functions_medieval_shared.php';
  include 'paths.php';
  include 'square_notation_paths.php';

  $notes = $text_string = $title = $id = $author = $language = $ms = "";

  $notes = test_input($_POST["notes"]);

  $text_string = test_input($_POST["text_string"]);
  $title = test_input($_POST["title"]);
  $id = test_input($_POST["id"]);
  $author = test_input($_POST["author"]);
  //$language = test_input($_POST["language"]); 
  $ms = test_input($_POST["ms"]);
  $ms_page = test_input($_POST["f_input"]);
  $annotations = test_input($_POST["annotations"]);
  $bar = json_decode(test_input($_POST["bar"]));
  $custos = json_decode(test_input($_POST["custos"]));
  $repetition_sequence = $_POST["repetitionPattern"];
  $repetition_pattern = fromSequenceToRepetitionPattern($repetition_sequence);
  $repetition_shift = 0;
  $groupSequence = 0;
  $pos = 0;
  
  $n_of_lines_in_staff_array = json_decode(test_input($_POST["linesInLine"]));

  $shapes_group = json_decode(test_input($_POST["shapeGroupNote"]));

  $note_shape = json_decode(test_input($_POST["shapeSingleNote"]));

  $stem_direction = json_decode(test_input($_POST["stemSingleNote"]));

  $connecting_line_param = json_decode(test_input($_POST["connectGroupNote"]));

  //--------------PARAMETERS-----------------
  // pes param
  $pes_post = test_input($_POST['pes'] ?? 1);

  // clivis param
  $clivis_post = test_input($_POST['clivis'] ?? 1);

  // climacus param
  $climacus_post = test_input($_POST['climacus'] ?? 1);;

  // porrectus param
  $porrectus_post = test_input($_POST['porrectus'] ?? 1);;

  // plica param
  $plica_post = test_input($_POST['plica'] ?? 1);;

  // scandicus param
  $scandicus_post = test_input($_POST['scandicus'] ?? 1);;
  //-------------------END PARAMETERS------------

  // Checkbox change layout
  if (empty($_POST["low"])) {
  } else {
    $low = test_input($_POST["low"]);
  }

  // Checkbox chop syllables
  if (empty($_POST["chop_syl"])) {
  } else {
    $chop_syl = test_input($_POST["chop_syl"]);
  } // end checkbox

  // Keeps checkbox checked
  if (isset($_POST['checkbox'])){
    foreach ($_POST['checkbox'] as $selectedcheckbox)
     $selected[$selectedcheckbox] = "checked";
   }

  if (empty($_POST["melodic_structure"])) {
  } else {
    $low = test_input($_POST["melodic_structure"]);
  } // End checkbox


  // FONT
  $textFontOld = get_font_family($_COOKIE['textFontOld']);
  $fontSizeOld = get_font_size($_COOKIE['fontSizeOld']);

  $stavesDistance = -20; // can be used as setting

  $notes_bar_separator = clean_note_input($notes);

  $notes_for_plica         = str_replace(array(     ")", "%", "#", "_",      "/", "[", "]", "<", ">", "^", "°", "b}", "h}", "B}", "1 ", "2 ", "3 ", "4 ", "5 ", "6 ", "7 ", "8 ", "9 ", "0 ", "1", "2","3","4","5","6","7","8","9", "0"),"", $notes_bar_separator);
  $notes_for_natural       = str_replace(array("(", ")",      "#", "_", "-", "/", "[", "]", "<", ">", "^", "°"),"", $notes_bar_separator);
  $notes_for_sharp         = str_replace(array("(", ")", "%",      "_",      "/", "[", "]", "<", ">", "^","°"),"", $notes_bar_separator);
  $notes_for_flat          = str_replace(array("(", ")", "%", "#",      "-", "/", "[", "]", "<", ">", "^", "°"),"", $notes_bar_separator);
  $notes_for_mid_bar       = str_replace(array("(", ")", "%", "#", "_", "-",      "[", "]", "<", ">", "^", "°", "b}", "h}", "B}", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0"), "", $notes_bar_separator);
  $notes_in_Brackets = str_replace(array("(", ")", "%", "#", "_", "-", "/",      "<", ">", "^", "°", "b}", "h}", "B}", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0"), "", $notes_bar_separator);
  $notes_in_angle_brack = str_replace(array("(", ")", "%", "#", "_", "-", "/", "[", "]", "<", ">", "b}", "h}", "B}", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0"), "", $notes_bar_separator);
  $notes_clean             = str_replace(array("(", ")", "%", "#", "_",      "/", "[", "]", "<", ">", "^", "°"),"", $notes_bar_separator);

  // create array to check b in key
  $notes_for_b_in_key_lines[] = explode("|", $notes_for_b_in_key);

  //-- Calculate position of plicas
  // Change notes preceded by parentesis to value "1"
  $Plica_string = str_replace(array('(a', '(b', '(c', '(d', '(e', '(f', '(g', '(h', '(J', '(A', '(B', '(C', '(D', '(E', '(F', '(G', '(H', '(u', '(p', '(q', '(r'), "1", $notes_for_plica);
  // Change all other notes to value "0"
  $Plica_string = preg_replace("[a|b|c|d|e|f|g|h|u|p|q|r|s|J|A|B|C|D|E|F|G|H]", "0", $Plica_string);
  $Plica_notes_groups_ar = format_string($Plica_string);
  
  //-- Calculate position of natural alteration
  // Change notes preceded by "%"" to value "1"
  $Natural_string = str_replace(array('%a', '%b', '%c', '%d', '%e', '%f', '%g', '%h', '%J', '%A', '%B', '%C', '%D', '%E', '%F', '%G', '%H', '%u', '%p', '%q', '%r'), "%", $notes_for_natural);
  // Change all other notes to value "0"
  $Natural_string = preg_replace("[a|b|c|d|e|f|g|h|u|p|q|r|s|J|A|B|C|D|E|F|G|H]", "0", $Natural_string);
  $Natural_string = preg_replace("/[0-9]/", "0", $Natural_string);
  $Natural_string = str_replace("%", "1", $Natural_string);

  $Natural_notes_groups_ar = format_string($Natural_string);

  //-- Calculate position of sharp alteration
  // Change notes preceded by "#"" to value "1"
  $Sharp_string = str_replace(array('#a', '#b', '#c', '#d', '#e', '#f', '#g', '#h', '#J', '#A', '#B', '#C', '#D', '#E', '#F', '#G', '#H', '#u', '#p', "#q", "#r", "#s"), "%", $notes_for_sharp);
  // Change all other notes to value "0"
  $Sharp_string = preg_replace("[a|b|c|d|e|f|g|h|u|p|q|r|J|A|B|C|D|E|F|G|H|1|2|3|4|5|6|7|8|9|0]", "0", $Sharp_string);
  $Sharp_string = str_replace("%", "1", $Sharp_string);
  $Sharp_notes_groups_ar = format_string($Sharp_string);

  //-- Calculate position of flat alteration
  // Change notes preceded by "_"" to value "1"
  $Flat_string = str_replace(array( '_a', '_b', '_c', '_d', '_e', '_f', '_g', '_J', '_A', '_B', '_C', '_D', '_E', '_F', '_G', '_H', '_u', '_p', "_q", "_r", "_s"), "%", $notes_for_flat);
  
  // Change all other notes to value "0"
  $Flat_string = preg_replace("[a|b|c|d|e|f|g|h|u|p|q|r|s|J|A|B|C|D|E|F|G|H|1|2|3|4|5|6|7|8|9]", "0", $Flat_string);
  $Flat_string = str_replace("%", "1", $Flat_string);
  $Flat_notes_groups_ar = format_string($Flat_string);
  
  //-- Calculate position of Middle bars
  $Mid_bar_notes_groups_ar = format_string($notes_for_mid_bar);
  
  //-- Get postiton of notes_in_Brackets
  // Replace patterns starting with "[" and ending with "]" with "2"
  $Brackets_string = preg_replace('/\[[a-zA-ZuJpqrs]\]/', "§", $notes_in_Brackets);

  // Replace patterns starting with "[" (but not ending with "]" {redundant}) with "1"
  $Brackets_string = preg_replace('/\[[a-zA-ZuJpqrs](?!\])/', "^", $Brackets_string);

  // Replace patterns ending with "]" (but not starting with "[" {redundant}) with "3"
  $Brackets_string = preg_replace('/(?<!\[)[a-zA-ZuJpqrs]\]/', "°", $Brackets_string);

  // Change all other notes to value "0"
  $Brackets_string = preg_replace("/[^\^§°| ]/", 0, $Brackets_string);
  // Assign values, now that all clefs and symbols are cleared
  $Brackets_string = str_replace('^', 1 ,$Brackets_string);
  $Brackets_string = str_replace('§', 2 ,$Brackets_string);
  $Brackets_string = str_replace('°', 3 ,$Brackets_string);
  // Format string into array
  $Square_brackets = format_string($Brackets_string);
  // Get postiton of Angle_brackets_string
  // Change notes preceded by parentesis to value "1"
  $Angle_brackets_string = preg_replace("/\^[a-hA-HJupqrs]°/", "§", $notes_in_angle_brack);
  $Angle_brackets_string = preg_replace("/\^[a-hA-HJupqrs]/", "^", $Angle_brackets_string);
  $Angle_brackets_string = preg_replace("/[a-h|A-H|J|u|p|q|r|s]°/", "°", $Angle_brackets_string);
  // Change all other notes to value "0"
  $Angle_brackets_string = preg_replace("/[^\^§°| ]/", 0, $Angle_brackets_string);
  $Angle_brackets_string = str_replace('^', 1 ,$Angle_brackets_string);
  $Angle_brackets_string = str_replace('§', 2 ,$Angle_brackets_string);
  $Angle_brackets_string = str_replace('°', 3 ,$Angle_brackets_string);

  $Angle_brackets = format_string($Angle_brackets_string);

  // Get melodic lines
  $lines_ar = explode("|", $notes_clean);

  // Count melodic lines
  $n_of_lines = count($lines_ar);

  // Clean text
  $text_string = str_replace("- ","-", $text_string);
  $text_string_clean = str_replace("-","- ", $text_string);
  $text_string_clean = str_replace("  "," ", $text_string_clean);
  
  // Get number of syllable of the longest musical line
  // for each line
  for ($q = 0; $q < $n_of_lines; $q++) {

    //clean line array (necessary!)
    $q_this_line_ar = rtrim($lines_ar[$q], " \|");
    $q_this_line_ar = trim($q_this_line_ar,"\|");
    $q_this_line_ar = ltrim($q_this_line_ar," ");

    // Create an array of all note groups in the musical line
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

    // Create an array of all note grups in the line
    $qq_notes_ar[] = explode(' ', $qq_this_line_ar);
  }
  
  for ($yy = 0; $yy < $longest_number_of_syl_in_line; $yy++){
    for ($yyy = 0; $yyy < $n_of_lines; $yyy++) {
       $column[] = strlen($qq_notes_ar[$yyy][$yy]); // to get the note names (and not the number of notes) omit strlen()
     }
  }

  $column_subar = array_chunk($column, $n_of_lines);

  for ($yyyy = 0; $yyyy < $longest_number_of_syl_in_line; $yyyy++) {
    $max_n_of_notes_in_column[] = max($column_subar[$yyyy]);
  }
  

$width_independent_from_columns = 1;

if ($width_independent_from_columns){
  // Caluculate independent div independent from column

  // Get text
  // [[3,4,5,3]] structure is [ (line) [sylTextCount,]]
  $count_text_syl_in_succession = get_array_of_text_syl_corresponding_to_music($qq_notes_ar, $text_string_clean);
  // Pair text (char count) and music
  $pair_notes_and_text = get_pair_notes_and_text($notes_bar_separator, $count_text_syl_in_succession);
  [$syl_widths, $line_width] = get_width_of_syls($pair_notes_and_text); // dimension [line[syl_x, syl_width]]
} else {
  // Calculate aligned columns width
  $max_number_of_letters_per_syl_in_column = get_array_of_text_syl_per_colums($column_subar, $qq_notes_ar, $text_string_clean);

  $array_of_extra_text_space = get_array_of_extra_text_space($max_n_of_notes_in_column, $max_number_of_letters_per_syl_in_column);

  [$x_columns, $line_width] = get_x_columns_and_maxline_width($max_n_of_notes_in_column, $array_of_extra_text_space);
}


// Print main content div
echo "<div id='main_container'";
if(!isset($low)) {echo " class='flex_container'";}
echo ">    <div id='music'";
if(!isset($low)) {echo " class='large_side'";}
echo ">";

echo "<div id='music_page'";
if(!isset($low)) {echo "class='min-height800'";}
echo ">";

if ($title){
  echo "<br/>";
  echo "<div id='title' style='font-family:$textFontOld'>";
  echo $title;
  echo "</div>";
}

if ($author){
  echo "<div id='author' style='font-family:$textFontOld'>";
  echo $author;
  echo "</div>";
}

if ($id){
  echo "<div id='id' style='font-family:$textFontOld'>";
  echo $id;
  echo "</div>";
}

if ($ms) {
  echo "<div id='ms' style='font-family:$textFontOld'>Ms: ";
  echo $ms;
  if ($ms_page) {
    echo " ".$ms_page;
  }
  echo "</div>";
}

// ************************
// Start printing the music
// ************************

echo "<div id='staves' style='margin-top:30px'>";

// for each musical line
for ($line = 0; $line < $n_of_lines; $line++){

  $b_in_key = 0;
  
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
  
  // Define height of svg line container based on lines, and position of lowest notes
  $lowest_note = check_if_notes_lower_than_staves($notes_ar, $n_of_lines_in_staff, $pos);
  
  $staff_height = 90 + $n_of_lines_in_staff * 8;
  
  if ($staff_height - 20 < $lowest_note){
    $staff_height +=  $lowest_note - ($staff_height-20);
  }
  
  // Text height must always be at the bottom of the svg (= to container's height)
  $text_height = $staff_height-5;
  
  //reset count of keys
  $n_of_keys_in_line = 0;
  
  // reset group count
  $index_group = 0;
  
  // Get number of lines 
  $n_linesCurrent = 4;
  if ($n_of_lines_in_staff_array[$line] != null) $n_linesCurrent = $n_of_lines_in_staff_array[$line];
  
  echo "<svg class='stave-line' height='".$staff_height."' width='$line_width' onclick='openLineOptionsMenu($line)' oncontextmenu='showLineNumber(this, $line, event)' id='line_$line' data-lines='".$n_linesCurrent."' data-line='".$line."'>";
  
  print_n_gram($n_of_lines_in_staff, $staff_height, $line);
  
  // Opening syllable groups container
  echo "<g class='groups'>";
  
  // Loop through syllables
  for ($i = 0; $i < $n_of_note_groups; $i++) {
    $note_y_ar = [];
    $coeff_y = 0;
    $coeff_x = 0;
    $tot_of_i += 1;
    $b_in_syl = $b_in_key;

    // Keep count of keys in line so far
    if (is_numeric($qq_notes_ar[$line][$i])){
      $n_of_keys_in_line += 1;
      $n_of_keys_total += 1;
      $n_of_keys_total_for_selection += strlen($qq_notes_ar[$line][$i]);
    }

    // Count notes in note group
    $n_of_notes_in_note_group[$i] = strlen($notes_ar[$i]);
    
    $function = "";

    // Skip if group is empty or contain key
    $groupNoteSequence = 0;
    $stripped_qq_notes = str_replace("[", "", $qq_notes_ar[$line][$i]);
    $stripped_qq_notes = str_replace("]", "", $stripped_qq_notes);
    
    if ($n_of_notes_in_note_group[$i] > 0  && !is_numeric($stripped_qq_notes)) {
      $function = "onclick='extractGroupFromClick($groupSequence, $line)' ";   
      $groupNoteSequence = $groupSequence;
      $groupSequence = $groupSequence + 1;
    }
    
    // Get syllable width
    if ($width_independent_from_columns){
      $syl_x = $syl_widths[$line][$i][0] + $start_x;
      $syl_width = $syl_widths[$line][$i][1] + $start_x;
    }else{
      $syl_x = $x_columns[$i]; 
      $syl_width = get_width_column($x_columns, $i, $line_width); // which is = width of colum
    }
    
    echo "<g class='groupNote'";
    echo " id='group_".$line."_$groupSequence'"; // this created id duplicates. I don't think it was ever used but let's not delete it just yet
    echo " transform='translate($syl_x, 0)'>";
    
    echo "<rect stroke='1' ";
    // echo "style='stroke-width:3;stroke:red'"; 
    echo " class='groupClickableRect' x='0' y='0' width='$syl_width' height='$staff_height' $function ></rect>";
    
    // Check OPEN square brackets (first element)
    $sq_b = $Square_brackets[$line][$i][0];
    if ($sq_b == 1 || $sq_b == 2) {
      print_open_square_bracket(0, $coeff_x);
      $coeff_x += 10;
    }
    
    // Check OPEN Angle brackets (first element)
    $Angle_br = $Angle_brackets[$line][$i][0];
    if ($Angle_br == 1 or $Angle_br == 2) {
      print_open_angle_brackets(0, $coeff_x);
      $coeff_x += 15;
    }
  
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
      $punctum_path = $original_punctum_path; // reassing original path in case it's been changed with short path
      $noteCount1++;
      $noteCount = $noteCount1 - $n_of_keys_total_for_selection; /** needed for selected note */
      $note_group_ar = str_split($notes_ar[$i], 1);

      // If brackets are in the first position they have been printed in the pre print $x loop
      if ($x > 0) {
        // Check OPEN square brackets
        $sq_b = $Square_brackets[$line][$i][$x];
        if ($sq_b == 1 || $sq_b == 2) {
          print_open_square_bracket($x, $coeff_x);
          $coeff_x += 10;
        }
        
        // Check OPEN Angle brackets
        $Angle_br = $Angle_brackets[$line][$i][$x];
        if ($Angle_br == 1 or $Angle_br == 2) {
          print_open_angle_brackets($x, $coeff_x);
          $coeff_x += 15;
        }
      }
      
      /*****
      * CLEF
      ******/

      if (is_numeric(implode($note_group_ar))){
        if ($x == 0){
          print_clef($note_group_ar);
        }
      } elseif ($note_group_ar[$x] == "-") {
        $connecting_line_param[$line][$index_group][$x+1] = 0;

      } elseif ($note_group_ar[$x] !== "?" and $note_group_ar[$x] !== "-") {
      
      /*****
      * Calculate note height
      ******/
      $note_y = get_note_y($note_group_ar[$x]) + $pos;
      $note_y_ar[$x] = $note_y;

      /*****
       * Preprocess: get the type of note group
       ****/

      // numeric value of first 3 notes
      $nn0 = note_number($note_group_ar[0]);
      $nn1 = note_number($note_group_ar[1]);
      $nn2 = note_number($note_group_ar[2]);
      $this_nn = note_number($note_group_ar[$x]);
      $next_nn = note_number($note_group_ar[$x+1]);
      $previous_nn = note_number($note_group_ar[$x-1]);

      $diff_this_next = $this_nn-$next_nn;
      $diff_previous_this = $previous_nn-$this_nn;

      // repeated pitch?
      if ($note_group_ar[$x] == $note_group_ar[$x-1]) {
          $coeff_x += 3;
      }

      // Plica?  -- the array of plicas doesn't include keys, but $i does so we have to subtract the number of precendent keys in the.
      $plica = $Plica_notes_groups_ar[$line][$i-$n_of_keys_in_line][$x];
      // Is next note a plica?
      $plica_next = $Plica_notes_groups_ar[$line][$i-$n_of_keys_in_line][$x+1];
      // Makeshift way to get aa(G) 
      if ($Plica_notes_groups_ar[$line][$i-$n_of_keys_in_line][$x+2] == 1 &&
        $aaG_liquescent) {
        $plica_next = 1;
        $plica_disc = 1;
      }
      
      if ($plica_next == 1){
        $plica_diff = $this_nn - $next_nn;

        if ($this_nn > $next_nn){
          $plica_asc = 0;
          $plica_disc = 1;
        } else {
          $plica_asc = 1;
          $plica_disc = 0;
         }

      } else {
         $plica_asc = 0;
         $plica_disc = 0;
       }

       // 2 notes
      if ($n_of_notes_in_note_group[$i] == 2) {
        if ($nn0 < $nn1){ // pes
          $pes = 1;
          $clivis = 0;
        }elseif ($nn0 > $nn1){ // clivis
          $clivis = 1;
          $pes = 0;
        }else{ // none of the above
          $pes = 0;
          $clivis = 0;
        }

      } else { // not 2 notes
        $pes = 0;
        $clivis = 0;
      }

      // 3 notes
       //climacus, scandicus or porrectus?
      if ($n_of_notes_in_note_group[$i] == 3 and $nn0 != "" and $nn1 != "" and $nn2 != ""){
        
        $is_last_note_plica = $Plica_notes_groups_ar[$line][$i-$n_of_keys_in_line][2] == 1;
        
        if ($nn0 > $nn1 and $nn1 > $nn2 and !$is_last_note_plica){
          $climacus = 1;
          $scandicus = 0;
          $porrectus = 0;
          $aaG_liquescent = 0;
          $aah_liquescent = 0;
        } elseif ($nn0 < $nn1 and $nn1 < $nn2){
          $climacus = 0;
          $scandicus = 1;
          $porrectus = 0;
          $aaG_liquescent = 0;
          $aah_liquescent = 0;
        } elseif ($nn0 > $nn1 and $nn1 < $nn2 and !$is_last_note_plica) {
          $porrectus = 1;
          $scandicus = 0;
          $climacus = 0;
          $aaG_liquescent = 0;
          $aah_liquescent = 0;
        } elseif ($nn0 == $nn1 and $nn1 > $nn2 and $is_last_note_plica) {
          $climacus = 0;
          $scandicus = 0;
          $porrectus = 0;
          $aaG_liquescent = 1;
          $aah_liquescent = 0;
        } elseif ($nn0 == $nn1 and $nn1 < $nn2 and $is_last_note_plica) {
          $climacus = 0;
          $scandicus = 0;
          $porrectus = 0;
          $aaG_liquescent = 0;
          $aah_liquescent = 1;
        } else{
          $climacus = 0;
          $porrectus = 0;
          $scandicus = 0;
          $aaG_liquescent = 0;
          $aah_liquescent = 0;
        }

      } else {
        $climacus = 0;
        $porrectus = 0;
        $scandicus = 0;
        $aaG_liquescent = 0;
        $aah_liquescent = 0;
      }

      //-----Parameters-----------
      //pes param
      $pes_type = intval($pes_post);

      //clivis param
      $united_clivis = $clivis_post;

      // climacus param
      $climacus_type = $climacus_post;

      // porrectus param
      $porrectus_type = $porrectus_post;

      // plica param
      $plica_type = $plica_post;

      // scandicus param
      $scandicus_type = $scandicus_post;
      
      // aa(G) aa(h)  
      $aaG_liquescent_type = 0;
      $aah_liquescent_type = 0;

      // reset stems
      $stem_bottom_left = 0;
      $stem_bottom_right = 0;
      $stem_top_right = 0;
      $stem_top_left = 0;

      // single note param
      $plica_type_next = $note_shape[$line][$index_group][$x];

      // plica type note level
      if     ($plica_type_next == 5)  {$plica_type = 1;}
      elseif ($plica_type_next == 6)  {$plica_type = 2;}
      elseif ($plica_type_next == 7)  {$plica_type = 3;}
      elseif ($plica_type_next == 8)  {$plica_type = 4;}
      elseif ($plica_type_next == 9)  {$plica_type = 5;}
      elseif ($plica_type_next == 10) {$plica_type = 6;}

      if ($stem_direction[$line][$index_group][$x] == 0){
        $stem_bottom_left = 0;
      }

      if ($stem_direction[$line][$index_group][$x] == 1){
        $stem_bottom_left = 1;
      }

      if ($stem_direction[$line][$index_group][$x] == 2){
        $stem_bottom_right = 1;
      }

      if ($stem_direction[$line][$index_group][$x] == 3){
        $stem_top_right = 1;
        $plica_type = 1;
      }

      if ($stem_direction[$line][$index_group][$x] == 4){
        $stem_top_left = 1;
      }

      // connecting line param
      if (is_null($connecting_line_param[$line][$index_group][$x]) or $connecting_line_param[$line][$index_group][$x] == 1 and !$prevent_connecting_line){
        $connecting_line = 1;
      } else {
        $connecting_line = 0;
      }

      // note group param
      if($shapes_group[$line][$index_group] == 1){
        $pes_type = 1;
        $porrectus_type = 1;
        $scandicus_type = 1;
        $plica_type = 1;
        $climacus_type = 1;
        $united_clivis = 1;
        $aaG_liquescent_type = 1;
        $aah_liquescent_type = 1;
      }

      if ($shapes_group[$line][$index_group] == 2){
        $pes_type = 2;
        $climacus_type = 2;
        $porrectus_type = 2;
        $scandicus_type = 2;
        $plica_type = 2;
        $united_clivis = 2;
      }

      if ($shapes_group[$line][$index_group] == 3) {
        $pes_type = 3;
        $climacus_type =3;
        $scandicus_type = 3;
        $plica_type = 3;
        $united_clivis = 0;
        $porrectus_type = 3;
      }

      if ($shapes_group[$line][$index_group] == 4) {
        $climacus_type = 4;
        $plica_type = 4;
      }

      if ($shapes_group[$line][$index_group] == 5) {
        $climacus_type = 5;
        $plica_type = 5;
      }

      // default values for stem in specific groups : united_clivis;[should be all clivis] porrectus
      // this is overridden by shape parameters
      if ((($clivis == 1 and $x == 0 and $plica_next != 1)
      or ($porrectus == 1 and $x == 0)
      or ($plica_next == 1 and $plica_disc == 1 and $plica_type != 4)
      or ($climacus == 1 and $x == 0 and $climacus_type == 2))
      and is_null($stem_direction[$line][$index_group][$x])) {

          $stem_bottom_left = 1;
      }

      if ((($climacus == 1 and $x == 0 and $climacus_type == 1)
      or ($plica_type == 1 and $plica_next == 1 and $plica_disc == 1))
      and is_null($stem_direction[$line][$index_group][$x])){

          $stem_bottom_right = 1;
      }

      if (($plica_type != 4 and $plica_type != 5
      and $plica_type != 6
      and $plica_next == 1 and $plica_asc == 1)
      and is_null($stem_direction[$line][$index_group][$x])){
        
          $stem_top_left = 1;
      }

      // get shape parameters from external file
      $single_note_shape = $note_shape[$line][$index_group][$x];
      $single_note_shape_next = $note_shape[$line][$index_group][$x+1];
      $note_shape_previous = $note_shape[$line][$index_group][$x-1];
      $note_shape_minus2 = $note_shape[$line][$index_group][$x-2];

      if (($climacus == 1 and $climacus_type == 1 and ($x == 1 or $x == 2))
      or ($climacus == 1 and $climacus_type == 5)
      or ($single_note_shape == 3 || $single_note_shape == 11)) {
        // if second and third note of climacus w/ inclinata
        $note_size_1 = 1;
        $note_size_2 = 1;
        // Make space for the first inclinatum of the climatus 
        if (($climacus == 1 and $x == 1)
        // Or any inclinatum
        or ($x > 0 and $single_note_shape == 3 and $note_shape_previous != 3)) {
          $coeff_x += 1;
        }
        $note_x = 7 * $x + $coeff_x;
        // Lower a bit of 
        $coeff_y = 5;

      } else { // normal note

        $note_size_1 = $note_size_2 = 1;
        
        // x of note: establish position of note within note group
        
        // pes (second note of type 1)
        // scadicus (second note of type 1)
        // ligatures (ascending note configured as pes)
        if (($pes == 1 and $x == 1 and $pes_type !== 2)
        or ($scandicus == 1 and $x == 1 and $scandicus_type == 1)
        or ($diff_previous_this < 0 and $note_shape_previous == 1)) {

          // Scandicus, second note followed by an ascending plica
          if ($scandicus == 1 and $x == 1 and $plica_next == 1 and $plica_asc == 1){
            $stem_top_left = 0;
            $note_x = 7 + $coeff_x; //? if this needs an adustment, maybe it should not be here
          }

          // if contiguous interval, slightly elevate the note 
          if ($diff_previous_this == -1){
            $coeff_y = -2.5;
          }else{
            $coeff_y = 0;
          }

          // Last note of resupinus
          if ($note_group_ar[$x] == $note_group_ar[$x-2]) {
            $punctum_path = $short_punctum_path;
          }
          
          // note after longa needs to be adjusted horizontally ($coeff_x does not work)
          if ($note_shape_previous == 4){
            $note_x =+ 8;
          }
        } elseif ($scandicus == 1 and $x == 2 and ($scandicus_type == 1 or $scandicus_type == 2)){
          $note_x = 7 + $coeff_x;
          if ($scandicus_type == 2 and $diff_previous_this == -1){
            $coeff_y = -2.5;
          }

        } elseif ($porrectus == 1 and $x == 2 and $diff_previous_this == -1 and $porrectus_type == 1) {
          $note_x = 7 * $x + $coeff_x;
          $coeff_y = -2.5;

        } elseif ($note_shape_minus2 == 1){
          $coeff_x += - 4;
          $note_x = 7 * $x + $coeff_x;
          $coeff_y = 0;

        } elseif ($plica_type == 6 and $note_shape_previous == 3){
          $coeff_x += - 4;
          $note_x = 7 * $x + $coeff_x;
          $coeff_y = 0;

        } elseif ($note_shape_previous == 4){
          $note_x = 7 * $x + $coeff_x;

        }else{
          $note_x = 7 * $x + $coeff_x;
          $coeff_y = 0;
        }
      }


      /*****
      * Start printing note
      ******/

      // LEFT STEMS
      if ($stem_bottom_left == 1) {
        // Tilted stem for inclinatum
        if ($single_note_shape == 3 || ($climacus == 1 and $climacus_type == 5)) {
          $x1 = 6 + 7 * $x + $coeff_x;
          $x2 = -6.5 + 7 * $x + $coeff_x;
          $y1 = $note_y + 7;
          $y2 = $note_y + 21;
        }else{
          // Regular stem
          $x1 = $x2 = 0.5 + 7 * $x + $coeff_x;
          $y1 = $note_y + 8;
          $y2 = $note_y + 28;
        }
        print_line($x1, $x2, $y1, $y2);
      }

     if ($stem_top_left == 1) {
        $x1 = $x2 = 0.5 + 7 * $x + $coeff_x;
        $y1 = $note_y - 6;
        $y2 = $note_y + 13;
        print_line($x1, $x2, $y1, $y2);
      }

      // OBLIQUE for 1) CLIVIS (united); 2) Climacus 3 and 4 3) notes that are manually told to (single_note_shape = 2)
      if (($clivis == 1 and $united_clivis == 1 and $x ==1)
        or ($climacus == 1 and $climacus_type == 3 and $x ==1)
        or ($climacus == 1 and $climacus_type == 4 and $x == 2)
        or ($note_shape_previous == 2 and $diff_previous_this > 0)) {} // erase a note of group which will be sobstituted by oblique

      elseif (((($clivis == 1 and $united_clivis == 1 and $plica_next != 1)
        or ($climacus == 1 and $climacus_type == 3)) and $x == 0)
        or ($climacus == 1 and $climacus_type == 4 and $x == 1)
        or ($single_note_shape == 2 and $diff_this_next > 0)) {
          
          $note_flag_0 = flag_note2number($note_group_ar[$x]);
          $note_flag_1 = flag_note2number($note_group_ar[$x+1]);
        
          $points=[
            (0  + 7 * $x + $coeff_x), (-30 + ($note_flag_0 * 7) + $pos),
            (22 + 7 * $x + $coeff_x), (-21 + (($note_flag_0 + $diff_this_next-1) * 7) + $pos),
            (22 + 7 * $x + $coeff_x), (-13 + (($note_flag_0 + $diff_this_next-1) * 7) + $pos),
            (0  + 7 * $x + $coeff_x), (-22 + ($note_flag_0 * 7) + $pos)
          ];
          
          print_oblique("oblique", $points, $noteCount, $line, $i, $x, $groupNoteSequence);
          
          $coeff_x +=7;

        if ($x > 0 && $connecting_line != 0){
          $x1 = $x2 = .5 + 7 * ($x-1) + $coeff_x;
          $y1 = $note_y_ar[$x-1] + 8;
          $y2 = $note_y + 8;
          print_line($x1, $x2, $y1, $y2, 14, 1, "connecting_line_climacus_type_4");
        }
      // Oblique of porrectus
      } elseif ($porrectus == 1 and ($porrectus_type == 1 or $porrectus_type == 2) and $x < 2){
        if ($x == 1){
          // Do not show second note
         } elseif ($x == 0) {
          $note_flag_0 = flag_note2number($note_group_ar[$x]);
          $note_flag_1 = flag_note2number($note_group_ar[$x+1]);
          $note_flag_2 = flag_note2number($note_group_ar[$x+2]);

          // Porrectus type
          if($porrectus_type == 1){
            $porrectus_flag_x2 = 22;
          } elseif ($porrectus_type == 2){
            $porrectus_flag_x2 = 15;
          }
        
          $points_oblique = [
              (7 * $x + $coeff_x), (-29+($note_flag_0*7)+$pos),  //top left
              ($porrectus_flag_x2 + 7 * $x + $coeff_x), (-19 + (($note_flag_0 + $diff_this_next-1) * 7) + $pos), // top right
              ($porrectus_flag_x2 + 7 * $x + $coeff_x), (-11 + (($note_flag_0 + $diff_this_next-1) * 7) + $pos), // bottom right
              (7 * $x + $coeff_x), (-21+($note_flag_0*7)+$pos) // bottom left
            ]; 
          
          print_oblique("porrectusOblique", $points_oblique, $noteCount, $line, $i, $x, $groupNoteSequence);

          // line connecting second and third note
          $x1 = $x2 = $porrectus_flag_x2 - 0.5 + 7 * $x + $coeff_x;
          $y1 = $pos + $note_flag_2 * 7 - 25;
          $y2 = $pos + $note_flag_1 * 7 - 20;
          print_line($x1, $x2, $y1, $y2, 14);

        } // end if first note of porrectus
      } // end if porrectus
  
      // all other scenarios (not oblique, i.e. not united_clivis or first 2 notes of porrectus)
      else {
        // y of note
        $note_y =  $note_y + $coeff_y;
        
        // punctum inclinatus of climacus
        if (($climacus == 1 and (($climacus_type == 1 and $x != 0) or $climacus_type == 5))
        or ($single_note_shape == 3 or $single_note_shape == 11)
        ) {
          print_path("inclinatum", $inclinatum_path, $noteCount, $note_x, $note_y, $line, $i, $x, $groupNoteSequence);
          
          if ($single_note_shape == 6) {
             $x1= $x * 7 + $coeff_x + 17;
             $x2= $x * 7 + $coeff_x + 31;
             $y1= $note_y - 6;
             $y2= $note_y - 6;
             print_line($x1, $x2, $y1, $y2, 16, 1);
          }
          
          // avoid stem on inclinatum
          $stem_bottom_right = 0;
          

        } elseif ($single_note_shape == 4) {

          // long note
          print_path("long_note", $long_note, $noteCount, $note_x, $note_y, $line, $i, $x, $groupNoteSequence);
          $coeff_x += 8;
        
        }
        // Print Plica
        elseif ($plica == 1) {
          // hide notes that are actually plicas, but close the g of the note though.
          logMessage($x.": cp1");

        } 
        // plica descendant
        elseif ($plica_next == 1 
                and $plica_disc == 1 
                and (($plica_type == 2 or $plica_type == 3 or $plica_type == 4))
              ) {

          if ($plica_diff < 2 || $aaG_liquescent == 1) { // intervallo congiunto
            logMessage($x.": cp2");
            print_path("plica-disc-type-".$plica_type, $plica_disc_intervallo_congiunto_path, $noteCount, $note_x, $note_y, $line, $i, $x, $groupNoteSequence);

            if ($plica_type == 2) {
              $x1 = $x2 = 7.5 + 7*$x + $coeff_x;
              $y1 = $note_y + 16;
              $y2 = $note_y + 30;
              
              print_line($x1, $x2, $y1, $y2);
            }

          } else { // descendant intervallo disgiunto
            logMessage($x.": cp3");

              print_path("plica_disc_intervallo_disgiunto", $plica_disc_intervallo_disgiunto_path, $noteCount, $note_x, $note_y, $line, $i, $x, $groupNoteSequence);
          }

          // plica ascendant // *** da sistemare ***
        } elseif ($plica_next == 1 and $plica_asc == 1) {
          logMessage($x.": cp4");

          if ($plica_diff > -2){

               if ($plica_type == 2 or $plica_type == 3 or $plica_type == 4){
                print_path('plica-asc-type-'.$plica_type, $plica_asc_intervallo_congiunto_path, $noteCount, $note_x, $note_y, $line, $i, $x, $groupNoteSequence);

                if ($plica_type == 2){
                       $x1 = $x2 = 7.5 + 7 * $x + $coeff_x;
                      $y1 = $note_y - 8;
                      $y2= $note_y + 8;
                print_line($x1, $x2, $y1, $y2);
                } elseif ($plica_type == 4){
                   $coeff_x = -4; //****???? +=?
                }

              } elseif ($plica_type == 6) { // inclinatum + ascendant tilted stem
                  
                  print_path("plica-asc-type-6", $plica_tilted_type_6, $noteCount, $note_x, $note_y, $line, $i, $x, $groupNoteSequence);
                  $x1 = 7*$x+$coeff_x+16;
                  $x2 = 7*$x+$coeff_x+4;
                  $y1 = $note_y+2;
                  $y2 = $note_y+16;
                  print_line($x1, $x2, $y1, $y2);

              } else { // plica_type == 1 or plica_type == 5
                print_path("plica-asc-type-1-5", $plica_asc_type_1_path, $noteCount, $note_x, $note_y, $line, $i, $x, $groupNoteSequence);

                $stem_top_right = 1;
              }

            } else { // plica ascendant intervallo disgiunto
            print_path("plica_asc_intervallo_disgiunto", $plica_asc_intervallo_disgiunto_path, $noteCount, $note_x, $note_y, $line, $i, $x, $groupNoteSequence);
          }

        } else { // simple note

          print_path("punctum", $punctum_path, $noteCount, $note_x, $note_y, $line, $i, $x, $groupNoteSequence, $coeff_x);

          if ($x > 0 and ($porrectus != 1 or $porrectus_type == 3)
                    and $scandicus != 1
                    and $pes != 1
                    and $note_shape_previous != 1
                    and $note_shape_minus2 != 1
                    and $note_shape_previous != 3
                    and $connecting_line != 0) {
            
            $x1 = $x2 = 0.5 + $x * 7 + $coeff_x;
            $y1= $note_y_ar[$x] + 14;
            $y2 = $note_y_ar[$x-1] + 8;
            print_line($x1, $x2, $y1, $y2, "connecting_line?");
          }

          // line connecting pes
          if (($pes == 1 and $x == 1 and $connecting_line == 1) or ($scandicus == 1 and $x == 1) or ($note_shape_previous == 1)) {
            
            $x1 = $x2 = 0.5 + $x * 7 + $coeff_x;
            $y1 = $note_y_ar[$x-1] + 15;
            $y2 = $note_y + 8;
            print_line($x1, $x2, $y1, $y2, 16, 1, ($note_y_ar[$x-1])." ".($note_y + 8)." connecting_line_pes");
          }

          // line connecting second and third note of scandicus (type 2)
          if ($scandicus == 1 and $x == 2 and $scandicus_type == 2) {
            
            $x1=$x2 = 14.5 + $coeff_x;
            $y1= $note_y+14;
            $y2= $note_y_ar[$x-1]+10;
            
            print_line($x1, $x2, $y1, $y2, 16, 1, "connecting_line_scandicus");
          }
          // Ledger line on punctus
          if ($single_note_shape == 10) {
              
              $x1 = $x*7+$coeff_x-3;
              $x2 = $x*7+$coeff_x+11;
              $y1 = $y2 = $note_y+12;
              
            print_line($x1, $x2, $y1, $y2, 16, 1.1);
          } 
        }
      } // End if not clivis unita and not porrectus


      // RIGHT STEMS
      if ($stem_bottom_right == 1) {
        if ($scandicus == 1 and $x == 1){
          // do nothing
        } else {
          if ($scandicus == 1 and $x == 2 and $scandicus_type != 3){
            $coord_x = 15.25 * $x + $coeff_x;
          }else{
            $coord_x = 0.5 + 7 * ($x+1) + $coeff_x;
          }
            $x1 = $x2 = $coord_x;
            $y1= $note_y+8;
             $y2= $note_y+28;
            
            print_line($x1, $x2, $y1, $y2);
         }
      }

      if ($stem_top_right == 1) {
        if ($scandicus == 1 and $x == 1 and $pes_type == 3){
          $coord_x = 8*$x+$coeff_x;
        }else{
          if ($scandicus == 1 and $x == 2){
            $coord_x = 7.5*$x+$coeff_x;
          }else {
            $coord_x = 7.5+7*$x+$coeff_x;
          }
        }
      
        print_line($coord_x, $coord_x, ($note_y-6), ($note_y+15));
      }
    }

    // make room for inclinata
    if ($single_note_shape == 3 and $single_note_shape_next != 3){
      $coeff_x += 6;
    };

    /*** END NOTES ***/

    // Close square
    $prevent_connecting_line = 0;
    $sq_b = $Square_brackets[$line][$i][$x];
    if ($sq_b == 2 || $sq_b == 3) {
      print_close_square_bracket($x, $coeff_x);
      $coeff_x += 12;
      $prevent_connecting_line = 1;
    }
    
    /** Divisio **/
    if ($width_independent_from_columns == 0){
      $rientro_bar = 10;
    }else{
      $rientro_bar = 0;
    }
    $y1 = $bar[$line][$index_group][0];
    $y2 = $bar[$line][$index_group][1];

    $stripped_qq_notes = str_replace("[", "", $qq_notes_ar[$line][$i]);
    $stripped_qq_notes = str_replace("]", "", $stripped_qq_notes);

    if ($bar[$line][$index_group][1] != 0 && !is_numeric($stripped_qq_notes)) {

      // point on bar
      if ($bar[$line][$index_group][2] == 2
      || $bar[$line][$index_group][2] == 3
      && !is_numeric($qq_notes_ar[$line][$index_group])) {
        $rientro_bar = 10;
        $cx = $syl_width - 2;
        $cy = ((14*($bar[$line][$index_group][0]+1)+17.5) + 14*($bar[$line][$index_group][1]+1)+17.5)/2;
        
        print_circle($cx, $cy, 1);
      }

      $x1 = $x2 = $syl_width-$rientro_bar;
      $y1 = 14 * ($bar[$line][$index_group][0] + 1) + 17.5;
      $y2 = 14 * ($bar[$line][$index_group][1] + 1) + 17.5;
      print_line($x1, $x2, $y1, $y2);
      
      if ($bar[$line][$index_group][2] == 1 || $bar[$line][$index_group][2] == 3){
        echo "<line x1='";
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
      
    // Check CLOSED Angle brackets
    $Angle_br = $Angle_brackets[$line][$i][$x];
    if ($Angle_br == 2 or $Angle_br == 3) {
      print_closed_angle_brackets($x, $coeff_x);
      $coeff_x += 14;
    }
  } // END OPERATION NOTE_

  // Continuation of operations for the whole syllable (for $i)

  // End Print music
  $stripped_qq_notes = str_replace("[", "", $qq_notes_ar[$line][$i]);
  $stripped_qq_notes = str_replace("]", "", $stripped_qq_notes);
  if (!is_numeric($stripped_qq_notes)){
    // Print text
    // Create and array of syllabes
    $text_ar = preg_replace(array('/\r/', '/\n/'), " ", $text_string_clean);
    $text_ar = str_replace("__", " ", $text_ar);
    $text_ar = explode(' ', $text_ar);
    $this_syl = html_entity_decode($text_ar[$tot_of_i-1-$n_of_keys_total+$repetition_shift], ENT_QUOTES, 'UTF-8');

    // Calculate x position of syl
    $this_syl_2 = str_replace("<i>", "", $this_syl);
    $this_syl_2 = str_replace("</i>", "", $this_syl_2);
    $this_syl_2 = str_replace("<b>", "", $this_syl_2);
    $this_syl_2 = str_replace("</b>", "", $this_syl_2);
    $this_syl_2 = str_replace("<strong>", "", $this_syl_2);
    $this_syl_2 = str_replace("</strong>", "", $this_syl_2);
    $syl_len = mb_strlen($this_syl_2, 'utf-8');

    if ($syl_len * $fontSizeOld / $syl_width > 1.3) {
      $text_position = 15 - $syl_len * 1.1;
      if ($text_position < 0){
        $text_position = 0;
      }
    } else {
      $text_position = 16;
    }
      
    $this_syl = str_replace("<i>", "<tspan class='italic'>", $this_syl);
    $this_syl = str_replace("</i>", "</tspan>", $this_syl);
    $this_syl = str_replace("<b>", "<tspan class='bold'>", $this_syl);
    $this_syl = str_replace("</b>", "</tspan>", $this_syl);
    $this_syl = str_replace("<strong>", "<tspan class='bold'>", $this_syl);
    $this_syl = str_replace("</strong>", "</tspan>", $this_syl);
    $this_syl = str_replace("_", " ", $this_syl);

    echo "<text x='0' y='".($text_height)."' style='font-family:$textFontOld; font-size:$fontSizeOld'>";

    // Print syl
    echo $this_syl;
    echo "</text>";
    $stripped_qq_notes = str_replace("[", "", $qq_notes_ar[$line][$i]);
    $stripped_qq_notes = str_replace("]", "", $stripped_qq_notes);

    // *****************
    // print repetition
    // *****************
    if ($repetition_pattern[0] !== false) {
      
        if ($tot_of_i-1-$n_of_keys_total >= $repetition_pattern[0][2]  
        and $tot_of_i-1-$n_of_keys_total < $repetition_pattern[0][3]) {

          $this_syl = html_entity_decode($text_ar[$tot_of_i-1-$n_of_keys_total+$repetition_count], ENT_QUOTES, 'UTF-8');
          
          // Calculate x position of syl
          $this_syl_2 = str_replace("<i>", "", $this_syl);
          $this_syl_2 = str_replace("</i>", "", $this_syl_2);
          $this_syl_2 = str_replace("<strong>", "", $this_syl_2);
          $this_syl_2 = str_replace("</strong>", "", $this_syl_2);
          $syl_len = mb_strlen($this_syl_2, 'utf-8');

          if ($syl_len * $fontSizeOld / $syl_width > 1.3) {
            $text_position = 15 - $syl_len*1.1;
          }else{
            $text_position = 16;
          }
          
          $this_syl = str_replace("<i>", "<tspan class='italic'>", $this_syl);
          $this_syl = str_replace("</i>", "</tspan>", $this_syl);
          $this_syl = str_replace("<strong>", "<tspan class='bold'>", $this_syl);
          $this_syl = str_replace("</strong>", "</tspan>", $this_syl);
          $this_syl = str_replace("_", " ", $this_syl);

          echo "<text x='".$text_position."' class='".$syl_len.$text_position."' y='".($text_height+20)."' style='font-family:$textFontOld; font-size:$fontSizeOld'>";
          // Print syl
          echo $this_syl;
          echo "</text>";
          $stripped_qq_notes = str_replace("[", "", $qq_notes_ar[$line][$i]);
          $stripped_qq_notes = str_replace("]", "", $stripped_qq_notes);
          if ($tot_of_i-1-$n_of_keys_total == $repetition_pattern[0][3]-1){
            $repetition_shift = $repetition_pattern[0][3]-$repetition_pattern[0][2];
          }
        }
      }

      if($n_of_notes_in_note_group[$i] > 0  && !is_numeric($stripped_qq_notes)) {
        $index_group = $index_group +1;
      }
    }

    
    // Close bar
    echo "</g>";

  } // End line
  
  // custos 2
  if ($custos[$line] !== null) {
    print_custos($line_width, $custos[$line]);
  }

  // I've decided not to use this. it only makes things complicated and useless. 
  // print_midbar($Mid_bar_notes_groups_ar[$line], $n_of_lines_in_staff, $staff_height, $line_width);
  echo "</g>"; // close groups container
  echo "</svg>";

  // Push this line width in the array of lines widths
} // End analyse string


//** ANNOTATIONS ** //
print_annotation($annotations, $textFontOld, $fontSizeOld);

echo "</div>";
echo "</div><!-- End of div#staves-->";
echo "</div><!-- End of div#music_page-->";
echo "</div> <!-- End of div#music -->";

echo "<style>";
echo ".groupClickableRect {fill: transparent}\n";
echo ".staffLines line {stroke:black; stroke-width:1}\n";
echo "#staves {display: flex; flex-direction: column;}";
echo "</style>";

function logMessage($message, $level = 'INFO', $logFile = 'squareNotation.log') {
    // Define the format of the log entry: [YYYY-MM-DD HH:MM:SS] [LEVEL] Message
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] [$level] $message" . PHP_EOL;
    
    // Append the log entry to the file
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}
function logArray($array, $level = 'INFO', $logFile = 'squareNotation.log') {
    // Convert array to a readable string
    $message = print_r($array, true);
    
    // Define the format of the log entry: [YYYY-MM-DD HH:MM:SS] [LEVEL] Message
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] [$level] " . PHP_EOL . $message . PHP_EOL;
    
    // Append the log entry to the file
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}