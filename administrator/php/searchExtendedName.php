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
$searchText = test_input($_POST['searchText'] ?? '');
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
        $searchText = explode(' ', $searchText);
        $compare = "";
        foreach($searchText as $word){
            if($compare != "")
                $compare = $compare." AND ";
            $compare = $compare."(ms LIKE '%".$word."%' OR language LIKE '%".$word."%' OR extendedName LIKE '%".$word."%')";
        }
        $query = "
            SELECT *
            FROM mm_extended_name
            WHERE ".$compare."
            ORDER BY ms DESC
        ";
        if($searchText == " "|| $searchText == ''){
            $query = "
                SELECT *
                FROM mm_extended_name
                ORDER BY ms DESC
            ";
        }

        $check = $pdo->prepare($query);
        $check->execute();
        $data = array();
        while ($row = $check->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {

            $a = array($row[0],$row[1],$row[2],$row[3]);
            array_push($data, $a);
        }
        echo json_encode($data);


            //echo "1"; //login completed
    }
}


?>
