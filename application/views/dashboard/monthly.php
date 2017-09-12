<?PHP

$c = currency_symbol($current_user->home_currency);
$months = array();
$lowest_month = false;
$highest_month = false;

$htmlIndex = array();

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
    $legend = date("M Y", $date_ended);
    if(!isset($months[$month])){
        $months[$month] = array(
            'legend' => $legend, 
            'arrived' => false, 
            'waiting' => false, 
            'some' => false, 
            'failed' => false
        );
        $htmlIndex[$legend] = array();
    }
    $value = $pledge->convert_to_currency($current_user->home_currency);

     $htmlIndex[$legend][] = $pledge;


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
        $('#projectsList tr').hide();

        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Month');
        data.addColumn('number', 'Delivered');
        data.addColumn('number', 'Some');
        data.addColumn('number', 'Waiting');
        data.addColumn('number', 'Failed');
        <?PHP
        $d = $lowest_month;
        while($d < $highest_month){
            $key = date("Y-m", $d);
            if(!isset($months[$key])){
                $row = array(
                    date("M Y", $d),  //'legend' 
                    'null',  //'arrived'
                    'null',  //'waiting'
                    'null',  //'some'   
                    'null' //'failed' 
                );
            } else {
                // var_dump($months[$key]);
                $row = array_values($months[$key]);

                $row = array(
                    date("M Y", $d),  //'legend' 
                    $months[$key]['arrived'] ? $months[$key]['arrived'] : 'null', 
                    $months[$key]['some']    ? $months[$key]['some']    : 'null',  
                    $months[$key]['waiting'] ? $months[$key]['waiting'] : 'null',   
                    $months[$key]['failed']  ? $months[$key]['failed']  : 'null',  
                );

                // foreach(array('arrived', 'some', 'waiting', 'failed') as $state){
                //     if ($months[$key][$state]){
                //         $row[] = sprintf('{v: %s, d: "Fooozle"}', $months[$key][$state]);
                //     } else {
                //         $row[] = 'null';
                //     }
                // }
            }

            printf("data.addRow(['%s', %s]);\n", array_shift($row), implode(",", $row));

            // Increase loop
            $d = strtotime("+1 month", $d);
            
        }
        ?>


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

        google.visualization.events.addListener(chart, 'select', selectHandler); 

        function selectHandler(e)     {   
            var month = data.getValue(chart.getSelection()[0].row, 0);
            // alert(data.getValue(chart.getSelection()[0].row, 0));

            $('#projectsList tbody tr:not([data-month="'+month+'"])').hide();
            $('#projectsList tbody tr[data-month="'+month+'"], #projectsList thead tr').show();
        }
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

    <table width="100%" class="table tablecloth text-right" id="projectsList">
        <thead>
            <tr>
                <th>Project</th>
                <th>Value</th>
                <th>State</th>
            </tr>
        </thead>
        <tbody>
        <?PHP 
        $lastheading = false;
        foreach($htmlIndex as $month => $data){
            $template = '<tr class="text-left projectsListTr" data-month="%s"><td>%s</td><td>%s</td><td>%s</td></tr>'."\n";
            if($lastheading !== $month){
                printf('<tr class="text-left projectsListTr" data-month="%1$s"><th colspan=3>%1$s</th></tr>'."\n", $month);
                $lastheading = $month;
            }
            foreach($data as $pledge){
                $value = $pledge->convert_to_currency($current_user->home_currency);
                $state = $pledge->is_delivered;

                switch ($state){
                    case 'Yes':
                        $state = "Delivered";
                        break;
                    case "No":
                        $state = "Waiting";
                }

                $name = '<img src="/assets/img/'.$pledge->campaign()->site.'.png" width="16" />';
                $name .= ' <a href="'.$pledge->campaign()->URL.'">'.$pledge->campaign()->name.'</a>';

                printf($template, $month, $name, $value, $pledge->is_delivered);
            }
        }
        ?>
        </tbody>
    </table>

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
