<h1>Possible Matches</h1>
<p>Not sure what you meant by that, here are some options</p>
<?PHP

foreach($campaigns as $campaign){
	$action = '<a class="btn btn-default" href="/pledge/create/campaign='.$campaign->id.'">Use this Campaign</a>';
	include(VIEWPATH.'fragments/campaign_panel.php');
}