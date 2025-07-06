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
require_once('../../php/serverConfig.php');

$id_staves = test_input($_POST['id_staves'] ?? '');
$email = test_input($_POST['email'] ?? '');
$password = test_input($_POST['password'] ?? '');
$id_user = '';

$query = "
		SELECT id_user
		FROM mm_users
		WHERE email = :email
		AND password = :password
";

$check = $pdo->prepare($query);
$check->bindParam(':email', $email, PDO::PARAM_STR);
$check->bindParam(':password', $password, PDO::PARAM_STR);
$check->execute();

$user = $check->fetch(PDO::FETCH_ASSOC);
//echo json_encode($email);
if (!$user) {
		$id_user = -1;
}else{
	$id_user = $user["id_user"];
}

if (empty($id_staves) || $id_staves == null) {
    return; //id staves not valid
}
else {
	$dataStaves = array();

    //------Get Stave Information-----------------------------------------------
    $query = "
        SELECT id, ms, staves, text, title, author, id_staves, f, annotations, settings
        FROM mm_staves_stored
        WHERE id_staves = :id_staves
				AND (visibility = 2 OR id_user = :id_user OR :id_user_1 = 10)
    ";

    $check = $pdo->prepare($query);
		$check->bindParam(':id_staves', $id_staves, PDO::PARAM_STR);
    $check->bindParam(':id_user', $id_user, PDO::PARAM_STR);
    $check->bindParam(':id_user_1', $id_user, PDO::PARAM_STR);
    $check->execute();

    while ($row = $check->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
    //0:id_staves 1:title 2:id 3:author 4:lenguage 5:ms 6:f 7:modernStyle 8:oldStyle 9:staves(JSON) 10:multiple-text 11:multiple-Annotations 12:visibility 13:id_user(publisher)

      $dataStaves = array($row[0],$row[1],$row[2],$row[3], $row[4], $row[5], $row[6], $row[7], $row[8], $row[9]);

     }
}
$pdo = null;
echo json_encode($dataStaves);

?>
