<?PHP

$c = currency_symbol($current_user->home_currency);
$months = array();
$lowest_month = false;
$highest_month = false;

foreach($pledges as $pledge){
	$campaign = $pledge->campaign();
	$date_ended = strtotime($campaign->date_end);

	if(!$lowest_month || $date_ended < $lowest_month ){
		$lowest_month = $date_ended;
	}
	if(!$highest_month || $date_ended > $highest_month ){
		$highest_month = $date_ended;
	}

	$month = date("Y-m", $date_ended);
	if(!isset($months[$month])){
		$months[$month] = array(
			'legend' => date("M Y", $date_ended), 
			'arrived' => 0, 
			'waiting' => 0, 
			'some' => 0, 
			'failed' => 0
		);
	}
	$value = $pledge->convert_to_currency($current_user->home_currency);
	switch ($pledge->is_delivered){
		case 'Yes':
			$months[$month]['arrived'] += $value;
			break;

		case 'Partially':
			$months[$month]['some']    += $value;
			break;

		case 'Failed':
			$months[$month]['failed']  += $value;
			break;

		default:
			$months[$month]['waiting'] += $value;
	}
}

?>

<script type="text/javascript">
	google.load("visualization", "1", {packages:["corechart"]});
	google.setOnLoadCallback(drawChart);
	function drawChart() {
		var data = google.visualization.arrayToDataTable([
			['Month', 'Delivered', 'Some', 'Waiting', 'Failed', { role: 'annotation' } ],
<?PHP
		$d = $lowest_month;
		while($d < $highest_month){
			$key = date("Y-m", $d);
			if(isset($months[$key])){
				$data = $months[$key];
			} else {
				$data = array(
					'legend' => date("M Y", $d), 
					'arrived' => 0, 
					'waiting' => 0, 
					'some' => 0, 
					'failed' => 0
				);
			}
			//foreach($months as $month => $data){
			echo "\t\t\t['".$data['legend']."', ".$data['arrived'].", ".$data['some'].", ".$data['waiting'].", ".$data['failed'].", ''],\n";
			$d = strtotime("+1 month", $d);
		}
			?>
		]);

		var options = {
			title: 'Lateness',

			isStacked: true,
			bar: { groupWidth: '75%' },
			legend: { position: 'top', maxLines: 3 },

			animation: { duration : 60 },
			backgroundColor: { fill:'transparent' },
			colors: ['green', 'orange', 'blue', 'red']
		};

		var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
		chart.draw(data, options);
	}
</script>
<div class="row">
<div class="col-md-12">
<ul class="nav nav-tabs">
  <li class="active"><a href="#graphs" data-toggle="tab"><span class="glyphicon glyphicon-stats"></span> Graphs</a></li>
  <li><a href="#data"   data-toggle="tab" ><span class="glyphicon glyphicon-list"></span> Data</a></li>
</ul>
<!-- Tab panes -->
<div class="tab-content">
	<div class="tab-pane active" id="graphs">

<div class="col-md-12" id="chart_div" style="height: 800px;">

</div>

	</div>
	<div class="tab-pane" id="data" >
		<table width="100%" class="table tablecloth text-right">
		<thead>
			<tr class="text-right">
				<th>Month</th>
				<th>Arrived</th>
				<th>Partially</th>
				<th>Waiting</th>
				<th>Failed</th>
				<th>Total</th>
			</tr>
		</thead>
		<tbody>
		<?PHP 
		$d = $lowest_month;
		while($d < $highest_month){
			$key = date("Y-m", $d);
			if(isset($months[$key])){ ?>
				<tr>
					<th><?PHP echo date("F Y", $d);          ?></th>
		       		<td><?PHP printf("%s%.2f", $c, $months[$key]['arrived'])      ?></td>
		       		<td><?PHP printf("%s%.2f", $c, $months[$key]['some'])      ?></td>
		       		<td><?PHP printf("%s%.2f", $c, $months[$key]['waiting'])      ?></td>
		       		<td><?PHP printf("%s%.2f", $c, $months[$key]['failed'])      ?></td>
		       		<td><?PHP printf("%s%.2f", $c, array_sum(array_values($months[$key])))      ?></td>
				</tr>
			<?PHP } else { ?>
				<tr>
					<th><?PHP echo date("F Y", $d);          ?></th>
		       		<td colspan="4" class="text-center">-</td>
		       		<td><?PHP printf("%s%.2f", $c, 0)      ?></td>
				</tr>
			<?PHP } 

			$d = strtotime("+1 month", $d);
		} ?>
		</tbody>
	</table>
	</div>
</div>