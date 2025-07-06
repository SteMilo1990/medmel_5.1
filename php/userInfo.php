
<?php
//this file get email and password(encript format) and return the user permission statemet
// it's used to check if use can do some operation like upload staves or override staves
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



if (empty($email) || empty($password)) {
    echo "-1"; //user or password not valid
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
        echo "-1"; // password not valid
    }
    else {
		$check = $pdo->prepare($query);
        $check->bindParam(':email', $email, PDO::PARAM_STR);
        $check->bindParam(':password', $password, PDO::PARAM_STR);
        $check->execute();
	  	$data = array();
	    while ($row = $check->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
		//0:id_user 1:name 2:surname 3:email 4:password 5:user_permission
	      $data = array($row[0],$row[1],$row[2],$row[3],$row[4],$row[5]);

	     }
	  	echo json_encode($data);
        //echo "1"; //login completed
    }

}
?>
