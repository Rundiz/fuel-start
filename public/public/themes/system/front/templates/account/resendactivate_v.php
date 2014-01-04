<article class="general-page-container">
	<h1><?php echo \Lang::get('account.account_resend_confirm_registration_email'); ?></h1>
	
	<?php echo \Form::open(array('class' => 'form-horizontal', 'role' => 'form')); ?> 
		<div class="form-status-placeholder">
			<?php if (isset($form_status) && isset($form_status_message)) { ?> 
			<div class="alert alert-<?php echo str_replace('error', 'danger', $form_status); ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
			<?php } ?> 
		</div>
		<?php echo \Extension\NoCsrf::generate(); ?> 
	
		<div class="form-group">
			<label class="col-sm-2 control-label" for="account_email"><?php echo __('account.account_email'); ?>: <span class="txt_require">*</span></label>
			<div class="col-sm-4">
				<?php echo \Extension\Form::email('account_email', (isset($account_email) ? $account_email : ''), array('id' => 'account_email', 'maxlength' => '255', 'class' => 'form-control')); ?> 
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				<button type="submit" class="btn btn-primary"><?php echo __('account.account_send'); ?></button>
			</div>
		</div>
	<?php echo \Form::close(); ?> 
</article>