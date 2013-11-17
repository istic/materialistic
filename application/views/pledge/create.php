<?PHP include(VIEWPATH.'fragments/campaign_panel.php'); ?>
<div class="row">
	<div class="col-sm-9 col-md-6">
		<?PHP
			if(isset($error)){
				?>
				<div class="alert alert-danger"><?PHP echo $error ?></div>
				<?PHP
			}
             echo validation_errors();
		?>

		<h1>Create new Pledge</h1>

		<form role="form" method="POST" action='/pledges/create'>
			<input type="hidden" id="campaign_id" name="campaign_id" class="form-control" value="<?php echo $campaign->id; ?>">
			<input type="hidden" id="id" name="id" class="form-control" value="<?php echo set_value('id', $pledge->id); ?>">
			<div class="form-group <?PHP echo form_error('backing_tier') ? 'has-error' : '' ?>">
				<label class="control-label" id="backing_tier">Backing Tier</label>
				<input type="text" id="backing_tier" name="backing_tier" class="form-control" value="<?php echo set_value('backing_tier', $pledge->backing_tier); ?>">
			</div>
			<div class="form-group <?PHP echo form_error('description') ? 'has-error' : '' ?>">
				<label class="control-label" id="description">Description</label>
				<textarea id="description" name="description" class="form-control"><?php echo set_value('description', $pledge->description); ?></textarea>
				<span class="help-block">Things you're expecting to recieve (Optional)</span>
			</div>
			<div class="form-group <?PHP echo form_error('description') ? 'has-error' : '' ?>">
				<label class="control-label" id="value">Pledge</label>
				<div class="input-group">
					<span class="input-group-addon"><?PHP echo currency_symbol($campaign->currency) ?></span>
					<input type="text" class="form-control" name="value" id="value" value="<?php echo set_value('value', $pledge->value); ?>">
				</div>
				<span class="help-block">(Optional)</span>
			</div>
			<div class="form-group <?PHP echo form_error('is_delivered') ? 'has-error' : '' ?>">
				<label class="control-label" id="is_delivered">Has Been Delivered?</label>
				<div class="radio">
				  <label>
				    <input type="radio" name="is_delivered" id="is_delivered1" value="Yes"
				     <?php echo set_value('is_delivered', $pledge->is_delivered) == 'Yes' ? 'checked' : '' ?>>
				    	Yes &mdash; I have everything I was expecting
				  </label>
				 </div>
				 <div class="radio">
				  <label>
				    <input type="radio" name="is_delivered" id="is_delivered3" value="Partially"
				     <?php echo set_value('is_delivered', $pledge->is_delivered) == 'Partially' ? 'checked' : '' ?>>
				    	Partially &mdash; Some things have arrived
				  </label>
				  </div>
				 <div class="radio">
				  <label>
				    <input type="radio" name="is_delivered" id="is_delivered3" value="No"
				     <?php echo set_value('is_delivered', $pledge->is_delivered) == 'No' ? 'checked' : '' ?>>
				    	No &mdash; There is an empty space in my life
				  </label>
				</div>	
			</div>
			<div class="form-group <?PHP echo form_error('date_promised') ? 'has-error' : '' ?>">
				<label class="control-label" id="date_promised">Promised Delivery Date</label>
				<input type="text" id="date_promised" name="date_promised" class="form-control datepicker" value="<?php echo set_value('date_promised', $pledge->date_promised); ?>">
				<span class="help-block">Original delivery date according to pledge box</span>
			</div>
			<div class="form-group <?PHP echo form_error('date_reasonable') ? 'has-error' : '' ?>">
				<label class="control-label" id="date_reasonable"><q>Reasonable</q> Delivery Date</label>
				<input type="text" id="date_reasonable" name="date_reasonable" class="form-control datepicker" value="<?php echo set_value('date_reasonable', $pledge->date_reasonable); ?>">
				<span class="help-block">Given stretch-goals and such, this is the point you would consider the delivery 'late'</span>
			</div>
			<div class="form-group <?PHP echo form_error('date_delivered') ? 'has-error' : '' ?>">
				<label class="control-label" id="date_delivered">Delivery Date</label>
				<input type="text" id="date_delivered" name="date_delivered" class="form-control datepicker" value="<?php echo set_value('date_delivered', $pledge->date_delivered); ?>">
				<span class="help-block">Date you recieved everything, if you checked "Yes" above (Optional).</span>
			</div>
			<div class="col-md-6 col-md-push-6">
				<input type="submit" class="btn btn-default btn-block" value="Register Pledge">
			</div>

		</form>
		</div>
		</div>