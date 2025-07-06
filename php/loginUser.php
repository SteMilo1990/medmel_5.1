<?php

function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}
//import database connection property
require_once('serverConfig.php');

$email = test_input($_POST['email'] ?? '');
$password = test_input($_POST['password'] ?? '');


$password = md5(md5($password)); //encript password

if (empty($email) || empty($password)) {
    echo "-1"; //user or password not valid
} else {
    //checking if email is present inside database
      $query = "
          SELECT id_user
          FROM mm_users
          WHERE email = :email
      ";

      $check = $pdo->prepare($query);
      $check->bindParam(':email', $email, PDO::PARAM_STR);
      $check->execute();

      $user = $check->fetchAll(PDO::FETCH_ASSOC);

      if (count($user) == 0) {
          echo "0";
      }
      else {
        $query = "
            SELECT *
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
					// record failed attempts
					$query = "
						UPDATE mm_users
						SET failed_attempts = failed_attempts + 1
						WHERE email = :email
					";
						$check = $pdo->prepare($query);
						$check->bindParam(':email', $email, PDO::PARAM_STR);
						$check->execute();

						// send right error message
						$query = "
		            SELECT *
		            FROM mm_users
		            WHERE email = :email
		        ";
						$check = $pdo->prepare($query);
						$check->bindParam(':email', $email, PDO::PARAM_STR);
						$check->execute();
						$data = array();
				    while ($row = $check->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
				      $data = array($row[6]);
				    }
						if ($data[0] > 5) {
							echo "-2";
						}else{
							echo "-1"; // password not valid
						}
        }
        else {
			$check = $pdo->prepare($query);
	        $check->bindParam(':email', $email, PDO::PARAM_STR);
	        $check->bindParam(':password', $password, PDO::PARAM_STR);
	        $check->execute();
		  	$data = array();
		    while ($row = $check->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
		      $data = array($row[0], $row[1], $row[2], $row[3], $password, $row[6]);
		     }

				 if ($data[5] < 6){ // how many failed attempts
					 $query = "
						 UPDATE mm_users
						 SET failed_attempts = 0
						 WHERE email = :email
					 ";
						 $check = $pdo->prepare($query);
						 $check->bindParam(':email', $email, PDO::PARAM_STR);
						 $check->execute();
			  	echo json_encode($data);
				}else{
					echo "-2"; // too many failed attempts
				}
            //echo "1"; //login completed
        }

    }
}

?>
