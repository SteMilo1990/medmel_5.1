<?php
    function test_input($data) {
    	$data = trim($data);
    	$data = stripslashes($data);
    	$data = htmlspecialchars($data); // changes characters used in html to their equivalents, for example: < to &gt;
    	return $data;
    }
    //import database connection property
    require_once('serverConfig.php');

    $staves_id = test_input($_POST['id_staves'] ?? "");


    $query = "
    SELECT id
    FROM mm_staves_stored
    WHERE id_staves =:staves_id
    ";
    $check = $pdo->prepare($query);
    $check->bindParam(':staves_id', $staves_id, PDO::PARAM_STR);
    $check->execute();

    $data = array();
    while ($row = $check->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
      echo $row[0];
    };


    $pdo = null;//close connection
 ?>
