<h1><?php echo \Lang::get('acperm_level_permission_of_module', array('module_name' => $module['name'])); ?></h1>

<div class="row cmds">
	<div class="col-sm-6">
		<?php echo \Extension\Html::anchor('admin/account-permission/index/' . $account_id, '<span class="glyphicon glyphicon-chevron-left"></span> ' . \Lang::get('acperm_go_back_to_system_permission'), array('class' => 'btn btn-default')); ?> 
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
				window.location.href = site_url+'admin/account-permission/module/'+ui.item.value+'/<?php echo $module_system_name; ?>';
				return false;
			}
		});
		// end auto complete find user
	});// jquery
</script>