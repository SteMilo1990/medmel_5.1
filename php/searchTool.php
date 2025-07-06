<?php
    function test_input($data) {
    	$data = trim($data);
    	$data = stripslashes($data);
    	$data = htmlspecialchars($data, ENT_NOQUOTES); // changes characters used in html to their equivalents, for example: < to &gt;
    	return $data;
    }
    //import database connection property
    require_once('serverConfig.php');

    $searchText = test_input($_POST['searchText'] ?? '');
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
    if($user_permission> 0 and $user_permission <3){//if user is super user
        $searchText = explode(' ', $searchText);
        $compare = "";
        foreach($searchText as $word){
            $compare = $compare." AND ";
            $compare = $compare."(id LIKE \"%".$word."%\" OR title LIKE \"%".$word."%\" OR author LIKE \"%".$word."%\" OR name LIKE \"%".$word."%\" OR surname LIKE \"%".$word."%\")";
        }
        $query = "
        SELECT id, ms,  title, author, id_staves, modernStyle, oldStyle, name, surname, mm_staves_stored.visibility
        FROM mm_staves_stored, mm_users
        WHERE mm_users.id_user = mm_staves_stored.id_user AND
        (visibility > 0 OR mm_staves_stored.id_user = '".$id_user."')".$compare."
        ORDER BY id
        ";
        if($searchText == ''){
            $query = "
            SELECT id, ms,  title, author, id_staves, modernStyle, oldStyle, name, surname, mm_staves_stored.visibility
            FROM mm_staves_stored, mm_users
            WHERE mm_users.id_user = mm_staves_stored.id_user AND
            (visibility > 0 OR mm_staves_stored.id_user = '".$id_user."')
            ORDER BY id
            ";
        }
        $check = $pdo->prepare($query);
       // $check->bindParam(':searchText', $searchText, PDO::PARAM_STR);
        $check->execute();

        $data = array();
        while ($row = $check->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
          $a = array($row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7]." ".$row[8], $row[9]);

          array_push($data, $a);
         }

        echo json_encode($data);
    }
    elseif($user_permission ==3){//if user is common user
        $searchText = explode(' ', $searchText);
        $compare = "";
        foreach($searchText as $word){
            $compare = $compare." AND ";
            $compare = $compare."(id LIKE '%".$word."%' OR title LIKE '%".$word."%' OR author LIKE '%".$word."%' OR name LIKE '%".$word."%' OR surname LIKE '%".$word."%')";
        }
        if ($page == "editor.html"){
            $query = "
            SELECT id, ms,  title, author, id_staves, modernStyle, oldStyle, name, surname, visibility
            FROM mm_staves_stored, mm_users
            WHERE mm_staves_stored.id_user = mm_users.id_user AND
            ( mm_staves_stored.id_user = '".$id_user."')".$compare."
            ORDER BY id
            ";
         }else{
            $query = "
            SELECT DISTINCT id, ms,  title, author, id_staves, modernStyle, oldStyle, name, surname, visibility
            FROM mm_staves_stored, mm_users
            WHERE mm_staves_stored.id_user = mm_users.id_user AND
            ( visibility = '2' OR mm_staves_stored.id_user = '".$id_user."')".$compare."
            ORDER BY id
            ";
          }
          if($searchText == ''){
            $query = "
            SELECT id, ms,  title, author, id_staves, modernStyle, oldStyle, name, surname, visibility
            FROM mm_staves_stored, mm_users
            WHERE mm_staves_stored.id_user = mm_users.id_user AND
            ( visibility = '2' OR mm_staves_stored.id_user = '".$id_user."')
            ORDER BY id
            ";
        }
        $check = $pdo->prepare($query);
       // $check->bindParam(':searchText', $searchText, PDO::PARAM_STR);
        $check->execute();

        $data = array();
        while ($row = $check->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
          $a = array($row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7]." ".$row[8], $row[9]);

          array_push($data, $a);
         }

        echo json_encode($data);
    }
    else{//if user is not logged (guest)
        $searchText = explode(' ', $searchText);
        $compare = "";
        foreach($searchText as $word){
            $compare = $compare." AND ";
            $compare = $compare."  (id LIKE '%".$word."%' OR title LIKE '%".$word."%' OR author LIKE '%".$word."%' OR name LIKE '%".$word."%' OR surname LIKE '%".$word."%')";
        }
        if ($page == "editor.html"){
          $query = "
          SELECT id, ms,  title, author, mm_staves_stored.id_staves, modernStyle, oldStyle, name, surname, visibility
          FROM mm_staves_stored, mm_users
          WHERE mm_users.id_user = mm_staves_stored.id_user AND visibility = '3' ".$compare."
          ORDER BY id
          ";
        } else {
          $query = "
          SELECT id, ms,  title, author, mm_staves_stored.id_staves, modernStyle, oldStyle, name, surname, visibility
          FROM mm_staves_stored, mm_users
          WHERE mm_users.id_user = mm_staves_stored.id_user AND visibility = '2' ".$compare."
          ORDER BY id
          ";
        }
        if($searchText == ''){
            $query = "
            SELECT id, ms,  title, author, mm_staves_stored.id_staves, modernStyle, oldStyle, name, surname, visibility
            FROM mm_staves_stored, mm_users
            WHERE mm_users.id_user = mm_staves_stored.id_user AND visibility = '3'
            ORDER BY id
            ";

        }
        $check = $pdo->prepare($query);
       // $check->bindParam(':searchText', $searchText, PDO::PARAM_STR);
        $check->execute();

        $data = array();
        while ($row = $check->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
          $a = array($row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7]." ".$row[8], $row[9]);

          array_push($data, $a);
         }

        echo json_encode($data);

    }
    $pdo = null;//close connection
 ?>
