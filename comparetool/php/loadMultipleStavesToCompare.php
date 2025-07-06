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

$id_staves = test_input($_GET['id_staves'] ?? '');

// id staves not valid
if (empty($id_staves) || $id_staves == null) {
    return; 
}
// id staves valid
else {
	$dataStaves = array();
	
	// Get Stave Information
	$id = "";
    $query = "
        SELECT id_staves, id
        FROM mm_staves_stored
        WHERE id_staves=:id_staves
    ";

    $check = $pdo->prepare($query);
    $check->bindParam(':id_staves', $id_staves, PDO::PARAM_STR);
    $check->execute();

    while ($row = $check->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
      //0:id_staves 1:id
      $a = array($row[0]);
	  	array_push($dataStaves, $a);
	  	$id = $row[1];
     }
		 
    // Get all other staves with same ID
    $query = "
        SELECT id_staves
        FROM mm_staves_stored
        WHERE id=:id AND visibility = 2 AND id_staves != :id_staves
				ORDER BY ms DESC
    ";

    $check = $pdo->prepare($query);
    $check->bindParam(':id', $id, PDO::PARAM_STR);
		$check->bindParam(':id_staves', $id_staves, PDO::PARAM_STR);
    $check->execute();


    // $check->execute();
    while ($row = $check->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
	    //0:id_staves
			$a = array($row[0]);
			array_push($dataStaves, $a);
	  }
}
$pdo = null;
echo json_encode($dataStaves);

?>
