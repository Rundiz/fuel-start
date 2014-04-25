<h1><?php echo (\Uri::segment(3) == 'add' ? \Lang::get('siteman_add') : \Lang::get('siteman_edit')); ?></h1>

<?php echo \Extension\Form::openMultipart(array('class' => 'form-horizontal', 'role' => 'form')); ?> 
	<div class="form-status-placeholder">
		<?php if (isset($form_status) && isset($form_status_message)) { ?> 
		<div class="alert alert-<?php echo str_replace('error', 'danger', $form_status); ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
		<?php } ?> 
	</div>
	<div class="hidden csrf-container">
		<?php echo \Extension\NoCsrf::generate(); ?> 
	</div>

	<div class="form-group">
		<label for="site_name" class="col-sm-2 control-label"><?php echo __('siteman_site_name'); ?>: <span class="txt_require">*</span></label>
		<div class="col-sm-10">
			<?php echo \Extension\Form::input('site_name', (isset($site_name) ? $site_name : ''), array('id' => 'site_name', 'maxlength' => '255', 'class' => 'form-control', 'required')); ?> 
		</div>
	</div>
	<div class="form-group">
		<label for="site_domain" class="col-sm-2 control-label"><?php echo __('siteman_site_domain'); ?>: <span class="txt_require">*</span></label>
		<div class="col-sm-10">
			<?php echo \Extension\Form::input('site_domain', (isset($site_domain) ? $site_domain : ''), array('id' => 'site_domain', 'maxlength' => '255', 'class' => 'form-control', 'required')); ?> 
			<div class="help-block"><?php echo \Lang::get('siteman_donot_enter_http_only_domain'); ?></div>
		</div>
	</div>
	<div class="form-group">
			<label for="site_status" class="col-sm-2 control-label"><?php echo __('siteman_site_status'); ?>:</label>
			<div class="col-sm-5">
				<select name="site_status" id="site_status" class="form-control chosen-select account_status">
					<option value="0"<?php if (isset($site_status) && $site_status == '0') { ?> selected="selected"<?php } ?>><?php echo \Lang::get('admin_disable'); ?></option>
					<option value="1"<?php if (isset($site_status) && $site_status == '1') { ?> selected="selected"<?php } ?>><?php echo \Lang::get('admin_enable'); ?></option>
				</select>
			</div>
		</div>

	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<button type="submit" class="btn btn-primary"><?php echo __('admin_save'); ?></button>
			<a href="<?php echo \Uri::create('admin/siteman'); ?>" class="btn btn-default"><?php echo \Lang::get('admin_cancel'); ?></a>
		</div>
	</div>
<?php echo \Form::close(); ?> 