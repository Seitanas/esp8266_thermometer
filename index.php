<html>
<head>
    <title>ESP8266 graph</title>
    <script src="js/jquery-3.1.1.min.js"></script>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="bootstrap/css/bootstrap-theme.min.css">
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script src="js/hammer.min.js"></script>
    <script src="js/Chart.js"></script>
    <script src="js/chartjs-plugin-zoom.min.js"></script>

    <meta name="author" content="Tadas Ustinavičius">
    <style>
    canvas {
	-moz-user-select: none;
	-webkit-user-select: none;
	-ms-user-select: none;
    }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
	<div class="col-md-1"></div>
	<div class="col-md-8">
	    <h2>ESP8266 temperature chart</h2>
	    <canvas id="ESPchart" height="400" width="1200"></canvas>
	</div>
	<div class="col-md-1">
	    <div class="form-group">
		<label for="year">Year:</label>
		<select class="form-control" id="year">
		</select>
		<label for="month">Month:</label>
		<select class="form-control" id="month">
		</select>
		<label for="day">Day:</label>
		<select class="form-control" id="day">
		</select>
		<label for="interval">Marker size:</label>
		<select class="form-control" id="interval">
		    <option value="1">1 min</option>
		    <option value="2">2 mins</option>
		    <option value="3">3 mins</option>
		    <option value="4">4 mins</option>
		    <option value="5" selected>5 mins</option>
		    <option value="10">10 mins</option>
		    <option value="10">30 mins</option>
		</select>
	    </div>
	</div>
	<div class="col-md-2"></div>
    </div>
    <div class="row">
	<div class="col-md-2"></div>
	<div class="col-md-8"></div>
	<div class="col-md-2"><a class="btn btn-xs btn-primary" href="https://github.com/Seitanas/esp8266_thermometer" target="_new">Download on GitHub</a></div>
    </div>
</div>
<script>
var ctx = document.getElementById('ESPchart').getContext('2d');
var ESPchart = new Chart(ctx, {
	type: 'line',
	data: {
	    labels: 0,
	    datasets: [{
    		label: 'Temperature',
    		data: 0,
		pointRadius: 0,
    		backgroundColor: "rgba(153,255,51,0.6)"
	    },]
	},
	options: {
	    responsive: false,
	    pan: {
    		enabled: true,
	        mode: 'xy'
		},
	    zoom: {
    		enabled: true,
    		mode: 'x',
		sensitivity: 0.0001,
		}
	    }
    });
function get_chart_data(year,month,day,interval){
    $.getJSON('get_data.php?year=' + year + '&month=' + month + '&day=' + day + '&interval=' + $('#interval').val(), function(data) {
	ESPchart.data.labels = data.date;
	ESPchart.data.datasets[0].data = data.data;
	ESPchart.update();
    });
}

function clear_select(type){
    if (type=='monthandday')
	$('option','#month').remove();
    $('option','#day').remove();
}
function fill_select(year, month){
    var x=0;
    if (!month){//only do full month+day requery if month=0 (another year selected)
	$.getJSON('get_date.php?type=listmonths&year=' + year, function(data) {
	    while (data.length != x){
		$('#month').append($('<option>').text(data[x].Month).attr('value', data[x].Month));
		++x;
	    }
	    if (!month)//if no month specified, then get last available month
		month=data[data.length-1].Month;
	    $('#month').val(month);
	    $.getJSON('get_date.php?type=listdays&year=' + year + '&month=' + month, function(data) {
		var x=0;
		while (data.length != x){
		    $('#day').append($('<option>').text(data[x].Day).attr('value', data[x].Day));
		    ++x;
		}
		day=data[data.length-1].Day; //we allways take last entry for selected month
		$('#day').val(day);
		get_chart_data(year, month, day, $('#interval').val());
	    });
	});
    }
    else{
	$.getJSON('get_date.php?type=listdays&year=' + year + '&month=' + month, function(data) {
	    var x=0;
	    while (data.length != x){
		$('#day').append($('<option>').text(data[x].Day).attr('value', data[x].Day));
		++x;
	    }
	    day=data[data.length-1].Day; //we allways take last entry for selected month
	    $('#day').val(day);
	    get_chart_data(year, month, day, $('#interval').val());
	});
    }
}
function select_init(){
    var year = '';
    var month= '';
    var day= '';
    $.getJSON('get_date.php', function(data) {
	var x=0;
	while (data.length != x){
	    $('#year').append($('<option>').text(data[x].Year).attr('value', data[x].Year));
	    ++x;
	}
	year=data[data.length-1].Year;
	$('#year').val(year);
	fill_select(year, 0, 0);
    });
}
$(document).ready(function () {
    select_init();
    $('#year').on('change', function(){
	clear_select('monthandday');
	fill_select ($( "#year" ).val(),0);
    });
    $('#month').on('change', function(){
	clear_select('day');
	fill_select ($( "#year" ).val(),$( "#month" ).val());
    });
    $('#day').on('change', function(){
	get_chart_data($( "#year" ).val(), $( "#month" ).val(), $( "#day" ).val());
    });
    $('#interval').on('change', function(){
	get_chart_data($( "#year" ).val(), $( "#month" ).val(), $( "#day" ).val());
    });
});

</script>

</body>
</html>
