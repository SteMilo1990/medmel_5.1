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

$id_user = test_input($_POST['userId'] ?? 0);
$id_staves = test_input($_POST['idStaves'] ?? '');
$rep_id = test_input($_POST['repId'] ?? '');
$page = test_input($_POST['page'] ?? '');

include "store_access.php";
storeAccess((int)$id_user, $id_staves, $rep_id, 0, $page, $pdo);
