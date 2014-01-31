<h1><?php echo \Lang::get('acperm.acperm_permission_of_module', array('module_name' => $module['name'])); ?></h1>

<div class="row cmds">
	<div class="col-sm-6">
		<?php echo \Extension\Html::anchor('admin/account-permission', '<span class="glyphicon glyphicon-chevron-left"></span> ' . \Lang::get('acperm.acperm_go_back_to_system_permission'), array('class' => 'btn btn-default')); ?> 
	</div>
</div>

<?php include_once __DIR__ . DS . 'accountpermission_partial_v.php'; ?> 

