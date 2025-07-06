<?php

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data); // changes characters used in html to their equivalents, for example: < to &gt;
  return $data;
}
//import database connection property
require_once('serverConfig.php');

$email = test_input($_POST['email'] ?? '');
$password = test_input($_POST['password'] ?? '');
$searchedId = test_input($_POST['searchedId'] ?? '');

$id_user = '';
$user_permission = 4;

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

if (!$user) {
    $id_user = -1;
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

// Disable ONLY_FULL_GROUP_BY
$set = "SET GLOBAL sql_mode='STRICT_TRANS_TABLES,STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,TRADITIONAL,NO_ENGINE_SUBSTITUTION';";
$setCheck = $pdo->prepare($set);
$setCheck->execute();

$where = "";
if ($user_permission == 1) { // super user
    $where = "(visibility > 0 OR id_user = '".$id_user."')";
}
elseif ($user_permission == 2) { // contributors
    $where = "(visibility > 0 OR id_user = '".$id_user."')";
}
else if ($user_permission == 3){ //common user
    $where = "(visibility = 2 OR id_user = '$id_user')";
}
else {
    $where = "visibility = 2";
}
    
$query = "
  SELECT COUNT(*)
  FROM mm_staves_stored
  WHERE {$where} AND id = '$searchedId'
";

$stmt = $pdo->prepare($query);
$stmt->execute();
$count = (int)$stmt->fetchColumn();

header('Content-Type: text/plain');
echo $count > 1 ? 'true' : 'false';

$pdo = null; //close connection

?>
