<h1><?php echo (\Uri::segment(3) == 'add' ? \Lang::get('account.account_add') : \Lang::get('account.account_edit')); ?></h1>

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
			<?php echo \Extension\Form::input('account_username', (isset($account_username) ? $account_username : ''), array('id' => 'account_username', 'maxlength' => '255', 'class' => 'form-control', (\Uri::segment(3) == 'edit' ? 'disabled' : 'required'))); ?> 
		</div>
	</div>
	<div class="form-group">
		<label for="account_email" class="col-sm-2 control-label"><?php echo __('account.account_email'); ?>: <span class="txt_require">*</span></label>
		<div class="col-sm-10">
			<?php echo \Extension\Form::email('account_email', (isset($account_email) ? $account_email : ''), array('id' => 'account_email', 'maxlength' => '255', 'class' => 'form-control', 'required' => '')); ?> 
		</div>
	</div>

	<fieldset>
		<legend><?php echo \Lang::get('account.account_password'); ?> <?php if (\Uri::segment(3) == 'edit') { ?><small><?php echo \Lang::get('account.account_enter_only_when_you_want_to_change'); ?></small><?php } ?></legend>
		<div class="form-group">
			<label for="account_password" class="col-sm-2 control-label"><?php echo (\Uri::segment(3) == 'add' ? __('account.account_password') : __('account.account_current_password')); ?>:</label>
			<div class="col-sm-7">
				<?php echo \Form::password('account_password', '', array('id' => 'account_password', 'maxlength' => '70', 'class' => 'form-control')); ?> 
			</div>
		</div>
		<?php if (\Uri::segment(3) == 'edit') { ?> 
		<div class="form-group">
			<label for="account_new_password" class="col-sm-2 control-label"><?php echo __('account.account_new_password'); ?>:</label>
			<div class="col-sm-7">
				<?php echo \Form::password('account_new_password', '', array('id' => 'account_new_password', 'maxlength' => '70', 'class' => 'form-control')); ?> 
			</div>
		</div>
		<?php } ?> 
	</fieldset>

	<fieldset>
		<legend><?php echo \Lang::get('account.account_display'); ?></legend>
		<div class="form-group">
			<label for="account_display_name" class="col-sm-2 control-label"><?php echo __('account.account_display_name'); ?>: <span class="txt_require">*</span></label>
			<div class="col-sm-7">
				<?php echo \Extension\Form::input('account_display_name', (isset($account_display_name) ? $account_display_name : ''), array('id' => 'account_display_name', 'maxlength' => '255', 'class' => 'form-control', 'required' => '')); ?> 
				<div class="help-block"><?php echo \Lang::get('account.account_display_name_use_for_prevent_showing_username'); ?></div>
			</div>
		</div>
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
		<div class="form-group">
			<label for="account_timezone" class="col-sm-2 control-label"><?php echo __('account.account_timezone'); ?>: <span class="txt_require">*</span></label>
			<div class="col-sm-5">
				<select name="account_timezone" id="account_timezone" class="form-control chosen-select" required="">
					<option value=""></option>
					<?php 
					if (isset($account_timezone)) {
						$tmp_account_timezone = $account_timezone;
					} else {
						$tmp_account_timezone = $default_timezone;
						$account_timezone = $default_timezone;
					}
					
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
				<div class="help-block"><?php echo \Lang::get('account.account_current_date_time_example', array('time' => \Extension\Date::gmtDate('%Y-%m-%d %H:%M:%S', (string)time(), $tmp_account_timezone))); ?></div>
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

	<fieldset>
		<legend><?php echo \Lang::get('account.account_role_and_status'); ?></legend>
		<div class="form-group">
			<label for="level_group_id" class="col-sm-2 control-label"><?php echo __('account.account_role'); ?>: <span class="txt_require">*</span></label>
			<div class="col-sm-5">
				<select name="level_group_id[]" multiple="multiple" id="level_group_id" class="form-control chosen-select" required="">
					<option value=""></option>
					<?php 
					if (isset($account_levels['items']) && is_array($account_levels['items'])) {
						foreach ($account_levels['items'] as $lvg) {
							echo '<option value="' . $lvg->level_group_id . '"' . (isset($level_group_id) && is_array($level_group_id) && in_array($lvg->level_group_id, $level_group_id) ? ' selected="selected"' : '') . '>' . $lvg->level_name . '</option>' . "\n";
						}
					}
					?> 
				</select>
			</div>
		</div>
		<div class="form-group">
			<label for="account_status" class="col-sm-2 control-label"><?php echo __('account.account_status'); ?>: <span class="txt_require">*</span></label>
			<div class="col-sm-5">
				<select name="account_status" id="account_status" class="form-control chosen-select account_status">
					<option value="0"<?php if (isset($account_status) && $account_status == '0') { ?> selected="selected"<?php } ?>><?php echo \Lang::get('admin.admin_disable'); ?></option>
					<option value="1"<?php if (isset($account_status) && $account_status == '1') { ?> selected="selected"<?php } ?>><?php echo \Lang::get('admin.admin_enable'); ?></option>
				</select>
			</div>
		</div>
		<div class="form-group account_status_text_group"<?php if (isset($account_status) && $account_status == '0') { ?> style="display: block;"<?php } ?>>
			<label for="account_status_text" class="col-sm-2 control-label"><?php echo __('account.account_status_text'); ?>:</label>
			<div class="col-sm-5">
				<?php echo \Extension\Form::input('account_status_text', (isset($account_status_text) ? $account_status_text : ''), array('id' => 'account_status_text', 'maxlength' => '255', 'class' => 'form-control')); ?> 
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
			<a href="<?php echo \Uri::create('admin/account'); ?>" class="btn btn-default"><?php echo \Lang::get('admin.admin_cancel'); ?></a>
			<?php if (\Uri::segment(3) == 'add') { ?> 
			<span class="text-muted"><?php echo \Lang::get('account.account_add_account_from_admin_will_not_send_email_to_user'); ?></span>
			<?php } // endif; ?> 
		</div>
	</div>
	<?php } // endif; ?> 
<?php echo \Form::close(); ?> 

<?php 
// set js for this page.
\Theme::instance()->asset->js('bootstrap-datepicker.js', array(), 'fuelstart_footer')->render('fuelstart_footer');
?> 

<script type="text/javascript">
	$(function() {
		<?php 
		// check that this is pc request to display datepicker
		include_once APPPATH . 'vendor' . DS . 'browser' . DS . 'lib' . DS . 'Browser.php';
		$browser = new Browser();
		if (!$browser->isMobile() && !$browser->isTablet()) { 
		?> 
		$('#account_birthdate').datepicker({
			format: 'yyyy-mm-dd'
		});
		<?php 
		} // endif;
		unset($browser);
		?> 
		
		// change account status and toggle status text
		$('.account_status').change(function() {
			if ($(this).val() == '0') {
				$('.account_status_text_group').fadeIn('fast');
			} else {
				$('.account_status_text_group').fadeOut('fast');
			}
		});
	}); // jquery start
	
	
	<?php if (\Uri::segment(3) == 'edit') { ?> 
	function ajaxDeleteAvatar() {
		$confirm = confirm('<?php echo \Lang::get('account.account_are_you_sure_delete_avatar'); ?>');
		
		if ($confirm == true) {
			$('.remove-avatar-status').html('<i class="fa fa-spinner fa-spin"></i>');
			
			$.ajax({
				url: '<?php echo \Uri::create('admin/account/delete_avatar'); ?>',
				type: 'POST',
				data: '<?php echo \Config::get('security.csrf_token_key'); ?>=<?php echo \Extension\NoCsrf::generate(null, true); ?>&account_id=<?php echo $account_id; ?>',
				dataType: 'json',
				success: function(data) {
					if (data.result == true) {
						$('.current-avatar').remove();
					} else {
						$('.form-status-placeholder').html('<div class="alert alert-'+data.form_status.replace('error', 'danger')+'">'+data.form_status_message+'</div>');
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
	<?php } ?> 
</script>