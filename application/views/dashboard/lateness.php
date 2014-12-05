<?PHP

$c = currency_symbol($current_user->home_currency);

?>


<script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Project', 'Weeks between end and deadline', 'Weeks after deadline', 'Category',     'Value (<?PHP echo $current_user->home_currency ?>)', 'Status'],
        <?PHP foreach($pledges as $pledge){ 
        	$promised   = strtotime($pledge->date_promised);
        	$date_ended = strtotime($pledge->campaign()->date_end);

        	if($pledge->is_delivered !== 'Yes'){
        		if($pledge->is_delivered == 'Failed'){
        			continue;
        		}
        		if($promised > time()){
        			continue;
        			$promised = 0;
        		}
        	}


        	?>
          ['<?PHP echo addslashes($pledge->campaign()->name) ?>', 
          	   <?PHP printf("%.2f", ($promised - $date_ended) / (60*60*24*7) ) ?>, 
          	   <?PHP printf("%.2f", $pledge->lateness()) ?>,   
          	   "<?PHP echo $pledge->campaign()->category ?>",
          	   <?PHP echo $pledge->convert_to_currency($current_user->home_currency) ?>,
          	   "<?PHP echo $pledge->status() ?>"],

       	<?PHP } ?>
        ]);

        var options = {
          title: 'Lateness',
          hAxis: {title: 'Time Allocated To Deliver (weeks)', logScale: true },
          vAxis: {title: 'Lateness (weeks)'},
          bubble: {textStyle: {fontSize: 11, color: 'none'}, opacity : .5 },

          animation: { duration : 60 },
          backgroundColor: { fill:'transparent' }
        };

        var chart = new google.visualization.BubbleChart(document.getElementById('chart_div'));
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
