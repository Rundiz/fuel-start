<article class="general-page-container page-edit-account">
	<h1><?php echo \Lang::get('account.account_edit'); ?></h1>
	
	<?php echo \Extension\Form::openMultipart(array('class' => 'form-horizontal', 'role' => 'form')); ?> 
		<div class="form-status-placeholder">
			<?php if (isset($form_status) && isset($form_status_message)) { ?> 
			<div class="alert alert-<?php echo str_replace('error', 'danger', $form_status); ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
			<?php } ?> 
		</div>
		<div class="hidden csrf-container">
			<?php echo \Extension\NoCsrf::generate(); ?> 
		</div>
	
		<?php if (!isset($hide_form) || (isset($hide_form) && $hide_form === false)) { ?> 
	
		<div class="form-group">
			<label for="account_username" class="col-sm-2 control-label"><?php echo __('account.account_username'); ?>: <span class="txt_require">*</span></label>
			<div class="col-sm-10">
				<p class="form-control-static"><?php if (isset($account_username)) {echo \Security::htmlentities($account_username);} ?></p> 
			</div>
		</div>
		<div class="form-group">
			<label for="account_email" class="col-sm-2 control-label"><?php echo __('account.account_email'); ?>: <span class="txt_require">*</span></label>
			<div class="col-sm-10">
				<?php echo \Extension\Form::email('account_email', $account_email, array('id' => 'account_email', 'maxlength' => '255', 'class' => 'form-control')); ?> 
			</div>
		</div>
	
		<fieldset>
			<legend><?php echo \Lang::get('account.account_password'); ?> <small><?php echo \Lang::get('account.account_enter_only_when_you_want_to_change'); ?></small></legend>
			<div class="form-group">
				<label for="account_password" class="col-sm-2 control-label"><?php echo __('account.account_current_password'); ?>:</label>
				<div class="col-sm-7">
					<?php echo \Form::password('account_password', '', array('id' => 'account_password', 'maxlength' => '70', 'class' => 'form-control')); ?> 
				</div>
			</div>
			<div class="form-group">
				<label for="account_new_password" class="col-sm-2 control-label"><?php echo __('account.account_new_password'); ?>:</label>
				<div class="col-sm-7">
					<?php echo \Form::password('account_new_password', '', array('id' => 'account_new_password', 'maxlength' => '70', 'class' => 'form-control')); ?> 
				</div>
			</div>
		</fieldset>
	
		<fieldset>
			<legend><?php echo \Lang::get('account.account_display'); ?></legend>
			<div class="form-group">
				<label for="account_display_name" class="col-sm-2 control-label"><?php echo __('account.account_display_name'); ?>: <span class="txt_require">*</span></label>
				<div class="col-sm-7">
					<?php echo \Extension\Form::input('account_display_name', (isset($account_display_name) ? $account_display_name : ''), array('id' => 'account_display_name', 'maxlength' => '255', 'class' => 'form-control')); ?> 
					<div class="help-block"><?php echo \Lang::get('account.account_display_name_use_for_prevent_showing_username'); ?></div>
				</div>
			</div>
			<?php if (isset($allow_avatar) && $allow_avatar == '1') { ?> 
			<div class="form-group">
				<label for="account_avatar" class="col-sm-2 control-label"><?php echo __('account.account_avatar'); ?>:</label>
				<div class="col-sm-7">
					<?php if (isset($account_avatar) && $account_avatar != null) { ?> 
					<div class="current-avatar">
						<a href="#" class="btn btn-danger btn-xs" onclick="return ajaxDeleteAvatar();"><span class="glyphicon glyphicon-remove"></span> <?php echo \Lang::get('account.account_delete_avatar'); ?></a>
						<span class="remove-avatar-status"></span>
						<img src="<?php echo \Uri::createNL($account_avatar); ?>" alt="" class="img-responsive" />
					</div>
					<?php } // endif $account_avatar; ?> 
					<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $avatar_size*1024; // KB to Bytes ?>" />
					<?php echo \Form::file('account_avatar', array('id' => 'account_avatar')); ?> 
					<div class="help-block"><?php echo \Lang::get('account.account_file_size_less_than_or_equal_to', array('file_size' => $avatar_size)); ?> 
						<?php echo \Lang::get('account.account_file_type_allowed', array('file_types' => str_replace('|', ', ', $avatar_allowed_types))); ?>
					</div>
				</div>
			</div>
			<?php } // endif; ?> 
			<div class="form-group">
				<label for="account_timezone" class="col-sm-2 control-label"><?php echo __('account.account_timezone'); ?>: <span class="txt_require">*</span></label>
				<div class="col-sm-5">
					<select name="account_timezone" id="account_timezone" class="form-control">
						<option value=""></option>
						<?php 
						foreach ($timezone_list as $val => $key) {
							echo '<option value="' . $key . '"' . (isset($account_timezone) && $account_timezone == $key ? ' selected="selected"' : '')  . '>' . $val . '</option>' . "\n";
							
							// selected, no more another selected because this list have many duplicate key.
							if (isset($account_timezone) && $account_timezone == $key) {
								unset($account_timezone);
							}
						}
						unset($key, $timezone_list, $val);
						?> 
					</select>
					<div class="help-block"><?php echo \Lang::get('account.account_current_date_time_example', array('time' => \Extension\Date::gmtDate('%Y-%m-%d %H:%M:%S', (string)time(), $row->account_timezone))); ?></div>
				</div>
			</div>
		</fieldset>
	
		<fieldset>
			<legend><?php echo \Lang::get('account.account_personal_info'); ?></legend>
			<div class="form-group">
				<label for="account_firstname" class="col-sm-2 control-label"><?php echo __('account.account_firstname'); ?>: </label>
				<div class="col-sm-5">
					<?php echo \Extension\Form::input('account_firstname', (isset($account_firstname) ? $account_firstname : ''), array('id' => 'account_firstname', 'maxlength' => '255', 'class' => 'form-control')); ?> 
				</div>
			</div>
			<div class="form-group">
				<label for="account_lastname" class="col-sm-2 control-label"><?php echo __('account.account_lastname'); ?>: </label>
				<div class="col-sm-5">
					<?php echo \Extension\Form::input('account_lastname', (isset($account_lastname) ? $account_lastname : ''), array('id' => 'account_lastname', 'maxlength' => '255', 'class' => 'form-control')); ?> 
				</div>
			</div>
			<div class="form-group">
				<label for="account_birthdate" class="col-sm-2 control-label"><?php echo __('account.account_birthdate'); ?>: </label>
				<div class="col-sm-10">
					<div class="row">
						<div class="col-sm-6">
							<?php echo \Extension\Form::date('account_birthdate', (isset($account_birthdate) ? $account_birthdate : ''), array('id' => 'account_birthdate', 'maxlength' => '10', 'class' => 'form-control')); ?> 
						</div>
					</div>
					<div class="help-block"><?php echo \Lang::get('account.account_birthdate_format_should_be'); ?></div>
				</div>
			</div>
		</fieldset>
	
		<?php /*<fieldset>
			<legend>Example custom field</legend>
			<div class="form-group">
				<label for="ex_af_phone" class="col-sm-2 control-label">Phone: </label>
				<div class="col-sm-5">
					<?php echo \Extension\Form::tel('account_field[phone]', (isset($account_field['phone']) ? $account_field['phone'] : ''), array('id' => 'ex_af_phone', 'maxlength' => '20', 'class' => 'form-control')); ?> 
				</div>
			</div>
			<div class="form-group">
				<label for="ex_af_homepage" class="col-sm-2 control-label">Home page: </label>
				<div class="col-sm-5">
					<?php echo \Extension\Form::url('account_field[homepage]', (isset($account_field['homepage']) ? $account_field['homepage'] : ''), array('id' => 'ex_af_homepage', 'maxlength' => '20', 'class' => 'form-control')); ?> 
				</div>
			</div>
			choice main:<br />
			<?php echo \Form::checkbox('account_field[a][0]', 'a1', (isset($account_field['a'][0]) ? $account_field['a'][0] : null)); ?> a1
			<?php echo \Form::checkbox('account_field[a][1]', 'a2', (isset($account_field['a'][1]) ? $account_field['a'][1] : null)); ?> a2<br />
			<br />
			choice sub of a2:<br />
			<?php echo \Form::checkbox('account_field[a][a2][0]', 'a2.1', (isset($account_field['a']['a2'][0]) ? $account_field['a']['a2'][0] : null)); ?> a2.1
			<?php echo \Form::checkbox('account_field[a][a2][1]', 'a2.2', (isset($account_field['a']['a2'][1]) ? $account_field['a']['a2'][1] : null)); ?> a2.2
			<?php echo \Form::checkbox('account_field[a][a2][2]', 'a2.2', (isset($account_field['a']['a2'][2]) ? $account_field['a']['a2'][2] : null)); ?> a2.3
		</fieldset>*/ ?> 
		
		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				<button type="submit" class="btn btn-primary"><?php echo __('account.account_submit'); ?></button>
				<a href="<?php echo \Uri::create('account/view-logins'); ?>" class="btn btn-default btn-sm"><?php echo \Lang::get('account.account_login_history'); ?></a>
			</div>
		</div>
		<?php } // endif; ?> 
	<?php echo \Form::close(); ?> 
</article>


<script type="text/javascript">
	function ajaxDeleteAvatar() {
		$confirm = confirm('<?php echo \Lang::get('account.account_are_you_sure_delete_avatar'); ?>');
		
		if ($confirm == true) {
			$('.remove-avatar-status').html('<i class="fa fa-spinner fa-spin"></i>');
			
			$.ajax({
				url: '<?php echo \Uri::create('account/edit/delete-avatar'); ?>',
				type: 'POST',
				data: '<?php echo \Config::get('security.csrf_token_key'); ?>=<?php echo \Extension\NoCsrf::generate(null, true); ?>&account_id=<?php echo $account_id; ?>',
				dataType: 'json',
				success: function(data) {
					if (data.result == true) {
						$('.current-avatar').remove();
					}
					$('.remove-avatar-status').html('');
					
					$('.csrf-container').html(data.csrf_html);
				},
				error: function(data, status, e) {
					alert(e);
					$('.remove-avatar-status').html('');
				}
			});
			return false;
		} else {
			return false;
		}
	}// ajaxDeleteAvatar
</script>