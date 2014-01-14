<?php 
// set login js file.
\Theme::instance()->asset->js('login.js', array(), 'fuelstart');

// include header page.
include dirname(dirname(__DIR__)) . DS . 'inc_html_head.php';
?> 
<?php if (isset($browser_check) && $browser_check != 'yes') { ?> 
<div class="browser-alert browser-alert-<?php echo $browser_check; ?>">
	<?php 
	if ($browser_check == 'no') {
		echo \Lang::get('admin.admin_get_modern_browser');
	} else {
		echo \Lang::get('admin.admin_using_unknow_browser');
	}
	?> 
</div>
<?php } // endif; $browser_check ?> 


<div class="container">
	<div class="row">
		<div class="col-sm-4 col-sm-offset-4">
			<div class="admin-login-page-layout">
				<div class="login-block">
					<h1><?php echo \Model_Config::getval('site_name'); ?></h1>
					<?php echo \Form::open(array('action' => \Uri::main() . '?rdr=' . $go_to, 'class' => 'form-horizontal', 'role' => 'form', 'onsubmit' => 'return ajaxAdminLogin($(this));')); ?> 
						<noscript><div class="alert alert-danger"><?php echo \Lang::get('admin.admin_please_enable_javascript'); ?></div></noscript>
						<div class="form-status-placeholder">
							<?php if (isset($form_status) && isset($form_status_message)) { ?> 
							<div class="alert alert-<?php echo str_replace('error', 'danger', $form_status); ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
							<?php } ?> 
						</div>
						<?php echo \Extension\NoCsrf::generate(); ?> 
						
						<div class="form-group">
							<label for="account_username" class="sr-only"><?php echo __('account.account_username_or_email'); ?>: <span class="txt_require">*</span></label>
							<div class="col-sm-12">
								<?php echo \Form::input('account_identity', (isset($account_identity) ? $account_identity : ''), array('placeholder' => __('account.account_username_or_email'), 'id' => 'account_username', 'maxlength' => '255', 'class' => 'form-control login-page-input-username', 'autocomplete' => 'off')); ?> 
							</div>
						</div>
						<div class="form-group">
							<label for="account_password" class="sr-only"><?php echo __('account.account_password'); ?>: <span class="txt_require">*</span></label>
							<div class="col-sm-12">
								<?php echo \Form::password('account_password', (isset($account_password) ? $account_password : ''), array('placeholder' => __('account.account_password'), 'id' => 'account_password', 'maxlength' => '70', 'class' => 'form-control')); ?> 
							</div>
						</div>
						<div class="form-group captcha-form-group<?php if (isset($show_captcha) && $show_captcha == true) { ?> show<?php }; ?>">
							<label for="account_captcha" class="sr-only"><?php echo __('account.account_captcha'); ?>: <span class="txt_require">*</span></label>
							<div class="col-sm-12">
								<img src="<?php echo Uri::createNL(\Theme::instance()->asset_path('img/securimage_show.php')); ?>" alt="securimage" class="captcha" />
								<a href="#" onclick="$('.captcha').attr('src', '<?php echo Uri::createNL(\Theme::instance()->asset_path('img/securimage_show.php')); ?>?' + Math.random()); this.blur(); return false;" tabindex="-1"><span class="fa fa-refresh fa-2x"></span></a>
								<div>
									<?php echo \Form::input('captcha', (isset($captcha) ? $captcha : null), array('id' => 'account_captcha', 'class' => 'form-control input-captcha', 'placeholder' => __('account.account_captcha_enter_text_you_see_in_image'))); ?> 
								</div>
							</div>
						</div>

						<div class="form-group">
							<div class="col-sm-12">
								<button type="submit" class="btn btn-primary btn-block admin-page-login-btn"><span class="ajac-admin-login-processing"></span> <?php echo __('account.account_login'); ?></button>
								
							</div>
						</div>
					<?php echo \Form::close(); ?> 
					
					<?php echo languageSwitchAdminSelectBox(); ?>
				</div><!--.login-block-->
				
				<div class="requirement-check-block">
					<span><?php echo \Lang::get('admin.admin_web_browser'); ?>: <small class="glyphicon glyphicon-<?php if (isset($browser_check)) {echo str_replace(array('yes', 'unknow', 'no'), array('ok', 'exclamation-sign', 'remove'), $browser_check);} ?>"></small></span>
					<span><?php echo \Lang::get('admin.admin_javascript'); ?>: <small class="glyphicon glyphicon-remove" id="login-page-js-check"></small></span>
				</div><!--.requirement-check-block-->
				
				<div class="forgot-user-pw-block">
					<?php echo \Html::anchor('account/forgotpw', \Lang::get('account.account_forgot_username_or_password'), array('class' => 'btn btn-default btn-block')); ?> 
				</div>
			</div><!--.admin-login-page-layout-->
		</div>
	</div>
</div>
<?php include dirname(dirname(__DIR__)) . DS . 'inc_html_foot.php'; ?> 