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
            SELECT *
            FROM mm_users
            WHERE id_user = :id_user
        ";

        $check = $pdo->prepare($query);
        $check->bindParam(':id_user', $id_user, PDO::PARAM_STR);
        $check->execute();
        $data = array();
        while ($row = $check->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
        //0:id_user 1:name 2:surname 3:email 4:password 5:user_permission
          $data = array($row[0],$row[1],$row[2],$row[3],$row[5]);
        }
        echo json_encode($data);


            //echo "1"; //login completed
    }
}


?>
