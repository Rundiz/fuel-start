<h1><?php echo \Lang::get('admin.admin_administrator_dashbord'); ?></h1>

<div class="form-status-placeholder">
	<?php if (isset($form_status) && isset($form_status_message)) { ?> 
	<div class="alert alert-<?php echo str_replace('error', 'danger', $form_status); ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
	<?php } ?> 
</div>

<div class="dashboard-block dashboard-block-introduce">
	<h2>Welcome to Fuel Start admin dashboard.</h2>
	<p>You can start modify this controller and theme to build your own admin dashboard.</p>
	<p>The theme of this page is located at <?php echo __FILE__; ?></p>
	<p>The controller of this page is located at <?php echo APPPATH . 'classes' . DS . 'controller' . DS . 'admin' . DS . 'index.php'; ?></p>
</div>
<div class="dashboard-block dashboard-block-account">
	<h2><?php echo \Html::anchor('admin/account', \Lang::get('index.index_account')); ?></h2>
	<p><?php echo \Lang::get('index.index_total_accounts', array('total_accounts' => $total_accounts)); ?></p>
</div>
