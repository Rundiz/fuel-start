<h1><?php echo \Lang::get('config.config_global_configuration'); ?></h1>

<?php echo \Form::open(array('class' => 'form-horizontal', 'role' => 'form')); ?> 
	<div class="form-status-placeholder">
		<?php if (isset($form_status) && isset($form_status_message)) { ?> 
		<div class="alert alert-<?php echo str_replace('error', 'danger', $form_status); ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
		<?php } ?> 
	</div>
	<?php echo \Extension\NoCsrf::generate(); ?> 

	<div class="config-page-form-tab-container">
		<ul class="nav nav-tabs bootstrap3-tabs">
			<li><a href="#tabs-website" data-toggle="tab"><?php echo \Lang::get('config.config_website'); ?></a></li>
			<li><a href="#tabs-account" data-toggle="tab"><?php echo \Lang::get('config.config_user_account'); ?></a></li>
			<li><a href="#tabs-email" data-toggle="tab"><?php echo \Lang::get('config.config_email'); ?></a></li>
			<li><a href="#tabs-content" data-toggle="tab"><?php echo \Lang::get('config.config_content'); ?></a></li>
			<li><a href="#tabs-media" data-toggle="tab"><?php echo \Lang::get('config.config_media'); ?></a></li>
			<li><a href="#tabs-ftp" data-toggle="tab"><?php echo \Lang::get('config.config_ftp'); ?></a></li>
		</ul>
		
		
		
		<div class="tab-content">
			<div class="tab-pane" id="tabs-website">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label for="cfg-site_name" class="col-sm-2 control-label"><?php echo __('config.config_site_name'); ?>: <span class="txt_require">*</span></label>
							<div class="col-sm-10">
								<?php echo \Extension\Form::input('site_name', (isset($site_name) ? $site_name : ''), array('maxlength' => '255', 'id' => 'cfg-site_name', 'class' => 'form-control', 'required' => '')); ?> 
							</div>
						</div>
						<div class="form-group">
							<label for="cfg-page_title_separator" class="col-sm-2 control-label"><?php echo __('config.config_page_title_separator'); ?>:</label>
							<div class="col-sm-3">
								<?php echo \Extension\Form::input('page_title_separator', (isset($page_title_separator) ? $page_title_separator : ''), array('maxlength' => '20', 'id' => 'cfg-page_title_separator', 'class' => 'form-control')); ?> 
							</div>
						</div>
						<div class="form-group">
							<label for="cfg-site_timezone" class="col-sm-2 control-label"><?php echo __('config.config_site_timezone'); ?>:</label>
							<div class="col-sm-10">
								<select name="site_timezone" id="cfg-site_timezone" class="form-control">
									<option value=""></option>
									<?php 
									$site_timezone_selectbox = (isset($site_timezone) ? $site_timezone : '');
									
									foreach ($timezone_list as $val => $key) {
										echo '<option value="' . $key . '"' . (isset($site_timezone_selectbox) && $site_timezone_selectbox == $key ? ' selected="selected"' : '')  . '>' . $val . '</option>' . "\n";

										// selected, no more another selected because this list have many duplicate key.
										if (isset($site_timezone_selectbox) && $site_timezone_selectbox == $key) {
											unset($site_timezone_selectbox);
										}
									}
									unset($key, $timezone_list, $val);
									?> 
								</select> 
								<?php if (isset($site_timezone)) { ?><span class="text-muted"><?php echo $site_timezone; ?></span><?php } ?> 
							</div>
						</div>
					</div>
				</div>
			</div><!--.tab-pane-->
			
			
			<div class="tab-pane" id="tabs-account">
				<div class="row">
					<div class="col-sm-6">
						<h2><?php echo \Lang::get('config.config_account_registration'); ?></h2>
						<div class="form-group">
							<div class="col-sm-12">
								<label class="checkbox-inline">
									<?php echo \Extension\Form::checkbox('member_allow_register', '1', (isset($member_allow_register) && $member_allow_register == '1' ? true : false), array('id' => 'cfg-member_allow_register')); ?> <?php echo \Lang::get('config.config_member_allow_register'); ?> 
								</label>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-12">
								<label class="checkbox-inline">
									<?php echo \Extension\Form::checkbox('member_register_notify_admin', '1', (isset($member_register_notify_admin) && $member_register_notify_admin == '1' ? true : false), array('id' => 'cfg-member_register_notify_admin')); ?> <?php echo \Lang::get('config.config_member_register_notify_admin'); ?> <span class="text-muted">(<?php echo \Lang::get('config.config_email_notification_still_send_when_require_admin_to_verify_register_user'); ?>)</span>
								</label>
							</div>
						</div>
						<div class="form-group">
							<label for="cfg-member_verification" class="col-sm-12"><?php echo \Lang::get('config.config_member_verification'); ?>:</label>
							<div class="col-sm-12">
								<?php 
								echo \Extension\Form::select('member_verification', (isset($member_verification) ? $member_verification : ''), 
									array(
										'0' => \Lang::get('config.config_member_verification_not_verify'),
										'1' => \Lang::get('config.config_member_verification_by_email'),
										'2' => \Lang::get('config.config_member_verification_by_admin'),
									), 
									array(
										'id' => 'cfg-member_verification',
										'class' => 'form-control'
									)
								);
								?> 
							</div>
						</div>
						<div class="form-group">
							<label for="cfg-member_admin_verify_emails" class="col-sm-12"><?php echo \Lang::get('config.config_member_admin_verify_emails'); ?>: <span class="txt_require">*</span></label>
							<div class="col-sm-12">
								<?php echo \Extension\Form::input('member_admin_verify_emails', (isset($member_admin_verify_emails) ? $member_admin_verify_emails : ''), array('maxlength' => '255', 'id' => 'cfg-member_admin_verify_emails', 'class' => 'form-control', 'required' => '')); ?> 
								<div class="help-block"><?php echo \Lang::get('config.config_member_admin_verify_emails_help'); ?></div>
							</div>
						</div>
						<div class="form-group">
							<label for="cfg-member_disallow_username" class="col-sm-12"><?php echo \Lang::get('config.config_member_disallow_username'); ?>:</label>
							<div class="col-sm-12">
								<?php echo \Extension\Form::input('member_disallow_username', (isset($member_disallow_username) ? $member_disallow_username : ''), array('maxlength' => '255', 'id' => 'cfg-member_disallow_username', 'class' => 'form-control')); ?> 
								<div class="help-block"><?php echo \Lang::get('config.config_member_disallow_username_help'); ?></div>
							</div>
						</div>
						
						<h2><?php echo \Lang::get('config.config_account_security'); ?></h2>
						<div class="form-group">
							<div class="col-sm-12">
								<label class="checkbox-inline">
									<?php echo \Extension\Form::checkbox('simultaneous_login', '1', (isset($simultaneous_login) && $simultaneous_login == '1' ? true : false), array('id' => 'cfg-simultaneous_login')); ?> <?php echo \Lang::get('config.config_account_simultaneous_login'); ?> 
								</label>
							</div>
						</div>
						<div class="form-group">
							<label for="cfg-member_max_login_fail" class="col-sm-12"><?php echo \Lang::get('config.config_member_max_login_fail'); ?>:</label>
							<div class="col-sm-5">
								<div class="input-group">
									<?php echo \Extension\Form::number('member_max_login_fail', (isset($member_max_login_fail) ? $member_max_login_fail : ''), array('maxlength' => '2', 'id' => 'cfg-member_max_login_fail', 'class' => 'form-control')); ?> 
									<span class="input-group-addon"><?php echo \Lang::get('config.config_member_max_login_fail_time'); ?></span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="cfg-member_login_fail_wait_time" class="col-sm-12"><?php echo \Lang::get('config.config_member_login_fail_wait_time'); ?>:</label>
							<div class="col-sm-5">
								<div class="input-group">
									<?php echo \Extension\Form::number('member_login_fail_wait_time', (isset($member_login_fail_wait_time) ? $member_login_fail_wait_time : ''), array('maxlength' => '3', 'id' => 'cfg-member_login_fail_wait_time', 'class' => 'form-control')); ?> 
									<span class="input-group-addon"><?php echo \Lang::get('config.config_member_login_fail_wait_time_minute'); ?></span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="cfg-member_login_remember_length" class="col-sm-12"><?php echo \Lang::get('config.config_member_login_remember_length'); ?>:</label>
							<div class="col-sm-5">
								<div class="input-group">
									<?php echo \Extension\Form::number('member_login_remember_length', (isset($member_login_remember_length) ? $member_login_remember_length : ''), array('maxlength' => '3', 'id' => 'cfg-member_login_remember_length', 'class' => 'form-control')); ?> 
									<span class="input-group-addon"><?php echo \Lang::get('config.config_member_login_remember_length_day'); ?></span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="cfg-member_confirm_wait_time" class="col-sm-12"><?php echo \Lang::get('config.config_member_confirm_wait_time'); ?>:</label>
							<div class="col-sm-5">
								<div class="input-group">
									<?php echo \Extension\Form::number('member_confirm_wait_time', (isset($member_confirm_wait_time) ? $member_confirm_wait_time : ''), array('maxlength' => '5', 'id' => 'cfg-member_confirm_wait_time', 'class' => 'form-control')); ?> 
									<span class="input-group-addon"><?php echo \Lang::get('config.config_member_confirm_wait_time_minute'); ?></span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-12">
								<label class="checkbox-inline">
									<?php echo \Extension\Form::checkbox('member_email_change_need_confirm', '1', (isset($member_email_change_need_confirm) && $member_email_change_need_confirm == '1' ? true : false), array('id' => 'cfg-member_email_change_need_confirm')); ?> <?php echo \Lang::get('config.config_member_email_change_need_confirm'); ?> 
									<span class="text-muted">(<?php echo \Lang::get('config.config_member_email_change_need_confirm_help'); ?>)</span>
								</label>
							</div>
						</div>
					</div>
					<div class="col-sm-6">
						<h2><?php echo \Lang::get('config.config_account_avatar'); ?></h2>
						<div class="form-group">
							<div class="col-sm-12">
								<label class="checkbox-inline">
									<?php echo \Extension\Form::checkbox('allow_avatar', '1', (isset($allow_avatar) && $allow_avatar == '1' ? true : false), array('id' => 'cfg-allow_avatar')); ?> <?php echo \Lang::get('config.config_account_allow_avatar'); ?> 
								</label>
							</div>
						</div>
						<div class="form-group">
							<label for="cfg-avatar_size" class="col-sm-12"><?php echo \Lang::get('config.config_account_avatar_size'); ?>:</label>
							<div class="col-sm-5">
								<div class="input-group">
									<?php echo \Extension\Form::number('avatar_size', (isset($avatar_size) ? $avatar_size : ''), array('maxlength' => '3', 'id' => 'cfg-avatar_size', 'class' => 'form-control')); ?> 
									<span class="input-group-addon"><?php echo \Lang::get('config.config_file_size_kb'); ?></span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="cfg-avatar_allowed_types" class="col-sm-12"><?php echo \Lang::get('config.config_account_avatar_allowed_types'); ?>:</label>
							<div class="col-sm-12">
								<?php echo \Extension\Form::input('avatar_allowed_types', (isset($avatar_allowed_types) ? $avatar_allowed_types : ''), array('maxlength' => '255', 'id' => 'cfg-avatar_allowed_types', 'class' => 'form-control')); ?> 
								<div class="help-block"><?php echo \Lang::get('config.config_account_avatar_allowed_types_help'); ?></div>
							</div>
						</div>
						<div class="form-group">
							<label for="cfg-avatar_path" class="col-sm-12"><?php echo \Lang::get('config.config_account_avatar_path'); ?>:</label>
							<div class="col-sm-12">
								<?php echo \Extension\Form::input('avatar_path', (isset($avatar_path) ? $avatar_path : ''), array('maxlength' => '255', 'id' => 'cfg-avatar_path', 'class' => 'form-control')); ?> 
								<div class="help-block"><?php echo \Lang::get('config.config_account_avatar_path_help'); ?></div>
							</div>
						</div>
					</div>
				</div>
			</div><!--.tab-pane-->
			
			
			<div class="tab-pane" id="tabs-email">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label for="cfg-mail_protocol" class="col-sm-2 control-label"><?php echo \Lang::get('config.config_mail_protocol'); ?>:</label>
							<div class="col-sm-10">
								<?php 
								echo \Extension\Form::select('mail_protocol', (isset($mail_protocol) ? $mail_protocol : ''), 
									array(
										'mail' => \Lang::get('config.config_mail_protocol_mail'),
										'sendmail' => \Lang::get('config.config_mail_protocol_sendmail'),
										'smtp' => \Lang::get('config.config_mail_protocol_smtp'),
									), 
									array(
										'id' => 'cfg-mail_protocol',
										'class' => 'form-control'
									)
								);
								?> 
							</div>
						</div>
						<div class="form-group">
							<label for="cfg-mail_mailpath" class="col-sm-2 control-label"><?php echo \Lang::get('config.config_mail_mailpath'); ?>:</label>
							<div class="col-sm-10">
								<?php echo \Extension\Form::input('mail_mailpath', (isset($mail_mailpath) ? $mail_mailpath : ''), array('maxlength' => '255', 'id' => 'cfg-mail_mailpath', 'class' => 'form-control')); ?> 
							</div>
						</div>
						<div class="form-group">
							<label for="cfg-mail_smtp_host" class="col-sm-2 control-label"><?php echo \Lang::get('config.config_mail_smtp_host'); ?>:</label>
							<div class="col-sm-10">
								<?php echo \Extension\Form::input('mail_smtp_host', (isset($mail_smtp_host) ? $mail_smtp_host : ''), array('maxlength' => '255', 'id' => 'cfg-mail_smtp_host', 'class' => 'form-control')); ?> 
							</div>
						</div>
						<div class="form-group">
							<label for="cfg-mail_smtp_user" class="col-sm-2 control-label"><?php echo \Lang::get('config.config_mail_smtp_user'); ?>:</label>
							<div class="col-sm-5">
								<?php echo \Extension\Form::input('mail_smtp_user', (isset($mail_smtp_user) ? $mail_smtp_user : ''), array('maxlength' => '255', 'id' => 'cfg-mail_smtp_user', 'class' => 'form-control')); ?> 
							</div>
						</div>
						<div class="form-group">
							<label for="cfg-mail_smtp_pass" class="col-sm-2 control-label"><?php echo \Lang::get('config.config_mail_smtp_pass'); ?>:</label>
							<div class="col-sm-5">
								<?php echo \Extension\Form::password('mail_smtp_pass', (isset($mail_smtp_pass) ? $mail_smtp_pass : ''), array('maxlength' => '255', 'id' => 'cfg-mail_smtp_pass', 'class' => 'form-control')); ?> 
							</div>
						</div>
						<div class="form-group">
							<label for="cfg-mail_smtp_port" class="col-sm-2 control-label"><?php echo \Lang::get('config.config_mail_smtp_port'); ?>:</label>
							<div class="col-sm-3">
								<?php echo \Extension\Form::number('mail_smtp_port', (isset($mail_smtp_port) ? $mail_smtp_port : ''), array('maxlength' => '3', 'id' => 'cfg-mail_smtp_port', 'class' => 'form-control')); ?> 
							</div>
						</div>
						<div class="form-group">
							<label for="cfg-mail_sender_email" class="col-sm-2 control-label"><?php echo \Lang::get('config.config_mail_sender_email'); ?>: <span class="txt_require">*</span></label>
							<div class="col-sm-5">
								<?php echo \Extension\Form::email('mail_sender_email', (isset($mail_sender_email) ? $mail_sender_email : ''), array('maxlength' => '255', 'id' => 'cfg-mail_sender_email', 'class' => 'form-control', 'required' => '')); ?> 
								<div class="help-block"><?php echo \Lang::get('config.config_mail_sender_email_help'); ?></div>
							</div>
						</div>
					</div>
				</div>
			</div><!--.tab-pane-->
			
			
			<div class="tab-pane" id="tabs-content">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label for="cfg-content_items_perpage" class="col-sm-2 control-label"><?php echo \Lang::get('config.config_content_items_perpage'); ?>: <span class="txt_require">*</span></label>
							<div class="col-sm-3">
								<?php echo \Extension\Form::number('content_items_perpage', (isset($content_items_perpage) ? $content_items_perpage : ''), array('maxlength' => '3', 'id' => 'cfg-content_items_perpage', 'class' => 'form-control', 'required' => '')); ?> 
							</div>
						</div>
						<div class="form-group">
							<label for="cfg-content_admin_items_perpage" class="col-sm-2 control-label"><?php echo \Lang::get('config.config_content_admin_items_perpage'); ?>: <span class="txt_require">*</span></label>
							<div class="col-sm-3">
								<?php echo \Extension\Form::number('content_admin_items_perpage', (isset($content_admin_items_perpage) ? $content_admin_items_perpage : ''), array('maxlength' => '3', 'id' => 'cfg-content_admin_items_perpage', 'class' => 'form-control', 'required' => '')); ?> 
							</div>
						</div>
					</div>
				</div>
			</div><!--.tab-pane-->
			
			
			<div class="tab-pane" id="tabs-media">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label for="cfg-media_allowed_types" class="col-sm-2 control-label"><?php echo __('config.config_media_allowed_types'); ?>:</label>
							<div class="col-sm-10">
								<?php echo \Extension\Form::input('media_allowed_types', (isset($media_allowed_types) ? $media_allowed_types : ''), array('maxlength' => '255', 'id' => 'cfg-media_allowed_types', 'class' => 'form-control')); ?> 
								<div class="help-block"><?php echo \Lang::get('config.config_media_allowed_types_help'); ?></div>
							</div>
						</div>
					</div>
				</div>
			</div><!--.tab-pane-->
			
			
			<div class="tab-pane" id="tabs-ftp">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label for="cfg-ftp_host" class="col-sm-2 control-label"><?php echo __('config.config_ftp_host'); ?>:</label>
							<div class="col-sm-10">
								<?php echo \Extension\Form::input('ftp_host', (isset($ftp_host) ? $ftp_host : ''), array('maxlength' => '255', 'id' => 'cfg-ftp_host', 'class' => 'form-control')); ?> 
							</div>
						</div>
						<div class="form-group">
							<label for="cfg-ftp_username" class="col-sm-2 control-label"><?php echo __('config.config_ftp_username'); ?>:</label>
							<div class="col-sm-5">
								<?php echo \Extension\Form::input('ftp_username', (isset($ftp_username) ? $ftp_username : ''), array('maxlength' => '255', 'id' => 'cfg-ftp_username', 'class' => 'form-control')); ?> 
							</div>
						</div>
						<div class="form-group">
							<label for="cfg-ftp_password" class="col-sm-2 control-label"><?php echo __('config.config_ftp_password'); ?>:</label>
							<div class="col-sm-5">
								<?php echo \Extension\Form::password('ftp_password', (isset($ftp_password) ? $ftp_password : ''), array('maxlength' => '255', 'id' => 'cfg-ftp_password', 'class' => 'form-control')); ?> 
							</div>
						</div>
						<div class="form-group">
							<label for="cfg-ftp_port" class="col-sm-2 control-label"><?php echo __('config.config_ftp_port'); ?>:</label>
							<div class="col-sm-3">
								<?php echo \Extension\Form::number('ftp_port', (isset($ftp_port) ? $ftp_port : ''), array('maxlength' => '255', 'id' => 'cfg-ftp_port', 'class' => 'form-control')); ?> 
							</div>
						</div>
						<div class="form-group">
							<label for="cfg-ftp_passive" class="col-sm-2 control-label"><?php echo __('config.config_ftp_passive'); ?>:</label>
							<div class="col-sm-3">
								<?php 
								echo \Extension\Form::select('ftp_passive', (isset($ftp_passive) ? $ftp_passive : ''), 
									array(
										'true' => \Lang::get('admin.admin_yes'),
										'false' => \Lang::get('admin.admin_no'),
									), 
									array(
										'id' => 'cfg-ftp_passive',
										'class' => 'form-control'
									)
								);
								?> 
							</div>
						</div>
						<div class="form-group">
							<label for="cfg-ftp_basepath" class="col-sm-2 control-label"><?php echo __('config.config_ftp_basepath'); ?>:</label>
							<div class="col-sm-10">
								<?php echo \Extension\Form::input('ftp_basepath', (isset($ftp_basepath) ? $ftp_basepath : ''), array('maxlength' => '255', 'id' => 'cfg-ftp_basepath', 'class' => 'form-control')); ?> 
								<div class="help-block"><?php echo \Lang::get('config.config_ftp_basepath_help'); ?></div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-10">
								<button type="button" class="btn btn-default test-ftp-btn" onclick="ajax_test_ftp();"><?php echo \Lang::get('config.config_test_ftp_connection'); ?></button>
								<div class="ftp-test-result"></div>
							</div>
						</div>
					</div>
				</div>
			</div><!--.tab-pane-->
		</div>
	</div><!--.config-page-form-tab-container-->
	
	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<button type="submit" class="btn btn-primary"><?php echo __('admin.admin_save'); ?></button>
			<?php echo \Extension\Html::anchor('admin', \Lang::get('admin.admin_cancel'), array('class' => 'btn btn-default')); ?> 
		</div>
	</div>
<?php echo \Form::close(); ?> 


<?php 
// set js for this page.
echo \Theme::instance()->asset->js('amplify.min.js', array(), 'fuelstart_config')->render('fuelstart_config');
?> 

<script type="text/javascript">
	$(function() {
		makeBs3Tabs();
	});
	
	
	function ajax_test_ftp() {
		$('.test-ftp-btn').prepend('<span class="fa fa-spinner fa-spin"></span> ');
		$('.test-ftp-btn').attr('disabled', 'disabled');
		$('.ftp-test-result').fadeOut();
		
		var ftp_host = $('#cfg-ftp_host').val();
		var username = $('#cfg-ftp_username').val();
		var password = $('#cfg-ftp_password').val();
		var port = $('#cfg-ftp_port').val();
		var passive = $('#cfg-ftp_passive').val();
		var basepath = $('#cfg-ftp_basepath').val();
		
		$.ajax({
			url: base_url+'admin/config/ajax_test_ftp',
			type: 'POST',
			data: csrf_name+'='+nocsrf_val+'&hostname='+ftp_host+'&username='+username+'&password='+password+'&port='+port+'&passive='+passive+'&basepath='+basepath,
			dataType: 'json',
			success: function(data) {
				$('.ftp-test-result').html('<div class="alert alert-'+data.form_status.replace('error', 'danger')+'">'+data.form_status_message+'</div>');
				if (typeof data.list_files != 'undefined') {
					$('.ftp-test-result').append(data.list_files);
				}
				$('.ftp-test-result').show();
				
				$('.test-ftp-btn').removeAttr('disabled');
				$('.test-ftp-btn').html('<?php echo \Lang::get('config.config_test_ftp_connection'); ?>');
			},
			error: function() {
				$('.ftp-test-result').html('');
				$('.ftp-test-result').fadeOut();
				$('.test-ftp-btn').removeAttr('disabled');
				$('.test-ftp-btn').html('<?php echo \Lang::get('config.config_test_ftp_connection'); ?>');
			}
		});
	}// ajax_test_ftp
</script>