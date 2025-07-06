<?php

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data); // changes characters used in html to their equivalents, for example: < to &gt;
    return $data;
}
//import database connection property
require_once('../../php/serverConfig.php');
//user Attributes
$email = test_input($_POST['email'] ?? '');
$password = test_input($_POST['password'] ?? '');
$id_extendedName = test_input($_POST['id_extendedName'] ?? '');
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
    while ($row = $check->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
        //0:id_user 1:user_permission
         $user_permission = $row[1];
    }


    if($user_permission == 1){

        $query = "
            DELETE FROM mm_extended_name
            WHERE id_extendedName = :id_extendedName
        ";
        $check = $pdo->prepare($query);
        $check->bindParam(':id_extendedName', $id_extendedName, PDO::PARAM_STR);
        if($check->execute() === TRUE){
            if($check->rowCount()>0)
                echo "deleted";
            else
                echo "error";
        }
        else {
            echo "error";
        }
    }
}


?>
