<?php
//this file get the editors' name from the ids of the transcription
function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}
//import database connection property
require_once('../../php/serverConfig.php');

$id_staves = test_input($_GET['id_staves'] ?? '');

$ids_array = explode(",",$id_staves);
$placeholders = implode(',', array_fill(0, count($ids_array), '?'));

//get user permission.
$query = "
		SELECT mm_staves_stored.id_user, mm_users.name, mm_users.surname
		FROM mm_staves_stored
		INNER JOIN mm_users ON mm_users.id_user=mm_staves_stored.id_user
		WHERE id_staves IN ($placeholders)
";

$check = $pdo->prepare($query);
foreach ($ids_array as $index => $id) {
		$check->bindValue($index + 1, $id, PDO::PARAM_INT); // Assuming IDs are integers
}
$check->execute();

$publishers = array();
while ($row = $check->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
	//0:id_staves
	$a = $row[1]." ".$row[2];
	array_push($publishers, $a);
}

$pdo = null;
echo json_encode($publishers);

?>
