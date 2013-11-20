<?PHP

$c = currency_symbol($current_user->home_currency);

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
				<th>Name</th>
				<th>Value</th>
				<th>Category</th>
				<th>Lateness (Weeks)</th>
			</tr>
		</thead>
		<tbody>
		<?PHP foreach($pledges as $pledge_id => $pledge) { 
			if($pledge->is_delivered == "Failed"){
				continue;
			}
			if(new \DateTime($pledge->date_promised) > new \DateTime() && ! $pledge->is_late() ){
				continue;
			}
			$value = $pledge->convert_to_currency($current_user->home_currency);
		?>
		<tr>
			<th><?PHP echo $pledge->campaign()->name          ?></th>
       		<td><?PHP echo view_currency($current_user->home_currency, $value)      ?></td>
       		<td><?PHP echo $pledge->campaign()->category      ?></td>
        	<td><?PHP printf("%.2f", $pledge->lateness() );      ?></td>
		</tr>
		<?PHP } ?>
		</tbody>
	</table>
	</div>
</div>