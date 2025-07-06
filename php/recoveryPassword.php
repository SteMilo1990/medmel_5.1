
<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}
//import database connection property
require_once('serverConfig.php');


$email = test_input($_POST['email'] ?? '');
// echo "email : ".$email;
$password = generateRandomString();

$newPassword = md5(md5($password));
// echo"<br> password : ".$password;
// echo"<br> password : ".$newPassword;
//check fields
if (empty($email) || empty($password) || empty($newPassword)) {
  echo "-1";
	$pdo = null;
}


else {
  //checking if email is present inside database
	$id_user = "";
  $query = "
      SELECT id_user, name, surname
      FROM mm_users
      WHERE email = :email
  ";

  $check = $pdo->prepare($query);
  $check->bindParam(':email', $email, PDO::PARAM_STR);

  if ($check->execute() === TRUE) {
		while ($row = $check->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
			$id_user = $row[0];
			$name = $row[1];
			$surname = $row[2];
		}
		// echo "new password ".$password;
		$userId = "".$name." ".$surname;
		sendMail($email, $userId , $password);
	}	else {
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
			$pdo = null;
		} else {
			echo "-1";
			$pdo = null;
		}
	}	else {
		echo "-1";
		$pdo = null;
	}
}


function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function sendMail($e, $user, $psw){
	$to = $e;
	$subject = 'Medieval Melody - Recovery Password';

	$headers = 'From: noreply@medievalmelody.com';


	$message =	'New Password : '.$psw;



	$sent = mail($to, $subject, $message, $headers); // finally sending the email
  if (!$sent) {
     error_log("Failed to send email to $to.");
 }else{
   error_log("Email to $to sent successfully.");

 }
}
