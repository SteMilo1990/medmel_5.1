<?php

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data); // changes characters used in html to their equivalents, for example: < to &gt;
    return $data;
}
//import database connection property
require_once('serverConfig.php');
//user Attributes
$email = test_input($_POST['email'] ?? '');
$password = test_input($_POST['password'] ?? '');
$id_user = test_input($_POST['id_user'] ?? '');
$new_permission = test_input($_POST['new_permission'] ?? '');
$user_permission = 4;

//-----------check user identity--------------
if (empty($email) || empty($password)) {
    echo "-1"; //user or password not valid
    echo"email or password not recived";
    return;
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
             $user_permission = $row[1];
        }
    }

    if($user_permission == 1){
        $query = "
        UPDATE mm_users
        SET user_permission=:new_permission
        WHERE id_user=:id_user
        ";

        $check = $pdo->prepare($query);
        $check->bindParam(':id_user', $id_user, PDO::PARAM_STR);
        $check->bindParam(':new_permission', $new_permission, PDO::PARAM_STR);
        $check->execute();
            //echo "1"; //login completed
    }
}


?>
