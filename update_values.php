<?php
$value=$_GET['value'];
$pass=$_GET['pass'];
include (dirname(__FILE__).'/config.php');
if ($pass==$service_secret && !empty($value)){
    require_once('functions.php');
    add_SQL_line("INSERT INTO data (data,date) VALUES ('$value', NOW())");
}
else echo "Bad credentials";