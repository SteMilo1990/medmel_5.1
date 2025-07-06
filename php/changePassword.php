
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
    $oldPassword = test_input($_POST['oldPassword'] ?? '');
    $newPassword = test_input($_POST['newPassword'] ?? '');


	$newPassword = md5(md5($newPassword));

    //check fields
    if (empty($email) || empty($oldPassword) || empty($newPassword)) {
        echo "-1";
		$pdo = null;
    }


    else {
      //checking if email is present inside database
	  $id_user = "";
        $query = "
            SELECT id_user
            FROM mm_users
            WHERE email = :email AND password = :oldPassword
        ";

        $check = $pdo->prepare($query);
        $check->bindParam(':email', $email, PDO::PARAM_STR);
		$check->bindParam(':oldPassword', $oldPassword, PDO::PARAM_STR);
        if ($check->execute() === TRUE) {
			while ($row = $check->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
				$id_user = $row[0];
			}
		}
		else {
			echo "-1";
		}

        if($id_user != ""){
			$updateUser ="
			UPDATE mm_users
			SET password=:newPassword
			WHERE id_user =:id_user";

			$check = $pdo->prepare($updateUser);
			$check->bindParam(':id_user', $id_user, PDO::PARAM_STR);
			$check->bindParam(':newPassword', $newPassword, PDO::PARAM_STR);
	        if ($check->execute() === TRUE) {
				echo "0";
			}
			else {
				echo "-1";
			}
		}
		else {
			echo "-1";
		}
    }
	$pdo = null;
