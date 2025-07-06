<?php
    function test_input($data) {
    	$data = trim($data);
    	$data = stripslashes($data);
    	$data = htmlspecialchars($data); // changes characters used in html to their equivalents, for example: < to &gt;
    	return $data;
    }
    //import database connection property
    require_once('serverConfig.php');


        $query = "
        SELECT mm_staves_stored.language FROM mm_staves_stored
        LEFT JOIN mm_extended_name ON mm_staves_stored.language = mm_extended_name.language
        WHERE mm_staves_stored.visibility = '2' AND  (mm_staves_stored.language IS NOT NULL AND mm_staves_stored.language  !='')
        UNION
        SELECT mm_extended_name.language FROM mm_extended_name
        LEFT JOIN mm_staves_stored ON mm_staves_stored.language = mm_extended_name.language
        WHERE mm_extended_name.language IS NOT NULL AND mm_extended_name.language != ''
        GROUP BY language
        ORDER BY language
        ";

        $check = $pdo->prepare($query);
       // $check->bindParam(':searchText', $searchText, PDO::PARAM_STR);
        $check->execute();

        $data = array();
        while ($row = $check->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
          $a = array($row[0]);

          array_push($data, $a);
         }



        echo json_encode($data);


    $pdo = null;//close connection
 ?>
