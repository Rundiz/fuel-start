<h1><?php echo \Lang::get('acperm.acperm_permission'); ?></h1>

<div class="row cmds">
	<div class="col-sm-6">
		<button type="button" class="btn btn-danger" onclick="return ajaxResetPermission();"><?php echo \Lang::get('acperm.acperm_reset_permission'); ?></button>
		<div class="btn-group">
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><?php echo \Lang::get('acperm.acperm_module_permissison'); ?> <span class="caret"></span></button>
			<ul class="dropdown-menu" role="menu">
				<?php if (isset($list_modules_perm) && is_array($list_modules_perm) && !empty($list_modules_perm)) { ?> 
				<?php foreach ($list_modules_perm as $module) { ?> 
				<li><?php echo \Extension\Html::anchor('admin/account-permission/module/' . $module['module_system_name'], $module['module_name']); ?></li>
				<?php } // endforeach; ?> 
				<?php } else { ?> 
				<li><a href="#" onclick="return false;">&mdash;</a></li>
				<?php } // endif; ?> 
			</ul>
		</div>
	</div>
</div>

<?php include_once __DIR__ . DS . 'accountpermission_partial_v.php'; ?> 

<script type="text/javascript">
	function ajaxResetPermission() 
	{
		var confirm_del = window.confirm('<?php echo \Lang::get('acperm.acperm_are_you_sure_to_reset'); ?>');
		
		if (confirm_del == true) {
			$.ajax({
				url: '<?php echo \Uri::main(); ?>/reset',
				type: 'POST',
				data: csrf_name+'='+nocsrf_val,
				dataType: 'json',
				success: function(data) {
					if (data.result == true) {
						alert('<?php echo \Lang::get('acperm.acperm_reset_completed'); ?>');
						location.reload();
					} else {
						alert('<?php echo \Lang::get('acperm.acperm_failed_to_reset_permission'); ?>');
						return false;
					}
				}
			});
		}
		
		return false;
	}
</script>