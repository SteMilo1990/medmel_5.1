<?php
    function test_input($data) {
    	$data = trim($data);
    	$data = stripslashes($data);
    	$data = htmlspecialchars($data); // changes characters used in html to their equivalents, for example: < to &gt;
    	return $data;
    }
    //import database connection property
    require_once('serverConfig.php');

    $language = test_input($_POST['language'] ?? "");
    $siglum = test_input($_POST['manuscript'] ?? "");

    if ($language == '' || $language == null || $siglum == '' || $siglum == null) {
      return;
    }

    $query = "
    SELECT extendedName
    FROM mm_extended_name
    WHERE language ='$language' AND ms ='$siglum'
    ";
    $check = $pdo->prepare($query);
   // $check->bindParam(':searchText', $searchText, PDO::PARAM_STR);
    $check->execute();

    $data = array();
    while ($row = $check->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
      echo $row[0];
    };


    $pdo = null;//close connection
 ?>
