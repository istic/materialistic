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
  'Delivered' => 0,
  'Waiting'   => 0,
  'Failed'    => 0
);
$chart_by_value = array(
  'Delivered' => 0,
  'Waiting'   => 0,
  'Failed'    => 0
);
$chart_by_lateness = array(
  'Delivered On Time'  => 0,
  'Delivered Late'     => 0,

  'In Progress - Late' => 0,
  'In Progress'        => 0,

//  'Failed'             => 0
);

$lateness_by_category = array();
$cost_by_category = array();

$howlate = array(
  'Days (0-14)'        => 0,
  'Weeks (2-6)'        => 0,
  'Months (2-6)'       => 0,
  'More Months (6-14)' => 0,
  'Years'              => 0
);

$define_howlate = array(
  'Days (0-14)'        => 14,
  'Weeks (2-6)'        => 42,
  'Months (2-6)'       => 180,
  'More Months (6-14)' => 420,
  'Years'              => 365*15
);

foreach($pledges as $pledge){
  $value = $pledge->convert_to_currency($current_user->home_currency);

  $stats['total']['count']++;
  $stats['total']['total'] += $value;
  $stats['total']['totals'][] = $value;

  if($pledge->is_delivered == "Failed"){ // FAILED
    $stats['failed']['count']++;
    $stats['failed']['total'] += $value;
    $stats['failed']['totals'][] = $value;

    $chart_by_count['Failed']++;
    $chart_by_value['Failed'] += $value;

    //$chart_by_lateness['Failed']++;

  } elseif($pledge->is_delivered != "Yes"){ // NOT DELIVERED
    $stats['waiting']['count']++;
    $stats['waiting']['total'] += $value;
    $stats['waiting']['totals'][] = $value;

    $chart_by_count['Waiting']++;
    $chart_by_value['Waiting'] += $value;

    if( $pledge->is_late() ){
      $chart_by_lateness['In Progress - Late']++;
    } else {
      $chart_by_lateness['In Progress']++;
    }

  } else { // DELIVERED
    $stats['delivered']['count']++;
    $stats['delivered']['total'] += $value;
    $stats['delivered']['totals'][] = $value;

    $chart_by_count['Delivered']++;
    $chart_by_value['Delivered'] += $value;

    if($pledge->is_late()){
      $chart_by_lateness['Delivered Late']++;
    } else {
      $chart_by_lateness['Delivered On Time']++;
    }

  }

  // Chart by lateness

  if($pledge->is_late() && $pledge->is_delivered !== "Failed"){
      $days = $pledge->lateness(60*60*24);
      $counted = False;
      foreach($define_howlate as $index => $boundry){
        if($days < $boundry and !$counted){
          //print $pledge->campaign()->name.' '.$days.'<br/>';
          $howlate[$index]++;
          $counted = True;
        }
      }
  }


  // Table of average lateness (days)

  $category = $pledge->campaign()->category;
  if(!isset($lateness_by_category[$category])) {
    $lateness_by_category[$category] = array(
      'count' => 0,
      'sum'   => 0,
    );
  }
  $lateness_by_category[$category]['count']++;
  $lateness_by_category[$category]['sum'] += abs($pledge->lateness(60*60*24));

  // Table of average cost (days)

  if(!isset($cost_by_category[$category])){
    $cost_by_category[$category] = array(
      'count' => 0,
      'sum'   => 0,
    );
  }
  $cost_by_category[$category]['count']++;
  $cost_by_category[$category]['sum'] += abs($value);

}

$chart_late_by_category = array();
foreach($lateness_by_category as $category => $data){
  $chart_late_by_category[$category] = $data['sum'] / $data['count'];
}

$chart_cost_by_category = array();
foreach($cost_by_category as $category => $data){
  $chart_cost_by_category[$category] = $data['sum'] / $data['count'];
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
          title: 'By Count',
          backgroundColor: { fill:'transparent' },
          colors: ['green', 'blue', 'red']
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
          title: 'By Value',
          backgroundColor: { fill:'transparent' },
          colors: ['green', 'blue', 'red']
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
          title: 'Lateness by Count',
          backgroundColor: { fill:'transparent' },
          colors: ['green', 'darkgreen', 'blue', 'darkblue', 'red']
        };

        var chart = new google.visualization.PieChart(document.getElementById('by_lateness'));
        chart.draw(data, options);

        // Chart by How Late

        var data = google.visualization.arrayToDataTable([
          ['Status', 'Value'],
          <?PHP 
          foreach($howlate as $section => $value){
            echo "['$section',     $value],\n";
          }
          ?>
        ]);

        var options = {
          title: 'Lateness',
          backgroundColor: { fill:'transparent' },
        };

        var chart = new google.visualization.PieChart(document.getElementById('how_late'));
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
        <td><?PHP echo $c.number_format(sprintf("%.2f", $stats['total']['total']),2) ?></td>
        <td><?PHP echo $c.number_format(sprintf("%.2f", array_average($stats['total']['totals'])),2)     ?></td>
        <td><?PHP echo $c.number_format(sprintf("%.2f", array_median($stats['total']['totals'])),2)      ?></td>
      </tr>
      <tr>
        <td>Waiting</td>
        <td><?PHP echo $stats['waiting']['count'] ?></td>
        <td><?PHP echo $c.number_format(sprintf("%.2f", $stats['waiting']['total']),2) ?></td>
        <td><?PHP echo $c.number_format(sprintf("%.2f", array_average($stats['waiting']['totals'])),2) ?></td>
        <td><?PHP echo $c.number_format(sprintf("%.2f", array_median($stats['waiting']['totals'])),2)  ?></td>
      </tr>
      <tr>
        <td>Delivered</td>
        <td><?PHP echo $stats['delivered']['count'] ?></td>
        <td><?PHP echo $c.number_format(sprintf("%.2f", $stats['delivered']['total']),2) ?></td>
        <td><?PHP echo $c.number_format(sprintf("%.2f", array_average($stats['delivered']['totals'])),2) ?></td>
        <td><?PHP echo $c.number_format(sprintf("%.2f", array_median($stats['delivered']['totals'])),2)  ?></td>
      </tr>
      <tr>
        <td>Failed</td>
        <td><?PHP echo $stats['failed']['count'] ?></td>
        <td><?PHP echo $c.number_format(sprintf("%.2f", $stats['failed']['total']),2) ?></td>
        <td><?PHP echo $c.number_format(sprintf("%.2f", array_average($stats['failed']['totals'])),2)    ?></td>
        <td><?PHP echo $c.number_format(sprintf("%.2f", array_median($stats['failed']['totals'])),2)     ?></td>
      </tr>
    </table>
  </div>

</div>
<div class="row">
  <div class="col-sm-6 col-md-6" style="height: 300px;" id="how_late"></div>
  <div class="col-sm-6 col-md-6">
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

<div class="row">
  <div class="col-sm-6 col-md-6">
    <table class="table">
      <tr>
        <th>Category</th>
        <th>Count</th>
      </tr>
      <?PHP foreach($chart_cost_by_category as $category => $value){ ?>
      <tr>
        <td><?PHP echo $category ?></td>
        <td><?PHP echo $c.number_format(sprintf("%.2f", $value),2)   ?></td>
      </tr>
      <?PHP } ?>
    </table>
  </div>
  <div class="col-sm-6 col-md-6">
    <table class="table">
      <tr>
        <th>Category</th>
        <th>Average Lateness (days)</th>
      </tr>
      <?PHP foreach($chart_late_by_category as $category => $value){ ?>
      <tr>
        <td><?PHP echo $category ?></td>
        <td><?PHP echo number_format(sprintf("%.2f", $value),2)   ?></td>
      </tr>
      <?PHP } ?>
    </table>
  </div>
</div>
