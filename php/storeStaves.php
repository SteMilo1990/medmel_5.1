<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// errocode guide
// -2 ask to create a copy if user doesn't have permission to override public stave
// -3 ask if user wants to override previus copy of stave in database
// -4 insert not completed / maybe connection problem or database does not respond

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    //$data = htmlspecialchars($data); // changes characters used in html to their equivalents, for example: < to &gt;
    return $data;
}
//import database connection property
require_once('serverConfig.php');
//user Attributes
$email = test_input($_POST['email'] ?? '');
$password = test_input($_POST['password'] ?? '');
$id_user = '';
$user_permission = 4;
//-----------check user identity--------------
if (empty($email) || empty($password)) {
    echo "-1"; //user or password not valid
    echo "email or password not recived";
}
else {
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

    $user = $check->fetch(PDO::FETCH_ASSOC);

    if (!$user ) {
        echo "-1"; // password not valid
        return;
    }
    else {
		$check = $pdo->prepare($query);
        $check->bindParam(':email', $email, PDO::PARAM_STR);
        $check->bindParam(':password', $password, PDO::PARAM_STR);
        $check->execute();
	    while ($row = $check->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
    		//0:id_user 1:user_permission
    	    $id_user = $row[0];
          $user_permission = $row[1];
	    }
    }
}
// at this point we have id_user and his permission degree

//--------------------------stave attributes---------------------
$id_staves = 'NA';
$title = test_input($_POST['title'] ?? '');
$id = test_input($_POST['id'] ?? '');
$author = test_input($_POST['author'] ?? '');
$language = test_input($_POST['language'] ?? '');
$ms = test_input($_POST['ms'] ?? '');
$f = test_input($_POST['f'] ?? '');
$modernStyle = test_input($_POST['modernStyle'] ?? '');
$oldStyle = test_input($_POST['oldStyle'] ?? '');
//------JSON information---------------------------------------
$staves = test_input($_POST['staves'] ?? '');
$text_staves = test_input($_POST['textStaves'] ?? '');
$annotation_staves = test_input($_POST['annotationStaves'] ?? '');
$settings = isset($_POST['settings']) ? test_input($_POST['settings']) : '{}';
$loadedId = test_input($_POST['loadedId'] ?? '');
$loadedMs = test_input($_POST['loadedMs'] ?? '');
//---------------------------------------------------------------
$visibility = test_input($_POST['visibility'] ?? '0'); // 0:private, 1:content creators, 2:all users
$upload = test_input($_POST['upload'] ?? '2'); // 0:modern, 1:old, 2:both
  
//------------------------Parameters------------------------------
$blank = [];
$lines = test_input($_POST['lines'] ?? json_encode($blank));
$shapeGroup = test_input($_POST['shapeGroup'] ?? json_encode($blank));
$shapeNote = test_input($_POST['shapeNote'] ?? json_encode($blank));
$stemNote = test_input($_POST['stemNote'] ?? json_encode($blank));
$connectNote = test_input($_POST['connectNote'] ?? json_encode($blank));
$barsGroup = test_input($_POST['barsGroup'] ?? json_encode($blank));
$pes = test_input($_POST['pes'] ?? '1');
$clivis = test_input($_POST['clivis'] ?? '1');
$climacus = test_input($_POST['climacus'] ?? '1');
$porrectus = test_input($_POST['porrectus'] ?? '1');
$plica = test_input($_POST['plica'] ?? '1');
$scandicus = test_input($_POST['scandicus'] ?? '1');
$melodic = test_input($_POST['melodic'] ?? '');

//-------------------------------------------------------------------

// actions
// $override = test_input($_POST['override'] ?? '0');
// $make_private_copy = test_input($_POST['make_private_copy'] ?? '0');
// $make_public = test_input($_POST['make_public'] ?? '0');//set if stave is public or private

// Staves changes - this attributes have boolean value
// true : information is different false : information is not different
// from the previus information stored in database
$checkChange = test_input($_POST['check_change'] ?? '1'); // true: insert change to workflow table
$c_title = test_input($_POST['c_title'] ?? '1');
$c_id = test_input($_POST['c_id'] ?? '1');
$c_author = test_input($_POST['c_author'] ?? '1');
$c_language = test_input($_POST['c_language'] ?? '1');
$c_ms = test_input($_POST['c_ms'] ?? '1');
$c_f = test_input($_POST['c_f'] ?? '1');
$c_modernStaves = test_input($_POST['c_modernStaves'] ?? '1');
$c_oldStaves = test_input($_POST['c_oldStaves'] ?? '1');
$c_text = test_input($_POST['c_text'] ?? '1');
$c_annotations = test_input($_POST['c_annotations'] ?? '1');
$c_lines = test_input($_POST['c_lines'] ?? '1');
$c_shapeGroup = test_input($_POST['c_shapeGroup'] ?? '1');
$c_shapeNote = test_input($_POST['c_shapeNote'] ?? '1');
$c_stemNote = test_input($_POST['c_stemNote'] ?? '1');
$c_connectNote = test_input($_POST['c_connectNote'] ?? '1');
$c_barsGroup = test_input($_POST['c_barsGroup'] ?? '1');
//echo "user : ".$user_permission;
//echo"<br>language : ".$language;
//echo"<br>ms : ".$ms;
//echo"<br>f : ".$f;
if($user_permission > 0 && $user_permission < 4){ // if user is valid
    $queryStatus = '';
    if ($visibility == 0){ // this insert is a private copy\update
        //check duplicate and get stave id
        $querySelectStaves = "
            SELECT *
            FROM mm_staves_stored
            WHERE id_user = :id_user AND id = :id AND ms = :ms AND visibility = '0'
        ";

        $checkDuplicateStaves = $pdo->prepare($querySelectStaves);
        $checkDuplicateStaves->bindParam(':id_user', $id_user, PDO::PARAM_STR);
        $checkDuplicateStaves->bindParam(':id', $id, PDO::PARAM_STR);
        $checkDuplicateStaves->bindParam(':ms', $ms, PDO::PARAM_STR);
        $checkDuplicateStaves->execute();
        //$status = $checkDuplicateStaves->fetch(PDO::FETCH_ASSOC);
        while ($row = $checkDuplicateStaves->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
            $id_staves = $row[0];
            //echo "<BR> ID STAVES : ".$id_staves;
        }

        //echo "<BR> ID STAVES AFTER while : ".$id_staves;
        if ($id_staves != 'NA'){//---- replace the copy-------
            $queryUpdateStaves = "
                UPDATE mm_staves_stored
                SET title=:title, id=:id, author=:author, language=:language,
                    ms=:ms, f=:f, modernStyle=:modernStyle,  oldStyle=:oldStyle,
                    staves=:staves, text=:texts, annotations=:annotations, settings=:settings,
                    visibility=:visibility
                WHERE id_staves =:id_staves;
            ";

            $queryStatus = $pdo->prepare($queryUpdateStaves);
            $queryStatus->bindParam(':title', $title, PDO::PARAM_STR);
            $queryStatus->bindParam(':id', $id, PDO::PARAM_STR);
            $queryStatus->bindParam(':author', $author, PDO::PARAM_STR);
            $queryStatus->bindParam(':language', $language, PDO::PARAM_STR);
            $queryStatus->bindParam(':ms', $ms, PDO::PARAM_STR);
            $queryStatus->bindParam(':f', $f, PDO::PARAM_STR);
            $queryStatus->bindParam(':modernStyle', $modernStyle, PDO::PARAM_STR);
            $queryStatus->bindParam(':oldStyle', $oldStyle, PDO::PARAM_STR);
            $queryStatus->bindParam(':staves', $staves, PDO::PARAM_STR);
            $queryStatus->bindParam(':texts', $text_staves, PDO::PARAM_STR);
            $queryStatus->bindParam(':annotations', $annotation_staves, PDO::PARAM_STR);
            $queryStatus->bindParam(':settings', $settings, PDO::PARAM_STR);
            $queryStatus->bindParam(':visibility', $visibility, PDO::PARAM_STR);
            $queryStatus->bindParam(':id_staves', $id_staves, PDO::PARAM_STR);
        }
        else {//-----insert new private stave------------------
            $query = "
              INSERT INTO mm_staves_stored
              (title, id, author, language, ms, f, modernStyle, oldStyle, staves, text, annotations, settings, visibility, id_user)
              VALUES (:title, :id, :author, :language, :ms, :f, :modernStyle , :oldStyle, :staves, :texts, :annotations, :settings, :visibility, :id_user)
            ";

            $queryStatus = $pdo->prepare($query);
            $queryStatus->bindParam(':title', $title, PDO::PARAM_STR);
            $queryStatus->bindParam(':id', $id, PDO::PARAM_STR);
            $queryStatus->bindParam(':author', $author, PDO::PARAM_STR);
            $queryStatus->bindParam(':language', $language, PDO::PARAM_STR);
            $queryStatus->bindParam(':ms', $ms, PDO::PARAM_STR);
            $queryStatus->bindParam(':f', $f, PDO::PARAM_STR);
            $queryStatus->bindParam(':modernStyle', $modernStyle, PDO::PARAM_STR);
            $queryStatus->bindParam(':oldStyle', $oldStyle, PDO::PARAM_STR);
            $queryStatus->bindParam(':staves', $staves, PDO::PARAM_STR);
            $queryStatus->bindParam(':texts', $text_staves, PDO::PARAM_STR);
            $queryStatus->bindParam(':annotations', $annotation_staves, PDO::PARAM_STR);
            $queryStatus->bindParam(':settings', $settings, PDO::PARAM_STR);
            $queryStatus->bindParam(':visibility', $visibility, PDO::PARAM_STR);
            $queryStatus->bindParam(':id_user', $id_user, PDO::PARAM_STR);
        }
    }
    else if (($visibility == 1 || $visibility == 2) && $user_permission < 3 && $user_permission > 0){ //this copy is visible by content creators
        // check duplicate and get stave id
        // !! I had added BINARY ms = :ms, but it created problems with ms with char ö.
        // I don't remember why I have added BINARY, so there might be other bugs now that I have deleted it (here and above)
        $selectDuplicate = "
            SELECT *
            FROM mm_staves_stored
            WHERE id = :id AND ms = :ms AND (visibility = '1' OR visibility = '2')
        ";

        $checkDuplicateStaves = $pdo->prepare($selectDuplicate);
        $checkDuplicateStaves->bindParam(':id', $id, PDO::PARAM_STR);
        $checkDuplicateStaves->bindParam(':ms', $ms, PDO::PARAM_STR);
        $checkDuplicateStaves->execute();

        while ($row = $checkDuplicateStaves->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
            $id_staves = $row[0];
        }


        if ($id_staves != 'NA'){//---- replace the copy-------
            $updateStaves = "
                UPDATE mm_staves_stored
                SET title=:title, id=:id, author=:author, language=:language,
                    ms=:ms, f=:f, modernStyle=:modernStyle,  oldStyle=:oldStyle,
                    staves=:staves, text=:texts, annotations=:annotations,
                    settings=:settings, visibility=:visibility
                WHERE id_staves =:id_staves;
            ";

            $queryStatus = $pdo->prepare($updateStaves);
            $queryStatus->bindParam(':title', $title, PDO::PARAM_STR);
            $queryStatus->bindParam(':id', $id, PDO::PARAM_STR);
            $queryStatus->bindParam(':author', $author, PDO::PARAM_STR);
            $queryStatus->bindParam(':language', $language, PDO::PARAM_STR);
            $queryStatus->bindParam(':ms', $ms, PDO::PARAM_STR);
            $queryStatus->bindParam(':f', $f, PDO::PARAM_STR);
            $queryStatus->bindParam(':modernStyle', $modernStyle, PDO::PARAM_STR);
            $queryStatus->bindParam(':oldStyle', $oldStyle, PDO::PARAM_STR);
            $queryStatus->bindParam(':staves', $staves, PDO::PARAM_STR);
            $queryStatus->bindParam(':texts', $text_staves, PDO::PARAM_STR);
            $queryStatus->bindParam(':annotations', $annotation_staves, PDO::PARAM_STR);
            $queryStatus->bindParam(':settings', $settings, PDO::PARAM_STR);
            $queryStatus->bindParam(':visibility', $visibility, PDO::PARAM_STR);
            $queryStatus->bindParam(':id_staves', $id_staves, PDO::PARAM_STR);
            // $queryStatus->execute();
            //
            // if ($queryStatus->rowCount() > 0) {
            //     echo "1";
            // }
            // else {
            //     echo "-4"; //update failed
            // }
        }
        else {//-----insert new private stave------------------
            $insertNew = "
              INSERT INTO mm_staves_stored
              (title, id, author, language, ms, f, modernStyle, oldStyle, staves, text, annotations, settings, visibility, id_user)
              VALUES (:title, :id, :author, :language, :ms, :f, :modernStyle , :oldStyle, :staves, :texts, :annotations, :settings, :visibility, :id_user)
            ";

            //echo "".$insertNew;
            $queryStatus = $pdo->prepare($insertNew);
            $queryStatus->bindParam(':title', $title, PDO::PARAM_STR);
            $queryStatus->bindParam(':id', $id, PDO::PARAM_STR);
            $queryStatus->bindParam(':author', $author, PDO::PARAM_STR);
            $queryStatus->bindParam(':language', $language, PDO::PARAM_STR);
            $queryStatus->bindParam(':ms', $ms, PDO::PARAM_STR);
            $queryStatus->bindParam(':f', $f, PDO::PARAM_STR);
            $queryStatus->bindParam(':modernStyle', $modernStyle, PDO::PARAM_STR);
            $queryStatus->bindParam(':oldStyle', $oldStyle, PDO::PARAM_STR);
            $queryStatus->bindParam(':staves', $staves, PDO::PARAM_STR);
            $queryStatus->bindParam(':texts', $text_staves, PDO::PARAM_STR);
            $queryStatus->bindParam(':annotations', $annotation_staves, PDO::PARAM_STR);
            $queryStatus->bindParam(':settings', $settings, PDO::PARAM_STR);
            $queryStatus->bindParam(':visibility', $visibility, PDO::PARAM_STR);
            $queryStatus->bindParam(':id_user', $id_user, PDO::PARAM_STR);
            //echo "insert new content creator copy";

        }
    }
    // After insert/update staves insert/update parameters
    if ($queryStatus->execute() === TRUE) {
        $newInsert = false;
        if($id_staves == 'NA'){
            $newInsert = true;
            $id_staves = $pdo->lastInsertId();
        }

        // insert in workflow table
        if ((changeStatus() || $newInsert) && ($visibility == 1 || $visibility == 2) && $user_permission < 3 && $user_permission > 0){
            $queryWorkflow = "
              INSERT INTO mm_workflow_staves
              (id_user, id_staves, title, id, author, language,
              ms, f, modernStaves, oldStaves, text, annotations, lines_number,
              shape_group, shape_note, stem_note, connect_note, bar_group, date)
              VALUES (:id_user,:id_staves,:c_title, :c_id, :c_author, :c_language,
                      :c_ms, :c_f, :c_modernStaves, :c_oldStaves, :c_text,
                      :c_annotations, :c_lines, :c_shapeGroup, :c_shapeNote,
                      :c_stemNote, :c_connectNote, :c_barsGroup, NOW())
            ";

            //echo "".$queryWorkflow;
            $queryStatusWorkflow = $pdo->prepare($queryWorkflow);
            $queryStatusWorkflow->bindParam(':id_user', $id_user, PDO::PARAM_STR);
            $queryStatusWorkflow->bindParam(':id_staves', $id_staves, PDO::PARAM_STR);
            $queryStatusWorkflow->bindParam(':c_title', $c_title, PDO::PARAM_STR);
            $queryStatusWorkflow->bindParam(':c_id', $c_id, PDO::PARAM_STR);
            $queryStatusWorkflow->bindParam(':c_author', $c_author, PDO::PARAM_STR);
            $queryStatusWorkflow->bindParam(':c_language', $c_language, PDO::PARAM_STR);
            $queryStatusWorkflow->bindParam(':c_ms', $c_ms, PDO::PARAM_STR);
            $queryStatusWorkflow->bindParam(':c_f', $c_f, PDO::PARAM_STR);
            $queryStatusWorkflow->bindParam(':c_modernStaves', $c_modernStaves, PDO::PARAM_STR);
            $queryStatusWorkflow->bindParam(':c_oldStaves', $c_oldStaves, PDO::PARAM_STR);
            $queryStatusWorkflow->bindParam(':c_text', $c_text, PDO::PARAM_STR);
            $queryStatusWorkflow->bindParam(':c_annotations', $c_annotations, PDO::PARAM_STR);
            $queryStatusWorkflow->bindParam(':c_lines', $c_lines, PDO::PARAM_STR);
            $queryStatusWorkflow->bindParam(':c_shapeGroup', $c_shapeGroup, PDO::PARAM_STR);
            $queryStatusWorkflow->bindParam(':c_shapeNote', $c_shapeNote, PDO::PARAM_STR);
            $queryStatusWorkflow->bindParam(':c_stemNote', $c_stemNote, PDO::PARAM_STR);
            $queryStatusWorkflow->bindParam(':c_connectNote', $c_connectNote, PDO::PARAM_STR);
            $queryStatusWorkflow->bindParam(':c_barsGroup', $c_barsGroup, PDO::PARAM_STR);
            $queryStatusWorkflow->execute();
        }
        // update insert parameters
        $id_parameters = '';

        $queryDuplicateParameters = "
            SELECT id_parameters
            FROM mm_staves_parameters
            WHERE id_staves = :id_staves
        ";
        $querySelectParameters = $pdo->prepare($queryDuplicateParameters);
        $querySelectParameters->bindParam(':id_staves', $id_staves, PDO::PARAM_STR);
        $querySelectParameters->execute();
        while ($row = $querySelectParameters->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
            $id_parameters = $row[0];
        }

        if($id_parameters == ''){
            $q_parameters = "
            INSERT INTO mm_staves_parameters
            (id_staves, lines_in_lines,
            shape_group_note, shape_single_note,
            stem_single_note, connect_group_note,
            pes_type, united_clivis, climacus_type,
            porrectus_type, plica_type, scandicus_type,
            melodic_structure, bars_group)
            VALUES (:id_staves, :lines, :shapeGroup, :shapeNote, :stemNote, :connectNote,
                    :pes, :clivis, :climacus, :porrectus, :plica,
                    :scandicus, :melodic, :barsGroup)";
        }
        else {
            $q_parameters = "
                UPDATE mm_staves_parameters
                SET id_staves=:id_staves, lines_in_lines=:lines,shape_group_note=:shapeGroup,
                    shape_single_note=:shapeNote, stem_single_note=:stemNote,
                    connect_group_note=:connectNote, pes_type=:pes,
                    united_clivis=:clivis, climacus_type=:climacus,
                    porrectus_type=:porrectus, plica_type=:plica,
                    scandicus_type=:scandicus, melodic_structure=:melodic, bars_group=:barsGroup
                WHERE id_parameters=".$id_parameters."
            ";
        }

        $queryParameters = $pdo->prepare($q_parameters);
        $queryParameters->bindParam(':id_staves', $id_staves, PDO::PARAM_STR);
        $queryParameters->bindParam(':lines', $lines, PDO::PARAM_STR);
        $queryParameters->bindParam(':shapeGroup', $shapeGroup, PDO::PARAM_STR);
        $queryParameters->bindParam(':shapeNote', $shapeNote, PDO::PARAM_STR);
        $queryParameters->bindParam(':stemNote', $stemNote, PDO::PARAM_STR);
        $queryParameters->bindParam(':connectNote', $connectNote, PDO::PARAM_STR);
        $queryParameters->bindParam(':pes', $pes, PDO::PARAM_STR);
        $queryParameters->bindParam(':clivis', $clivis, PDO::PARAM_STR);
        $queryParameters->bindParam(':climacus', $climacus, PDO::PARAM_STR);
        $queryParameters->bindParam(':porrectus', $porrectus, PDO::PARAM_STR);
        $queryParameters->bindParam(':plica', $plica, PDO::PARAM_STR);
        $queryParameters->bindParam(':scandicus', $scandicus, PDO::PARAM_STR);
        $queryParameters->bindParam(':melodic', $melodic, PDO::PARAM_STR);
        $queryParameters->bindParam(':barsGroup', $barsGroup, PDO::PARAM_STR);
        if ($queryParameters->execute() === TRUE) {
            //echo "<br>Parametri inseriti";
        }
        //---clean old workflow-----------------------------------------
        $cleanWorkflow = "
            DELETE FROM mm_workflow_staves
            WHERE date < (SELECT DATE_FORMAT(now(),'%Y-%m-%d')-INTERVAL 90 DAY)
        ";
        $queryClean = $pdo->prepare($cleanWorkflow);
        $queryClean->execute();

        echo json_encode($id_staves);

    } else {
        //echo "<BR> Error insert/update staves";
    }

}
else { // user is not logged
    echo "-1";
}
$pdo = null; //close connection
function changeStatus(){
    $c_title = test_input($_POST['c_title'] ?? '1');
    $c_id = test_input($_POST['c_id'] ?? '1');
    $c_author = test_input($_POST['c_author'] ?? '1');
    $c_language = test_input($_POST['c_language'] ?? '1');
    $c_ms = test_input($_POST['c_ms'] ?? '1');
    $c_f = test_input($_POST['c_f'] ?? '1');
    $c_modernStaves = test_input($_POST['c_modernStaves'] ?? '1');
    $c_oldStaves = test_input($_POST['c_oldStaves'] ?? '1');
    $c_text = test_input($_POST['c_text'] ?? '1');
    $c_annotations = test_input($_POST['c_annotations'] ?? '1');
    $c_lines = test_input($_POST['c_lines'] ?? '1');
    $c_shapeGroup = test_input($_POST['c_shapeGroup'] ?? '1');
    $c_shapeNote = test_input($_POST['c_shapeNote'] ?? '1');
    $c_stemNote = test_input($_POST['c_stemNote'] ?? '1');
    $c_connectNote = test_input($_POST['c_connectNote'] ?? '1');
    $c_barsGroup = test_input($_POST['c_barsGroup'] ?? '1');

    if($c_title == 1) return 1;
    if($c_id == 1) return 1;
    if($c_author == 1) return 1;
    if($c_language == 1) return 1;
    if($c_ms == 1) return 1;
    if($c_f == 1) return 1;
    if($c_modernStaves == 1) return 1;
    if($c_oldStaves == 1) return 1;
    if($c_text == 1) return 1;
    if($c_annotations == 1) return 1;
    if($c_lines == 1) return 1;
    if($c_shapeGroup == 1) return 1;
    if($c_shapeNote == 1) return 1;
    if($c_stemNote == 1) return 1;
    if($c_connectNote == 1) return 1;
    if($c_barsGroup == 1) return 1;
}

?>
