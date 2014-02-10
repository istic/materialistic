
<div class="row">
	<div class="col-sm-9 col-md-8 col-md-push-2">
		<?PHP include(VIEWPATH.'fragments/campaign_panel.php'); ?>
		<form role="form" method="POST" action='/pledges/delivered'>
			<input type="hidden" id="id" name="id" class="form-control" value="<?php echo set_value('id', $pledge->id); ?>">
			
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

			<div class="form-group <?PHP echo form_error('date_delivered') ? 'has-error' : '' ?>">
				<label class="control-label" id="date_delivered">Delivery Date</label>
				<input type="text" id="date_delivered" name="date_delivered" class="form-control datepicker" value="<?php echo set_value('date_delivered', $pledge->date_delivered_if_exists() ); ?>">
				<span class="help-block">Date you recieved everything, if you checked "Yes" above (Optional).</span>
			</div>
			<div class="col-md-6 col-md-push-6">
				<input type="submit" class="btn btn-default btn-block" value="Save Pledge">
			</div>
		</form>
	</div>
</div>