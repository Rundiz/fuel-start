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
								<?php echo \Extension\Form::input('site_name', (isset($site_name) ? $site_name : ''), array('maxlength' => '255', 'id' => 'cfg-site_name', 'class' => 'form-control')); ?> 
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
							<label for="cfg-member_admin_verify_emails" class="col-sm-12"><?php echo \Lang::get('config.config_member_admin_verify_emails'); ?>:</label>
							<div class="col-sm-12">
								<?php echo \Extension\Form::input('member_admin_verify_emails', (isset($member_admin_verify_emails) ? $member_admin_verify_emails : ''), array('maxlength' => '255', 'id' => 'cfg-member_admin_verify_emails', 'class' => 'form-control')); ?> 
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
				email config.<br />
				<pre>
				// mail_protocol
				// mail_mailpath
				// mail_smtp_host
				// mail_smtp_user
				// mail_smtp_pass
				// mail_smtp_port
				// mail_sender_email
				</pre>
			</div><!--.tab-pane-->
			
			
			<div class="tab-pane" id="tabs-content">
				content config.<br />
				<pre>
				// content_items_perpage
				// content_admin_items_perpage
				</pre>
			</div><!--.tab-pane-->
			
			
			<div class="tab-pane" id="tabs-media">
				media config.<br />
				<pre>
				// media_allowed_types
				</pre>
			</div><!--.tab-pane-->
			
			
			<div class="tab-pane" id="tabs-ftp">
				ftp config.<br />
				<pre>
				// ftp_host
				// ftp_username
				// ftp_password
				// ftp_port
				// ftp_passive
				// ftp_basepath
				</pre>
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
</script>