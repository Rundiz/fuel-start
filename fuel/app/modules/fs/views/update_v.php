<h1><?php echo \Lang::get('fs_updater'); ?></h1>

<?php echo \Extension\Form::openMultipart(array('class' => 'form-horizontal', 'role' => 'form')); ?> 
	<div class="form-status-placeholder">
		<?php if (isset($form_status) && isset($form_status_message)) { ?> 
		<div class="alert alert-<?php echo str_replace('error', 'danger', $form_status); ?>"><?php echo $form_status_message; ?></div>
		<?php } ?> 
	</div>
	<div class="hidden csrf-container">
		<?php echo \Extension\NoCsrf::generate(); ?> 
	</div>

	<?php if (!isset($hide_form) || (isset($hide_form) && $hide_form === false)) { ?> 
	<input type="hidden" name="act" value="update">
	<button type="submit" class="btn btn-primary fs-update-button"><?php echo \Lang::get('fs_update_now'); ?></button>
	<?php } // endif; ?> 
<?php echo \Form::close(); ?> 

	<script>
		$(function() {
			$('.fs-update-button').click(function() {
				$(this).prepend('<span class="fa fa-spinner fa-spin"></span> ');
			});
		});
	</script>