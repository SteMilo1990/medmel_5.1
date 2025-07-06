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
$ms = test_input($_POST['ms'] ?? '');
$language = test_input($_POST['language'] ?? '');
$extendedName = test_input($_POST['extendedName'] ?? '');
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
            INSERT INTO mm_extended_name
            (ms, language, extendedName)
            VALUES (:ms, :language, :extendedName)
        ";
        $check = $pdo->prepare($query);
        $check->bindParam(':ms', $ms, PDO::PARAM_STR);
        $check->bindParam(':language', $language, PDO::PARAM_STR);
        $check->bindParam(':extendedName', $extendedName, PDO::PARAM_STR);
        if($check->execute() === TRUE){
            if($check->rowCount()>0)
                echo "Extended Name Added!";
            else
                echo "error";
        }
        else {
            echo "error";
        }


            //echo "1"; //login completed
    }
}


?>
