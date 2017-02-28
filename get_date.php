<?php
require_once('functions.php');
$year=$_GET['year'];
$month=$_GET['month'];
$day=$_GET['day'];
$type=$_GET['type'];
if (empty($year) && empty($month) && empty($day)){
    $year_list=get_SQL_array("SELECT DATE_FORMAT(date, '%Y') AS Year FROM data GROUP BY Year;");
    echo json_encode($year_list);
    exit;
}
else if ($type=='listmonths'){
    if (!empty($year)){
	$month_list=get_SQL_array("SELECT DATE_FORMAT(date, '%Y') AS Year, DATE_FORMAT(date, '%m') AS Month FROM data WHERE date LIKE '$year%' GROUP BY Month;");
	echo json_encode($month_list);
	exit;
    }
}
else if ($type=='listdays'){
    if (!empty($year) && !empty($month)){
	$day_list=get_SQL_array("SELECT DATE_FORMAT(date, '%Y') AS Year, DATE_FORMAT(date, '%m') AS Month, DATE_FORMAT(date, '%d') AS Day FROM data  WHERE date LIKE '$year-$month%' GROUP BY Day;");
	echo json_encode($day_list);
	exit;
    }
}
