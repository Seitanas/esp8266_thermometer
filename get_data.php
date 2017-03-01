<?php
require_once('functions.php');
$year=$_GET['year'];
$month=$_GET['month'];
$day=$_GET['day'];
$interval=$_GET['interval'];
if (empty($year))
    $year=date("Y");
if (empty($month))
    $month=date("m");
if (empty($day))
    $day=date("d");
if (empty($interval))
    $interval=5;
$temperature_data=get_SQL_array("SELECT date, data FROM data WHERE date LIKE '$year-$month-$day%' AND  mod(id,'$interval')=0");
$js_chart_data=array();
$x=0;
while (isset($temperature_data[$x]['data'])){
    $chart_date[]=$temperature_data[$x]['date'];
    $chart_data[]=$temperature_data[$x]['data'];
    ++$x;
}
$js_chart_data['date']=$chart_date;
$js_chart_data['data']=$chart_data;
$js_chart_data=json_encode($js_chart_data);
echo($js_chart_data);

