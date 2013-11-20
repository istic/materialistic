<?php
$c = currency_symbol($current_user->home_currency);

$stats = array(
  'total' => array(
    'count'  => 0,
    'total'  => 0,
    'totals' => array()
  ),
  'waiting' => array(
    'count'  => 0,
    'total'  => 0,
    'totals' => array()
  ),
  'delivered' => array(
    'count'  => 0,
    'total'  => 0,
    'totals' => array()
  ),
  'failed' => array(
    'count'  => 0,
    'total'  => 0,
    'totals' => array()
  )
);

$chart_by_count = array(
  'Waiting'   => 0,
  'Delivered' => 0,
  'Failed'    => 0
);
$chart_by_value = array(
  'Waiting'   => 0,
  'Delivered' => 0,
  'Failed'    => 0
);
$chart_by_lateness = array(
  'Delivered On Time'  => 0,
  'In Progress'        => 0,
  'Delivered Late'     => 0,
  'In Progress - Late' => 0,
  'Failed'             => 0
);

foreach($pledges as $pledge){
  $value = $pledge->convert_to_currency($current_user->home_currency);

  $stats['total']['count']++;
  $stats['total']['total'] += $value;
  $stats['total']['totals'][] = $value;

  if($pledge->is_delivered == "Failed"){
    $stats['failed']['count']++;
    $stats['failed']['total'] += $value;
    $stats['failed']['totals'][] = $value;

    $chart_by_count['Failed']++;
    $chart_by_value['Failed'] += $value;

    $chart_by_lateness['Failed']++;

  } elseif($pledge->is_delivered != "Yes"){
    $stats['waiting']['count']++;
    $stats['waiting']['total'] += $value;
    $stats['waiting']['totals'][] = $value;

    $chart_by_count['Delivered']++;
    $chart_by_value['Delivered'] += $value;

    if($pledge->is_late()){
      $chart_by_lateness['Delivered Late']++;
    } else {
      $chart_by_lateness['Delivered On Time']++;
    }

  } else {
    $stats['delivered']['count']++;
    $stats['delivered']['total'] += $value;
    $stats['delivered']['totals'][] = $value;

    $chart_by_count['Waiting']++;
    $chart_by_value['Waiting'] += $value;


    if( $pledge->is_late() ){
      $chart_by_lateness['In Progress - Late']++;
    } else {
      $chart_by_lateness['In Progress']++;
    }

  }


}
?>

<script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        // Chart by Count
        var data = google.visualization.arrayToDataTable([
          ['Status', 'Count'],
          <?PHP 
          foreach($chart_by_count as $section => $count){
            echo "['$section',     $count],\n";
          }
          ?>
        ]);

        var options = {
          title: 'By Count'
        };

        var chart = new google.visualization.PieChart(document.getElementById('by_item'));
        chart.draw(data, options);

        // Chart by Value

        var data = google.visualization.arrayToDataTable([
          ['Status', 'Count'],
          <?PHP 
          foreach($chart_by_value as $section => $value){
            echo "['$section',     $value],\n";
          }
          ?>
        ]);

        var options = {
          title: 'By Value'
        };

        var chart = new google.visualization.PieChart(document.getElementById('by_value'));
        chart.draw(data, options);


        // Chart by Lateness

        var data = google.visualization.arrayToDataTable([
          ['Status', 'Value'],
          <?PHP 
          foreach($chart_by_lateness as $section => $value){
            echo "['$section',     $value],\n";
          }
          ?>
        ]);

        var options = {
          title: 'By Value'
        };

        var chart = new google.visualization.PieChart(document.getElementById('by_lateness'));
        chart.draw(data, options);

      }
    </script>

<div class="row">
  <div class="col-sm-6 col-md-6" style="height: 300px;" id="by_item"></div>
  <div class="col-sm-6 col-md-6" style="height: 300px;" id="by_value"></div>
</div>
<div class="row">
  <div class="col-sm-6 col-md-6" style="height: 300px;" id="by_lateness"></div>
  <div class="col-sm-6 col-md-6">
    <table class="table">
      <tr>
        <th> </th>
        <th>Count</th>
        <th>Total</th>
        <th>Average</th>
        <th>Median</th>
      </tr>
      <tr>
        <td>Total</td>
        <td><?PHP echo $stats['total']['count'] ?></td>
        <td><?PHP printf("%s%.2f", $c, $stats['total']['total']) ?></td>
        <td><?PHP printf("%s%.2f", $c, array_average($stats['total']['totals']))     ?></td>
        <td><?PHP printf("%s%.2f", $c, array_median($stats['total']['totals']))      ?></td>
      </tr>
      <tr>
        <td>Waiting</td>
        <td><?PHP echo $stats['waiting']['count'] ?></td>
        <td><?PHP printf("%s%.2f", $c, $stats['waiting']['total']) ?></td>
        <td><?PHP printf("%s%.2f", $c, array_average($stats['waiting']['totals']))   ?></td>
        <td><?PHP printf("%s%.2f", $c, array_median($stats['waiting']['totals']))    ?></td>
      </tr>
      <tr>
        <td>Delivered</td>
        <td><?PHP echo $stats['delivered']['count'] ?></td>
        <td><?PHP printf("%s%.2f", $c, $stats['delivered']['total']) ?></td>
        <td><?PHP printf("%s%.2f", $c, array_average($stats['delivered']['totals'])) ?></td>
        <td><?PHP printf("%s%.2f", $c, array_median($stats['delivered']['totals']))  ?></td>
      </tr>
      <tr>
        <td>Failed</td>
        <td><?PHP echo $stats['failed']['count'] ?></td>
        <td><?PHP printf("%s%.2f", $c, $stats['failed']['total']) ?></td>
        <td><?PHP printf("%s%.2f", $c, array_average($stats['failed']['totals']))    ?></td>
        <td><?PHP printf("%s%.2f", $c, array_median($stats['failed']['totals']))     ?></td>
      </tr>
    </table>

    <table class="table">
      <tr>
        <th>Status</th>
        <th>Count</th>
      </tr>
      <?PHP foreach($chart_by_lateness as $status => $count){ ?>
      <tr>
        <td><?PHP echo $status ?></td>
        <td><?PHP echo $count  ?></td>
      </tr>
      <?PHP } ?>
    </table>
  </div>
</div>