<!DOCTYPE html>
<html>
<!-- php 8.2 -->
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Search sequence tool</title>
  <!-- favicon -->
  <link rel="shortcut icon" type="image/png" href="img/favicon/logo.png"/>
  <!-- Bootstrap core CSS -->
  <link href="css/bootstrap/bootstrap-5.0.0.css" rel="stylesheet">
  
  <link rel="stylesheet" type="text/css" href="css/style.css"/>
  <link rel="stylesheet" type="text/css" href="css/searchTooltip.css"/>
  <link rel="stylesheet" type="text/css" href="css/headerStyle.css"/>
  <link rel="stylesheet" type="text/css" href="css/searchTool.css"/>


  <script type="text/javascript" src="script/libraries/jquery-2.2.3.min.js"></script>
  <script type="text/javascript" src="script/libraries/jquery.dataTables.min.js"></script>
  <link rel="stylesheet" type="text/css" href="css/jquery.dataTables.min.css"/>
  <script type="text/javascript" src="script/searchTooltip.js"></script>

  <!-- Custom JavaScript controller -->
  <script type="text/javascript" src="script/controllerMenu.js"></script>
  <script language="JavaScript" type="text/JavaScript" src="./script/userEvent.js"></script>

</head>
    <body oncontextmenu="return false;">
        <header>
        <div id="header"></div>
        </header>

        <!-- Page Content -->
        
        <!-- Main container -->
        <div id="bodyContainer" class="mainContainer col-md-12 containerColor" style="margin-bottom:30px;">
            <div class="clear"> </div>
          
            <!-- Tooltip -->
            <div id="searchInfoInput">
              <span id="searchInfoLabel" onclick="showSearchTooltip()">How to use the Search Tool</span>
              <input id="infoIcon" type=image src="img/icons/info.png" class="menuButton" onclick="showSearchTooltip()" title="Info">
            </div>
            
            
            <!-- <div id="cursor" class="cursor"></div> -->
            <div id="divSearch" style="width:94%; background-color:white; margin-left:3%; margin-top: 0px;">
            <!-- Tooltip -->
            <div id="searchTooltip" class="pointingUp" style="display:none">
              <div class="closeButton">
                  <input type=image src="img/icons/close1.png" class="menuButton" style=" width:10px; "  onclick="hideInfo()" title="Close">
              </div>
              
              <span id="info0" class="tooltiptext info">Compose the melodic sequence to search by clicking on the staff or by typing the <a href="./pages/manual.html">name of the notes</a>. 
              </span>
              
              <span id="info1" class="tooltiptext info" style="display:none">
              Alternatively, melodies can be entered in the "MedMel input" as simple characters, following the <a href="./pages/manual.html">MedMel encoding standard</a>.
              </span>
              
              <span id="info2" class="tooltiptext info" style="display:none">Press space &nbsp;  <img src="img/icons/space.png" width="8%"/>&nbsp; to separate groups of notes.
              <br><br>Alternatively, click on the <span class="ttBtn">New syllable</span> button. 
              </span>
              
              <span id="info3" class="tooltiptext info" style="display:none">To delete a note, press the delete key <img src="img/icons/delete.png" width="20px"/><br><br>
              or click on the <span class="ttBtn">Delete&nbsp;note</span> button.
              </span>
              
              <span id="info4" class="tooltiptext info" style="display:none">To insert  liquescent notes or accidentals, select the relevant button from the options box.
              
              <br><br>
              Alternatively, in the MedMel input, put liquescent notes in brackets, e.g. <i>a(G)</i>.<br>
              B-flat is by far the most common accidental: use <i>b</i> for b-flat and <i>h</i> for b-natural.<br>
              As for the <a href="./pages/manual.html">MedMel encoding standard</a>, other accidentals are defined by special characters preceding the note: use underscore for flat, hashtag for sharp, percert sign for natural, e.g. <i>_a</i>, <i>#a</i>, <i>%a</i>.
              
              </span>
            
              <span id="info5" class="tooltiptext info" style="display:none">Use the Match accuracy slider to include non-identical matches, specifing the desired degree of tolerance.</span>
              
              <span id="info6" class="tooltiptext info" style="display:none">Use the option checkbox to include results that could be relevant, considering the shifting nature of melodies in different sources: <br>
                1) Include transposed versions of the searched sequence;<br>
                2) Take into account or disregard the syllabic divisions. This may allow to match melodies that underwent different distribution of notes on text syllables.<br>
                3) Consider liquencent notes or consider pitch alone. Liquencences may provide relevant information on performance in some contexts and constitute mere graphic variants in others.
              </span>
              
              <span id="info7" class="tooltiptext info" style="display:none">
                Specify the position of the searched sequence within the matched melodic lines.
              </span>
              <span style="float:right">
                <input id="prevTooltip" class="arrow" type="button" onclick="previousTooltip()" value="<" disabled=true/>
                <input id="nextTooltip" class="arrow" type="button" onclick="nextTooltip()" value=">">
              </span>
            </div>

            <!-- Music score input-->
            <svg id="svgSearchWrap" height="140px" width="100%" overflow="visible" 
            >

              <polygon id="+h" class="clickablePolygon" points="0,7 0,14 1500,14 1500,7"/>
              <polygon id="+a" class="clickablePolygon" points="0,14 0,21 1500,21 1500,14"/>
              <polygon id="g"  class="clickablePolygon" points="0,21 0,28 1500,28 1500,21"/>
              <polygon id="f"  class="clickablePolygon" points="0,28 0,36 1500,36 1500,28"/>
              <polygon id="e"  class="clickablePolygon" points="0,35 0,42 1500,42 1500,35"/>
              <polygon id="d"  class="clickablePolygon" points="0,42 0,49 1500,49 1500,42"/>
              <polygon id="c"  class="clickablePolygon" points="0,49 0,56 1500,56 1500,49"/>
              <polygon id="h"  class="clickablePolygon" points="0,56 0,63 1500,63 1500,56"/>
              <polygon id="a"  class="clickablePolygon" points="0,63 0,70 1500,70 1500,63"/>
              <polygon id="G"  class="clickablePolygon" points="0,70 0,77 1500,77 1500,70"/>
              <polygon id="F"  class="clickablePolygon" points="0,77 0,84 1500,84 1500,77"/>
              <polygon id="E"  class="clickablePolygon" points="0,84 0,91 1500,91 1500,84"/>
              <polygon id="D"  class="clickablePolygon" points="0,91 0,98 1500,98 1500,91"/>
              <polygon id="C"  class="clickablePolygon" points="0,98 0,105 1500,105 1500,98"/>
              <polygon id="H"  class="clickablePolygon" points="0,105 0,112 1500,112 1500,105"/>
              <polygon id="A"  class="clickablePolygon" points="0,112 0,119 1500,119 1500,112"/>
              <polygon id="*G" class="clickablePolygon" points="0,119 0,126 1500,126 1500,119"/>
              
              <!-- G-clef -->
              <path transform="matrix(0.7, 0, 0,0.7, -7, 8)" d="m51.688 4 c-5.427-0.1409-11.774 12.818-11.563 24.375 0.049 3.52 1.16 10.659 2.781 19.625-10.223 10.581-22.094 21.44-22.094 35.688-0.163 13.057 7.817 29.692 26.75 29.532 2.906-0.02 5.521-0.38 7.844-1 1.731 9.49 2.882 16.98 2.875 20.44 0.061 13.64-17.86 14.99-18.719 7.15 3.777-0.13 6.782-3.13 6.782-6.84 0-3.79-3.138-6.88-7.032-6.88-2.141 0-4.049 0.94-5.343 2.41-0.03 0.03-0.065 0.06-0.094 0.09-0.292 0.31-0.538 0.68-0.781 1.1-0.798 1.35-1.316 3.29-1.344 6.06 0 11.42 28.875 18.77 28.875-3.75 0.045-3.03-1.258-10.72-3.156-20.41 20.603-7.45 15.427-38.04-3.531-38.184-1.47 0.015-2.887 0.186-4.25 0.532-1.08-5.197-2.122-10.241-3.032-14.876 7.199-7.071 13.485-16.224 13.344-33.093 0.022-12.114-4.014-21.828-8.312-21.969zm1.281 11.719c2.456-0.237 4.406 2.043 4.406 7.062 0.199 8.62-5.84 16.148-13.031 23.719-0.688-4.147-1.139-7.507-1.188-9.5 0.204-13.466 5.719-20.886 9.813-21.281zm-7.719 44.687c0.877 4.515 1.824 9.272 2.781 14.063-12.548 4.464-18.57 21.954-0.781 29.781-10.843-9.231-5.506-20.158 2.312-22.062 1.966 9.816 3.886 19.502 5.438 27.872-2.107 0.74-4.566 1.17-7.438 1.19-7.181 0-21.531-4.57-21.531-21.875 0-14.494 10.047-20.384 19.219-28.969zm6.094 21.469c0.313-0.019 0.652-0.011 0.968 0 13.063 0 17.99 20.745 4.688 27.375-1.655-8.32-3.662-17.86-5.656-27.375z"></path>
              
              <!-- Pentagram -->
              <line x1="0" y1="31.2" x2="100%" y2="31.2" style="stroke-width:1"></line>
              <line x1="0" y1="45.2" x2="100%" y2="45.2" style="stroke-width:1"></line>
              <line x1="0" y1="59.2" x2="100%" y2="59.2" style="stroke-width:1"></line>
              <line x1="0" y1="73.2" x2="100%" y2="73.2" style="stroke-width:1"></line>
              <line x1="0" y1="87.2" x2="100%" y2="87.2" style="stroke-width:1"></line>
            </svg>
            
            <!-- Buttons -->
            <div class="" style="display: flex;">
              
              <input type="button" id="newSylBtn" class="editBtn" value="New syllable" onclick="createSpace()"/>
              <input type="button" id="deleteBtn" class="editBtn"  value="Delete note" onclick="deleteNote()"/>
              
              <!-- Mofidifiers box -->
              <div id="optionsBox">
                <input type="button" id="plica-status-btn" class="modifier" value="Liquescent" onclick="changePlicaStatus()" style="border-radius:5px 0 0 5px"/>
                <input type="button" id="acc-flat" class="modifier accMod" value="♭" onclick="changeAccidentalStatus(-1, 'acc-flat')" style="border-radius:0 0 0 0;border-right:none"/>
                <input type="button" id="acc-sharp" class="modifier accMod" value="♯" onclick="changeAccidentalStatus(+1, 'acc-sharp')" style="border-radius:0; border-right:none"/>
                <input type="button" id="acc-nat" class="modifier accMod" value="♮" onclick="changeAccidentalStatus(0, 'acc-nat')" style="border-radius:0 5px 5px 0;"/>
              </div>
          </div>
            
            
            
              <div class="col-md-12" style="padding:10px;">

                <form action="" method="POST" onsubmit="addToSearchHistory();">
                  
                  <div style="display:flex">
                    <label class="col-md-6" for="medmelInput" class="label" style="margin-bottom:20px;margin-top:20px;">MedMel input: 
                      <input type="search" id="musicStringInput" name="musicStringInput" onkeyup="updateFromString(this.value)" style="width: 275px;"/>
                    </label>
                    
                  </div>
                  <hr style=" width:100%;  padding:0px; margin-bottom:15px; margin-top:0px;">
                  <div id="optionsContainer" class="col-md-12" style="margin-top:10px;" >

                    <div id="option-box-container">
                        <div id="filtersContainer" class="option-box">
                          
                          <div id="matchAccuracyContainer">
                            <label for="tolerance" class="label" style="width: 150px;">Match accuracy</label><br>
                            <input class="slider" type="range" id="tolerance" name="tolerance"
                                min="50" max="100" step="1" value="100"
                                onmousemove="document.getElementById('txttolerance').innerHTML= $('#tolerance').val()+'%'">&nbsp;&nbsp;
                              <span id="txttolerance">100%</span>
                          </div>
                          <br>
                          <label for="selectLanguage" class="label">Language</label>
                          <select id="selectLanguage" name="selectLanguage" onchange="this.form.submit()"></select>
                        </div>
                        
                          <!-- Checkboxes -->
                          <div id="checkboxesContainer" class="option-box">
                            <span class="label optionLabel">Options</span>
                            
                            <label for="intervalCB" class="label-container">Transpositions
                              <input type=checkbox id=intervalCB name=intervalCB onclick="searchTranspositions()"/>
                              <span class="checkmark"></span>
                            </label>
                            
                            <label for="spacesCB" class="label-container">Consider syllable separations
                              <input type=checkbox id=spacesCB name=spacesCB onclick="setConsiderSpaces()" checked/>
                              <span class="checkmark"></span>
                            </label>
                            
                            <label for="plicaCB" class="label-container">Consider liquescence as ordinary note
                              <input type=checkbox id=plicaCB name=plicaCB onclick="setConsiderPlicae()" checked/>
                              <span class="checkmark"></span>
                            </label>
                          </div>
                          
                          <!-- RADIO BUTTONS -->
                          <div id="positionOptionBox" class="option-box" style="padding: 7px 0 5px 15px;">
                            <span class="label optionLabel">Match position</span>
                            <span style="font-size:3px;">&nbsp;</span><br>
                            
                            <label for="searchAnywhereRadio" class="radio-container">Anywhere in the melodic line
                              <input type=radio id=searchAnywhereRadio name=searchPosition onchange="setSearchPosition(0)" value=0 />
                              <span class="radio-checkmark"></span>
                            </label>

                            <br>
                            
                            <label for="lineBeginningRadio" class="radio-container">Line beginning
                              <input type=radio id=lineBeginningRadio name=searchPosition onchange="setSearchPosition(1)" value=1 />
                              <span class="radio-checkmark"></span>
                            </label>
                            
                            <br>
                            
                            <label for="lineEndingRadio" class="radio-container">Line ending
                              <input type=radio id=lineEndingRadio name=searchPosition onchange="setSearchPosition(2)" value=2 />
                              <span class="radio-checkmark"></span>
                            </label>
                            
                            <br>
                            
                            <label for="searchWholeLine" class="radio-container">Match whole line
                              <input type=radio id=searchWholeLine name=searchPosition onchange="setSearchPosition(3)" value=3 />
                              <span class="radio-checkmark"></span>
                            </label>
                          </div>
                          
                          
                          <div id="searchMelodiesWrapper" class="option-box">
                            <div style="display:flex" >
                              <div class="toggle-container" onclick="toggleExpand()">
                                
                                <span class="label optionLabel">Searched sequences</span>
                                <span class="history-arrow">▼</span>

                              </div>
                              <div id="clearHistoryBtnWrapper">
                                <div id="clearHistoryBtn" onclick="clearHistory()">Clear</div>
                              </div>
                            </div>
                            <div id="searchedMelodies">
                            </div>
                          </div>
                          
                      </div>
                    </div>
                    <input type="hidden" id="userId" name="userId" value="none">
                    <input type="hidden" id="password_input" name="password_input" value="none">
                    <div style="margin-top:20px;">
                        <input type="text" id="searchText" name="searchText" hidden >
                        <input type="submit" id="startQuerySearch"  value="Search"/>
                    </div>
                </form>

              </div>

              <div class="col-md-9" style="padding-top:30px;padding-bottom:30px;margin-left:12%;">
                  <div class="progress" style="display:none">
                      <div  class="progress-bar progress-bar-custom progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>
                      <small id="txtProgressBar" style="position: absolute; left: 45%;"></small>
                  </div>
              </div>
          </div>

            
            <hr style="padding:0px; margin-bottom:0px;border-top:10px solid white;">
            
            <!-- Results -->
            <div style="background:white; padding-bottom:10px; border-left: 3vw solid white; border-right: 3vw solid white;">
                <div class="clear"></div>
        
                <center>
                    <br><br>
                    <div id="resultDiv" style="width:100%; ">
                        <table id="stavesTable" class="table" style="width:100%;">
                            <thead >
                                <tr>
                                    <th style="width:20%;">Attributes</th>
                                    <th>Melody</th>
                                    <th style="width:12%;">Score</th>
                                </tr>
                            </thead>
                            <tbody id="resultBodyContainer">
                            </tbody>
                        </table>
                    </div>
                </center>
            </div>
            <hr style=" width:94%; margin-left:3%; padding:0px; margin-bottom:30px; margin-top:0px;">
        </div>

        <div id="overlayPanel" style="display:none; overflow: auto;" class="overlayPanel ">
        </div>
    </body>
</html>
<script type="text/javascript" src="script/searchTool.js"></script>

<script>
// this function must stay in the php file
function drawPreviusSearch() {
  syl_number = 0;
  accidental_status = "none";
  plica_status = 0;
  
  let search = "<?php echo $_POST['musicStringInput'] ?? ''; ?>";
  search = search.replace(/\)/g,"");
  let j = search.length;
  for (let i = 0; i < j; i = i + 1) {
    if (search.substring(i, i + 1).match(/^[a-hA-H]/g)) {
        let st = search.charAt(i)
        createNote(st, {
            "accidental_status": accidental_status,
            "plica_status": plica_status,
            "syl_number": syl_number
          }
        );
        // Reset status
        plica_status = 0;
        accidental_status = "none";
    }
    else {
      if (search.charAt(i) == " "){
        syl_number += 1;
      }
      else {
        if (search.charAt(i) == "*" || search.charAt(i) == "+"){
          let st = search.charAt(i) + search.charAt(i+1);
          createNote(st, {
            "accidental_status": accidental_status,
            "plica_status": plica_status,
            "syl_number": syl_number}
          );
          // Reset status
          plica_status = 0;
          accidental_status = "none";
          i = i + 1;
          
        // Set modifiers
        } else if (search.charAt(i) == "_") {
          accidental_status = -1;
        }  else if (search.charAt(i) == "#") {
          accidental_status = +1;
        } else if (search.charAt(i) == "%") {
          accidental_status = 0;
        } else if (search.charAt(i) == "(") {
          plica_status = +1;
        } else if (search.charAt(i) == ")") {
          // just skip character
        }
      }
    }
  }
}

</script>




<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

include "php/calculateIntervals.php";
include "php/search_helper_functions.php";

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data); // changes characters used in html to their equivalents, for example: < to &gt;
  return $data;
}
$code = "";
// Parameters and filters
$selectLanguage = test_input($_POST['selectLanguage'] ?? '');
$tolerance = test_input($_POST['tolerance'] ?? '');

$search_intervals = test_input($_POST['intervalCB'] ?? '');
if ($search_intervals == "on") {
  $search_intervals = 1;
}

$consider_spaces = test_input($_POST['spacesCB'] ?? '');
if ($consider_spaces == "on") {
  $consider_spaces = true;
}else{
  $consider_spaces = false;
}

$consider_plica = test_input($_POST['plicaCB'] ?? '');
if ($consider_plica == "on") {
  $consider_plica = false;
}else{
  $consider_plica = true;
}
$searchPosition = test_input($_POST['searchPosition'] ?? '');
$separator = 1;
$unisons = 0;

echo "<script type='text/javascript'>";
echo "drawPreviusSearch();";
echo "drawHeader();";
echo "setuptolerance('".$tolerance."');";
echo "setupLenguageUI('".$selectLanguage."');";
echo "</script>";

//import database connection property
require_once('php/serverConfig.php');
$search = test_input($_POST['musicStringInput'] ?? '');
$search = preg_replace('/ +/', ' ', $search);

// Check credentials
$email = test_input($_POST['userId'] ?? '');
$password = test_input($_POST['password_input'] ?? '');

$id_user = -1;
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
    //0:id_user 1:user_permission
     $id_user = $row1[0];
     $user_permission = $row1[1];
  }
}

if ($search != '' && $search != null) {

    $where = "";

    if ($selectLanguage != '')
      {
        $where = "AND language = '".$selectLanguage."'";
      }
      
    $query = "
    SELECT id_staves, JSON_EXTRACT(staves, '$[0]'), id, ms, f, title, author, JSON_EXTRACT(text, '$[0]'), id_user
    FROM mm_staves_stored
    WHERE (visibility = 2 OR id_user = $id_user) ".$where."
    ORDER BY id
    ";

    $check = $pdo->prepare($query);
    $check->execute();
    $data = array();
    
    while ($row = $check->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT))
      {
        $a = array($row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7]);
        array_push($data, $a);
      }
      
    if (ob_get_level() == 0) ob_start();
    
   
   /************
    * Analyses *
    ************/
    
    // Loop through melodies
    for ($i = 0; $i < count($data); $i++) {
      $lines = explode('\n', $data[$i][1]);
      $textLines = explode('\n', $data[$i][7]);
      
      // Loop through lines of melody
      for ($j = 0; $j < count($lines); $j++){
          // Reset matching score at each line
          $score = 100;
          // Reset deficit at each line
          $deficit = 0;
          
          if (isset($lines[$j])) 
            {
              //$strLine contain notes in staff i line j
              $strLine = preg_replace('/\'|\"/', '', $lines[$j]);
              /******************************
               Inoltre, se ci sono più versi in una canzone che corrispondono alla ricerca, se la spunta è ciccata allora li fa vedere, altrimenti no...
              ******************************/
              // The original str will feed the display, the cleaned str is for comparison.
              $strLine_original = $strLine;
              $strLine = cleanStrLine($strLine);
              /******************************/
            }
          // If a next line doesn't exist, procede to next melody.
          else break;

        
          // When we use transposition, there is no tolerance (yet).
          // It is only possible to get matches that don't start or end in the same way
          // Default for $transpose is false, it may change if $searc_intervals is active
          $transposed = false;
          if ($search_intervals == 1) {
            $res = search_transposed($strLine, $search, $tolerance);
            $deficit_transposed = $res[0];
            $deficit = $res[0];
            $code = $res[1];
            $transposed = $res[2];
            $interval_string = $res[3];
          }
          else {
            // Analyse melodies as pitch
            list($deficit, $code) = search_pitch($strLine, $search, $tolerance);
            
            $deficit_pitch = $deficit;
          }
          
          if ($deficit > 0) {
            $score = 100 - $deficit;
            $score = round($score, 2);
          }
          
          // Show this melody (line with match) if the score is above the tolerance threshold
          $show_melody = false;
          if ($score >= $tolerance) {
            // If search anywhere ON
            if ($searchPosition == 0) { 
              $show_melody = true;
            } 
            // If search beginning ON and result is beginning or whole
            elseif ($searchPosition == 1 and ($code == 1 or $code == 3)) {
              $show_melody = true;  
            }
            // If search ending ON and result is ending or whole
            elseif ($searchPosition == 2 and ($code == 2 or $code == 3)) {
              $show_melody = true;  
            } 
            // If search Whole ON and result is Whole
            elseif ($searchPosition == 3 and $code == 3) {
              $show_melody = true;  
            }
          }
          
          if ($show_melody) {

            $txtLine = '';
            try {
              if (isset( $textLines[$j])) {
                // $txtLine contains text in staves i line j
                $txtLine = preg_replace('/\'|\"/', '', $textLines[$j]);
              }
            } catch (Exception $e) { $txtLine = '';}

            $title = str_replace("'","&#39;", $data[$i][4]);
            $author = str_replace("'","&#39;", $data[$i][5]);
            $author = str_replace("'","&#39;",$author);
            
            echo "<script type='text/javascript'>";
            echo "updateProgressBar(".(($i + 1) * 100) / (count($data)).");";
            echo "document.getElementById('resultBodyContainer').appendChild(getLineStavesUI('".$strLine_original."','".$data[$i][0]."', '".$data[$i][2]."', '".$data[$i][3]."', \"".$title."\", \"".$author."\",\"".$data[$i][6]."\", \"".$txtLine."\", '".$j."', '".$score."','".$transposed."', '".$code."'));";
            echo "</script>";
            // echo "<br><br> ";
            
            ob_flush();
            flush();
          }

        }
    }
    echo "<script type='text/javascript'>";
    echo "updateProgressBar(100);";
    echo "$('#stavesTable').DataTable({'lengthMenu': [ 100, 150, 200 ], 'pageLength': 100,});";
    echo "</script>";
    ob_flush();
    flush();
    ob_end_flush();
}


function calculateMatch($notes, $match){
    // Don't understand why this is here
    $matchValue = 9.654654646;
    // So let's just make it = 0
    $matchValue = 0;

    /*
    do some control match and increse $matchValue with match weight
    */

    return $matchValue;
}

function cleanStrLine($str) {
  global $consider_plica;
  $str = str_replace('+a', 'u', $str);
  $str = str_replace('+b', 'p', $str);
  $str = str_replace('+h', 'q', $str);
  $str = str_replace('+c', 'r', $str);
  $str = str_replace('+d', 's', $str);
  $str = str_replace("*G", "J", $str);
  $str = str_replace(array("%", "/", "-", "[", "]", "'", "&039;"),"", $str);
  $str = preg_replace("/[CFGDA]\d ?|[bh]} ?/i", "", $str);

  // Plicas are always treated as simple notes (a controller will need to be added)
  if (!$consider_plica) {
    $str = str_replace("(", "", $str);
    $str = str_replace(")", "", $str);
  }
  
  return $str;
}

// Seach functions 
function search_transposed($strLine, $search, $tolerance) {
  global $consider_plica;
  global $consider_spaces;
  // Transform notes into interval if required
  $strLine_pitch = $strLine;
  $strLine = notesToIntervals($strLine, $consider_plica);

  $search_pitch = $search;
  $search = notesToIntervals($search, $consider_plica);
  $deficit = 0;
  $code = 0;
  
  // Define positional patterns
  // Premilinary passage
  if ($consider_spaces) {
    $space = " ";
  } else {
    $strLine = str_replace(" ", "", $strLine);
    $search = str_replace(" ", "", $search);
    $space = "";
  }
  
  // Now define positional patterns
  $exactSearch = $space.$search.$space;
  $beginSearch = $search.$space;
  $endSearch  = $space.$search;
  $searchLen = strlen($search);

  $patt = $search;
  $patt = str_replace("(", "\(", $patt);
  $patt = str_replace(")", "\)", $patt);
  $patt = preg_quote($patt, '/'); // fixes issue with "+" and "-" interpreted as quantifiers in preg_match, while in the transposition string they are part of the path

  $exact_patt = "/ [^ ][^ ]$patt /";
  $begin_patt = $search.$space;
  $end_patt = "/ [^ ][^ ]$patt/";
  $unperfect_beginning_patt = "/[^ ][^ ]$patt /";
  $unperfect_ending_patt = "/ [^ ][^ ]$patt/";
  $unperfect_patt = "/$patt/";
  // Exact matches in the middle, at the beginning, as the entire line, or at the end have deficit: 0 (score: 100)

  /* whole line 100% */
  if ($strLine == $search) {
    $code = 3; 
  }
  
  /* start 100% */
  elseif (strpos($strLine, $begin_patt) === 0) {
    $code = 1;
  }
  
  /* ending 100% */
  elseif (strpos($strLine, $search) === strlen($strLine) - strlen($search) 
          && preg_match($end_patt, $strLine) > 0) {
    $code = 2;
  }
  
  /* Anywhere in the line 100% */
  elseif (preg_match($exact_patt, $strLine) > 0) {
    $code = "0";
  }

  // Matches that have a different beginning or end, have deficit: 10 
    elseif (preg_match($unperfect_beginning_patt, $strLine) > 0) {/* 90% */
    $deficit += 10;
    $code = 4;
  }
  elseif (preg_match($unperfect_ending_patt, $strLine) > 0) {/* 90% */
    $deficit += 10;
    $code = 5;
  }
  // Matches that have a different beginning and end, have deficit: 20 
  elseif (preg_match($unperfect_patt, $strLine) > 0) {/* 80% */
    $deficit += 20;
    $code = 6;
  }
  // If at least the first interval is in the line
  elseif (strpos($strLine, substr($search, 0, 2)) !== false and $tolerance < 100) {
      if (!$consider_spaces) {
        $strLine = str_replace(" ", "", $strLine);
        $search = str_replace(" ", "", $search);
      }
      
      $res = nw_intervals($search, $strLine);
      
      list($line_start, $line_compare) = trimSequences($res[0], $res[1]);
      list($sym, $matches) = compare_aligned_lines($line_start, $line_compare);
      // $code = 7;
      $deficit = 100 - $sym * 100;
  }

  // Otherwise deficit: 51 (no match).
  else {
    $deficit = 51;
  }

  // After having matched intervals, it is necessary to check if the pitch corresponds as well.
  // Needs a lot of refinement. This makes sense only with codes 0-6, not 7.
  if (strpos($strLine_pitch, $search_pitch) !== false) {
    $transposed = false;
  } else {
    $transposed = true;
  }

  return [$deficit, $code, $transposed, $strLine];
}


function search_pitch($strLine, $search, $tolerance) {
  global $consider_spaces;
  global $consider_plica;

  // Set deficit
  $deficit = 0;
  // Label all matches as pitch matches
  // $transposed = false;
  $code = "";
  
  // Define positional patterns
  // Premilinary passage
  if ($consider_spaces) {
    $space = " ";
  } else {
    $strLine = str_replace(" ", "", $strLine);
    $search = str_replace(" ", "", $search);
    $space = "";
  }
  if (!$consider_plica) {
    $strLine = str_replace(["(",")"], "", $strLine);
    $search = str_replace(["(",")"], "", $search);
    $space = "";
  }
  

  // Now define positional patterns
  $exactSearch = $space.$search.$space;
  $beginSearch = $search.$space;
  $endSearch  = $space.$search;
    
  // The searched melody corresponds to the current melodic line
  if ($strLine == $search) {
    /* 100% */
    $code = 3;
  }
    // The searched melody is contained at the beginning of the line as whole melodic group
  elseif (strpos($strLine, $beginSearch) === 0) {
    /* 100% */
    $code = 1;
  }
  // The searched melody is contained at the end of the line as whole melodic group
  elseif (strpos($strLine, $endSearch) === strlen($strLine) - strlen($endSearch)) {
    /* 100% */ 
    $code = 2;
  }
  // The searched melody is contained within the line as whole melodic group
  elseif (strpos($strLine, $exactSearch) !== false) {
    /* 100% */ 
    $code = 0;
  }
  // If the searched melody is contained within the line within larger group 
  elseif (strpos($strLine, $search) !== false) {
     $deficit += 5;
     $code = 4;
  }

  /************
  * tolerance
  *************/
                
  // If the first note is in the line and there is some tolerance
  elseif (strpos($strLine, $search[0]) != false && $tolerance < 100)
    {
      $code = 5;
    
      // Get starting position
      $startMatchPos = strpos($strLine, $search[0]);
      // Start from second note, as we aldready know that first note matches
      $searchNote = 1;
      
      $searchLen = strlen($search);

      for ($i=0; $i < $searchLen; $i++) {
        if ($deficit >= (100 - $tolerance)){
          // If deficit surpasses tolerance return the value (and it will not pass)
          return [$deficit, $code];
        } else if ($searchNote >= $searchLen) {
          // If .... the index is equals (or higher?) than the searched line
          // it means that the input sequence is longer. For each note that is longer, substract a percentage.. Is it working? Dunno
          $deficit += (strlen($search)-$searchNote)*(100 / $searchLen);
          return [$deficit, $code];
        }
        else if ($startMatchPos + $searchNote >= strlen($strLine)-1)
        {
          // same as before...why? Dunno
          $deficit += (strlen($search)-$searchNote)*(100 / $searchLen);
          return [$deficit, $code];
        } else {
          // Corresponds
          if ($search[$searchNote] == $strLine[$startMatchPos + $searchNote]){
            // Continue without adding to deficit
          }
          // does not correspond
          else {           
              $deficit += 100 / $searchLen;
          }
          
          $searchNote += 1;
        }
      }
    }
    else {
      $deficit = 51;
    }
    return [$deficit, $code];
}

include "php/store_access.php";
storeAccess($id_user, $search, 0, 0, "searchSequence", $pdo);

?>
