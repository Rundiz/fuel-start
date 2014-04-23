<?php echo \Form::open(array('action' => 'admin/account-permission/save/' . $account_id, 'class' => 'form-horizontal', 'role' => 'form')); ?> 
	<div class="form-status-placeholder">
		<?php if (isset($form_status) && isset($form_status_message)) { ?> 
		<div class="alert alert-<?php echo str_replace('error', 'danger', $form_status); ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
		<?php } ?> 
	</div>
	<?php echo \Extension\NoCsrf::generate(); ?> 

	<input type="hidden" name="permission_core" value="<?php echo $permission_core; ?>" />
	<input type="hidden" name="module_system_name" value="<?php if (isset($module_system_name)) {echo $module_system_name;} ?>" />

	<table class="table table-hover table-bordered tableWithFloatingHeader">
		<thead>
			<tr>
				<th class="perm-page-cell-head"><?php echo \Lang::get('acperm_permission_page'); ?></th>
				<th class="perm-action-cell-head"><?php echo \Lang::get('acperm_permission_action'); ?></th>
				<th><?php echo __('acperm_account_x', array('account_username' => (isset($account_username) ? htmlspecialchars($account_username) : null))); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th class="perm-page-cell-head"><?php echo \Lang::get('acperm_permission_page'); ?></th>
				<th class="perm-action-cell-head"><?php echo \Lang::get('acperm_permission_action'); ?></th>
				<th><?php echo __('acperm_account_x', array('account_username' => (isset($account_username) ? htmlspecialchars($account_username) : null))); ?></th>
			</tr>
		</tfoot>
		<tbody>
			<?php if (isset($list_permissions) && is_array($list_permissions) && !empty($list_permissions)) { ?> 
			<?php 
			$count_all = 1;
			foreach ($list_permissions as $perm_page => $perm_actions) {
				$count_act = 1;
				foreach ($perm_actions as $perm_action) { 
			?> 
			<tr>
				<?php if ($count_act == 1): ?><td rowspan="<?php echo count($perm_actions); ?>" class="perm-page-cell perm-page-row"><?php echo \Lang::get($perm_page); ?></td><?php endif; ?> 
				<td class="perm-action-cell<?php if ($count_act == 1) { ?> perm-page-row<?php } ?>"><?php echo \Lang::get($perm_action); ?></td>
				<td class="perm-account-cell<?php if ($count_act == 1) { ?> perm-page-row<?php } ?>">
					<input type="hidden" name="permission_page[<?php echo $count_all; ?>]" value="<?php echo $perm_page; ?>" />
					<input type="hidden" name="permission_action[<?php echo $count_all; ?>]" value="<?php echo $perm_action; ?>" />
					<input type="checkbox" name="account_id[<?php echo $count_all; ?>]" value="<?php echo $account_id; ?>"<?php
						if (in_array(
								array(
									$perm_page => 
									array($perm_action => $account_id)
								), 
								$list_permissions_check
							)
							|| ($account_id == '1')
							|| (
								isset($level_group_check)
								&& is_array($level_group_check)
								&& in_array(1, $level_group_check)// if this user is in level group id 1 (super admin).
							)
						) {
							
							echo ' checked="checked"';
						}
					?> class="custom-checkbox" />
				</td>
			</tr>
			<?php 
					$count_act++; 
					$count_all++;
				}// endforeach;
			} // endforeach; 
			?> 
			<?php } else { ?> 
			<tr>
				<td colspan="<?php echo $column; ?>"><?php echo \Lang::get('fslang_no_data'); ?></td>
			</tr>
			<?php } // endif; ?> 
		</tbody>
	</table>

	<div class="row cmds">
		<div class="col-sm-6">
			<button type="submit" class="btn btn-primary"><?php echo \Lang::get('admin_save'); ?></button>
			<?php echo \Extension\Html::anchor('admin', \Lang::get('admin_cancel'), array('class' => 'btn btn-default')); ?> 
		</div>
	</div>
<?php echo \Form::close(); ?> 


<script type="text/javascript">
	$(function() {
		tableWithFloatingheader();
	});// jquery start
</script>