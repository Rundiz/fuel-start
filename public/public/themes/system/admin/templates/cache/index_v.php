<h1><?php echo __('cacheman'); ?></h1>

<?php echo \Form::open(array('class' => 'form-horizontal', 'role' => 'form')); ?> 
	<div class="form-status-placeholder">
		<?php if (isset($form_status) && isset($form_status_message)) { ?> 
		<div class="alert alert-<?php echo str_replace('error', 'danger', $form_status); ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
		<?php } ?> 
	</div>
	<div class="hidden csrf-container">
		<?php echo \Extension\NoCsrf::generate(); ?> 
	</div>

	<p><?php echo __('cacheman_please_select_action'); ?>:</p>
	<select name="act" class="chosen-select">
		<option value=""></option>
		<option value="clear"><?php echo __('cacheman_clear_cache'); ?></option>
	</select>
	<button type="submit" class="btn btn-warning"><?php echo __('admin_submit'); ?></button>
<?php echo \Form::close(); ?>