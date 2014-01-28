<h1><?php echo (\Uri::segment(3) == 'add' ? \Lang::get('accountlv.accountlv_add_role') : \Lang::get('accountlv.accountlv_edit_role')); ?></h1>

<?php echo \Extension\Form::open(array('class' => 'form-horizontal', 'role' => 'form')); ?> 
	<div class="form-status-placeholder">
		<?php if (isset($form_status) && isset($form_status_message)) { ?> 
		<div class="alert alert-<?php echo str_replace('error', 'danger', $form_status); ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
		<?php } ?> 
	</div>
	<div class="hidden csrf-container">
		<?php echo \Extension\NoCsrf::generate(); ?> 
	</div>

	<div class="form-group">
		<label for="level_name" class="col-sm-2 control-label"><?php echo __('accountlv.accountlv_role'); ?>: <span class="txt_require">*</span></label>
		<div class="col-sm-5">
			<?php echo \Extension\Form::input('level_name', (isset($level_name) ? $level_name : ''), array('id' => 'level_name', 'maxlength' => '255', 'class' => 'form-control', 'required' => '')); ?> 
		</div>
	</div>
	<div class="form-group">
		<label for="level_description" class="col-sm-2 control-label"><?php echo __('accountlv.accountlv_description'); ?>:</label>
		<div class="col-sm-5">
			<?php echo \Extension\Form::input('level_description', (isset($level_description) ? $level_description : ''), array('id' => 'level_description', 'maxlength' => '255', 'class' => 'form-control')); ?> 
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<button type="submit" class="btn btn-primary"><?php echo __('admin.admin_save'); ?></button>
			<a href="<?php echo \Uri::create('admin/account-level'); ?>" class="btn btn-default"><?php echo \Lang::get('admin.admin_cancel'); ?></a>
		</div>
	</div>
<?php echo \Form::close(); ?> 