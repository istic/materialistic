<?PHP

$c = currency_symbol($current_user->home_currency);
$categories = array();
foreach($pledges as $pledge){
	$campaign = $pledge->campaign();
	if(!isset($categories[$campaign->category])){
		$categories[$campaign->category] = array('total' => 0, 'data' => array(), 'lateness' => array());
	}
	$value = $pledge->convert_to_currency($current_user->home_currency);
	$categories[$campaign->category]['total']     += $value;
	$categories[$campaign->category]['data'][]     = $value;
	$categories[$campaign->category]['lateness'][] = $pledge->lateness();

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
		<table width="100%" class="table tablecloth">
		<thead>
			<tr>
				<th>Category</th>
				<th>Total</th>
				<th>Median Value</th>
				<th>Median Lateness</th>
			</tr>
		</thead>
		<tbody>
		<?PHP foreach($categories as $category => $values) { ?>
		<tr>
			<th><?PHP echo $category          ?></th>
       		<td><?PHP printf("%s%.2f", $c, $values['total'])      ?></td>
       		<td><?PHP printf("%s%.2f", $c, array_median($values['data']))      ?></td>
        	<td><?PHP printf("%.2f weeks", array_median($values['lateness']));      ?></td>
		</tr>
		<?PHP } ?>
		</tbody>
	</table>
	</div>
</div>