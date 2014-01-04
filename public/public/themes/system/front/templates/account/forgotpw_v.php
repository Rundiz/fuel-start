<article class="general-page-container">
	<h1><?php echo \Lang::get('account.account_forgot_username_or_password'); ?></h1>
	<p><?php echo \Lang::get('account.account_please_enter_email_that_is_using_by_your_account'); ?></p>
	
	<?php echo \Form::open(array('action' => \Uri::main(), 'class' => 'form-horizontal', 'role' => 'form')); ?> 
		<div class="form-status-placeholder">
			<?php if (isset($form_status) && isset($form_status_message)) { ?> 
			<div class="alert alert-<?php echo str_replace('error', 'danger', $form_status); ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
			<?php } ?> 
		</div>
		<?php echo \Extension\NoCsrf::generate(); ?> 
	
		<?php if (!isset($hide_form) || (isset($hide_form) && $hide_form === false)) { ?> 
		<div class="form-group">
			<label class="col-sm-2 control-label" for="account_email"><?php echo __('account.account_email'); ?>: <span class="txt_require">*</span></label>
			<div class="col-sm-4">
				<?php echo \Extension\Form::email('account_email', (isset($account_email) ? $account_email : ''), array('id' => 'account_email', 'maxlength' => '255', 'class' => 'form-control', 'autocomplete' => 'off')); ?> 
			</div>
		</div>
		<div class="form-group">
			<label for="account_captcha" class="col-sm-2 control-label"><?php echo __('account.account_captcha'); ?>: <span class="txt_require">*</span></label>
			<div class="col-sm-5">
				<img src="<?php echo Uri::createNL(\Theme::instance()->asset_path('img/securimage_show.php')); ?>" alt="securimage" class="captcha" />
				<a href="#" onclick="$('.captcha').attr('src', '<?php echo Uri::createNL(\Theme::instance()->asset_path('img/securimage_show.php')); ?>?' + Math.random()); this.blur(); return false;" tabindex="-1"><img src="<?php echo Uri::createNL(\Theme::instance()->asset_path('img/reload.gif')); ?>" alt="" /></a>
				<div>
					<?php echo \Form::input('captcha', (isset($captcha) ? $captcha : null), array('id' => 'account_captcha', 'class' => 'form-control input-captcha', 'placeholder' => __('account.account_captcha_enter_text_you_see_in_image'))); ?> 
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				<button type="submit" class="btn btn-primary"><?php echo __('account.account_send'); ?></button>
			</div>
		</div>
		<?php } // endif; ?> 
	<?php echo \Form::close(); ?> 
</article>