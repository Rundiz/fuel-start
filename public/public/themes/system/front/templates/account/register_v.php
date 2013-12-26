<article class="general-page-container">
	<h1><?php echo __('account.account_register'); ?></h1>
	<?php if ($config['member_allow_register']['value'] == '1') { ?> 
	
	<?php echo \Form::open(array('class' => 'form-horizontal', 'role' => 'form')); ?> 
		<div class="form-status-placeholder">
			<?php if (isset($form_status) && isset($form_status_message)) { ?> 
			<div class="alert alert-<?php echo str_replace('error', 'danger', $form_status); ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
			<?php } ?> 
		</div>
		<?php echo \Extension\NoCsrf::generate(); ?> 
		
		<?php if (!isset($hide_register_form) || (isset($hide_register_form) && $hide_register_form == false)) { ?> 
		<div class="form-group">
			<label for="account_username" class="col-sm-2 control-label"><?php echo __('account.account_username'); ?>: <span class="txt_require">*</span></label>
			<div class="col-sm-10">
				<?php echo \Form::input('account_username', $account_username, array('id' => 'account_username', 'maxlength' => '255', 'class' => 'form-control')); ?> 
			</div>
		</div>
		<div class="form-group">
			<label for="account_email" class="col-sm-2 control-label"><?php echo __('account.account_email'); ?>: <span class="txt_require">*</span></label>
			<div class="col-sm-10">
				<?php echo \Extension\Form::email('account_email', $account_email, array('id' => 'account_email', 'maxlength' => '255', 'class' => 'form-control')); ?> 
			</div>
		</div>
		<div class="form-group">
			<label for="account_password" class="col-sm-2 control-label"><?php echo __('account.account_password'); ?>: <span class="txt_require">*</span></label>
			<div class="col-sm-7">
				<?php echo \Form::password('account_password', $account_password, array('id' => 'account_password', 'maxlength' => '70', 'class' => 'form-control')); ?> 
			</div>
		</div>
		<div class="form-group">
			<label for="account_confirm_password" class="col-sm-2 control-label"><?php echo __('account.account_confirm_password'); ?>: <span class="txt_require">*</span></label>
			<div class="col-sm-7">
				<?php echo \Form::password('account_confirm_password', $account_confirm_password, array('id' => 'account_confirm_password', 'maxlength' => '70', 'class' => 'form-control')); ?> 
			</div>
		</div>
		<div class="form-group">
			<label for="account_captcha" class="col-sm-2 control-label"><?php echo __('account.account_captcha'); ?>: <span class="txt_require">*</span></label>
			<div class="col-sm-5">
				<img src="<?php echo Uri::createNL(\Theme::instance()->asset_path('img/securimage_show.php')); ?>" alt="securimage" class="captcha" />
				<a href="#" onclick="$('.captcha').attr('src', '<?php echo Uri::createNL(\Theme::instance()->asset_path('img/securimage_show.php')); ?>?' + Math.random()); this.blur(); return false;" tabindex="-1"><img src="<?php echo Uri::createNL(\Theme::instance()->asset_path('img/reload.gif')); ?>" alt="" /></a>
				<div>
					<?php echo \Form::input('captcha', $captcha, array('id' => 'account_captcha', 'class' => 'form-control input-captcha', 'placeholder' => __('account.account_captcha_enter_text_you_see_in_image'))); ?> 
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				<button type="submit" class="btn btn-primary"><?php echo __('account.account_submit'); ?></button> <a href="<?php echo \Uri::create('account/resend-activate'); ?>" class="btn btn-default btn-sm"><?php echo \Lang::get('account.account_didnot_recieve_confirm_registration_email'); ?></a>
			</div>
		</div>
		<?php } // endif; ?> 
	<?php echo \Form::close(); ?> 
	
	<?php } else { ?> 
	<div class="alert alert-danger">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<p><?php echo __('account.account_administrator_does_not_allow_to_register'); ?></p>
	</div>
	<?php } // endif; ?> 
</article>