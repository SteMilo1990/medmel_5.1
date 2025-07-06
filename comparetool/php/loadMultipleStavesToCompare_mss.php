<?php
//this file get staves id and return all info from database concerning the songs in all mss
// it's used recursively from the loadMulipleStaves() function in storeLoadStavesController.js
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
$user_permission = 4;
$page = test_input($_POST['page'] ?? '');

//get user permission.
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
//echo json_encode($email);
if (!$user) {
		$id_user = -1;
}else{
	$id_user = $user["id_user"];
}
// id staves not valid
if (empty($id_staves) || $id_staves == null) {
    return; 
}
// id staves valid
else {
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $dataStaves = [];

    // 1) Fetch the “master” stave
    $sql1 = "
        SELECT id_staves, id, ms, f
        FROM mm_staves_stored
        WHERE id_staves = :id_staves
          AND (visibility = 2 OR id_user = :user OR :isAdmin = 1)
    ";
    $stmt1 = $pdo->prepare($sql1);
    $stmt1->execute([
        ':id_staves' => $id_staves,
        ':user'      => $id_user,
        ':isAdmin'   => ($id_user === 10) ? 1 : 0,
    ]);

    
    $master = $stmt1->fetch(PDO::FETCH_ASSOC);
    // Push master stave as the first stave in the array
    $dataStaves[] = $master['id_staves'];
    $id =  $master['id'];
    $ms =  $master['ms'];
    $f  =  $master['f'];
     
    // Get all other staves with same ID
    $query_2 = "
        SELECT id_staves
        FROM mm_staves_stored
        WHERE id=:id 
				AND id_staves != :id_staves 
				AND (ms != :ms OR f != :f)
				AND (visibility = 2 OR id_user = :id_user OR :id_user_1 = 10)
				ORDER BY ms DESC
    ";

    $check = $pdo->prepare($query_2);
    $check->bindParam(':id', $id, PDO::PARAM_STR);
		$check->bindParam(':id_staves', $id_staves, PDO::PARAM_STR);
    $check->bindParam(':id_user', $id_user, PDO::PARAM_INT);
    $check->bindParam(':id_user_1', $id_user, PDO::PARAM_INT);
		$check->bindParam(':ms', $ms, PDO::PARAM_STR);
		$check->bindParam(':f', $f, PDO::PARAM_STR);
    $check->execute();
    
    while ($row = $check->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
			array_push($dataStaves, $row[0]);
	  }
}
$pdo = null;
echo json_encode($dataStaves);


function debug_to_console($output) {
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}
?>
