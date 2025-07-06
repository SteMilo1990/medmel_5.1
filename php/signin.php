
<?php
function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

//import database connection property
require_once('serverConfig.php');

$name = test_input($_POST['name'] ?? '');
$surname = test_input($_POST['surname'] ?? '');
$email = test_input($_POST['email'] ?? '');
$password = test_input($_POST['password'] ?? '');

$user_permission = 3;
$failed_attempts = 0;

$password = md5(md5($password)); //encript password


//check fields
if (empty($name) || empty($surname)|| empty($email)|| empty($password)) {
		echo "-1";
$pdo = null;
}


else {
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

    if (count($user) > 0) {
        echo "0";
	$pdo = null;

    }
    else {
        $query = "
            INSERT INTO mm_users
            (name, surname, email, password, user_permission, failed_attempts)
            VALUES (:name, :surname, :email, :password, :user_permission, :failed_attempts)
        ";

        $check = $pdo->prepare($query);
        $check->bindParam(':name', $name, PDO::PARAM_STR);
        $check->bindParam(':surname', $surname, PDO::PARAM_STR);
        $check->bindParam(':email', $email, PDO::PARAM_STR);
        $check->bindParam(':password', $password, PDO::PARAM_STR);
				$check->bindParam(':user_permission', $user_permission, PDO::PARAM_STR);
				$check->bindParam(':failed_attempts', $failed_attempts, PDO::PARAM_STR);
        $check->execute();

        if ($check->rowCount() > 0) {
            echo "1";
						$pdo = null;
        }
        else {
            echo "-1";
						$pdo = null;
        }
    }
}
