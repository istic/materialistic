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

	$month = date("M Y", $date_ended);
	if(!isset($months[$month])){
		$months[$month] = array('arrived' => 0, 'waiting' => 0, 'some' => 0, 'failed' => 0);
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
<div class="row">
<div class="col-md-12">
<ul class="nav nav-tabs">
  <li><a href="#graphs" data-toggle="tab"><span class="glyphicon glyphicon-stats"></span> Graphs</a></li>
  <li class="active"><a href="#data"   data-toggle="tab" ><span class="glyphicon glyphicon-list"></span> Data</a></li>
</ul>
<!-- Tab panes -->
<div class="tab-content">
	<div class="tab-pane" id="graphs">
	<div style="padding: 2em;" class="text-center"><img src="/assets/img/underconstruction.gif"/></div>
	</div>
	<div class="tab-pane active" id="data" >
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
			$key = date("M Y", $d);
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