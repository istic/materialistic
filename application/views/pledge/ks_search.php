<div class="row">
<h1>Possible Matches</h1>

<?PHP if(count($campaigns) == 0){ ?>

	<p>Couldn't find anything for "<?PHP echo $query ?>", but the search API's not great. Try something else?</p>

	<form method="POST" role="form" action="/pledges/kickstarter" >
	<div class="form-group col-md-6">
		<label class="control-label" for="query">Search For</label>

		<div class="input-group">
			<input class="form-control" value="<?PHP echo $query ?>" name="query" id="query" />
			<span class="input-group-btn">
				<input class="btn btn-default" type="submit" value="Search" />
			</span>
		</div><!-- /input-group -->
	</div>
	</form> 


<?PHP } else { ?>

	<p>Not sure what you meant by that, here are some options for "<?PHP echo $query ?>"</p>

	<?PHP

	foreach($campaigns as $campaign){
		$action = '<a class="btn btn-default" href="/pledges/create?campaign='.$campaign->id.'">Use this Campaign</a>';
		include(VIEWPATH.'fragments/campaign_panel.php');
	}

}
?>
</div>