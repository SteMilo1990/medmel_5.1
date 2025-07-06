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
$id_staves = test_input($_POST['id_staves'] ?? '');
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
            SELECT name, surname, date, title, id, author, language, ms, f, modernStaves,
            oldStaves, text, annotations, lines_number, shape_group, shape_note, stem_note, connect_note
            FROM mm_users, mm_workflow_staves
            WHERE mm_users.id_user = mm_workflow_staves.id_user AND  mm_workflow_staves.id_staves = :id_staves
            ORDER BY date DESC
        ";

        $check = $pdo->prepare($query);
        $check->bindParam(':id_staves', $id_staves, PDO::PARAM_STR);
        $check->execute();
        $data = array();
        while ($row = $check->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {

            $a = array($row[0],$row[1],$row[2],$row[3],$row[4],$row[5],$row[6],
                        $row[7],$row[8],$row[9],$row[10],$row[11],$row[12],
                        $row[13],$row[14],$row[15],$row[16],$row[17]);
            array_push($data, $a);
        }
        echo json_encode($data);


            //echo "1"; //login completed
    }
}


?>
