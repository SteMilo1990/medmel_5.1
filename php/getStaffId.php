
<?php
include "linkerRScorrespondence.php";


function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}
//import database connection property
require_once('serverConfig.php');

    $query = "
        SELECT *
        FROM mm_staves_stored
        WHERE visibility = '2'
    ";

    $check = $pdo->prepare($query);

        $check->execute();
	    while ($row = $check->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
				//0:id_staves 1:title 2:id 3:author 4:lenguage 5:ms 6:f 7:modernStyle 8:oldStyle 9:staves(JSON) 10:multiple-text 11:multiple-Annotations 12:visibility 13:id_user(publisher)
				if (substr($row[2],0,2) == "RS"){
					for ($i = 0; $i < count($linker_RS); $i++) {
						if ($linker_RS[$i][1] == $row[2]){
							$work_id = $linker_RS[$i][0];
						}
					}
				}else{
					$work_id = $row[2];
				}
				$dataStaves[] = array("unique_id"=>$row[0],"work_id"=>$work_id,"ms"=>$row[5]);
			}

echo json_encode($dataStaves);

?>
