
<?php
//this file get staves id and return all info from database
// it's used from loadStavesFromDatabase() function in storeLoadStavesController.js
function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

//import database connection property
require_once('serverConfig.php');

//user Attributes
$email = test_input($_POST['email'] ?? '');
$password = test_input($_POST['password'] ?? '');
$id_user = -1;
$user_permission = 4;
$page = test_input($_POST['page'] ?? '');


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
while ($row = $check->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
	//0:id_user 1:user_permission
	 $id_user = $row[0];
	 $user_permission = $row[1];
}


$id_staves = test_input($_POST['id_staves'] ?? '');


if (empty($id_staves) || $id_staves == null) {
    echo "-1"; //id staves not valid
}
else {
	$dataStaves = array();
	$dataStavesParameters = array();

	//------Get Stave Information-----------------------------------------------
	$where = "";
    if($user_permission> 0 and $user_permission <3){//if user is super user
        $visibility = 0;
        $where = "(visibility > '".$visibility."' OR mm_staves_stored.id_user = '".$id_user."')";
    }
    else if($user_permission == 3 and $page == "editor.html"){//common user on editor
            $visibility = 2;
            $where = "(mm_staves_stored.id_user = '".$id_user."')";
    }
    else if($user_permission == 3){//common user on viewer of compare
        $visibility = 2;
        $where = "(visibility = '".$visibility."' OR mm_staves_stored.id_user = '".$id_user."')";
    }
		//ADDED (Looks like it works: only load demo, even if you input the exact url)
		else if($user_permission == 4 and $page == "editor.html"){//common user on viewer of compare
        $visibility = 3;
        $where = "(visibility = '".$visibility."')";
    }
		//END ADDED
    else { // guest
        $visibility = 2;
        $where = "(visibility = '".$visibility."' OR visibility = 3) ";
    }
    $query = "
        SELECT mm_staves_stored.*, mm_users.name, mm_users.surname
        FROM mm_staves_stored
				JOIN mm_users ON mm_staves_stored.id_user = mm_users.id_user
        WHERE id_staves = :id_staves AND ".$where."
    ";

    $check = $pdo->prepare($query);
    $check->bindParam(':id_staves', $id_staves, PDO::PARAM_STR);
    $check->execute();

    $user = $check->fetch(PDO::FETCH_ASSOC);

    if (!$user ) {
        echo "-1"; // id stave not valid
		return;
    }
    else {
        $check->execute();
	    while ($row = $check->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
		//0:id_staves 1:title 2:id 3:author 4:language 5:ms 6:f 7:modernStyle 8:oldStyle 9:staves(JSON) 10:multiple-text 11:multiple-Annotations 12:settings. 13:visibility 14:id_user(publisher), 15_editor's name 16:editor's surname
        $rep_id = $row[2];
	      $dataStaves = array($row[0],$row[1],$row[2],$row[3],$row[4],$row[5],$row[6],$row[7],$row[8],$row[9],$row[10],$row[11],$row[12],$row[13],$row[14],$row[15],$row[16]);

	     }
        //echo "1"; //login completed
    }

	//------Get Staves Parameters-----------------------------------------------
	$queryParameters = "
        SELECT *
        FROM mm_staves_parameters
        WHERE id_staves = :id_staves
    ";

	$check = $pdo->prepare($queryParameters);
    $check->bindParam(':id_staves', $id_staves, PDO::PARAM_STR);
    $check->execute();

    $user = $check->fetch(PDO::FETCH_ASSOC);

    if (!$user ) {
        //echo "-1"; // id stave not valid
    }
    else {
        $check->execute();
	    while ($row = $check->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
			//0:id_parameters 1: id_staves 2:lines_in_line 3: shape_group_note
			//4: shape_single_note 5: pes_type 6: stem_single_note 7: connect_group_note
			//8: united_clivis 9: climacus_type 10: porrectus_type 11: plica_type
			//12: scandicus_type 13: melodic_structure 14:bars_group
	      	$dataStavesParameters = array($row[2],$row[3],$row[4],$row[5],$row[6],$row[7],$row[8],$row[9],$row[10],$row[11],$row[12],$row[13],$row[14]);
	     }
        //echo "1"; //login completed
    }
	echo json_encode(array("staves"=>$dataStaves, "parameters"=>$dataStavesParameters));
  
  include "store_access.php";
  storeAccess($id_user, $id_staves, $rep_id, 0, $page, $pdo);
}
?>
