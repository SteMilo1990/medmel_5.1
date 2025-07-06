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
    $id_user = '';
    $user_permission = 4;
//-----------check user identity--------------
    if (empty($email) || empty($password)) {
        echo "-1"; //user or password not valid
        echo"email or password not recived";
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
        	     $id_user = $row[0];
                 $user_permission = $row[1];
    	    }
        }
    }
    //at this point we have id_user and his permission degree
    //echo "<br> ID USER : ".$id_user;
    //echo "<br> PERMISSION : ".$user_permission;
    //echo "<br>";
//--------------------------stave attributes------------------------------------
    $id_staves = 'NA';
    $id = test_input($_POST['id'] ?? '');
    $ms = test_input($_POST['ms'] ?? '');
    $visibility = test_input($_POST['visibility'] ?? '0'); //0:private, 1:content creators, 2:all users
  
//------------------------------------------------------------------------------

    if($user_permission > 0 && $user_permission < 4){ // if user is valid
        $queryStatus = '';
        if ($visibility == 0){ //this insert is a private copy\update
            //check duplicate and get stave id
            $querySelectStaves = "
                SELECT *
                FROM mm_staves_stored
                WHERE id_user = :id_user AND id = :id AND BINARY ms = :ms AND visibility = '0'
            ";

            $checkDuplicateStaves = $pdo->prepare($querySelectStaves);
            $checkDuplicateStaves->bindParam(':id_user', $id_user, PDO::PARAM_STR);
            $checkDuplicateStaves->bindParam(':id', $id, PDO::PARAM_STR);
            $checkDuplicateStaves->bindParam(':ms', $ms, PDO::PARAM_STR);
            $checkDuplicateStaves->execute();
            //$status = $checkDuplicateStaves->fetch(PDO::FETCH_ASSOC);
            while ($row = $checkDuplicateStaves->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
                $id_staves = $row[0];
                //echo "<BR> ID STAVES : ".$id_staves;
            }
        }
        else if (($visibility == 1 || $visibility == 2) && $user_permission < 3 && $user_permission > 0){ //this copy is visible by content creators
            //check duplicate and get stave id
            $selectDuplicate = "
                SELECT *
                FROM mm_staves_stored
                WHERE id = :id AND BINARY ms = :ms AND (visibility = '1' OR visibility = '2')
            ";

            $checkDuplicateStaves = $pdo->prepare($selectDuplicate);
            $checkDuplicateStaves->bindParam(':id', $id, PDO::PARAM_STR);
            $checkDuplicateStaves->bindParam(':ms', $ms, PDO::PARAM_STR);
            $checkDuplicateStaves->execute();
            //$status = $checkDuplicateStaves->fetch(PDO::FETCH_ASSOC);
            while ($row = $checkDuplicateStaves->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
                $id_staves = $row[0];
            }

        }
        echo json_encode($id_staves);
    }
    else { // user is not logged
        echo json_encode('-1');
    }
    $pdo = null; //close connection

?>
