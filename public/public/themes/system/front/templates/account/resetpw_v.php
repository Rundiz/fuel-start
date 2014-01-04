<article class="general-page-container">
	<h1><?php echo \Lang::get('account.account_reset_password'); ?></h1>
	<?php if ($reset_action == 'reset') { ?> 
	<p><?php echo \Lang::get('account.account_please_enter_your_new_password'); ?></p>
	<?php } // endif; ?> 
	
	<?php echo \Form::open(array('action' => \Uri::main(), 'class' => 'form-horizontal', 'role' => 'form')); ?> 
		<div class="form-status-placeholder">
			<?php if (isset($form_status) && isset($form_status_message)) { ?> 
			<div class="alert alert-<?php echo str_replace('error', 'danger', $form_status); ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
			<?php } ?> 
		</div>
		<?php echo \Extension\NoCsrf::generate(); ?> 
		
		<?php if (!isset($hide_form) || (isset($hide_form) && $hide_form === false)) { ?> 
		<div class="form-group">
			<label class="col-sm-2 control-label" for="account_password"><?php echo __('account.account_password'); ?>: <span class="txt_require">*</span></label>
			<div class="col-sm-4">
				<?php echo \Extension\Form::password('account_password', '', array('id' => 'account_password', 'maxlength' => '70', 'class' => 'form-control')); ?> 
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="account_confirm_password"><?php echo __('account.account_confirm_password'); ?>: <span class="txt_require">*</span></label>
			<div class="col-sm-4">
				<?php echo \Extension\Form::password('account_confirm_password', '', array('id' => 'account_confirm_password', 'maxlength' => '70', 'class' => 'form-control')); ?> 
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				<button type="submit" class="btn btn-primary"><?php echo __('account.account_submit'); ?></button>
			</div>
		</div>
		<?php } // endif; ?> 
	<?php echo \Form::close(); ?> 
</article>