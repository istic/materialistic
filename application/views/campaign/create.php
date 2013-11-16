
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

		<h1>Create new Campaign</h1>

		<form role="form" method="POST" action='/campaigns/create'>
			<div class="form-group <?PHP echo form_error('name') ? 'has-error' : '' ?>">
				<label class="control-label" id="name">Name</label>
				<input type="text" id="name" name="name" class="form-control" value="<?php echo set_value('name'); ?>">
			</div>
			<div class="form-group <?PHP echo form_error('url') ? 'has-error' : '' ?>">
				<label class="control-label" id="url">URL</label>
				<input type="text" class="form-control" id="url" name="url" value="<?php echo set_value('url'); ?>">
			</div>
			
			
			<div class="row">
				<div class="form-group col-md-6 <?PHP echo form_error('target') ? 'has-error' : '' ?>">
					<label class="control-label" id="target">Funding Goal</label>
					<input type="number" class="form-control" id="target" name="target" value="<?php echo set_value('target'); ?>">
				</div>
				<div class="form-group col-md-6 <?PHP echo form_error('currency') ? 'has-error' : '' ?>">
					<label class="control-label" id="target">Currency</label>
					<select class="form-control col-md-1" id="currency" name="currency" value="<?php echo set_value('currency'); ?>">
						<option value="USD">USD ($)</option>
						<option value="GBP">GBP (&pound;)</option>
						<option value="CAD">CAD ($)</option>
						<option value="AUS">AUS ($)</option>
					</select>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-md-6 <?PHP echo form_error('status') ? 'has-error' : '' ?>">
					<label class="control-label" id="status">Status</label>
					<select class="form-control" id="status" name="status">
						<option <?php echo set_value('name') == 'live' ? 'SELECTED' : '' ?>>live</option>
						<option <?php echo set_value('name') == 'successful' ? 'SELECTED' : '' ?>>successful</option>
						<option <?php echo set_value('name') == 'failed' ? 'SELECTED' : '' ?>>failed</option>
						<option <?php echo set_value('name') == 'suspended' ? 'SELECTED' : '' ?>>suspended</option>
						<option <?php echo set_value('name') == 'deleted' ? 'SELECTED' : '' ?>>deleted</option>
						<option <?php echo set_value('name') == 'canceled' ? 'SELECTED' : '' ?>>canceled</option>
					</select>
				</div>
				<div class="form-group col-md-6 <?PHP echo form_error('creator') ? 'has-error' : '' ?>">
					<label class="control-label" id="creator">Creator</label>
					<input type="text" class="form-control" id="creator" name="creator" value="<?php echo set_value('creator'); ?>">
				</div>
			</div>
			<div class="row">
			<div class="form-group col-md-6 <?PHP echo form_error('category') ? 'has-error' : '' ?>">
				<label class="control-label" id="category">Category</label>
				<input type="text" class="form-control" id="category" name="category" value="<?php echo set_value('category'); ?>">
			</div>
			<div class="form-group  col-md-6 <?PHP echo form_error('country') ? 'has-error' : '' ?>">
				<label class="control-label" id="country">Country</label>
				<input type="text" class="form-control" id="country" name="country" maxlength="2" value="<?php echo set_value('country'); ?>"> (2 letter code)
			</div>
			<div class="col-md-6 col-md-push-6">
				<input type="submit" class="btn btn-default btn-block" value="Create Campaign">
			</div>
		</form>
	</div>
</div>