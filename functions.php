<?php
function SQL_connect(){
    include (dirname(__FILE__).'/config.php');
    $mysql_connection=mysqli_connect('localhost',$dbuser,$dbpass);
    mysqli_select_db($mysql_connection, $dbname);
    return $mysql_connection;
}
//##############################################################################
function get_SQL_array($sql_line){
    $query_array=array();
    $mysql_connection=SQL_connect();
    $q_string = mysqli_query($mysql_connection, $sql_line)or die (mysqli_error($mysql_connection));
    while ($row=mysqli_fetch_array($q_string, MYSQL_ASSOC)){
        $query_array[]=$row;
    }
    mysqli_close($mysql_connection);
    return $query_array;
}
//##############################################################################
function add_SQL_line($sql_line){
    $mysql_connection=SQL_connect();
    mysqli_query($mysql_connection, $sql_line) or die (mysqli_error($mysql_connection));
    mysqli_close($mysql_connection);
    return 0;
}
