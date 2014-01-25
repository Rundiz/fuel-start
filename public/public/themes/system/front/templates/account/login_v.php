<article class="general-page-container">
	<h1><?php echo \Lang::get('account.account_login'); ?></h1>
	
	<?php echo \Form::open(array('action' => \Uri::main() . (isset($go_to) ? '?rdr=' . $go_to : ''), 'class' => 'form-horizontal', 'role' => 'form')); ?> 
		<div class="form-status-placeholder">
			<?php if (isset($form_status) && isset($form_status_message)) { ?> 
			<div class="alert alert-<?php echo str_replace('error', 'danger', $form_status); ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
			<?php } ?> 
		</div>
		<?php echo \Extension\NoCsrf::generate(); ?> 
	
		<div class="form-group">
			<label for="account_username" class="col-sm-2 control-label"><?php echo __('account.account_username_or_email'); ?>: <span class="txt_require">*</span></label>
			<div class="col-sm-4">
				<?php echo \Form::input('account_identity', (isset($account_identity) ? $account_identity : ''), array('id' => 'account_username', 'maxlength' => '255', 'class' => 'form-control login-page-input-username', 'autocomplete' => 'off')); ?> 
			</div>
		</div>
		<div class="form-group">
			<label for="account_password" class="col-sm-2 control-label"><?php echo __('account.account_password'); ?>: <span class="txt_require">*</span></label>
			<div class="col-sm-4">
				<?php echo \Form::password('account_password', (isset($account_password) ? $account_password : ''), array('id' => 'account_password', 'maxlength' => '70', 'class' => 'form-control')); ?> 
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				<label class="bootstrap-tooltip" data-placement="top" data-toggle="tooltip" data-original-title="<?php echo \Lang::get('account.account_remember_me_tooltip'); ?>">
					<?php echo \Form::checkbox('remember', 'yes', (isset($remember) ? $remember : false)); ?> 
					<?php echo \Lang::get('account.account_remember_me'); ?> 
				</label>
			</div>
		</div>
	
		<?php if (isset($show_captcha) && $show_captcha == true) { ?> 
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
		<?php } // endif captcha; ?> 
	
		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				<button type="submit" class="btn btn-primary"><?php echo __('account.account_login'); ?></button>
				<a href="<?php echo \Uri::create('account/forgotpw'); ?>" class="btn btn-default btn-sm"><?php echo \Lang::get('account.account_forgot_username_or_password'); ?></a>
			</div>
		</div>
	<?php echo \Form::close(); ?> 
	
	<hr />
	<h2>Ajax form</h2>
	
	<?php echo \Form::open(array('action' => \Uri::main() . (isset($go_to) ? '?rdr=' . $go_to : ''), 'class' => 'form-horizontal ajax-member-login', 'role' => 'form')); ?> 
		<div class="form-status-placeholder">
			<?php if (isset($form_status) && isset($form_status_message)) { ?> 
			<div class="alert alert-<?php echo str_replace('error', 'danger', $form_status); ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
			<?php } ?> 
		</div>
		<?php echo \Extension\NoCsrf::generate(); ?> 
	
		<div class="form-group">
			<label for="account_username2" class="col-sm-2 control-label"><?php echo __('account.account_username_or_email'); ?>: <span class="txt_require">*</span></label>
			<div class="col-sm-4">
				<?php echo \Form::input('account_identity', (isset($account_identity) ? $account_identity : ''), array('id' => 'account_username2', 'maxlength' => '255', 'class' => 'form-control', 'autocomplete' => 'off')); ?> 
			</div>
		</div>
		<div class="form-group">
			<label for="account_password2" class="col-sm-2 control-label"><?php echo __('account.account_password'); ?>: <span class="txt_require">*</span></label>
			<div class="col-sm-4">
				<?php echo \Form::password('account_password', (isset($account_password) ? $account_password : ''), array('id' => 'account_password2', 'maxlength' => '70', 'class' => 'form-control')); ?> 
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				<label class="bootstrap-tooltip" data-placement="top" data-toggle="tooltip" data-original-title="<?php echo \Lang::get('account.account_remember_me_tooltip'); ?>">
					<?php echo \Form::checkbox('remember', 'yes', (isset($remember) ? $remember : false), array('class' => 'remember')); ?> 
					<?php echo \Lang::get('account.account_remember_me'); ?> 
				</label>
			</div>
		</div>
	
		<div class="form-group show_captcha hide_captcha">
			<label for="account_captcha2" class="col-sm-2 control-label"><?php echo __('account.account_captcha'); ?>: <span class="txt_require">*</span></label>
			<div class="col-sm-5">
				<img src="<?php echo Uri::createNL(\Theme::instance()->asset_path('img/securimage_show.php')); ?>" alt="securimage" class="captcha" />
				<a href="#" onclick="$('.captcha').attr('src', '<?php echo Uri::createNL(\Theme::instance()->asset_path('img/securimage_show.php')); ?>?' + Math.random()); this.blur(); return false;" tabindex="-1"><img src="<?php echo Uri::createNL(\Theme::instance()->asset_path('img/reload.gif')); ?>" alt="" /></a>
				<div>
					<?php echo \Form::input('captcha', (isset($captcha) ? $captcha : null), array('id' => 'account_captcha2', 'class' => 'form-control input-captcha', 'placeholder' => __('account.account_captcha_enter_text_you_see_in_image'))); ?> 
				</div>
			</div>
		</div>
	
		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				<button type="button" class="btn btn-primary" onclick="ajax_member_login();"><?php echo __('account.account_login'); ?></button>
			</div>
		</div>
	<?php echo \Form::close(); ?> 
</article>


<script type="text/javascript">
	function ajax_member_login() 
	{
		var serialize_val = $('.ajax-member-login').serialize();
		var form_place_holder = $('.ajax-member-login .form-status-placeholder');
		
		$.ajax({
			url: $('.ajax-member-login').attr('action'),
			type: 'POST',
			data: serialize_val,
			dataType: 'json',
			success: function(data) {
				if (data.form_status == 'error') {
					form_place_holder.html(
						'<div class="alert alert-danger">'+data.form_status_message+'</div>'
					);
				}
				
				if (data.login_status === true) {
					window.location = data.go_to;
				} else {
					if (data.show_captcha === true) {
						$('.ajax-member-login .show_captcha').show();
						$('.captcha').attr('src', '<?php echo Uri::createNL(\Theme::instance()->asset_path('img/securimage_show.php')); ?>?' + Math.random());
					} else {
						$('.ajax-member-login .show_captcha').hide();
					}
				}
			},
			error: function(d, s, e) {
				alert(e);
			}
		});
	}// ajax_member_login
</script>