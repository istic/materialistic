<div class="row">
<div class="col-md-6">
	<form role="form" action="/pledges/from_url" method="POST">
	<div class="form-group">
		<label for="url">New Pledge from URL</label>
		<div class="input-group">
			<input name="url" class="form-control" id="url" placeholder="eg: http://www.kickstarter.com/projects/obsidian/project-eternity" />
			<span class="input-group-btn">
				<input class="btn btn-default" type="submit" value="Search" />
			</span>
		</div>
	</div>
	</form>
</div>
<div class="col-md-6">
	<p><label for="bookmarklet">Or drag this bookmarklet to your bookmarks bar, go to a project page and click it</label>
	<a id="bookmarklet" class="btn btn-primary" href="javascript:if(document.getSelection)%7Bs=document.getSelection();%7Delse%7Bs='';%7D;document.location='http://material.istic.net/pledges/from_url?url='+encodeURIComponent(location.href)">&gt; Materialistic</a></p>
	
</div>
</div>
<div class="row">
<div class="col-md-12">
<ul class="nav nav-tabs">
  <li class="active"><a href="#waiting" data-toggle="tab"><span class="glyphicon glyphicon-time"></span> Waiting</a></li>
  <li><a href="#complete" data-toggle="tab"><span class="glyphicon glyphicon-ok"></span> Complete</a></li>
  <li><a href="#failed" data-toggle="tab"><span class="glyphicon glyphicon-remove"></span> Failed</a></li>
</ul>
<!-- Tab panes -->
<div class="tab-content">
	<div class="tab-pane active" id="waiting" >
		<table width="100%" class="table tablecloth">
		<thead>
			<tr>
				<th>Site</th>
				<th>Name</th>
				<th>Status</th>
				<th>Concluded</th>
				<th>Deadline (<?PHP echo REASONABLE ? 'Reasonable' : 'Original' ?>)</th>
				<th>Value</th>
				<th> </th>
			</tr>
		</thead>
		<tbody>
			<?PHP 
			$completed = array();
			$failed    = array();
			foreach($pledges as $id => $pledge){ 
				if($pledge->is_delivered == "Yes"){
					$completed[] = $pledge;
					continue;
				} elseif($pledge->is_delivered == "Failed"){
					$failed[] = $pledge;
					continue;
				}
			?>
			<tr>
				<th><img src="/assets/img/<?PHP echo $pledge->campaign()->site ?>.png" width="16" /></th>
				<th><a href="<?PHP echo $pledge->campaign()->URL ?>"><?PHP echo $pledge->campaign()->name ?></a></th>
				<td><?PHP 
				if( strtotime($pledge->campaign()->date_end) > time() ){
					echo "Campaigning";
				} elseif( $pledge->is_late() ){
					echo "Late";
				} else {
					echo "Patience";
				}
				?></td>
				<td><?PHP echo date("Y-m-d", strtotime($pledge->campaign()->date_end)) ?></td>
				<td><?PHP echo $pledge->deadline() ?></td>
				<td class="text-right"><?PHP 
					$local = $pledge->convert_to_currency($current_user->home_currency);
					echo view_currency($current_user->home_currency, $local);
				?></td>
				<td class="text-right">
					<div class="btn-group btn-group-xs">
						<a class="btn btn-success" href="/pledges/delivered?id=<?PHP echo $pledge->id ?>">
							<span class="glyphicon glyphicon-gift"></span> Arrived</a>
						<a class="btn btn-danger" href="/pledges/fail?id=<?PHP echo $pledge->id ?>">
							<span class="glyphicon glyphicon-remove"></span> Failed</a>
						<a class="btn btn-default" href="/pledges/edit?id=<?PHP echo $pledge->id ?>">
							<span class="glyphicon glyphicon-edit"></span> Edit</a>
					</div>
				</td>
			</tr>
			<?PHP } ?>
			</tbody>
		</table>
	</div>
	<div class="tab-pane" id="complete">
		<table width="100%" class="table">
		<thead>
			<tr>
				<th>Name</th>
				<th>Status</th>
				<th>Deadline (<?PHP echo REASONABLE ? 'Reasonable' : 'Original' ?>)</th>
				<th>Delivered</th>
				<th>Value</th>
				<th> </th>
			</tr>
		</thead>
		<tbody>
			<?PHP foreach($completed as $id => $pledge){ ?>
			<tr>
				<th><a href="<?PHP echo $pledge->campaign()->URL ?>"><?PHP echo $pledge->campaign()->name ?></a></th>
				<td><?PHP 
				if( $pledge->is_late() ){
					echo "Delivered Late";
				} else {
					echo "Delivered on Time";
				}
				?></td>
				<td><?PHP echo $pledge->deadline() ?></td>
				<td><?PHP echo $pledge->date_delivered ?></td>
				<td class="text-right"><?PHP 
					$local = $pledge->convert_to_currency($current_user->home_currency);
					echo view_currency($current_user->home_currency, $local);
				?></td>
				<td class="text-right">
					<div class="btn-group btn-group-xs">
						<a class="btn btn-info" href="/pledges/undeliver?id=<?PHP echo $pledge->id ?>">
							<span class="glyphicon glyphicon-gift"></span> Undeliver</a>
						<a class="btn btn-default" href="/pledges/edit?id=<?PHP echo $pledge->id ?>">
							<span class="glyphicon glyphicon-edit"></span> Edit</a>
					</div>
				</td>
			</tr>
			<?PHP } ?>
			</tbody>
		</table>
	</div>
	<div class="tab-pane" id="failed">
		<table width="100%" class="table">
		<thead>
			<tr>
				<th>Name</th>
				<th>Promised</th>
				<th>Reasonable</th>
				<th>Value</th>
				<th> </th>
			</tr>
		</thead>
		<tbody>
			<?PHP foreach($failed as $id => $pledge){ ?>
			<tr>
				<th><a href="<?PHP echo $pledge->campaign()->URL ?>"><?PHP echo $pledge->campaign()->name ?></a></th>
				
				<td><?PHP echo $pledge->date_promised_if_exists() ?></td>
				<td><?PHP echo $pledge->date_reasonable_if_exists() ?></td>

				<td class="text-right"><?PHP 
					$local = $pledge->convert_to_currency($current_user->home_currency);
					echo view_currency($current_user->home_currency, $local);
				?></td>
				<td class="text-right">
					<div class="btn-group btn-group-xs">
						<a class="btn btn-success" href="/pledges/unfail?id=<?PHP echo $pledge->id ?>">
							<span class="glyphicon glyphicon-ok"></span> Unfail</a>
						<a class="btn btn-default" href="/pledges/edit?id=<?PHP echo $pledge->id ?>">
							<span class="glyphicon glyphicon-edit"></span> Edit</a>
					</div>
				</td>
			</tr>
			<?PHP } ?>
		</tbody>
		</table>
	</div>
</div>


</div>
</div>
