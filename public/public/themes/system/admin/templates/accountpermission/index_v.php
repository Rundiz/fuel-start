<h1><?php echo \Lang::get('acperm_user_permission'); ?></h1>

<div class="row cmds">
	<div class="col-sm-6">
		<?php if ($account_check_result === true) { ?> 
		<button type="button" class="btn btn-danger" onclick="return ajaxResetPermission();"><?php echo \Lang::get('acperm_reset_permission'); ?></button>
		<?php } // endif ?> 
		<div class="btn-group">
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><?php echo \Lang::get('acperm_module_permissison'); ?> <span class="caret"></span></button>
			<ul class="dropdown-menu" role="menu">
				<?php if (isset($list_modules_perm) && is_array($list_modules_perm) && !empty($list_modules_perm)) { ?> 
				<?php foreach ($list_modules_perm as $module) { ?> 
				<li><?php echo \Extension\Html::anchor('admin/account-permission/module/' . $account_id . '/' . $module['module_system_name'], $module['module_name']); ?></li>
				<?php } // endforeach; ?> 
				<?php } else { ?> 
				<li><a href="#" onclick="return false;">&mdash;</a></li>
				<?php } // endif; ?> 
			</ul>
		</div>
	</div>
	<div class="col-sm-6 text-right">
		<form class="form-inline">
			<div class="form-group">
				<label class="control-label" for="find-account"><?php echo __('account_username'); ?>:</label>
				<input type="text" class="form-control" id="find-account" name="account_username" value="<?php if (isset($account_username)) {echo htmlspecialchars($account_username);} ?>" placeholder="<?php echo __('acperm_find_account'); ?>" maxlength="255" />
			</div>
		</form>
	</div>
</div>

<?php if ($account_check_result !== true) { ?> 
<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $account_check_result; ?></div>
<?php } elseif ($account_check_result === true) { ?> 
<?php include_once __DIR__ . DS . 'permission_table.php'; ?> 
<?php } // endif; ?> 

<script type="text/javascript">
	<?php if ($account_check_result === true) { ?> 
	function ajaxResetPermission() 
	{
		var confirm_del = window.confirm('<?php echo \Lang::get('acperm_are_you_sure_to_reset_for_this_user'); ?>');
		
		if (confirm_del == true) {
			$.ajax({
				url: site_url+'admin/account-permission/reset/<?php echo $account_id; ?>',
				type: 'POST',
				data: csrf_name+'='+nocsrf_val,
				dataType: 'json',
				success: function(data) {
					if (data.result == true) {
						alert('<?php echo \Lang::get('acperm_reset_completed'); ?>');
						location.reload();
					} else {
						alert('<?php echo \Lang::get('acperm_failed_to_reset_permission'); ?>');
						return false;
					}
				}
			});
		}
		
		return false;
	}
	<?php } // endif; ?> 

	$(function() {
		// auto complete find user
		$('#find-account').autocomplete({
			source: site_url+'admin/account-permission/ajaxfindaccount',
			minLength: 1,
			focus: function(event, ui) {
				$('#find-account').val(ui.item.label);
				return false;
			},
			select: function(event, ui) {
				$('#find-account').val(ui.item.label);
				window.location.href = site_url+'admin/account-permission/index/'+ui.item.value;
				return false;
			}
		});
		// end auto complete find user
	});// jquery
</script>
