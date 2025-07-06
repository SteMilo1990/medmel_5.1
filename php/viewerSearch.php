<?php
    function test_input($data) {
    	$data = trim($data);
    	$data = stripslashes($data);
    	$data = htmlspecialchars($data); // changes characters used in html to their equivalents, for example: < to &gt;
    	return $data;
    }
    //import database connection property
    require_once('serverConfig.php');

    $email = test_input($_POST['email'] ?? '');
    $password = test_input($_POST['password'] ?? '');
    $tab = test_input($_POST['tab'] ?? '');
    $subParam = test_input($_POST['subParam'] ?? '');
    $author = test_input($_POST['author'] ?? '');
    $language = test_input($_POST['language'] ?? '');
    $ms_2 = test_input($_POST['ms2'] ?? '');
    $lang_2 = test_input($_POST['lang2'] ?? '');
    $id_user = '';
    $user_permission = 4;

    //get user permission.
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
        $id_user = -1;
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

    // Disable ONLY_FULL_GROUP_BY
    $set = "SET GLOBAL sql_mode='STRICT_TRANS_TABLES,STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,TRADITIONAL,NO_ENGINE_SUBSTITUTION';";
    $setCheck = $pdo->prepare($set);
    $setCheck->execute();

    //at this point we have id_user and his permission degree
    //check field
    
    // 1 super user
    // 2 collaborators
    // 3 registered user
    // 4 not authenticated 

    $where = "";
    if ($user_permission == 1) { // super user
        $where = "(visibility > 0 OR mm_staves_stored.id_user = '".$id_user."')";
    }
    elseif ($user_permission == 2) { // contributors
        $where = "(visibility > 0 OR mm_staves_stored.id_user = '".$id_user."')";
    }
    else if ($user_permission == 3){ //common user
        $where = "(visibility = 2 OR mm_staves_stored.id_user = '$id_user')";
    }
    else {
        $where = "visibility = 2";
    }

    if ($tab == "works") {
        if($language != '' && $language!=null){
            $where = $where." AND language ='".$language."'";
        }
        $query = "
          SELECT
            id            AS work_id,
            id_staves     AS work_stave_id,
            title,
            author,
            language,
            ms,
            mm_users.name        AS editor_name,
            mm_users.surname     AS editor_surname,
            visibility
          FROM mm_staves_stored
          JOIN mm_users
            ON mm_staves_stored.id_user = mm_users.id_user
          WHERE {$where}
          ORDER BY id, ms
        ";

        $stmt = $pdo->prepare($query);
       // $check->bindParam(':searchText', $searchText, PDO::PARAM_STR);
        $stmt->execute();

        $data = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $wid = $row['work_id'];
              // se è la prima volta che incontro questo work, inizializzo
              if (!isset($works[$wid])) {
                  $works[$wid] = [
                      'id'         => $wid,
                      'id_staves'  => $row['work_stave_id'],
                      'title'      => $row['title'],
                      'author'     => $row['author'],
                      'language'   => $row['language'],
                      'ms_items'   => []
                  ];
              }
              // aggiungo un elemento ms al work corrispondente
              $works[$wid]['ms_items'][] = [
                  'id_staves'  => $row['work_stave_id'],
                  'ms'         => $row['ms'],
                  'editor'     => $row['editor_name'].' '.$row['editor_surname'],
                  'visibility' => $row['visibility']
              ];
         }
         $data = $works;
    }
    else if ($tab == "manuscripts"){
        if ($language != '' && $language!=null){
            $where = $where." AND (mm_staves_stored.language ='".$language."' OR  mm_extended_name.language ='".$language."')";
        }
        $query = "
        SELECT DISTINCT  id_staves, mm_staves_stored.language, mm_extended_name.language, mm_staves_stored.ms,  extendedName
        FROM mm_staves_stored LEFT JOIN mm_extended_name ON BINARY mm_staves_stored.ms = BINARY mm_extended_name.ms
        WHERE ".$where."
        GROUP BY BINARY mm_staves_stored.ms, mm_extended_name.language
        ORDER BY ms
        ";
        $check = $pdo->prepare($query);
       // $check->bindParam(':searchText', $searchText, PDO::PARAM_STR);
        $check->execute();

        $data = array();
        while ($row = $check->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
          $a = array($row[0], $row[1], $row[2], $row[3], $row[4]);

          array_push($data, $a);
         }
    }
    else if($tab == "manuscripts_id" ){
      $language_param = "";
        if($language != '' && $language!=null){
            $language_param = " AND language ='".$language."'";
        }
        $additional = "";
        if ($ms_2 != null and $lang_2 != null) {
          $additional = " OR BINARY mm_staves_stored.ms = BINARY '".$ms_2."' 
          AND language='".$lang_2."'";
        }
        
        $query = "
        SELECT id_staves, f, id, title, author
        FROM mm_staves_stored, mm_users
        WHERE ".$where."
        AND mm_staves_stored.id_user = mm_users.id_user
        AND (BINARY mm_staves_stored.ms = BINARY '".$subParam."'
        ".$language_param.
        $additional.
        ")
        ORDER BY id
        ";
        $check = $pdo->prepare($query);
       // $check->bindParam(':searchText', $searchText, PDO::PARAM_STR);
        $check->execute();

        $data = array();
        while ($row = $check->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
          $a = array($row[0], $row[1], $row[2], $row[3], $row[4]);

          array_push($data, $a);
         }
    }
    else if($tab == "authors" ){
        if($language != '' && $language!=null){
            $where = $where." AND mm_staves_stored.language = '".$language."'";
        }
        $query = "
        SELECT author
        FROM mm_staves_stored
        WHERE ".$where."
        GROUP BY author
        ORDER BY author
        ";
        $check = $pdo->prepare($query);
       // $check->bindParam(':searchText', $searchText, PDO::PARAM_STR);
        $check->execute();

        $data = array();
        while ($row = $check->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
          $a = array($row[0]);

          array_push($data, $a);
         }
    }
    else if($tab == "authors_id" ){
        $params = []; // To dynamically hold parameters for the query.
        if($language != '' && $language!=null){
          $where .= " AND mm_staves_stored.language = :language";
          $params['language'] = $language; // Add language to parameters array.
        }
        $query = "
        SELECT DISTINCT id_staves, id,  title, language
        FROM mm_staves_stored
        WHERE REPLACE(author, '\'', '') = :author AND ($where)
        GROUP BY id
        ORDER BY id
        ";
        
        $check = $pdo->prepare($query);
        // $check->bindParam(':searchText', $searchText, PDO::PARAM_STR);
        $params['author'] = str_replace("&#039;", "",$author); // Add author to parameters array.
        $check->execute($params);

        $data = array();
        while ($row = $check->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
          $a = array($row[0], $row[1], $row[2], $row[3]);
          array_push($data, $a);
         }
    }
    else if($tab =="authors_ms"){//return subtabWork info
        $params = [];
        if ($language != '' && $language!=null) {
          $where = $where." AND mm_staves_stored.language = :language";
          $params['language'] = $language; // Add language to parameters array.

        }
        $query = "
        SELECT id_staves, ms, name, surname
        FROM mm_staves_stored, mm_users
        WHERE ".$where."
        AND mm_staves_stored.id_user = mm_users.id_user
        AND mm_staves_stored.id = '".$subParam."'
        AND REPLACE(mm_staves_stored.author, '\'','') = :author
        ORDER BY id
        ";

        $check = $pdo->prepare($query);
       // $check->bindParam(':searchText', $searchText, PDO::PARAM_STR);
        $params['author'] = str_replace("&#039;", "",$author); // Add author to parameters array.
        $check->execute($params);

        $data = array();
        while ($row = $check->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
          $a = array($row[0], $row[1], $row[2]." ".$row[3]);

          array_push($data, $a);
         }
    }


    echo json_encode(array_values($data));

    $pdo = null; //close connection
    
    function logMessage($message, $level = 'INFO', $logFile = 'app.log') {
        // Define the format of the log entry: [YYYY-MM-DD HH:MM:SS] [LEVEL] Message
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp] [$level] $message" . PHP_EOL;
        
        // Append the log entry to the file
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }

    function debug_to_console($data) {
        $output = $data;
        if (is_array($output))
            $output = implode(',', $output);

        echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
    }
 ?>
