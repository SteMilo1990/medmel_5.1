<?php
    function test_input($data) {
    	$data = trim($data);
    	$data = stripslashes($data);
    	$data = htmlspecialchars($data); // changes characters used in html to their equivalents, for example: < to &gt;
    	return $data;
    }
    //import database connection property
    require_once('serverConfig.php');

    $searchText = test_input($_POST['searchText'] ?? '');
    $email = test_input($_POST['email'] ?? '');
    $password = test_input($_POST['password'] ?? '');
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

    if (!$user ) {
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
    //at this point we have id_user and his permission degree
    //check field
    if($user_permission == 1){//if user is super user
        $searchText = explode(' ', $searchText);
        $compare = "";
        foreach($searchText as $word){
            if ($compare != "" ) $compare = $compare." AND ";
            $compare = $compare."(name LIKE '%".$word."%' OR surname LIKE '%".$word."%' OR email LIKE '%".$word."%')";
        }

        $query = "
        SELECT id_user, name, surname, email, user_permission
        FROM  mm_users
        WHERE ".$compare;
        if($searchText == " " || $searchText == ''){
            $query = "
            SELECT id_user, name, surname, email, user_permission
            FROM  mm_users";
        }
        $check = $pdo->prepare($query);
       // $check->bindParam(':searchText', $searchText, PDO::PARAM_STR);
        $check->execute();

        $data = array();
        while ($row = $check->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
          $a = array($row[0], $row[1], $row[2], $row[3], $row[4]);

          array_push($data, $a);
         }

        echo json_encode($data);
    }
    $pdo = null;//close connection
 ?>
