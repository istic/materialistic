
<div class="row">
  <div class="col-sm-6 col-md-3">
    <a href="#" class="thumbnail">
      <img src="<?PHP echo $campaign->photo ?>" alt="...">
    </a>
  </div>
  <div class="col-sm-6 col-md-6">
  	<h1><a href="<?PHP echo $campaign->URL; ?>"><?PHP echo $campaign->name ?></a></h1>
  	<p><?PHP echo $campaign->status; ?>, <?PHP echo number_format($campaign->backer_count); ?> backers
  	raised <?PHP echo view_currency($campaign->currency, $campaign->pledged); ?> of a total of 
  		<?PHP echo view_currency($campaign->currency, $campaign->target); ?> 


<br/>

  	(<?PHP number_format(printf('%d', ($campaign->pledged/$campaign->target) * 100)); ?>% Funded)
  	</p>
  	<p><?PHP echo $campaign->date_start; ?> &ndash; <?PHP echo $campaign->date_end; ?></p>
    <?PHP if(isset($action)){ ?>
    	<div class="pull-right">
    		<?PHP echo $action ?>
    	</div>
    <?PHP } ?>
  </div>
</div>
