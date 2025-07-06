<?php
ini_set('display_errors', 0);
// Turn off all error reporting
error_reporting(E_ALL);

function c($t){
  global $c_active;
  if ($c_active){
    if (gettype($t) == "array") {
      print_r($t);
    }else{
      echo $t."\n";
      error_log("--Start--");
      error_log($t);
      error_log("--End--");
    }
  }
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data); // changes characters used in html to their equivalents, for example: < to &gt;
  return $data;
}

$notes = $_POST['notes'];
$id_input = $_POST['idinput'];
$text_string = $_POST['text_string'];
$title = $_POST['title'];
$author = $_POST['author'];
$language = test_input($_POST['language'] ?? '');
$ms_input = test_input($_POST['manuscript'] ?? '');
$f_input = $_POST['folio'];
$annotations = adapt_annotations($_POST['annotations']);
$currentStyle = $_POST['currentStyle'];
$lines_in_line = json_decode($_POST['linesInLine']);
$shapeGroupNote = json_decode($_POST['shapeGroupNote']);
$shape_single_note = json_decode($_POST['shapeSingleNote']);
$stem_direction = json_decode($_POST['stemSingleNote']);
$connecting_line_param = json_decode($_POST['connectGroupNote']);
$pes_type_default = $_POST['pes'];
$clivis_type_default = $_POST['clivis'];
$climacus_type_default = $_POST['climacus'];
$porrectus_type_default = $_POST['porrectus'];
$plica_type_default = $_POST['plica'];
$scandicus_type_default = $_POST['scandicus'];
$user_name = $_POST['userName'];
$bar = json_decode($_POST['bar']);
$custos = json_decode($_POST['custos']);
$automatic_manualMelodicStructure = $_POST['automatic_manualMelodicStructure'];
$melodic_structure = $_POST['melodicStructure'];
$show_taxonomy_descriptions_cb = $_POST['includeDetails'];
$include_neume_type_cb = $_POST['includeNeumeType'];
$include_neume_class_cb = $_POST['includeNeumeClass'];
$modern_notation_cb = $_POST['modernNotation'];
$mei_compliance_cb = $_POST['meiCompliance'];
$old_line = 0;
$old_index_group = 0;
$index_group_continuous = 0;
$metrical_line = 0;
$keySig = "";

$c_active = true; // debug mode

include "./functions_shared.php";

$show_taxonomy_descriptions = true;
if ($show_taxonomy_descriptions_cb == "false") {
  $show_taxonomy_descriptions = false;
}

$include_neume_type = true;
if ($include_neume_type_cb == "false") {
  $include_neume_type = false;
}
$include_neume_class = false;
if ($include_neume_class_cb == "false") {
  $include_neume_class = true;
}

$modern_notation = true;
if ($modern_notation_cb == "false") {
  $modern_notation = false;
}

$mei_compliance = true;
if ($mei_compliance_cb == "false") {
  $mei_compliance = false;
}

//clean note input
if ($currentStyle == 0){
  $notes_bar_separator = preg_replace('/\r|\n/', '|', $notes);
  $notes_bar_separator = str_replace("'", '* ', $notes_bar_separator);
}else{
  $notes_bar_separator = str_replace('\'', '|', $notes);
  $notes_bar_separator = preg_replace('/\r|\n/', ' * ', $notes_bar_separator);
}

// $notes_bar_separator = str_replace("'", "* ", $notes_bar_separator);
$notes_bar_separator = str_replace(" |", "|", $notes_bar_separator);
$notes_bar_separator = str_replace("| ", "|", $notes_bar_separator);
$notes_bar_separator = preg_replace('/\n/', '', $notes_bar_separator);
$notes_bar_separator = str_replace('+a', 'u', $notes_bar_separator);
$notes_bar_separator = str_replace('+b', 'p', $notes_bar_separator);
$notes_bar_separator = str_replace('+h', 'q', $notes_bar_separator);
$notes_bar_separator = str_replace('+c', 'r', $notes_bar_separator);
$notes_bar_separator = str_replace("*G", "J", $notes_bar_separator);
$notes_bar_separator = str_replace("C1", "01", $notes_bar_separator);
$notes_bar_separator = str_replace("C2", "02", $notes_bar_separator);
$notes_bar_separator = str_replace("C3", "03", $notes_bar_separator);
$notes_bar_separator = str_replace("C4", "04", $notes_bar_separator);
$notes_bar_separator = str_replace("F1", "05", $notes_bar_separator);
$notes_bar_separator = str_replace("F2", "06", $notes_bar_separator);
$notes_bar_separator = str_replace("F3", "07", $notes_bar_separator);
$notes_bar_separator = str_replace("F4", "08", $notes_bar_separator);
$notes_bar_separator = str_replace("F5", "09", $notes_bar_separator);
$notes_bar_separator = str_replace("b}", "10", $notes_bar_separator);
$notes_bar_separator = str_replace("h}", "11", $notes_bar_separator);

$notes_for_plica    = str_replace(array(     ")", "%", "+", "_", "#", "/",      "[", "]", "01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11"),"", $notes_bar_separator);
$notes_for_plica = trim($notes_for_plica);
$notes_for_b_in_key = str_replace(array("(", ")", "%", "+",      "#", "/", "-", "[", "]", "01", "02", "03", "04", "05", "06", "07", "08", "09", "10","11"),"", $notes_bar_separator);
$notes_for_natural  = str_replace(array("(", ")",      "+", "_", "#", "/", "-", "[", "]", "01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11"), "", $notes_bar_separator);
$notes_for_sharp    = str_replace(array("(", ")", "%", "+", "_",      "/", "-", "[", "]", "01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11"),"", $notes_bar_separator);
$notes_for_flat     = str_replace(array("(", ")", "%", "+",      "#", "/", "-", "[", "]", "01 ", "02 ", "03 ", "04 ", "05 ", "06 ", "07 ", "08 ", "09 ", "10 ", "11 ","01", "02", "03", "04", "05", "06", "07", "08", "09","10", "11"),"", $notes_bar_separator);
$notes_for_brackets = str_replace(array("(", ")", "%", "+", "_", "#", "/", "-",           "01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11"),"", $notes_bar_separator);
$notes_clean        = str_replace(array("(", ")", "%", "+", "_", "#", "/",      "[", "]"),"", $notes_bar_separator);

// create array to check b in key
$notes_for_b_in_key_lines[] = explode("|", $notes_for_b_in_key);

// Calculate position of plicas
$Plica_string = str_replace(array('(a', '(b', '(c', '(d', '(e', '(f', '(g', '(h', '(J', '(A', '(B', '(C', '(D', '(E', '(F', '(G', '(H', '(u', '(p', '(q', '(r'),   "1", $notes_for_plica);
$Plica_string = str_replace("|",   " ", $Plica_string);
$Plica_string = str_replace("  ",   " ", $Plica_string);
$Plica_string = str_replace("* ",   "*", $Plica_string);
$Plica_string = str_replace(" *",   "*", $Plica_string);

$Plica_string = preg_replace("[a|b|c|d|e|f|g|h|u|p|q|r|J|A|B|C|D|E|F|G|H]", "0", $Plica_string);
// Separate lines and groups
$Plica_string_lines[] = explode("*", $Plica_string);
foreach ($Plica_string_lines as $Plica_line) {
  foreach ($Plica_line as $Plica_group) {
    $Plica_notes_groups_ar[] = explode(" ", $Plica_group);
  }
}

// Calculate position of natural alteration
$Natural_string = str_replace(array( '%a', '%b', '%c', '%d', '%e', '%f', '%g', '%h', '%J', '%A', '%B', '%C', '%D', '%E', '%F', '%G', '%H', '%u', '%p', '%q', '%r'), "1", $notes_for_natural);
$Natural_string = preg_replace("[a|b|c|d|e|f|g|h|u|p|q|r|J|A|B|C|D|E|F|G|H]", "0", $Natural_string);
// Separate LINES
$Natural_string_lines[] = explode("|", $Natural_string);
foreach ($Natural_string_lines as $Natural_line) {
  foreach ($Natural_line as $Natural_group) {
    $Natural_notes_groups_ar[] = explode(" ", $Natural_group);
  }
}

// Calculate position of sharp alteration
$Sharp_string = str_replace(array('#a', '#b', '#c', '#d', '#e', '#f', '#g', '#h', '#J', '#A', '#B', '#C', '#D', '#E', '#F', '#G', '#H', '#u', '#p', "#q", "#r"), "1", $notes_for_sharp);
$Sharp_string = preg_replace("[a|b|c|d|e|f|g|h|J|A|B|C|D|E|F|G|H|u|p|q|r]", "0", $Sharp_string);
// Separate LINES
$Sharp_string_lines[] = explode("|", $Sharp_string);
foreach ($Sharp_string_lines as $Sharp_line) {
  foreach ($Sharp_line as $Sharp_group) {
    $Sharp_notes_groups_ar[] = explode(" ", $Sharp_group);
  }
}

// Calculate position of flat alteration
$Flat_string = str_replace("1", "0", $notes_for_flat);
$Flat_string = str_replace(array( '_a', '_b', '_c', '_d', '_e', '_f', '_g', '_h', '_J', '_A', '_B', '_C', '_D', '_E', '_F', '_G', '_H', '_u', '_p', "_q", "_r"), "1", $Flat_string);
$Flat_string = preg_replace("[a|b|c|d|e|f|g|h|J|A|B|C|D|E|F|G|H|u|p|q|r]", "0", $Flat_string);
// Separate LINES
$Flat_string = trim($Flat_string);

$Flat_string_lines[] = explode("|", $Flat_string);
foreach ($Flat_string_lines as $Flat_line) {
  foreach ($Flat_line as $Flat_group) {
    $Flat_notes_groups_ar[] = explode(" ", $Flat_group);
  }
}

// Calculate position of natural alteration
$Brackets_string = str_replace(array( '[a]', '[b]', '[c]', '[d]', '[e]', '[f]', '[g]', '[h]', '[J]', '[A]', '[B]', '[C]', '[D]', '[E]', '[F]', '[G]', '[H]', '[u]', '[p]', '[q]', '[r]'), "3", $notes_for_brackets);
$Brackets_string = str_replace(array( '[a', '[b', '[c', '[d', '[e', '[f', '[g', '[h', '[J', '[A', '[B', '[C', '[D', '[E', '[F', '[G', '[H', '[u', '[p', '[q', '[r'), "1", $Brackets_string);
$Brackets_string = str_replace(array( 'a]', 'b]', 'c]', 'd]', 'e]', 'f]', 'g]', 'h]', 'J]', 'A]', 'B]', 'C]', 'D]', 'E]', 'F]', 'G]', 'H]', 'u]', 'p]', 'q]', 'r]'), "2", $Brackets_string);
$Brackets_string = preg_replace("[a|b|c|d|e|f|g|h|u|p|q|r|J|A|B|C|D|E|F|G|H]", "0", $Brackets_string);
// Separate LINES
$Brackets_ar_lines[] = explode("|", $Brackets_string);
foreach ($Brackets_ar_lines as $Brackets_lines) {
  foreach ($Brackets_lines as $Brackets_group) {
    $Brackets_notes_groups_ar[] = explode(" ", $Brackets_group);
  }
}

// Get melodic lines
$lines_ar = explode("|", $notes_clean);

// Count melodic lines
$n_of_lines = count($lines_ar);


// Clean text
$text_string_clean = str_replace("- ","-", $text_string);
$text_string_clean = str_replace("-","- ", $text_string_clean);
$text_string_clean = str_replace("  "," ", $text_string_clean);
$text_string_clean = str_replace("_","+", $text_string_clean);
$text_string_clean = str_replace("-","_", $text_string_clean);

// $text_string_newline_to_bar_separator = str_replace("\n","|", $text_string_clean); // new line as text separator
$text_string_clean = str_replace("\n"," ", $text_string_clean); // new line as text separator

// Divide textual lines
// $text_line_ar = explode('|', $text_string_newline_to_bar_separator);

// Create and array of syllabes
$text_ar = explode(' ', $text_string_clean);

if ($mei_compliance || $modern_notation) {
  header('Content-Type: text/xml');
  echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" . PHP_EOL;
  echo "<?xml-model href=\"https://music-encoding.org/schema/5.0/mei-Neumes.rng\" type=\"application/xml\" schematypens=\"http://relaxng.org/ns/structure/1.0\"?>" . PHP_EOL;
  echo "<?xml-model href=\"https://music-encoding.org/schema/5.0/mei-Neumes.rng\" type=\"application/xml\" schematypens=\"http://purl.oclc.org/dsdl/schematron\"?>";
} else {
  header('Content-Type: text/xml');
  echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" . PHP_EOL;
  echo "<?xml-model href=\"https://medmel.seai.uniroma1.it/resources/mei-Neumes_mm.rng\" type=\"application/xml\" schematypens=\"http://relaxng.org/ns/structure/1.0\"?>" . PHP_EOL;
  echo "<?xml-model href=\"https://medmel.seai.uniroma1.it/resources/mei-Neumes_mm.rng\" type=\"application/xml\" schematypens=\"http://purl.oclc.org/dsdl/schematron\"?>";
}

?>

<mei xmlns="http://www.music-encoding.org/ns/mei" meiversion="5.0">
  <meiHead>
    <fileDesc>
      <titleStmt>
        <title><?php echo $title;?></title>
        <composer>
          <persName role="creator"><?php echo $author;?></persName>
        </composer>
        <respStmt>
          <persName role="editor"><?php echo $user_name;?></persName>
        </respStmt>
      </titleStmt>
      <pubStmt>
        <publisher>
          <corpName role="publisher"></corpName>
        </publisher>
        <date></date>
        <availability>
          <useRestrict>Public domain</useRestrict>
        </availability>
      </pubStmt>
      <seriesStmt>
        <title/>
      </seriesStmt>
      <sourceDesc>
        <source target="#<?php echo str_replace(' ', '', $ms_input);?>">
          <locus><?php echo $f_input;?></locus>
        </source>
      </sourceDesc>
<?php
  if ($automatic_manualMelodicStructure == "manual") {
    echo "<notesStmt>\n";
    echo "        <annot label=\"musicalStructure\" type=\"".$automatic_manualMelodicStructure."\">". $melodic_structure."</annot>\n";
    echo "      </notesStmt>\n";
  }
?>
    </fileDesc>
    <encodingDesc>
      <appInfo>
        <application>
          <name>MedMel2MEI v2.0</name>
        </application>
      </appInfo>
<?php
if (!$modern_notation and $show_taxonomy_descriptions and $include_neume_class) {
  echo "      <classDecls>
        <taxonomy type=\"clivis\"> 
          <category xml:id=\"clivis.1\">
            <desc>A clivis composed of two glyphs, in the shape of two puncti.</desc>
          </category>
          <category xml:id=\"clivis.2\">
            <desc>A clivis composed of one glyph representing two descending ligated notes.</desc>
          </category>
        </taxonomy>
        <taxonomy type=\"pes\">
          <category xml:id=\"pes.1\">
            <desc>A pes composed to two gliphs aligned vertically.</desc>
          </category>
          <category xml:id=\"pes.2\">
            <desc>A pes composed to two gliphs positioned one after the other.</desc>
          </category>
          <category xml:id=\"align.vertical\">
            <desc>In a larger ligature, indicates a gliphs aligned vertically with respect to the preceding one.</desc>
          </category>
        </taxonomy>
        <taxonomy type=\"plica\"> 
          <category xml:id=\"plica.1\">
            <desc>A plica (or liquescent note) composed of a punctus 
            and two stems (or tails) positioned one on each side.</desc>
          </category>
          <category xml:id=\"plica.2\">
            <desc>A plica (or liquescent note) composed of a curved glyph 
            and two stems (or tails) positioned one on each side.</desc>
          </category>
          <category xml:id=\"plica.3\">
            <desc>A plica (or liquescent note) composed of a curved glyph 
            and one stems (or tail) positioned one on its left side.</desc>
          </category>
          <category xml:id=\"plica.4\">
            <desc>A plica (or liquescent note) composed of a curved glyph.</desc>
          </category>
          <category xml:id=\"plica.5\">
            <desc>An ascending plica (or liquescent note) composed of a punctus 
            and a  stem (or tail) with a right-upwards position.</desc>
          </category>
        </taxonomy>
        <taxonomy type=\"porrectus\"> 
          <category xml:id=\"porrectus.1\">
            <desc>A porrectus composed of two glyphs, the first of which represents two descending ligated notes, 
            and the following is in the shape of a punctus positioned on the left of a connecting line.</desc>
          </category>
          <category xml:id=\"porrectus.2\">
            <desc>A porrectus composed of two glyphs, the first of which represents two descending ligated notes, 
            and the following is in the shape of a punctus positioned on the right of a connecting line.</desc>
          </category>
          <category xml:id=\"porrectus.3\">
            <desc>A porrectus composed of three glyphs, 
            all of which are positioned on the right of the preceding one.</desc>
          </category>
        </taxonomy>
        <taxonomy type=\"torculus\"> 
          <category xml:id=\"torculus.1\">
            <desc>A torculus composed of three glyphs, 
            the second of which is aligned vertically with respect to the preceding one.</desc>
          </category>
          <category xml:id=\"torculus.2\">
            <desc>A torculus composed of three glyphs, 
            all of which are positioned on the right of the preceding one.</desc>
          </category>
        </taxonomy>
        <taxonomy type=\"climacus\"> 
          <category xml:id=\"climacus.1\">
            <desc>A climacus composed of three glyphs, 
            the first of which is in the shape of a virga, 
            and the following two are puncti inclinati.</desc>
          </category>
          <category xml:id=\"climacus.2\">
            <desc>A climacus composed of three glyphs 
            shaped as three connected puncti.</desc>
          </category>
          <category xml:id=\"climacus.3\">
            <desc>A climacus composed of two glyphs, 
            the first of which represents two  descending ligated notes 
            and the following is in the shape of a punctus.</desc>
          </category>
          <category xml:id=\"climacus.4\">
            <desc>A climacus composed of two glyphs, 
            the first of which is in the shape of a punctus 
            and the following represents two descending ligated notes.</desc>
          </category>
        </taxonomy>
        <taxonomy type=\"scandicus\"> 
          <category xml:id=\"scandicus.1\">
            <desc>A scandicus composed of three glyphs, 
            the second of which is aligned vertically with respect to the first, 
            and the third is positioned on the right of the preceding one.</desc>
          </category>
          <category xml:id=\"scandicus.2\">
            <desc>A scandicus composed of three glyphs, 
            the second of which is positioned on the right of the first, 
            and the third is aligned vertically with respect to the second.</desc>
          </category>
          <category xml:id=\"scandicus.3\">
            <desc>A scandicus composed of three glyphs, 
            all of which are positioned on the right of the preceding one.</desc>
          </category>
        </taxonomy>
      </classDecls>";
  
  } elseif (!$modern_notation and $include_neume_class) {

  echo "      <classDecls>
        <taxonomy type=\"clivis\"> 
          <category xml:id=\"clivis.1\"/>
          <category xml:id=\"clivis.2\"/>
        </taxonomy>
        <taxonomy type=\"pes\">
          <category xml:id=\"pes.1\"/>
          <category xml:id=\"pes.2\"/>
        </taxonomy>
        <taxonomy type=\"align.vertical\">
          <category xml:id=\"align.vertical\"/>
        </taxonomy>
        <taxonomy type=\"plica\"> 
          <category xml:id=\"plica.1\"/>
          <category xml:id=\"plica.2\"/>
          <category xml:id=\"plica.3\"/>
          <category xml:id=\"plica.4\"/>
          <category xml:id=\"plica.5\"/>
        </taxonomy>
        <taxonomy type=\"porrectus\"> 
          <category xml:id=\"porrectus.1\"/>
          <category xml:id=\"porrectus.2\"/>
          <category xml:id=\"porrectus.3\"/>
        </taxonomy>
        <taxonomy type=\"torculus\"> 
          <category xml:id=\"torculus.1\"/>
          <category xml:id=\"torculus.2\"/>
        </taxonomy>
        <taxonomy type=\"climacus\"> 
          <category xml:id=\"climacus.1\"/>
          <category xml:id=\"climacus.2\"/>
          <category xml:id=\"climacus.3\"/>
          <category xml:id=\"climacus.4\"/>
        </taxonomy>
        <taxonomy type=\"scandicus\"> 
          <category xml:id=\"scandicus.1\"/>
          <category xml:id=\"scandicus.2\"/>
          <category xml:id=\"scandicus.3\"/>
        </taxonomy>
      </classDecls>\n";
  }
?>    </encodingDesc>
    <workList>
      <work>
        <identifier><?php echo $id_input;?></identifier>
        <title><?php echo $title;?></title>
        <composer>
          <persName role="composer"><?php echo $author;?></persName>
        </composer>
        <langUsage>
          <language<?php 
          if    (strtolower($language) == "french")  {echo " xml:id=\"fr\"";}
          elseif(strtolower($language) == "spanish") {echo " xml:id=\"es\"";}
          elseif(strtolower($language) == "german")  {echo " xml:id=\"de\"";}
          elseif(strtolower($language) == "italian") {echo " xml:id=\"it\"";}
          echo ">";
          echo ucfirst($language);
          ?></language>
        </langUsage>
      </work>
    </workList>
    <manifestationList>
      <manifestation xml:id="<?php 
        if ($ms_input != "") {
          echo str_replace(' ', '', $ms_input);
        }else{
          echo "undefined_source";
        }
        ?>">
        <identifier><?php echo $ms_input;?></identifier>
<?php 
  $extended_name = get_extended_name($language, $ms_input);
  if ($extended_name != "") {
    echo "        <identifier>$extended_name</identifier>\n";
  }
?>
      </manifestation>
    </manifestationList>
  </meiHead>
  <music xmlns="http://www.music-encoding.org/ns/mei" meiversion="5.0">
    <body>
      <mdiv>
        <score>
          <scoreDef>
<?php
$s = "            ";
$wordpos = "s";

echo $s."<staffGrp n=\"1\">\n";

if ($currentStyle == 0) {
  $linesOldTotal = substr_count($notes, "'") + 1;
} else {
  $linesOldTotal = substr_count($notes, "\n") + 1;
}

$s = $s."  ";
for ($line_pre = 0; $line_pre < $linesOldTotal; $line_pre++) {
  if (!$modern_notation) {
    $current_n_of_lines_in_line = $lines_in_line[$line_pre];
  }else{
    $current_n_of_lines_in_line = 5;
  }
  echo $s."<staffDef";
  echo " n=\"".($line_pre+1)."\"";
  echo " lines=\"$current_n_of_lines_in_line\"";
  
  if (substr($notes_for_b_in_key_lines[0][$line_pre], 0, 2) == "b}") {
    echo " key.sig=\"f\"";
  }
        
  echo ">\n";
  $s = $s."  ";
  echo $s."<label></label>\n";
  $s = substr($s, 0, -2);
  echo $s."</staffDef>\n";
}
$s = substr($s, 0, -2);
echo $s."</staffGrp>\n";

echo "          </scoreDef>\n";
echo "          <section>\n";


  // ************************
  // start printing the music
  // ************************

  echo $s."<staff>\n";
  $s = $s."  ";
  echo $s."<layer n=\"1\">\n";
  $s = $s."  ";

  // for each musical line
  for ($line = 0; $line < $n_of_lines; $line++) {
    if ($metrical_line > 0) {
      echo $s."<sb";
      if (!$modern_notation){
        echo " type=\"metricalLineBreak\"";
      }
      echo "/>\n";
    }
    $metrical_line = $metrical_line + 1;
    // Clean line array
    $this_line_ar = rtrim($lines_ar[$line], " \|");
    $this_line_ar = trim($this_line_ar,"\|");
    $this_line_ar = ltrim($this_line_ar," ");

    // Create an array of all note groups in line
    $notes_ar = explode(' ', $this_line_ar);

    // Count note groups in line
    $n_of_note_groups = count($notes_ar);

    //reset keycount --- MA COSA SUCCEDE CON L'OLD???
    $key_count = 0;
    $index_group = 0;

    // for each note group
    for ($i = 0; $i < $n_of_note_groups; $i++) {
      $neume_name = "";
      $plica_asc = 0;
      $plica_disc = 0;
      $pes = 0;
      $clivis = 0;
      $climacus = 0;
      $scandicus = 0;
      $porrectus = 0;
      $group_type = "";
      $neume_accidental_sign = "";

      if (is_numeric($notes_ar[$i]) or $notes_ar[$i] == "*") {
        $key_count = $key_count + 1;
      }

      // convert MedMel syllable separation sign (dash) with MEI's (underscore)
      $this_syl = str_replace("-", "_", $text_ar[$index_group_continuous]);
      // convert placeholder for synalepha to space
      $this_syl = str_replace("+", " ", $text_ar[$index_group_continuous]);
    
      if ($notes_ar[$i] == "*") {
        if (!$modern_notation){
          // Take this chance to check custos
          if ($custos[$old_line] !== null) {
            $custos_info = getCustosPitch($clef_ref, $custos[$old_line]);
            echo $s."<custos pname=\"".$custos_info[0]."\" oct=\"".$custos_info[1]."\"/>\n";
          }
          
          echo $s."<sb type=\"msSystemBreak\"/>\n";
          $old_line = $old_line + 1;
          $old_index_group = 0;
        }
      }
    
      elseif (!is_numeric($notes_ar[$i])) {
        /*****logic of old for groups****/
        // Count notes in note group
        $n_of_notes_in_note_group[$i] = strlen($notes_ar[$i]);
        
        // 1 note
        if ($n_of_notes_in_note_group[$i] == 1) {
          if (!empty($stem_direction)) {
            if ($stem_direction[$old_line][$old_index_group][0] == 2) {
              $neume_name = "virga";
            } elseif ($stem_direction[$old_line][$old_index_group][0] == 0) {
              $neume_name = "punctum";
            }
          }
        }

        // 2 notes
        if ($n_of_notes_in_note_group[$i] == 2 and $Plica_notes_groups_ar[$old_line][$old_index_group][1] == 0) {
          
          // numeric value of first 2 notes
          $nn0 = note_number($notes_ar[$i][0]);
          $nn1 = note_number($notes_ar[$i][1]);

          if ($nn0 < $nn1) { // pes
            $pes = 1;
            $neume_name = "pes";
          } elseif ($nn0 > $nn1) { // clivis
            $clivis = 1;
            $neume_name = "clivis";
          }
        }
      
        if ($n_of_notes_in_note_group[$i] == 2 and $Plica_notes_groups_ar[$old_line][$old_index_group][1] == 1) {
          $neume_name = "plica";
          $plica = 1;
        } else {
          $plica = 0; // create variable just not to prmpt php non blocking error
        }

      // 3 notes
      // defone vars just not to prompt non blocking php errors
      $climacus = 0;
      $scandicus = 0;
      $porrectus = 0;
      $torculus = 0;
      if ($n_of_notes_in_note_group[$i] == 3) {
        // numeric value of first 3 notes
        $nn0 = note_number($notes_ar[$i][0]);
        $nn1 = note_number($notes_ar[$i][1]);
        $nn2 = note_number($notes_ar[$i][2]);

        if ($nn0 > $nn1 and $nn1 > $nn2){
          $climacus = 1;
          $neume_name = "climacus";
        }
        elseif ($nn0 < $nn1 and $nn1 < $nn2){
          $scandicus = 1;
          $neume_name = "scandicus";
        }
        elseif ($nn0 > $nn1 and $nn1 < $nn2) {
          $porrectus = 1;
          $neume_name = "porrectus";
        }
        elseif ($nn0 < $nn1 and $nn1 > $nn2) {
          $torculus = 1;
          $neume_name = "torculus";
        }
      }

      //-----Parameters-----------
      // fecth standards
      if ($shapeGroupNote[$old_line][$old_index_group] == 0) {
        if ($pes == 1) {
          $pes_type = $pes_type_default;
          $group_type = $pes_type_default;
        }
        elseif ($clivis == 1) {
          $clivis_type = $clivis_type_default;
          $group_type = $clivis_type_default;
        }
        elseif ($plica == 1) {
          $plica_type = $plica_type_default;
          $group_type = $plica_type_default;
        }
        elseif ($climacus == 1) {
          $climacus_type = $climacus_type_default;
          $group_type = $climacus_type_default;
        }
        elseif ($porrectus == 1) {
          $porrectus_type = $porrectus_type_default;
          $group_type = $porrectus_type_default;
        }
        elseif ($scandicus == 1) {
          $scandicus_type = $scandicus_type_default;
          $group_type = $scandicus_type_default;
        }
      }

      if ($torculus == 1) {
        if ($shape_single_note[$old_line][$old_index_group][0] == 1) {
          $group_type = 1;
        }
        if ($shape_single_note[$old_line][$old_index_group][0] === null) {
          $group_type = 2;
        }
        if ($shape_single_note[$old_line][$old_index_group][1] == 2) {
          $group_type = 3;
        }
      }

      // note group param
      if ($shapeGroupNote[$old_line][$old_index_group] == 1) {
        $pes_type = 1;
        $porrectus_type = 1;
        $scandicus_type = 1;
        $plica_type = 1;
        $climacus_type = 1;
        $torculus_type = 1;
        $clivis_type = 1;
        $group_type = 1;
      }
      elseif ($shapeGroupNote[$old_line][$old_index_group] == 2) {
        $pes_type = 2;
        $climacus_type = 2;
        $porrectus_type = 2;
        $scandicus_type = 2;
        $plica_type  = 2;
        $clivis_type = 2;
        $torculus_type = 2;

        $group_type = 2;
      }
      elseif ($shapeGroupNote[$old_line][$old_index_group] == 3) {
        $pes_type = 3;
        $climacus_type =3;
        $scandicus_type = 3;
        $plica_type = 3;
        $clivis_type = 0;
        $porrectus_type = 3;

        $group_type = 3;
      }
      if ($shapeGroupNote[$old_line][$old_index_group] == 4) {
        $climacus_type =4;
        $plica_type = 4;

        $group_type = 4;
      }
      if ($shapeGroupNote[$old_line][$old_index_group] == 5) {
        $plica_type = 5;

        $group_type = 5;
      }
      /*********end logic of old for groups********/

      // Brackets embracing the entire syllable (opening)
      $brackets = $Brackets_notes_groups_ar[$old_line][$old_index_group];
      $brackets_type = get_brackets_type($Brackets_notes_groups_ar[$old_line][$old_index_group]);
      $external = $brackets_type[0];
      $internal = $brackets_type[1];
      $supplied_external_opening_tag = $brackets_type[2];
      $supplied_external_closing_tag = $brackets_type[3];
      $supplied_internal_opening_tag = $brackets_type[4];
      $supplied_internal_closing_tag = $brackets_type[5];
      
      // supply external opening tag in cases like ...]
      $type_supplied_tags = "";
      if ($external and 
          ($supplied_external_opening_tag 
          or $brackets[0] == "1" 
          or $brackets[0] == "3")
        ) {
        
        if ($supplied_external_opening_tag) {
          $type_supplied_tags =" type=\"suppliedExternalOpeningTag\"";
        }
        if ($supplied_external_closing_tag) {
          $type_supplied_tags = $type_supplied_tags." type=\"suppliedExternalClosingTag\"";
        }
        
        echo $s."<corr".$type_supplied_tags.">\n";
        $s = $s."  ";
      }
      
      list($current_syl, $wordpos) = get_word_position($this_syl, $wordpos);
      if ($current_syl !== "") {
        $wordpos_attribute = " wordpos=\"$wordpos\"";
      }

      echo $s."<syllable>\n";
      $s = $s."  ";
      echo $s."<syl$wordpos_attribute>";
      echo $current_syl;
      echo "</syl>\n";
      
      // Accidental element
      $pos_of_flat = strpos($Flat_notes_groups_ar[$line][$index_group], 1);
      
      // B|b|+b
      // if ($keySig != "f" 
      // and $neume_accidental_sign != "f" 
      // and preg_match('/[bBp]/', $notes_ar[$i])) {
      //   $loc = get_accidental_loc($lines_in_line[$line], $notes_ar[$i][$pos_of_flat], $clef_ref);
      //   echo $s."<accid accid=\"f\" loc=\"$loc\"/>\n";
      //   $neume_accidental_sign = "f";
      // }
      // // _b|_B|_+b (forced accidental sign even when keySig is already active)
      // elseif ($keySig == "f" 
      // and $pos_of_flat !== false
      // and preg_match('/[bBp]/', $notes_ar[$i][$pos_of_flat])) {
      //   $loc = get_accidental_loc($lines_in_line[$line], $notes_ar[$i][$pos_of_flat], $clef_ref);
      //   if (!$modern_notation) {
      //     $loc_attr = " loc=\"$loc\"";
      //   }
      //   echo $s."<accid accid=\"f\"$loc_attr/>\n";
      //   $neume_accidental_sign = "f";
      // }
      // // 
      // if (preg_match('/_[JACDEFGacdefgur]/', $notes_ar[$i][$pos_of_flat])) {
      //     // Left @corresp in favour of @loc. This means that all are treated the same. Anyway, for modernNotation there is no way to say the @loc
      //     // We should then only use @corresp
      // 
      //     if ($modern_notation) {
      //       list($pname, $oct) = get_pname_and_oct($notes_ar[$i][$pos_of_flat]);
      //       echo $s."<accid accid=\"f\" corresp=\"#".$pname.$oct."_".$line.".".$index_group.".".$pos_of_flat."\"/>\n";
      //     }else{
      //       $loc = get_accidental_loc($lines_in_line[$line], $notes_ar[$i][$pos_of_flat], $clef_ref);
      //       if (!$modern_notation) {
      //         $loc_attr = " loc=\"$loc\"";
      //         echo $s."<accid accid=\"f\"$loc_attr/>\n";
      //       }
      //     }
      
      if (
        ($keySig != "f" // the b flat is not already speficied in the keySignature
        and $neume_accidental_sign != "f" // If the alteration has not already been specified in the same neume
        and preg_match('/[bBp]/', $notes_ar[$i])) // If it is a b flat in the neume
        OR
        ($keySig == "f" // Key signature is specified, but there is an redundant accidental (_b)
        and $pos_of_flat !== false
        and preg_match('/[bBp]/', $notes_ar[$i][$pos_of_flat]))
        OR
        ($pos_of_flat !== false 
        and preg_match('/[JAHCDEFGahcdefguqr]/', $notes_ar[$i][$pos_of_flat])) // a note that is not b is flat
      ) {
        $func_attr = "";
        if (preg_match('/[Hhq]/', $notes_ar[$i][$pos_of_flat])) { // _h
          // add edit to func attr (still uncertain wheter to use <supplied>)
          $func_attr = " func=\"edit\"";
        }else if (preg_match('/_[bBp]/', $notes_ar[$i][$pos_of_flat])) {
          $neume_accidental_sign = "f";
        }
        if ($modern_notation) { // don't know how to fix this, it's a mess
          $specifying_attr = " corresp=\"#".$pname.$oct."_".$line.".".$i.".".$pos_of_flat."\"";
        }else{
          $loc = get_accidental_loc($lines_in_line[$line], $notes_ar[$i][$pos_of_flat], $clef_ref);
          $specifying_attr = " loc=\"$loc\"";
        }
        echo $s."<accid accid=\"f\"".$specifying_attr.$func_attr."/>\n";
      }
    
    
      // Da aggiustare
      elseif($keySig != "s" and preg_match('/[#]/', $notes_ar[$i])) {
        echo $s."<accid accid=\"s\"/>\n";
      // DA aggiustare
      } elseif($keySig != "n" and preg_match('/[%]/', $notes_ar[$i])) {
        echo $s."<accid accid=\"s\"/>\n";
      }
      
      // Start printing <neume>
      echo $s."<neume";
      
      if ($neume_name != "" 
      and !$modern_notation
      and $include_neume_type) {
        echo " type=\"$neume_name\"";
      }
      if ($group_type != "" 
      and !$modern_notation 
      //and $n_of_notes_in_note_group[$i] <= 3
      and $include_neume_class) {
          echo " class=\"#$neume_name.$group_type\"";          
      }
      echo ">\n";
      $s = $s."  ";
      
      // supply internal opening tag for cases like ..]..
      if ($supplied_internal_opening_tag) {
        $type_supplied_tags = "";
        if ($supplied_internal_closing_tag) {
          $type_supplied_tags = " suppliedInternalClosingTag";
        }
        echo $s."<corr type=\"suppliedInternalOpeningTag".$type_supplied_tags."\">\n";
        $s = $s."  ";
      }

      // Loop through notes in note group
      for ($x = 0; $x < $n_of_notes_in_note_group[$i]; $x++) {
        
        // Check internal brackets (encoded as <corr>)
        if ($internal and ($brackets[$x] == "1" || $brackets[$x] == "3")) {
          if ($supplied_internal_closing_tag and $medmelCompatible) {
            $type_supplied_tags = " type=\"suppliedInternalClosingTag\"";
          }
          echo $s."<corr".$type_supplied_tags.">\n";
          $s = $s."  ";
        }
        
        $note_group_ar = str_split($notes_ar[$i], 1);
        $stem_bottom_left = "";
        $stem_bottom_right = "";
        $stem_top_right = "";
        $stem_top_left = "";
        $stem = "";

        /* Logic old for single notes */
        $this_nn = note_number($note_group_ar[$x]);
        if ($x + 1 < count($note_group_ar)) {
            $next_nn = note_number($note_group_ar[$x + 1]);
        } else {
            // Handle the case when there is no next element
            $next_nn = null;  // Or any fallback logic
        }
        if ($x - 1 > count($note_group_ar)) {
            $previous_nn = note_number($note_group_ar[$x - 1]);
        } else {
            // Handle the case when there is no next element
            $previous_nn = null;  // Or any fallback logic
        }

        $diff_this_next = $this_nn-$next_nn;
        $diff_previous_this = $previous_nn-$this_nn;

        //  Plica?  -- the array of plicas doesn't include keys, but $i does so we have to subtract the number of precendent keys in the.
        // $plica = $Plica_notes_groups_ar[$line][$i-$n_of_keys_in_line][$x];
        if ($Plica_notes_groups_ar[$old_line][$old_index_group][$x] == 1) {
          continue;
        }
        // Is next note a plica?
        $plica_next = $Plica_notes_groups_ar[$old_line][$old_index_group][$x+1];
         if ($plica_next == 1) {
           if ($this_nn > $next_nn) {
             $plica_disc = 1;
           } else {
             $plica_asc = 1;
           }
         }

        // single note param
        $plica_type_next = $shape_single_note[$old_line][$old_index_group][$x];
        // echo "line: $line, old_line: $old_line, index_group: $index_group, old_index_group: $old_index_group, x: $x  (i:$i)\n";
        // plica type note level
        $plica_type_single_note = "";
        if     ($plica_type_next == 5){$plica_type_single_note = 1;}
        elseif ($plica_type_next == 6){$plica_type_single_note = 2;}
        elseif ($plica_type_next == 7){$plica_type_single_note = 3;}
        elseif ($plica_type_next == 8){$plica_type_single_note = 4;}
        if ($plica_type_single_note != ""){
          // echo " plica_type_single_note: $plica_type_single_note ";
        }

        // STEMS
        // Get stem info
        if ($stem_direction[$old_line][$old_index_group][$x] == 0) { // if stem "0" or ""
          $stem_bottom_left = 0;
          $stem_bottom_right = 0;
          $stem_top_right = 0;
          $stem_top_left = 0;
          
          if ($stem_direction[$old_line][$old_index_group][$x] === 0) { // if stem "0"
            $stem = "none";
          }
        }
        elseif ($stem_direction[$old_line][$old_index_group][$x] == 1) {
          $stem_bottom_left = 1;
        }
        elseif ($stem_direction[$old_line][$old_index_group][$x] == 2) {
          $stem_bottom_right = 1;
        }
        elseif ($stem_direction[$old_line][$old_index_group][$x] == 3) {
          $stem_top_right = 1;
          $plica_type = 1;
        }
        elseif ($stem_direction[$old_line][$old_index_group][$x] == 4) {
          $stem_top_left = 1;
        }

        // get connecting line param
        if (is_null($connecting_line_param[$line][$index_group][$x]) or $connecting_line_param[$line][$index_group][$x] == 1) {
          $connecting_line = 1;
        } else {
          $connecting_line = 0;
        }

        // default values for stem in particular groups : clivis_type = 1;[should be all clivis] porrectus
        // this is overridden by shape parameters
        if ((($clivis == 1 and $x == 0 and $plica_next != 1)
          or ($porrectus == 1 and $x == 0)
          or ($plica_next == 1 and $plica_disc == 1 and $plica_type !=4)
          or ($climacus == 1 and $x == 0 and $climacus_type == 2))
          and is_null($stem_direction[$old_line][$old_index_group][$x])) {
            $stem_bottom_left = 1;
        }
        
        // default values for stem in particular groups : climacus (overridden by more specific info)
        if (($climacus == 1 and $x == 0 and $climacus_type == 1)
          and is_null($stem_direction[$old_line][$old_index_group][$x])) {
            $stem_bottom_right = 1;
        }
        
        // default values for stem in particular groups : plica (overridden by more specific info)
        if  (($plica_type != 4 
          and $plica_next == 1 
          and $plica_asc == 1)
          and is_null($stem_direction[$old_line][$old_index_group][$x])) {
            $stem_top_left = 1;
        }

        // get shape parameters from external file
        $single_note_shape = $shape_single_note[$old_line][$old_index_group][$x];
        $single_note_shape_next = $shape_single_note[$old_line][$old_index_group][$x+1];
        $note_shape_previous = $shape_single_note[$old_line][$old_index_group][$x-1];
        $note_shape_minus2 = $shape_single_note[$old_line][$old_index_group][$x-2];
        /******end logic old for single notes******/
        

        echo $s."<nc";
        
        list($pname, $oct) = get_pname_and_oct($note_group_ar[$x]);
        echo " pname=\"$pname\" oct=\"$oct\"";
        
        // accidentals
        $Natural = $Natural_notes_groups_ar[$line][$index_group][$x];
        if ($Natural == 1) {
          if ($modern_notation) {
            echo " xml:id=\"".$pname.$oct."_".$line.".".$index_group.".".$x."\"";
          }
          echo " accid.ges=\"n\"";
        }
        

        $Sharp = $Sharp_notes_groups_ar[$line][$index_group][$x];
        if ($Sharp == 1) {
          if ($modern_notation) {
            echo " xml:id=\"".$pname.$oct."_".$line.".".$index_group.".".$x."\"";
          }
          echo " accid.ges=\"s\"";
        }

        $Flat = $Flat_notes_groups_ar[$line][$index_group][$x];
        // I have commented this, so that all flat, including b, in modern get an xml:id, used by the <accid@corresp>
        if ($note_group_ar[$x] == "b" 
        or $note_group_ar[$x] == "B" 
        or $note_group_ar[$x] == "p"
        or $Flat == 1) {
          if ($modern_notation) {
            echo " xml:id=\"".$pname.$oct."_".$line.".".$index_group.".".$x."\"";
          }
          echo " accid.ges=\"f\"";
        }
        
        
        if ($plica_asc) {
          echo " curve=\"a\"";
        } elseif ($plica_disc) {
          echo " curve=\"c\"";
        }

        // Set stem positions
        if ($stem_bottom_left == 1) {
            $stem = 'bottomLeft';
        }
        if ($stem_top_left == 1) {
            $stem = 'topLeft';
        }
        if ($stem_top_right == 1) {
            $stem = 'topRight';
        }
        if ($stem_bottom_right == 1) {
            $stem = 'bottomRight';
        }

        // note shapes
        $noteShape = 0;
        if ($single_note_shape == 1) {
          if (!$modern_notation and $include_neume_class){
            // echo " class=\"#pes.vertical.start\""; // I'bve decided that this is redundant
          }
        } elseif ($single_note_shape == 2) {
          $noteShape = "firstNoteOfFlag";
        } elseif ($single_note_shape == 3) {
          $noteShape = "punctumInclinatum";
        } elseif ($note_shape_previous == 1) {
          if (!$modern_notation and $include_neume_class){
            echo " class=\"#align.vertical\"";
          }
        }

        // calculate standards
        // @ligated for 
        // 1) clivis (ligated); 
        // 2) Climacus 3 and 4; 
        // 3) notes that are explicitely encoded as ligated (single_note_shape = 2)
        if (($clivis == 1 and $clivis_type == 1 and $x == 1)
          or ($climacus == 1 and $climacus_type == 3 and $x == 1)
          or ($climacus == 1 and $climacus_type == 4 and $x == 2)
          or ($note_shape_previous == 2 and $diff_previous_this > 0)) 
        {
          $noteShape = "secondNoteOfFlag";
        }
        elseif (
            (
             (
              ($clivis == 1 and $clivis_type == 1 and $plica_next != 1)
               or ($climacus == 1 and $climacus_type == 3))
              and $x == 0
            )
            or ($climacus == 1 and $climacus_type == 4 and $x == 1)
            or ($single_note_shape == 2 and $diff_this_next > 0)
            ) 
        {
          $noteShape = "firstNoteOfFlag";
        }

        // flag of porrectus
        elseif ($porrectus == 1 and ($porrectus_type == 1 or $porrectus_type == 2) and $x < 2) {
          if ($x == 1) {
            $noteShape="secondNoteOfFlag";
          }
           elseif ($x == 0) {
            $noteShape="firstNoteOfFlag";
          }
        }
        // If the note is not ligated
        // DANGEROUS OPERATION: all this until "else {$noteShape = "";}" was wrapped in an else stmt. But I think it shoudl flow as a concatenation of elseif.
        
        // punctum inclinatum of climacus
        elseif (($climacus == 1 and $climacus_type == 1 and $x != 0) or ($single_note_shape == 3)) {
          $noteShape = "punctumInclinatum";
        }
        // // Print Plica
        // elseif ($plica == 1){
        //   //nota liquescente
        //   $stem="bottomRight bottomLeft";
        // }
        // plica descentant
        elseif ($plica_next == 1 and $plica_disc == 1) {  
          // plica
          if ($plica_type == 2 or $plica_type == 1 and ($plica_type_single_note != 4 and $plica_type_single_note != 3)){
            $stem = "bottomRight bottomLeft";
          } elseif ($plica_type_single_note == 3 or $plica_type == 3) {
            $stem = "bottomLeft";
          }
        }
        // plica ascendant
        elseif ($plica_next == 1 and $plica_asc == 1) {
          if ($plica_type == 2 or $plica_type == 7 or $plica_type == 8){
            // plica un po' piegata
             if ($plica_type == 2) {
                $stem = "topRight topLeft";
             }
           } else {
             if ($plica_type == 5){
                
             }
             if ($plica_type == 4){
             }
            // if plica_type == 1
            $stem = "topRight";
           }
        } else {
          $noteShape = "";
        }
        
        // Print note shape attributes @ligated, @tilt, and @con
        if ($noteShape !== "" and !$modern_notation) {
          if ($noteShape == "firstNoteOfFlag" or $noteShape == "secondNoteOfFlag") {
            echo " ligated=\"true\"";
          }
          elseif ($noteShape == "punctumInclinatum") {
            if (!($plica_next == 1 && $plica_type != 1)){
              echo " tilt=\"se\"";
            }
            if ($plica_next != 1) {
              echo " con=\"g\"";
            }
          }
        }
        
        // Print stems
        if ($stem != "" and !$mei_compliance and !$modern_notation){
          if ($stem == "topLeft") {
            echo " stem.pos=\"left\" stem.dir=\"up\"";
          } elseif ($stem == "topRight") {
            echo " stem.pos=\"right\" stem.dir=\"up\"";
          } elseif ($stem == "bottomLeft") {
            echo " stem.pos=\"left\" stem.dir=\"down\"";
          } elseif ($stem == "bottomRight") {
            echo " stem.pos=\"right\" stem.dir=\"down\"";
          } elseif ($stem =="none"){
            // echo " stem=\"none\"";
          }
        }

        // Print @class of plica
        if ($plica_type_single_note != "" and $Plica_notes_groups_ar[$old_line][$old_index_group][$x+1] == 1 
        and !$modern_notation 
        and !$mei_compliance) {
          echo " class='#plica.$plica_type_single_note'";
        }

        // Retrieve Connecting line info and print it
        $connecting_line = $connecting_line_param[$old_line][$old_index_group][$x];
        if ($connecting_line === 0 and !$modern_notation) {
          echo " con=\"g\"";
        }

        // Plica check
        $liquescent = false;
        if ($Plica_notes_groups_ar[$old_line][$old_index_group][$x+1] == 1) {
          $liquescent = true;
        }
        
        if ($liquescent) {
          // close opening nc tag
          echo ">\n";
          if ($liquescent) {
            echo $s."  <liquescent/>\n";
          }

          echo $s."</nc>\n";
        } else {
          // self closing tag for nc
          echo "/>\n";
        }
        
        // Close internal <corr>
        if ($internal and ($brackets[$x] == "2" || $brackets[$x] == "3")) {
          $s = substr($s, 0, -2);
          echo $s."</corr>\n";
        }
      }
      // end operation for single note in note group
      
      // Close internal correction tag
      if ($supplied_internal_closing_tag) {
        $s = substr($s, 0, -2);
        echo $s."</corr>\n";
      }
      
      // Close syllable
      $s = substr($s, 0, -2);
      echo $s."</neume>\n";
      $s = substr($s, 0, -2);
      echo $s."</syllable>\n";

      // Close external correction tag
      if ($external and (substr($brackets, -1) == "2" || substr($brackets, -1) == "3" || $supplied_external_closing_tag)) {
        $s = substr($s, 0, -2);
        echo $s."</corr>\n";
      }
  
      //** Divisiones (divLine) **// 
      // Check if there is a bar corresponding to the syllable
      if ($bar[$old_line][$old_index_group][1] != 0 
      and !$modern_notation) {
        // Unpack information about divisio
        $divLine_info = $bar[$old_line][$old_index_group];
        $divLine_start = $divLine_info[0];
        $divLine_end = $divLine_info[1];
        $divLine_length = abs($divLine_start - $divLine_end);
        
        // Check if it's special divLine
        $divLine_additional_info = $divLine_info[2]; // 0 = single | 1 = double | 2 = point | 3 = double+point
        $divLine_double = false;
        $divLine_point = false;
        if ($divLine_additional_info == 1) {
          $divLine_double = true;
        } elseif ($divLine_additional_info == 2) {
          $divLine_point = true;
        } elseif ($divLine_additional_info == 3) {
          $divLine_double = true;
          $divLine_point = true;
        }
        
        // Translate from MedMel coordinates system (from top line of staff) to MEI's (from bottom line of staff)
        $divLineLoc_start = get_divLine_loc($lines_in_line[$old_line], $divLine_start);
        $divLineLoc_end = get_divLine_loc($lines_in_line[$old_line], $divLine_end);
        
        // Transform exact length to approximate fixed lengths
        if ($divLine_length > 0 and $divLine_length < 1.5) {
          $form = "minima";
        } elseif ($divLine_length >= 1.5 and $divLine_length <= 2.5) {
          $form = "maior";
        } elseif ($divLine_length > 2.5) {
          $form = "maxima";
        }
        
        // Print divLine
        echo $s."<divLine";
        
        // The use of @form and @loc limits the customization of the divLines
        // but covers almost all scenarios. @start and @end offer more flexibility
        if ($mei_compliance) {
          echo " form=\"$form\" loc=\"$divLineLoc_start\"";
        } else {
          echo " start=\"".$divLineLoc_start."\" end=\"".$divLineLoc_end."\"";
        }
        echo "/>\n";
        
        // Print double line
        if ($divLine_double) {
          echo $s."<divLine";
          if ($mei_compliance) {
            echo " form=\"$form\" loc=\"$divLineLoc_start\"";
          }else{
            echo " start=\"".$divLineLoc_start."\" end=\"".$divLineLoc_end."\"";
          }
          echo "/>\n";
        }
        // Print point
        if ($divLine_point) {
          echo $s."<point/>\n";
        }
      } // End divLine
      
      // Increment number of index_group (of $line? of $i?)
      $index_group = $index_group + 1;
      // Increment number of old_index_group (of $old_line)
      $old_index_group = $old_index_group + 1;
      // Increment absolute number of index group (used?)
      $index_group_continuous = $index_group_continuous + 1;
    
    } else {
      // Else is numeric (e.g. clef or keySig)
      $clef_ar = str_split($notes_ar[$i], 2);
      $clef_ar = array_map('intval', $clef_ar);
      for ($r = 0; $r < count($clef_ar); $r++) {
        echo "                ";
        if ($clef_ar[$r] == 1) {
          echo "<clef shape=\"C\" line=\"".get_MEI_clef_position(1,$lines_in_line[$old_line])."\"/>\n"; 
          $clef_ref=-3;
        }
        elseif ($clef_ar[$r] == 2) {
          echo "<clef shape=\"C\" line=\"".get_MEI_clef_position(2,$lines_in_line[$old_line])."\"/>\n"; 
          $clef_ref=-5;}
        elseif ($clef_ar[$r] == 3) {
          echo "<clef shape=\"C\" line=\"".get_MEI_clef_position(3,$lines_in_line[$old_line])."\"/>\n"; 
          $clef_ref=-7;
        }
        elseif ($clef_ar[$r] == 4) {
          echo "<clef shape=\"C\" line=\"".get_MEI_clef_position(4,$lines_in_line[$old_line])."\"/>\n";
          $clef_ref=-9;}
        elseif ($clef_ar[$r] == 5) {
          echo "<clef shape=\"F\" line=\"".get_MEI_clef_position(1,$lines_in_line[$old_line])."\"/>\n";
          $clef_ref=1;}
        elseif ($clef_ar[$r] == 6) {
          echo "<clef shape=\"F\" line=\"".get_MEI_clef_position(2,$lines_in_line[$old_line])."\"/>\n";
          $clef_ref=-1;}
        elseif ($clef_ar[$r] == 7) {
          echo "<clef shape=\"F\" line=\"".get_MEI_clef_position(3,$lines_in_line[$old_line])."\"/>\n";
          $clef_ref=-3;}
        elseif ($clef_ar[$r] == 8) {
          echo "<clef shape=\"F\" line=\"".get_MEI_clef_position(4,$lines_in_line[$old_line])."\"/>\n";
          $clef_ref=-5;}
        elseif ($clef_ar[$r] == 9) {
          echo "<clef shape=\"F\" line=\"".get_MEI_clef_position(5,$lines_in_line[$old_line])."\"/>\n";
          $clef_ref=-7;}
        elseif ($clef_ar[$r] == 10) {
            $keySig = "f";
            // If accidental is after a clef, or at the beginning of the line
            // At the end I decided that b} and h} are keySig, 
            // if the user doesn't want to use them, they can use _[note] %[note]
            //if ($r > 0 || $i == 0) {
              echo "<keySig accid=\"f\"/>\n"; 
            // }else{
            //   echo "<accid accid=\"f\"/>\n"; 
            // }
        } elseif ($clef_ar[$r] == 11) {
            $keySig = "n";
            //if ($r > 0 || $i == 0) {
              echo "<keySig accid=\"n\"/>\n"; 
            // }else{
            //   echo "<accid accid=\"n\"/>\n"; 
            // }
        }
      }
    }
  } // End operations note group
} // End line
echo "              </layer>\n";
if ($annotations) {
  echo "              <annot>$annotations</annot>\n";
}
echo "            </staff>\n";
echo "          </section>\n";
echo "        </score>\n";
echo "      </mdiv>\n";
echo "    </body>\n";
echo "  </music>\n";
echo "</mei>";

function get_brackets_type($brackets_group) {
  if (strpos("1", $brackets_group) == false && !strpos("2", $brackets_group) == false && !strpos("3", $brackets_group) == false){
    return [false, false, false, false, false, false];
  }
  if ($brackets_group == 3) {
    $external = true;
    $internal = false;
    $supplied_external_opening_tag = false;
    $supplied_external_closing_tag = false;
    $supplied_internal_opening_tag = false;
    $supplied_internal_closing_tag = false;
    return [$external, $internal, $supplied_external_opening_tag, $supplied_external_closing_tag, $supplied_internal_opening_tag, $supplied_internal_closing_tag];
  }
  
  $initial = false;
  $final = false;
  $state = ""; // open | closed
  $closed_before_opening = false;
  $internally_open = false;
  $internally_closed = false;
  $external = false;
  $internal = false;
  $supplied_external_opening_tag = false;
  $supplied_external_closing_tag = true;
  $supplied_internal_opening_tag = false;
  $supplied_internal_closing_tag = false;
        
  /* check: 
  1) if closes or stays open 
  2) if closes before opening 
  3) if it opens internally 
  4) if it closes internally
  5) if starts with brackets
  6) if closes with brakets
  */
  $last_i = strlen(strval($brackets_group))-1;
      
  for ($i = 0; $i < strlen(strval($brackets_group)); $i++){
    if (($state == "closed" || $state == "") and $brackets_group[$i] == "1") { 
      $state = "open";
      if ($i == 0) {
        $initial = true; // [...
      } else {
        $internally_open = true; // ..[..
      }
    } 
    elseif ($state == "open" and $brackets_group[$i] == "2") { // ...[...]...
      $state = "closed";
      if ($i == $last_i) {
        $final = true; // ...[...] or [...]
      }else{
        $internally_closed = true;
      }
    } 
    elseif ($state == "" and $brackets_group[$i] == "2") { //..]...
      $closed_before_opening = "true"; 
      if ($i == $last_i) {
        $final = true; // ...[...] or [...]
      }else{
        $internally_closed = true;
      }
    }  
    elseif ($brackets_group[$i] == "3") {
      $state = "closed";
      $internally_closed = true;
    } else {
      // it means that $brackets_group[$i] == 0
    }
  }
          
  if ($state == "open") {
    if ($initial == true and $internally_open == false) { // "[..."
      $external = true;
      $internal = false;
      $supplied_external_opening_tag = false;
      $supplied_external_closing_tag = true;
      $supplied_internal_opening_tag = false;
      $supplied_internal_closing_tag = false;

    } elseif ($initial == true and $internally_open == true) { // "[..]..[.."    this and the next one are the same 
      $external = false;        
      $internal = true;
      $supplied_external_opening_tag = false;
      $supplied_external_closing_tag = false;
      $supplied_internal_opening_tag = false;
      $supplied_internal_closing_tag = true;
      
    } elseif ($initial == false and $internally_open == true and $closed_before_opening == false) { // "..[.."
      $external = false;
      $internal = true;
      $supplied_external_opening_tag = false;
      $supplied_external_closing_tag = false;
      $supplied_internal_opening_tag = false;
      $supplied_internal_closing_tag = true;
      
    } elseif ($initial == false and $internally_open == true and $closed_before_opening == true) { // ".].[.."
      $external = false;
      $internal = true;
      $supplied_external_opening_tag = false;
      $supplied_external_closing_tag = false;
      $supplied_internal_opening_tag = true;
      $supplied_internal_closing_tag = true;
    } 
  } elseif ($state == "closed" and $closed_before_opening == false) {
    if ($initial and $final and $internally_open == false) { // [...]
      $external = true;
      $internal = false;
      $supplied_external_opening_tag = false;
      $supplied_external_closing_tag = false;
      $supplied_internal_opening_tag = false;
      $supplied_internal_closing_tag = false;
    }else{ //  [...]... or ...[...] or ...[...]... or ...[...]..[..]...
      $external = false;
      $internal = true;
      $supplied_external_opening_tag = false;
      $supplied_external_closing_tag = false;
      $supplied_internal_opening_tag = false;
      $supplied_internal_closing_tag = false;
    }
  }
  elseif ($closed_before_opening) {
    if ($final == true and $internally_closed == false) { // "...]"
      $external = true;
      $internal = false;
      $supplied_external_opening_tag = true;
      $supplied_external_closing_tag = false;
      $supplied_internal_opening_tag = false;
      $supplied_internal_closing_tag = false;
    } elseif ($final == true and $internally_closed == true) { // "..]..[..]" // this and the next have the same and are redundant, but I leave it so it's clearer
      $external = false;
      $internal = true;
      $supplied_external_opening_tag = false;
      $supplied_external_closing_tag = false;
      $supplied_internal_opening_tag = true;
      $supplied_internal_closing_tag = false;
    } elseif ($final == false and $internally_closed == true) { // "..].."
      $external = false;
      $internal = true;
      $supplied_external_opening_tag = false;
      $supplied_external_closing_tag = false;
      $supplied_internal_opening_tag = true;
      $supplied_internal_closing_tag = false;
    }
  }
  return [$external, $internal, $supplied_external_opening_tag, $supplied_external_closing_tag, $supplied_internal_opening_tag, $supplied_internal_closing_tag];
}
        
        
function getCustosPitch($key, $cus){
  $n = ""; // necessary?
  $res = $cus+$key;
  if ($res ==-9) {$p="c";$o="5";}
  elseif ($res ==-8) {$p="b";$o="4";}
  elseif ($res ==-7) {$p="a";$o="4";}
  elseif ($res ==-6) {$p="g";$o="4";}
  elseif ($res ==-5) {$p="f";$o="4";}
  elseif ($res ==-4) {$p="e";$o="4";}
  elseif ($res ==-3) {$p="d";$o="4";}
  elseif ($res ==-2) {$p="c";$o="4";}
  elseif ($res ==-1) {$p="b";$o="3";}
  elseif ($res == 0) {$p="a";$o="3";}
  elseif ($res == 1) {$p="g";$o="3";}
  elseif ($res == 2) {$p="f";$o="3";}
  elseif ($res == 3) {$p="e";$o="3";}
  elseif ($res == 4) {$p="d";$o="3";}
  elseif ($res == 5) {$p="c";$o="3";}
  elseif ($res == 6) {$p="a";$o="2";}
  elseif ($res == 7) {$p="g";$o="2";}
  
  return [$p, $o];
}

function get_MEI_clef_position($pos,$lines){
  return $lines - $pos + 1;
}

function get_extended_name($language, $ms_input) {
  // Import database connection property
  require_once('serverConfig.php');

  // Securely prepare the query
  $query = "
  SELECT extendedName
  FROM mm_extended_name
  WHERE language = :language AND BINARY ms = :ms_input
  ";
  $check = $pdo->prepare($query);

  // Bind parameters to prevent SQL injection
  $check->bindParam(':language', $language, PDO::PARAM_STR);
  $check->bindParam(':ms_input', $ms_input, PDO::PARAM_STR);

  // Execute the prepared statement
  $check->execute();

  // Fetch the result correctly
  while ($row = $check->fetch(PDO::FETCH_ASSOC)) {  
    // Update the extendedName variable
    $extendedName = $row["extendedName"];
  }
  // Close the connection
  $pdo = null;
  // End getting ms extended name
  return   $extendedName;
}

function adapt_annotations($annotations) {
  $annotations = str_replace("<i>", "<rend rend=\"italic\">", $annotations);
  $annotations = str_replace("</i>", "</rend>", $annotations);
  return $annotations;
}

function get_pname_and_oct($note) {
  if ($note === "J") {
  return ["g","2"];
  }elseif($note === "A") {
  return ["a","2"];
  }elseif ($note === "H" || $note === "B") {
    return ["b","2"];
  }elseif ($note === "C") {
    return ["c","3"];
  }elseif($note === "D") {
    return ["d","3"];
  }elseif($note === "E") {
    return ["e","3"];
  }elseif($note === "F") {
    return ["f","3"];
  }elseif($note === "G") {
    return ["g","3"];
  }elseif($note === "a") {
    return ["a","3"];
  }elseif($note === "h" || $note === "b") {
    return ["b","3"];
  }elseif($note === "c") {
    return ["c","4"];
  }elseif($note === "d") {
    return ["d","4"];
  }elseif($note === "e") {
    return ["e","4"];
  }elseif($note === "f") {
    return ["f","4"];
  }elseif($note === "g") {
    return ["g","4"];
  }elseif($note === "u") {
    return ["a","4"];
  }elseif($note === "p" || $note === "q") {
    return ["b","4"];
  }elseif($note === "r") {
    return ["c","5"];
  }
}

function get_divLine_loc($lines_count, $divLine_point){
  $loc = round((($lines_count - 1) - $divLine_point) * 2);
  return $loc;
}
function get_accidental_loc($lines_count, $pitch, $clef_ref){
  $coef = get_coeff_accidental_pitch($pitch);
  $loc = $clef_ref + $lines_count * 2 + $coef;
  return $loc;
}
function get_coeff_accidental_pitch($note) {
    if ($note === "J") {
    return -9;
    }elseif($note === "A") {
    return -8;
    }elseif ($note === "H" || $note === "B") {
      return -7;
    }elseif ($note === "C") {
      return -6;
    }elseif($note === "D") {
      return -5;
    }elseif($note === "E") {
      return -4;
    }elseif($note === "F") {
      return -3;
    }elseif($note === "G") {
      return -2;
    }elseif($note === "a") {
      return -1;
    }elseif($note === "h" || $note === "b") {
      return 0;
    }elseif($note === "c") {
      return 1;
    }elseif($note === "d") {
      return 2;
    }elseif($note === "e") {
      return 3;
    }elseif($note === "f") {
      return 4;
    }elseif($note === "g") {
      return 5;
    }elseif($note === "u") {
      return 6;
    }elseif($note === "p" || $note === "q") {
      return 7;
    }elseif($note === "r") {
      return 8;
    }
}

function get_word_position($this_syl, $wordpos) {
  // Get the last character of the string (assuming $this_syl is a string)
  $last_char = substr($this_syl, -1);
  
  // Check the word position logic
  if ($wordpos == "s") {
    if ($last_char == "_") {
      $wordpos = "i"; // Intermediate
    } else {
      $wordpos = "s"; // Single syllable word or start of the word
    }
  }
  elseif ($wordpos == "i" || $wordpos == "m") {
    if ($last_char == "_") {
      $wordpos = "m"; // Middle of the word
    } else {
      $wordpos = "t"; // Terminal syllable of the word
    }
  }
  elseif ($wordpos == "t") {
    if ($last_char == "_") {
      $wordpos = "i"; // Intermediate again
    } else {
      $wordpos = "s"; // Restart at single or starting
    }
  }
  
  // Remove underscores from the syllable
  $this_syl = str_replace("_", "", $this_syl);
  
  // Return the cleaned syllable and the updated word position
  return [$this_syl, $wordpos];
}
?>
