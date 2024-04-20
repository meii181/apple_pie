<?php

define('_NAME_MIN_LEN', 2);
define('_NAME_MAX_LEN', 10);
define('_PASSWORD_MIN_LEN', 7);
define('_PASSWORD_MAX_LEN', 15);
define('_ITEM_MIN_LEN', 5);
define('_ITEM_MAX_LEN', 20);

function _db()
{

  $database_url = parse_url(getenv("JAWSDB_URL"));
  $database_user_name = $database_url["user"];
  $database_password = $database_url["pass"];
  $database_host = $database_url["host"];
  $database_db = ltrim($database_url["path"], '/');

  $database_connection = "mysql:host={$database_host}; dbname={$database_db}; charset=utf8mb4";

  $database_options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
  ];
  return new PDO($database_connection, $database_user_name, $database_password, $database_options);
}
