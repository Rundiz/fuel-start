<h1><?php echo (\Uri::segment(4) == 'add' ? \Lang::get('blog_new_post') : \Lang::get('blog_edit_post')); ?></h1>

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
		<label for="post_name" class="col-sm-2 control-label"><?php echo __('blog_post_name'); ?>: <span class="txt_require">*</span></label>
		<div class="col-sm-10">
			<?php echo \Extension\Form::input('post_name', (isset($post_name) ? $post_name : ''), array('id' => 'post_name', 'maxlength' => '255', 'class' => 'form-control')); ?> 
		</div>
	</div>
	<div class="form-group">
		<label for="post_body" class="col-sm-2 control-label"><?php echo __('blog_post_content'); ?>: <span class="txt_require">*</span></label>
		<div class="col-sm-10">
			<?php echo \Extension\Form::textarea('post_body', (isset($post_body) ? $post_body : ''), array('id' => 'post_body', 'rows' => '10', 'class' => 'form-control')); ?> 
			<div class="help-block"><?php echo \Lang::get('blog_html_allowed'); ?></div>
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<button type="submit" class="btn btn-primary"><?php echo __('admin.admin_save'); ?></button>
			<a href="<?php echo \Uri::create('blog/admin'); ?>" class="btn btn-default"><?php echo \Lang::get('admin.admin_cancel'); ?></a>
		</div>
	</div>
<?php echo \Extension\Form::close(); ?> 