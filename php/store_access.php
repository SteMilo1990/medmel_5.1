<?php
function storeAccess($id_user, $id_staves, $start_melody_id, $repertory, $page, $pdo) {
    if ($id_user == 10){
      return;
    }
    // Get the IP address of the user
    $ip = $_SERVER['REMOTE_ADDR'];
    
    // Get the current date
    $date = date('Y-m-d H:i:s');
    
    // Prepare the SQL statement
    $sql = "INSERT INTO mm_access (id_user, ip, date, id_staves, start_melody_id, repertory, page) VALUES (:id_user, :ip, :date, :id_staves, :start_melody_id, :repertory, :page)";
    
    // Prepare and execute the statement
    $stmt = $pdo->prepare($sql);
    
    // Bind the parameters
    $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
    $stmt->bindParam(':ip', $ip, PDO::PARAM_STR);
    $stmt->bindParam(':date', $date, PDO::PARAM_STR);
    $stmt->bindParam(':id_staves', $id_staves, PDO::PARAM_INT);
    $stmt->bindParam(':start_melody_id', $start_melody_id, PDO::PARAM_INT);
    $stmt->bindParam(':repertory', $repertory, PDO::PARAM_STR);
    $stmt->bindParam(':page', $page, PDO::PARAM_STR);
    
    // Execute the query
    if ($stmt->execute()) {
        return "Access stored successfully." . json_encode([$id_user, $id_staves, $start_melody_id, $repertory, $page]);
    } else {
        return "Error storing access: " . implode(", ", $stmt->errorInfo());
    }
}
