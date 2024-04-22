<?php

define('_NAME_MIN_LEN', 2);
define('_NAME_MAX_LEN', 10);
define('_PASSWORD_MIN_LEN', 7);
define('_PASSWORD_MAX_LEN', 15);
define('_ITEM_MIN_LEN', 5);
define('_ITEM_MAX_LEN', 20);

function _db()
{

    $database_user_name = "t02rp92s6ftmx92u";
    $database_password = "y0ns8lj573lzw4r3";
    $database_host = "zpfp07ebhm2zgmrm.chr7pe7iynqr.eu-west-1.rds.amazonaws.com";
    $database_db = "j8t44ogexz2q0qqy";

    $database_connection = "mysql:host={$database_host}; dbname={$database_db}; charset=utf8mb4";

    $database_options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ];

    try {
        return new PDO($database_connection, $database_user_name, $database_password, $database_options);
    } catch (PDOException $e) {
        exit("Connection failed: " .  $e->getMessage());
    }
}
