<?php
function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

function clean_note_input($notes) {
    //clean note input
    $notes = preg_replace("/&lt;/", "^", $notes);
    $notes = preg_replace("/&gt;/", "°", $notes);
    $notes = preg_replace("[i|j|k|m|n|o|p|q|r|s|u|v|w|x|y|z|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|\.|\']", "", $notes);

    $single_quote = "&039;";
    
    // $notes = utf8_encode($notes);

    $notes = str_replace("\'", "", $notes);
    $notes = str_replace("&#039;", "", $notes);
    
    $note_for_SM = $notes;
    
    // Normalize line breaks and spaces
    $notes_bar_separator = preg_replace(['/\r/', '/\n/'], ['', '|'], $notes);
    $notes_bar_separator = str_replace([" |", "| "], "|", $notes_bar_separator);

    // Define replacements
    $replacements = array(
        '+a' => 'u', '+b' => 'p', '+h' => 'q', '+c' => 'r', '+d' => 's',
        '*G' => 'J', 'C1' => '11', 'C2' => '12', 'C3' => '13', 'C4' => '14',
        'C5' => '15', 'C6' => '16', 'C7' => '17', 'C8' => '18', 'C9' => '19',
        'F1' => '21', 'F2' => '22', 'F3' => '23', 'F4' => '24', 'F5' => '25',
        'F6' => '26', 'F7' => '27', 'F8' => '28', 'F9' => '29', 'G1' => '31',
        'G2' => '32', 'G3' => '33', 'G4' => '34', 'G5' => '35', 'g1' => '41',
        'g2' => '42', 'g3' => '43', 'g4' => '44', 'g5' => '45', 'b}' => '90',
        'h}' => '91', 'B}' => '92'
    );

    $notes_bar_separator = str_replace(array_keys($replacements), array_values($replacements), $notes_bar_separator);

    return $notes_bar_separator;
}

function c($thing, $name="") {
  if (gettype($thing) == "array"){
    echo "<pre id='ms' class='ced: $name' >";
    print_r($thing);
    echo "</pre>";
  }else{
    echo "<div class='ced' id='ms'>";// so it display it
    echo $thing;
    echo "</div>";
  }
}

// print lines of n-tagram
function print_n_gram($n_of_lines_in_staff, $staff_height, $line){
  echo "<g class='staffLines'>";
  
  for ($i = 0; $i < $n_of_lines_in_staff; $i++){
    $y = 31.2 + $i*14;

    echo "<line x1='0' y1='$y' x2='100%' y2='$y'/>\n";
  }
  
  echo "</g>";
}

function print_rect($syl_width, $staff_height, $function) {
  echo "<rect stroke='1' class='groupClickableRect' x='0' y='0' width='$syl_width' height='$staff_height' $function ></rect>";
}

function get_array_of_text_syl_corresponding_to_music($notes_ar, $text_string_clean){
  $syl_in_succession = [];
  $i_text = 0;
  
  // clean text
  $text_for_col = str_replace("\n", " ", $text_string_clean);
  $text_for_col = html_entity_decode($text_for_col, ENT_QUOTES, 'UTF-8');
  // Ignore <i> and </i>, <strong> and </strong> tags
  $text_for_col = preg_replace('/<\/?(i|strong)>/', '', $text_for_col);
  
  // Create continuous array of syllables
  $text_for_col = explode(" ", $text_for_col);  
  $line_count = count($notes_ar);
  // loop through music lines

  for ($line_n = 0; $line_n < $line_count; $line_n++) {
    // loop through music elements
    $syls_in_line = count($notes_ar[$line_n]);
    for ($syl_n = 0; $syl_n < $syls_in_line; $syl_n++) {
      // create a series of character count corresponding to notes (assign "0" empty note measures, clefs and keys)
      if ($notes_ar[$line_n][$syl_n] != "" and !is_numeric($notes_ar[$line_n][$syl_n])) {
        array_push($syl_in_succession, strlen($text_for_col[$i_text]));
        $i_text += 1;
      }else{
        array_push($syl_in_succession, 0);
      }
    }
  }

  return $syl_in_succession;
}

function get_array_of_text_syl_per_colums($column_subar, $notes_ar, $text_string_clean){
  $text_for_col = str_replace("\n", " ", $text_string_clean);
  $text_for_col = html_entity_decode($text_for_col, ENT_QUOTES, 'UTF-8');
  // Ignore <i> and </i>, <strong> and </strong> tags
  $text_for_col = preg_replace('/<\/?(i|strong)>/', '', $text_for_col);
  $text_for_col = explode(" ", $text_for_col);

  $syl_in_succession = [];
  $i_text = 0;
  
  $line_count = count($column_subar[0]);
  $column_count = count($column_subar);
  // loop through lines
  for ($line_n = 0; $line_n < $line_count; $line_n++) {
    // loop though columns
    for ($col_n = 0; $col_n < $column_count; $col_n++){
      // create a series of character count correspondind to notes (assign "0" empty note measures, clefs and keys)
      if ($notes_ar[$line_n][$col_n] != "" and !is_numeric($notes_ar[$line_n][$col_n])) {
        array_push($syl_in_succession, strlen($text_for_col[$i_text]));
        $i_text += 1;
      }else{
        array_push($syl_in_succession, 0);
      }
    }
  }
  // Now we have the succession
  // $syl_in_succession  is no more useful because we don't have empty measures anymore
  // loop throught the lines, we know the number of lines, because any element of $column_subar counts the n of lines
  $syllables_chunked = array_chunk($syl_in_succession, count($column_subar));
  
  $number_of_letters_per_syl_in_column = [];
  for ($counter_i = 0; $counter_i < count($column_subar); $counter_i++) {
    array_push($number_of_letters_per_syl_in_column, array());
    for ($counter_j = 0; $counter_j < count($column_subar[0]); $counter_j++) {
      $number_of_letters_per_syl_in_column[$counter_i][$counter_j] = $syllables_chunked[$counter_j][$counter_i];
    }
  }
  $max_number_of_letters_per_syl_in_column = [];
  foreach($number_of_letters_per_syl_in_column as $element) {
    array_push($max_number_of_letters_per_syl_in_column, max($element));
  }
  return $max_number_of_letters_per_syl_in_column;
};

function get_array_of_extra_text_space($max_n_of_notes_in_column, $max_number_of_letters_per_syl_in_column) {
  $array_of_extra_text_space = [];
  for ($i = 0; $i < count($max_n_of_notes_in_column); $i++){
    $additionalTextSpace = 0;
    if ($max_number_of_letters_per_syl_in_column[$i] - $max_n_of_notes_in_column[$i] > 3) {
      $additionalTextSpace = ($max_number_of_letters_per_syl_in_column[$i] - $max_n_of_notes_in_column[$i]) * 5;
    }
    array_push($array_of_extra_text_space, $additionalTextSpace);
  }
  return $array_of_extra_text_space;
}

function print_clef($note_group_ar) {
  // divide all key elements (because they could be multiple)
  $key_elements = str_split(implode($note_group_ar), 2);
  $i = 0;
  // print all of them
  foreach ($key_elements as $key) {

    if ($key[0] < 9){
      set_pos($key);
      
      // C-Clef
      if ($key[0] == 1){
        $key_name = "C-clef";
        $key_height = 30+14*$key[1];
        $key_path = $GLOBALS['c_key_path'];
        $key_scale = 0.050000;
      
      } 
      // F-Clef
      elseif ($key[0] == 2){
        $key_name = "F-clef";
        $key_height = 31+14*($key[1]);
        $key_path = $GLOBALS['f_key_path'];
        $key_scale = 0.004;
        
      } 
      // G-Clef
      elseif ($key[0] == 3){
        $key_name = "G-clef";
        $key_height = 20+14*($key[1]);
        $key_path = $GLOBALS['G_key_path'];
        $key_scale = 0.06;
      }
      // g-Clef
      elseif ($key[0] == 4){
        $key_name = "G-clef";
        $key_height = 20+14*($key[1]);
        $key_path = $GLOBALS['G_key_path'];
        $key_scale = 0.06;
      }

      // print key
      echo "<g class='".$key_name."' transform='translate(5,$key_height) scale($key_scale,",($key_scale*-1),")'>";
      echo $key_path;
      echo "</g>";
    }

    /*****
    * b in key
    ******/
    elseif ($key[0] == 9) {
      // b}
       if ($key == 90){
        $position = 38 + $GLOBALS['pos'];
        $flat_path = $GLOBALS["flat_path"];
        $rotate = "";
        $GLOBALS["b_in_key"] = 1;
      }
      // h}
      elseif ($key == 91){    
        $position = 43 + $GLOBALS['pos'];
        $flat_path = $GLOBALS["natural_path_h"];
        $GLOBALS["b_in_key"] = 0;
      }
      // B}
      elseif ($key == 92){
        $position =  -10 + $GLOBALS['pos'];
        $flat_path = $GLOBALS["flat_path"];  
        $GLOBALS["b_in_key"] = 1;
      }
      
      // flat/natural
      echo "<g transform='matrix(0.03, 0,  0, -0.03, ";

      if ($i == 0) {
        echo 10;
      } else {
        echo 30;
      }
      echo ",";
      echo $position;
      echo ")";
      echo "'>";
      echo "<path class='b_in_key' id='b_key_".$line."'";
      echo " d='$flat_path'>";
      echo "</g>";
    }
    
    $i = $i+1;
  }
}

function set_pos($key) {
      $GLOBALS["pos"] = get_pos($key);
}

function get_pos($key) {
  // C
  if     ($key==11){$pos = 0;}
  elseif ($key==12){$pos = 14;}
  elseif ($key==13){$pos = 28;}
  elseif ($key==14){$pos = 42;}
  elseif ($key==15){$pos = 56;}
  // F
  elseif ($key==21){$pos = -28;}
  elseif ($key==22){$pos = -14;}
  elseif ($key==23){$pos = 0;}
  elseif ($key==24){$pos = 14;}
  elseif ($key==25){$pos = 28;}
  // G
  elseif ($key==31){$pos = -21;}
  elseif ($key==32){$pos = -7;}
  elseif ($key==33){$pos = 7;}
  elseif ($key==34){$pos = 21;}
  elseif ($key==35){$pos = 35;}
  // g
  elseif ($key==41){$pos = 28;}
  elseif ($key==42){$pos = 42;}
  elseif ($key==43){$pos = 56;}
  elseif ($key==44){$pos = 70;}
  elseif ($key==45){$pos = 84;}
  return $pos;
}

function get_font_family($cookie_font) {
    if ($cookie_font == "2"){
      return '"EB Garamond"';
    }elseif ($cookie_font == "3"){
      return "Courier";
    }elseif ($cookie_font == "4"){
      return "Roboto";
    }elseif ($cookie_font == "5"){
      return "Junicode";
    }else{
      // 0 || 1 || fallback
      return '"Times New Roman"';
    }
}

function get_font_size($cookie_font_size){
  if ($cookie_font_size == 0){
    return 12;
  }else{
    return $cookie_font_size;
  }
} 

function get_x_columns_and_maxline_width($max_n_of_notes_in_column, $array_of_extra_text_space){
  $x_columns = [0];
  $col_widths_ar = [];
  $line_width = 0;

  for ($column = 0; $column < count($max_n_of_notes_in_column); $column++) {
    $col_width = round(34 + 20 * $max_n_of_notes_in_column[$column] + $array_of_extra_text_space[$column]);
    $line_width += $col_width;
    array_push($x_columns, $line_width);
    // array_push($col_widths_ar, $col_width);
  }
  return [$x_columns, $line_width];
}

function get_width_column($x_columns, $i, $line_width) {
  
  if (count($x_columns) > $i) {
    $width_column = $x_columns[$i+1] - $x_columns[$i];
  } else {
    $width_column = $line_width - $x_columns[$i];
  }
  return $width_column;
}

function get_pair_notes_and_text($notes, $text_count) {
  
  $pair_notes_text = [];
  $text_i = 0;
  $notes_ar = format_notes($notes);
  
  foreach ($notes_ar as $line_n => $line) {
    array_push($pair_notes_text, []);
    foreach ($line as $el_n => $el) {
      $this_syl_count = $text_count[$text_i];
      $this_note = $notes_ar[$line_n][$el_n];
      array_push($pair_notes_text[$line_n], [$this_note, $this_syl_count]);
      $text_i++;
    }
  }
  
  return $pair_notes_text;
}

function get_array_of_text_syl_corresponding_to_music_repetitions($notes_ar, $text_line_ar, $repetitions) {
  $syl_in_succession = [];
  $i_text = 0;
  
  $new_text_line_ar = [];
  // loop through metrical text line
  for ($i=0; $i < count($text_line_ar); $i++) {
    $this_text_line = $text_line_ar[$i];
    $this_text_line = html_entity_decode($this_text_line, ENT_QUOTES, 'UTF-8');

    $this_text_line = preg_replace('/<\/?(i|strong)>/', '', $this_text_line);

    $text_syls = explode(" ", $this_text_line);
    // It's a repeated line
    if (isset($repetitions[$i])) {
      $new_line = [];
      $repetitions_on_line = $repetitions[$i]; 
      $repeated_line_indexes = array_keys($repetitions_on_line);

      // Loop through syllables
      for ($j=0; $j < count($text_syls); $j++) {
        // syllable on the first line
        $longest_syl_vertical = $text_syls[$j];
        // Loop vertically in repetead syls
        for ($z=0; $z < count($repeated_line_indexes); $z++) {
          $index_of_repetition = $repeated_line_indexes[$z];
          

          $this_repetition_line = html_entity_decode($repetitions_on_line[$index_of_repetition], ENT_QUOTES, 'UTF-8');
          $this_repetition_line = preg_replace('/<\/?(i|strong)>/', '', $this_repetition_line);
          $this_repetition_line = explode(" ", $this_repetition_line);
          $this_repetition_syl = $this_repetition_line[$j];
          if (strlen($this_repetition_syl) > strlen($longest_syl_vertical)){
            $longest_syl_vertical = $this_repetition_line[$j];
          }
        }
        $new_line[$j] = $longest_syl_vertical;
      
      }
      $new_line = implode(" ", $new_line);
      $new_text_line_ar[$i] = $new_line;
    }else{
      $new_text_line_ar[$i] = $this_text_line;
    }
  }
  logArray($new_text_line_ar);
  // clean text
  $new_text_line_ar = implode(" ", $new_text_line_ar);

  // Create continuous array of syllables
  $text_for_col = explode(" ", $new_text_line_ar);  
  $line_count = count($notes_ar);
  // loop through music lines

  for ($line_n = 0; $line_n < $line_count; $line_n++) {
    // loop through music elements
    $syls_in_line = count($notes_ar[$line_n]);
    for ($syl_n = 0; $syl_n < $syls_in_line; $syl_n++) {
      // create a series of character count corresponding to notes (assign "0" empty note measures, clefs and keys)
      if ($notes_ar[$line_n][$syl_n] != "" and !is_numeric($notes_ar[$line_n][$syl_n])) {
        array_push($syl_in_succession, strlen($text_for_col[$i_text]));
        $i_text += 1;
      }else{
        array_push($syl_in_succession, 0);
      }
    }
  }

  return $syl_in_succession;
}

function format_notes($notes_bar_separator){
  $lines_ar = explode("|", $notes_bar_separator);
  $formatted_music = [];
  foreach ($lines_ar as $line_n => $line) {
    array_push($formatted_music, []);
    $syllable_ar = explode(" ", $line);
    foreach ($syllable_ar as $syl_n => $element) {
      $formatted_music[$line_n][$syl_n] = $element;
    }
  }
  return $formatted_music;
}

function get_width_of_syls($pairs) {
  global $bar;
  $widths = [];
  $line_widths = [];
  foreach ($pairs as $line_n => $lines) {
    array_push($widths, []);
    $line_width = 0;
    $syl_n_bar = 0;
    foreach ($lines as $syl_n => $pair) {    
      $el = $pair[0];
      $txt = $pair[1];
      
      // increase only with note elements
      
      // get the added space for following a bar
      if ($syl_n_bar == 0 || $bar[$line_n][$syl_n_bar-1][0] !== null){
        $post_bar_space = 10;
      }else{
        $post_bar_space = 0;
      }
      if (!is_numeric($el)){
        $syl_n_bar++;
      }
      
      $el_width = get_music_element_width($el);
      $txt_width = get_text_element_width($txt);
      // select the largest
      $syl_width = max([$el_width, $txt_width]);
      $syl_x = $line_width + $post_bar_space;
      $line_width += $syl_width + $post_bar_space;
      // Store the values for syllable
      $widths[$line_n][$syl_n] = [$syl_x, $syl_width];
    }
    $line_widths[$line_n] = $line_width;
  }
  $max_line_width = max($line_widths);
  
  return [$widths, $max_line_width];
}

function get_music_element_width($el){ // needs to be refined of course
  $fixed = 20;
  $note_width = 7; // but, regular note is 50, so?
  $add = 0;
  $syl_w = 0;
  for ($i = 0; $i < strlen($el); $i++) {
    
    // clefs and alterations
    if (is_numeric($el)){ 
      $syl_w += processNumberCouples($el);
    } 
    // Brakets
    elseif ($el[$i] == ">" || $el[$i] == "<") {
      $syl_w += 10;
    }else{
      // simple note
      $syl_w += $note_width;
      
      // unison
      if ($i > 0 and $el[$i] == $el[$i-1]) {
        $add += 3;
      }
    }    
  }
  $res = $fixed + $syl_w + $add;
  return round($res);
}

function processNumberCouples($numberString) {
    $space = 0;
    // Split the string into couples of two digits
    $couples = str_split($numberString, 2);
    
    // Iterate through the couples and perform specific actions
    foreach ($couples as $index => $n) {
        if (10 < $n and $n < 46) { // clef
            $space += 5;
        } elseif ($n > 89) { // alteration
            $space += 2;
        }
    }
    return $space;
}

function get_text_element_width($el) { // needs to be adjusted and font size added
  global $fontSizeOld;
  $font_size_adjustment = 0;
  if ($fontSizeOld > 18){
    // Add 2 px per char for each exceeding fontSize value over 12
    $font_size_adjustment = ($fontSizeOld - 18) * 1.5;
  } 
  return 10 + $el * 8 + $font_size_adjustment;
}


function print_path($class, $path, $noteCount, $note_x, $note_y, $line, $i, $x, $groupNoteSequence, $coeff_x=0){
  if (str_starts_with($class, "plica")){
    $class .= " note" . ($noteCount+1);
  }
  echo "<path id='note.$line.$i.$x' class='note$noteCount ".$class."' transform='translate($note_x, $note_y)'";
  echo " onclick='extractNoteFromClick($groupNoteSequence, $x, $line)'";
  echo " d='$path'></path>";
}

function print_oblique($class, $points, $noteCount, $line, $i, $x, $groupNoteSequence){ 
  global $noteCount;

  echo "<polygon id='note.$line.$i.$x' class='$class note$noteCount note".($noteCount+1)."'";
  echo " onclick='extractNoteFromClick($groupNoteSequence, $x, $line)'";
  echo " points='$points[0],$points[1] $points[2],$points[3] $points[4],$points[5] $points[6],$points[7]'/>";
}

function print_line($x1, $x2, $y1, $y2, $indent=14, $stroke_width=1, $class="") {
  if ($class != ""){
    $class = " class='$class' ";
  }
  echo "<line$class x1=$x1 x2=$x2 y1=$y1 y2=$y2 style='stroke:black; stroke-width:$stroke_width'/>";
}

function get_note_y($note){
  $note_y_map = [
    "J" => 90, "A" => 83, "H" => 76, "B" => 76, "C" => 69, "D" => 62,
    "E" => 55, "F" => 48, "G" => 41, "a" => 34, "h" => 27, "b" => 27,
    "c" => 20, "d" => 13, "e" => 6, "f" => -1, "g" => -8,
    "u" => -15, "p" => -22, "q" => -22, "r" => -29, "s" => -36
  ];

  if (isset($note_y_map[$note])) {
      return $note_y_map[$note];
  }
}

function print_circle($cx, $cy, $r){
  echo "<circle cx=$cx cy=$cy r=$r stroke='black' stroke-width='1' fill='black'/>";
}

function print_open_angle_brackets($x, $coeff_x) {
  $x1 = $x*7 + $coeff_x + 3;
  echo "<g class='angle_bracker_open'>";
  echo "<line x1=".($x1+9)." y1='28' x2=$x1 y2='52.5' style='stroke:black; stroke-width:2; stroke-linecap: round;'/>";
  echo "<line x1='$x1' y1='52.5' x2='".($x1+9)."' y2='77' style='stroke:black; stroke-width:2; stroke-linecap: round;'/>";
  echo "</g>";
}

function print_closed_angle_brackets($x, $coeff_x){
  $self_width = 13;
  $note_width = 7;
  $n_of_notes = $x+1;
  $x1 = $n_of_notes * $note_width + $self_width + $coeff_x;
  echo "<g class='angle_bracker_close'>";
  echo "<line x1=".($x1 - 10)." y1=28 x2=".($x1-1)." y2=52.5 style='stroke:black; stroke-width:2; stroke-linecap: round;'/>";
  echo "<line x1=".($x1 - 1)." y1=52.5 x2=".($x1-10)." y2=77 style='stroke:black; stroke-width:2; stroke-linecap: round;'/>";
  echo "</g>";  
}

function getFlatHeight($note) {
    $flat_height_map = [
        "b" => 38, "B" => 88, "p" => -11, "J" => 102, "A" => 95,
        "C" => 81, "D" => 74, "E" => 67, "F" => 60, "G" => 53,
        "a" => 46, "c" => 31, "d" => 24, "e" => 17, "f" => 10,
        "g" => 3, "u" => -4, "r" => -17
    ];

    return $flat_height_map[$note] ?? null; // Returns null if not found
}

function print_flat($note, $pos, $coeff_x) {
  global $flat_path;
  $flat_height = getFlatHeight($note);
  
  echo "<path class='flat-symbol' transform='matrix(0.03, 0,  0, -0.03, ".($coeff_x).", ".($flat_height + $pos).")' d='$flat_path'></path>";
  
  // to enable supplied flat (_h) integrate this in the
  /*if ($note_group_ar[$x] === "H" or $note_group_ar[$x] === "h" or $note_group_ar[$x] === "q"){/
    echo "<path transform='matrix(0.025, 0,  0,\n";
    echo "                       -0.025,";
    echo 3+20*($x+1).",";
  }*/
}

function getNaturalHeight($note) {
    $natural_height_map = [
        "J" => 81.5, "A" => 73.5, "H" => 66.5, "C" => 59.5, "D" => 52.5,
        "E" => 45.5, "F" => 38.5, "G" => 31.5, "a" => 24.5,
        "h" => 17.5, "b" => 17.5, "c" => 10.5, "d" => 2.5, "e" => -3.5,
        "f" => -10.5, "g" => -17.5, "u" => -24.5, "p" => -31.5, "q" => -31.5,
        "r" => -38.5
    ];

    return $natural_height_map[$note] ?? null; // Returns null if not found
}

function print_natural($note, $pos, $coeff_x) {
  global $natural_sign_path;
  
  $natural_height = getNaturalHeight($note);
  echo "<path class='natural-symbol' transform='matrix(2.2, 0, 0, 2.2, ".($coeff_x-2).", ".($natural_height + $pos).")' d='$natural_sign_path'></path>";
}

function getSharpHeight($note) {
  $sharp_height_map = [
    "J" => 70, "A" => 63, "H" => 56, "C" => 49, "D" => 42,
    "E" => 35, "F" => 28, "G" => 21, "a" => 14, "h" => 7,
    "c" => 0, "d" => -7, "e" => -14, "f" => -21, "g" => -28,
    "u" => -35, "p" => -42, "q" => -49, "r" => -56
  ];

  return $sharp_height_map[$note] ?? null; // Returns null if not found
}

function print_sharp($note, $pos, $coeff_x) {
  global $sharp_path;
  $sharp_height = getSharpHeight($note);
  echo "<path transform='matrix(1, 0, 0, 1, ".($coeff_x).", ".($sharp_height+$pos).")'";
  echo " d='$sharp_path' style='fill:#000000'/>";
}

function print_custos($syl_width, $custos_line){
  echo "<polygon class='custos' transform='translate(".($syl_width-16).", ". (28+($custos_line-1)*7).")' points='-0.5,3 5.6667,-0.3333 10,3 3.3333,6.3333' fill='black' />";
  
  $x1 = $syl_width-12.7;
  $y1 = $custos_line*7+27;
  $x2 = $syl_width;
  $y2 = $custos_line*7+20;
  print_line($x1, $x2, $y1, $y2);
}

function print_midbar($Mid_bar_double_slash, $n_of_lines_in_staff, $staff_height, $line_width) {
  if (substr(end($Mid_bar_double_slash), -2) == "//") {
    echo  "<g height='".$staff_height."px' width='100%'>";
    echo   "<line x1='".($line_width-12)."' y1='".(14*$n_of_lines_in_staff+17.5)."' x2='".($line_width-12)."' y2='30.68' style='stroke:black;stroke-width:1'/>";
    echo   "<line x1='".($line_width-3)."' y1='".(14*$n_of_lines_in_staff+17.5)."' x2='".($line_width-3)."' y2='30.68' style='stroke:black;stroke-width:6'/>";
    echo  "</g>";

  }  else {
    // Check mid_bar (check if last char of line is "/")
    $Mid_bar =  substr(end($Mid_bar_double_slash), -1);

    if ($Mid_bar == "/") {
      echo  "<g height='".$staff_height."px' width='100%'>";
      echo   "<line x1='".($line_width-0.5)."' y1='".(14*$n_of_lines_in_staff+17.5)."' x2='".($line_width-0.5)."' y2='30.68' style='stroke:black;stroke-width:1'/>";
      echo  "</g>";
    }
  }
}

function format_string($string) {
  $lines[] = explode("|", $string);
  // separate GROUPS
  foreach ($lines as $line) {
    foreach ($line as $group) {
      $ar[] = explode(" ", $group);
    }
  }
  return $ar;
}

function print_open_square_bracket($x, $coeff_x) {
  global $bracket_left_path;
  $x1 = $x * 7 + $coeff_x - 3;

  echo "<path transform='matrix(0.3, 0, 0, 0.5, $x1, 27)' class='bracket_left' d='$bracket_left_path'/>";
}

function print_close_square_bracket($x, $coeff_x) {
  global $bracket_right_path;
    $note_width = 7;
    $n_of_notes = $x + 1;
    $x1 = $n_of_notes * $note_width + $self_width + $coeff_x;
    echo "<path transform='matrix(0.3, 0, 0, 0.5,$x1, 27)' class='bracket_right' d='$bracket_right_path'/>";
}

function space_for_accidentals($flats, $naturals, $sharps, $notes_in_syl, $b_in_key){
  $acc_space = 8;
  $accidentals_count = 0;
  // check_flats
  // 1. when there is at least a b|B|q and no $b_in_key
  // 2. when there is a flat-> for each flat == 1 check the position and for each new note name get a space
  if (preg_match('/[bBp]/', $notes_in_syl) and !$b_in_key) {
    $accidentals_count++;
  }

  $flat_notes = [];
  foreach (findAllPositions($flats, 1) as $i => $flat_position) {
    $note = $notes_in_syl[$flat_position];
    if (!in_array($note, $flat_notes, true)) {
      $accidentals_count++;
      array_push($flat_notes, $note);
    }
  }
  
  $natural_notes = [];
  foreach (findAllPositions($naturals, 1) as $i => $natural_position) {
    $note = $notes_in_syl[$natural_position];
    if (!in_array($note, $natural_notes, true)) {
      $accidentals_count++;
      array_push($natural_notes, $note);
    }
  }
  
  $sharp_notes = [];
  foreach (findAllPositions($sharps, 1) as $i => $sharp_position) {
    $note = $notes_in_syl[$sharp_position];
    if (!in_array($note, $sharp_notes, true)) {
      $accidentals_count++;
      array_push($sharp_notes, $note);
    }
  }
  return $accidentals_count * $acc_space;
}

function findAllPositions($string, $char) {
    $positions = [];
    $offset = 0;

    while (($pos = strpos($string, $char, $offset)) !== false) {
        $positions[] = $pos;
        $offset = $pos + 1; // Move to the next character after the found position
    }

    return $positions;
}
function is_b_flat($char){
  $b = ["b", "B", "p"];
  if (in_array($char, $b)){
    return 1;
  }
  return 0;
}

function print_element($id, $content){
  global $textFontOld;
  echo "<div id='$id' style='font-family:".$textFontOld."'>";
  echo $content;
  echo "</div>";
}

function get_indexes_of_syllables_starting_new_line($text_lines){
  $indexes_of_syllables_starting_new_line = []; // first syllable is always 0 (is it? keys?)
  $cumulative_syl_count = 0;
  foreach ($text_lines as $i => $line) {
    $line_syls = explode(' ', $line);
    if ($i < count($text_lines)-1){ // skip last line
      $cumulative_syl_count += count($line_syls);
      
      array_push($indexes_of_syllables_starting_new_line, $cumulative_syl_count);
    }
  }
  return $indexes_of_syllables_starting_new_line;
}

function check_if_notes_lower_than_staves($notes_ar, $n_of_lines_in_staff, $pos){
  $lowest = 0;
  $last_line_height = 31.2 + ($n_of_lines_in_staff-1) * 14;
  $temp_pos = $pos;

  for ($i = 0; $i < count($notes_ar); $i++) {
      $el = $notes_ar[$i];
      // adjust position if necessary
      if (is_numeric($el)){
        $temp_pos = get_pos($el);
      }else{
        // loop through notes of syllable
        for ($j=0; $j < strlen($el); $j++) {
          $note_height = get_note_y($el[$j]) + $temp_pos + 7;
          if ($note_height > $last_line_height){
            if ($note_height > $lowest){
              // set a new lowest
              $lowest = $note_height;
            }
          }
        }
      }
  }
  // return extra space needed for notes not to bump into text
  
  return $lowest + 7;
}

function print_annotation($annotations, $textFontOld, $fontSizeOld){
  echo "<div style='bottom:10px; margin-top:30px;' id='annotationsSection'>";

  if ($_COOKIE['showAnnotations'] != 2 and $annotations != "") {
    echo "<br/>";
    echo "<div style='font-family:$textFontOld;font-size:".$fontSizeOld."px'>";
    $annotations = str_replace("\n", "<br/>", $annotations);
    echo html_entity_decode($annotations)."<br/>";
  }
  echo "</div>";
}