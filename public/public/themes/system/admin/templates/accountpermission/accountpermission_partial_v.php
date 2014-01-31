<?php echo \Form::open(array('action' => 'admin/account-permission/save', 'class' => 'form-horizontal', 'role' => 'form')); ?> 
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
				<th class="perm-page-cell-head"><?php echo \Lang::get('acperm.acperm_permission_page'); ?></th>
				<th class="perm-action-cell-head"><?php echo \Lang::get('acperm.acperm_permission_action'); ?></th>
				<?php 
				$column = 2; 
				foreach ($list_levels['items'] as $lv) { ?> 
				<th class="perm-lv-cell-head"><?php echo $lv->level_name; ?></th>
				<?php 
					$column++;
				} // endforeach; 
				?> 
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th class="perm-page-cell-head"><?php echo \Lang::get('acperm.acperm_permission_page'); ?></th>
				<th class="perm-action-cell-head"><?php echo \Lang::get('acperm.acperm_permission_action'); ?></th>
				<?php foreach ($list_levels['items'] as $lv) { ?> 
				<th class="perm-lv-cell-head"><?php echo $lv->level_name; ?></th>
				<?php } // endforeach; ?> 
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
				<td class="perm-action-cell<?php if ($count_act == 1): ?> perm-page-row<?php endif; ?>"><?php echo \Lang::get($perm_action); ?></td>
				<?php foreach ($list_levels['items'] as $lv) { ?> 
				<td class="perm-lv-cell<?php if ($count_act == 1): ?> perm-page-row<?php endif; ?>">
					<input type="hidden" name="permission_page[<?php echo $count_all; ?>]" value="<?php echo $perm_page; ?>" />
					<input type="hidden" name="permission_action[<?php echo $count_all; ?>]" value="<?php echo $perm_action; ?>" />
					<input type="checkbox" name="level_group_id[<?php echo $count_all; ?>][]" value="<?php echo $lv->level_group_id; ?>"<?php if (in_array(array($perm_page => array($perm_action => $lv->level_group_id)), $list_permissions_check) || $lv->level_group_id == '1'): ?> checked="checked"<?php endif; ?> />
				</td>
				<?php } // endforeach; ?> 
			</tr>
			<?php 
					$count_act++; 
					$count_all++;
				}// endforeach;
			} // endforeach; 
			?> 
			<?php } else { ?> 
			<tr>
				<td colspan="<?php echo $column; ?>"><?php echo \Lang::get('fslang.fslang_no_data'); ?></td>
			</tr>
			<?php } // endif; ?> 
		</tbody>
	</table>

	<div class="row cmds">
		<div class="col-sm-6">
			<button type="submit" class="btn btn-primary"><?php echo \Lang::get('admin.admin_save'); ?></button>
			<?php echo \Extension\Html::anchor('admin', \Lang::get('admin.admin_cancel'), array('class' => 'btn btn-default')); ?> 
		</div>
	</div>
<?php echo \Form::close(); ?> 


<script type="text/javascript">
	$(function() {
		
	});// jquery start
</script>