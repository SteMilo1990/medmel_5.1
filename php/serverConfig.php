<?php

 $config = [
   'db_engine' => 'mysql',
   'db_host' => 'localhost:8889', // Change if needed
   'db_name' => '[NAME DATABASE]', // <-- CHANGE
   'db_user' => '[DB USER]', // <-- CHANGE
   'db_password' => '[DB PASSWORD]', // <-- CHANGE
];

$db_config = $config['db_engine'] . ":host=".$config['db_host'] . ";dbname=" . $config['db_name'] . ";charset=utf8mb4";

try {
    $pdo = new PDO($db_config, $config['db_user'], $config['db_password'], [
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ]);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    exit("Cannot connect to database: " . $e->getMessage());
}
