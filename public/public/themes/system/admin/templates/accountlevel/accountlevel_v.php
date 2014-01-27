<h1><?php echo \Lang::get('accountlv.accountlv_role'); ?></h1>

<div class="row cmds">
	<div class="col-sm-6">
		<?php echo \Html::anchor('admin/account-level/add', \Lang::get('admin.admin_add'), array('class' => 'btn btn-default')); ?> 
		| <?php printf(\Lang::get('admin.admin_total', array('total' => (isset($list_levels['total']) ? $list_levels['total'] : '0')))); ?>
	</div>
	<div class="col-sm-6">
		<form method="get" class="form-inline pull-right">
			<div class="form-group">
				<?php echo \Form::input('q', (isset($q) ? $q : ''), array('class' => 'form-control search-input', 'maxlength' => '255')); ?> 
			</div>
			<button type="submit" class="btn btn-default"><?php echo \Lang::get('admin.admin_search'); ?></button>
		</form>
	</div>
</div>

<?php echo \Form::open(array('action' => 'admin/account/multiple', 'class' => 'form-horizontal', 'role' => 'form')); ?> 
	<div class="form-status-placeholder">
		<?php if (isset($form_status) && isset($form_status_message)) { ?> 
		<div class="alert alert-<?php echo str_replace('error', 'danger', $form_status); ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
		<?php } ?> 
	</div>
	<?php echo \Extension\NoCsrf::generate(); ?> 

	<div class="table-responsive">
		<table class="table table-striped list-logins-table">
			<thead>
				<tr>
					<th class="check-column"><input type="checkbox" name="id_all" value="" onclick="checkAll(this.form,'id[]',this.checked)" /></th>
					<th><?php echo \Lang::get('accountlv.accountlv_level_priority'); ?> <span class="glyphicon glyphicon-question-sign bootstrap-tooltip" data-toggle="tooltip" data-original-title="<?php echo \Lang::get('accountlv.accountlv_higher_priority_will_come_first'); ?>"></span></th>
					<th><?php echo \Lang::get('accountlv.accountlv_role'); ?></th>
					<th><?php echo \Lang::get('accountlv.accountlv_description'); ?></th>
					<th></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th class="check-column"><input type="checkbox" name="id_all" value="" onclick="checkAll(this.form,'id[]',this.checked)" /></th>
					<th><?php echo \Lang::get('accountlv.accountlv_level_priority'); ?> <span class="glyphicon glyphicon-question-sign bootstrap-tooltip" data-toggle="tooltip" data-original-title="<?php echo \Lang::get('accountlv.accountlv_higher_priority_will_come_first'); ?>"></span></th>
					<th><?php echo \Lang::get('accountlv.accountlv_role'); ?></th>
					<th><?php echo \Lang::get('accountlv.accountlv_description'); ?></th>
					<th></th>
				</tr>
			</tfoot>
			<tbody>
				<?php if (isset($list_levels['items']) && is_array($list_levels['items']) && !empty($list_levels['items'])) { ?> 
				<?php foreach ($list_levels['items'] as $row) { ?> 
				<tr>
					<td class="check-column"><?php echo \Extension\Form::checkbox('id[]', $row->level_group_id, array((in_array($row->level_group_id, $disallowed_edit_delete) ? 'disabled' : null), 'title' => $row->level_group_id)); ?></td>
					<td><?php echo $row->level_priority; ?></td>
					<td><?php echo $row->level_name; ?></td>
					<td><?php echo $row->level_description; ?></td>
					<td>
						<?php if (\Model_AccountLevelPermission::checkAdminPermission('accountlv_perm', 'accountlv_edit_perm')) { ?> 
						<?php echo \Extension\Html::anchor('admin/account-level/edit/' . $row->level_group_id, '<span class="glyphicon glyphicon-pencil"></span> ' . \Lang::get('admin.admin_edit'), array('class' => 'btn btn-default btn-xs' . (in_array($row->level_group_id, $disallowed_edit_delete) ? ' disabled' : null))); ?> 
						<?php } ?> 
					</td>
				</tr>
				<?php } // endforeach; ?> 
				<?php } else { ?> 
				<tr>
					<td colspan="5"><?php echo \Lang::get('fslang.fslang_no_data'); ?></td>
				</tr>
				<?php } // endif; ?> 
			</tbody>
		</table>
	</div>

	<div class="row cmds">
		<div class="col-sm-6">
			<?php if (\Model_AccountLevelPermission::checkAdminPermission('accountlv_perm', 'accountlv_delete_perm')) { ?> 
			<select name="act" class="form-control select-inline chosen-select">
				<option value="" selected="selected"></option>
				<option value="del"><?php echo \Lang::get('admin.admin_delete'); ?></option>
			</select>
			<button type="submit" class="bb-button btn btn-warning"><?php echo \Lang::get('admin.admin_submit'); ?></button>
			<?php echo \Extension\Html::anchor('admin', \Lang::get('admin.admin_cancel'), array('class' => 'btn btn-default')); ?> 
			<?php } ?> 
		</div>
		<div class="col-sm-6">
			
		</div>
	</div>
<?php echo \Form::close(); ?> 