<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//errocode guide
// -2 ask for create a copy if user don't have permission to override public stave
// -3 ask if user want override previus copy of stave in database
// -4 insert not completed / maybe connection problem or database not responds
    //echo "upload php ";
    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        //$data = htmlspecialchars($data); // changes characters used in html to their equivalents, for example: < to &gt;
        return $data;
    }
    //import database connection property
    require_once('serverConfig.php');
    //user Attributes
    $email = test_input($_POST['email'] ?? '');
    $password = test_input($_POST['password'] ?? '');
    $id_staves = test_input($_POST['id_staves'] ?? '');
    $id_user = '';
    $user_permission = 4;
//-----------check user identity--------------
    if (empty($email) || empty($password)) {
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
    	     $id_user = $row[0];
             $user_permission = $row[1];

        }
    }
    if($user_permission == 3 || $user_permission == 2){
        //------Get Stave Information-----------------------------------------------
        $query = "
            DELETE FROM mm_staves_stored
            WHERE id_staves = :id_staves AND id_user =:id_user
        ";

        $check = $pdo->prepare($query);
        $check->bindParam(':id_staves', $id_staves, PDO::PARAM_STR);
        $check->bindParam(':id_user', $id_user, PDO::PARAM_STR);
        if($check->execute() === TRUE){
            if($check->rowCount()>0)
                echo "The melody has been deleted!";
            else
                echo "Operation not allowed!";
        }
        else {
            echo "Operation not allowed!";
        }

    }
    else if ($user_permission > 0 && $user_permission < 3){
        $query = "
            DELETE FROM mm_staves_stored
            WHERE id_staves = :id_staves
        ";

        $check = $pdo->prepare($query);
        $check->bindParam(':id_staves', $id_staves, PDO::PARAM_STR);
        if( $check->execute() === TRUE){
            if ($check->rowCount()>0)
                echo "The melody has been deleted!";
            else
                echo "Error, operation failed!";

        }
        else {
            echo "Error, operation failed!";
        }
    }

$pdo = null;

?>
