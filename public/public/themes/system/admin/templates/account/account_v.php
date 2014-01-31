<h1><?php echo \Lang::get('account.account_accounts'); ?></h1>

<div class="row cmds">
	<div class="col-sm-6">
		<?php if (\Model_AccountLevelPermission::checkAdminPermission('account.account_perm', 'account.account_edit_perm')) {echo \Html::anchor('admin/account/add', \Lang::get('admin.admin_add'), array('class' => 'btn btn-default'));} ?> 
		| <?php printf(\Lang::get('admin.admin_total', array('total' => (isset($list_accounts['total']) ? $list_accounts['total'] : '0')))); ?>
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
		<table class="table table-striped table-hover">
			<?php 
			// except querystring to generate
			$except_querystring[] = 'page';
			?> 
			<thead>
				<tr>
					<th class="check-column"><input type="checkbox" name="id_all" value="" onclick="checkAll(this.form,'id[]',this.checked)" /></th>
					<th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'account_id', 'sort' => $next_sort), $except_querystring, null, \Lang::get('account.account_id')); ?></th>
					<th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'account_username', 'sort' => $next_sort), $except_querystring, null, \Lang::get('account.account_username')); ?></th>
					<th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'account_email', 'sort' => $next_sort), $except_querystring, null, \Lang::get('account.account_email')); ?></th>
					<th><?php echo \Lang::get('account.account_role'); ?></th>
					<th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'account_create', 'sort' => $next_sort), $except_querystring, null, \Lang::get('account.account_register_since')); ?></th>
					<th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'account_last_login', 'sort' => $next_sort), $except_querystring, null, \Lang::get('account.account_last_login')); ?></th>
					<th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'account_status', 'sort' => $next_sort), $except_querystring, null, \Lang::get('account.account_status')); ?></th>
					<th></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th class="check-column"><input type="checkbox" name="id_all" value="" onclick="checkAll(this.form,'id[]',this.checked)" /></th>
					<th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'account_id', 'sort' => $next_sort), $except_querystring, null, \Lang::get('account.account_id')); ?></th>
					<th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'account_username', 'sort' => $next_sort), $except_querystring, null, \Lang::get('account.account_username')); ?></th>
					<th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'account_email', 'sort' => $next_sort), $except_querystring, null, \Lang::get('account.account_email')); ?></th>
					<th><?php echo \Lang::get('account.account_role'); ?></th>
					<th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'account_create', 'sort' => $next_sort), $except_querystring, null, \Lang::get('account.account_register_since')); ?></th>
					<th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'account_last_login', 'sort' => $next_sort), $except_querystring, null, \Lang::get('account.account_last_login')); ?></th>
					<th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'account_status', 'sort' => $next_sort), $except_querystring, null, \Lang::get('account.account_status')); ?></th>
					<th></th>
				</tr>
			</tfoot>
			<tbody>
				<?php if (isset($list_accounts['items']) && is_array($list_accounts['items']) && !empty($list_accounts['items'])) { ?> 
				<?php foreach ($list_accounts['items'] as $row) { ?> 
				<tr>
					<td class="check-column"><?php echo \Extension\Form::checkbox('id[]', $row->account_id, array(($row->account_id == '0' ? 'disabled' : null))); ?></td>
					<td><?php echo $row->account_id; ?></td>
					<td><?php echo \Security::htmlentities($row->account_username); ?></td>
					<td><?php echo $row->account_email; ?></td>
					<td>
						<?php 
						$i = 1;
						foreach($row->account_level as $lvl) {
							$lvg = \Model_AccountLevelGroup::find($lvl->level_group_id);
							echo $lvg->level_name;

							if (end($row->account_level) != $lvl) {
								echo ', ';
							}

							if ($i > 5) {
								echo '...';
								break;
							}

							$i++;
						} 
						unset($lvg, $lvl);
						?>
					</td>
					<td><?php echo \Extension\Date::gmtDate('', $row->account_create); ?></td>
					<td><?php if ($row->account_last_login != null) {echo \Extension\Date::gmtDate('', $row->account_last_login);} ?></td>
					<td><span class="glyphicon glyphicon-<?php echo ($row->account_status == '1' ? 'ok' : 'remove'); ?>"></span> <?php echo $row->account_status_text; ?></td>
					<td>
						<?php if ($row->account_id != '0') { ?> 
						<ul class="actions-inline">
							<?php if (\Model_AccountLevelPermission::checkAdminPermission('account.account_perm', 'account.account_edit_perm')) { ?> <li><?php echo \Extension\Html::anchor('admin/account/edit/' . $row->account_id, '<span class="glyphicon glyphicon-pencil"></span> ' . \Lang::get('admin.admin_edit'), array('class' => 'btn btn-default btn-xs')); ?></li><?php } ?> 
							<?php if (\Model_AccountLevelPermission::checkAdminPermission('account.account_perm', 'account.account_viewlogin_log_perm')) { ?> <li><?php echo \Extension\Html::anchor('admin/account/viewlogins/' . $row->account_id, '<span class="glyphicon glyphicon-list"></span> ' . \Lang::get('account.account_view_login_history'), array('class' => 'btn btn-default btn-xs')); ?></li><?php } ?> 
						</ul>
						<?php } ?> 
					</td>
				</tr>
				<?php } // endofreach; ?> 
				<?php } else { ?> 
				<tr>
					<td colspan="9"><?php echo \Lang::get('fslang.fslang_no_data'); ?></td>
				</tr>
				<?php } // endif; ?> 
			</tbody>
		</table>
	</div>
	
	<div class="row cmds">
		<div class="col-sm-6">
			 
			<select name="act" class="form-control select-inline chosen-select">
				<option value="" selected="selected"></option>
				<?php if (\Model_AccountLevelPermission::checkAdminPermission('account.account_perm', 'account.account_edit_perm')) { ?><option value="enable"><?php echo \Lang::get('admin.admin_enable'); ?></option><?php } ?> 
				<?php if (\Model_AccountLevelPermission::checkAdminPermission('account.account_perm', 'account.account_edit_perm')) { ?><option value="disable"><?php echo \Lang::get('admin.admin_disable'); ?></option><?php } ?> 
				<?php if (\Model_AccountLevelPermission::checkAdminPermission('account.account_perm', 'account.account_delete_perm')) { ?><option value="del"><?php echo \Lang::get('admin.admin_delete'); ?></option><?php } ?> 
			</select>
			<button type="submit" class="bb-button btn btn-warning"><?php echo \Lang::get('admin.admin_submit'); ?></button>
			<?php echo \Extension\Html::anchor('admin', \Lang::get('admin.admin_cancel'), array('class' => 'btn btn-default')); ?> 
		</div>
		<div class="col-sm-6">
			<?php if (isset($pagination)) {echo $pagination->render();} ?> 
		</div>
	</div>
<?php echo \Form::close(); ?> 