<h1><?php echo \Lang::get('account.account_view_login_history_of', array('name' => \Security::htmlentities($account->account_username))); ?> <small><?php echo \Extension\Html::anchor('admin/account/edit/' . $account_id, '<span class="glyphicon glyphicon-pencil"></span> ' . \Lang::get('admin.admin_edit'), array('class' => 'btn btn-default btn-xs')); ?></small></h1>

<?php echo \Form::open(array('action' => 'admin/account/delete_log/' . $account_id, 'class' => 'form-horizontal', 'role' => 'form')); ?> 
	<div class="form-status-placeholder">
		<?php if (isset($form_status) && isset($form_status_message)) { ?> 
		<div class="alert alert-<?php echo str_replace('error', 'danger', $form_status); ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
		<?php } ?> 
	</div>
	<?php echo \Extension\NoCsrf::generate(); ?> 

	<div class="table-responsive">
		<table class="table table-striped table-hover list-logins-table">
			<?php 
			// except querystring to generate
			$except_querystring[] = 'page';
			?> 
			<thead>
				<tr>
					<th style="width: 50%;"><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'login_ua', 'sort' => $next_sort), $except_querystring, null, \Lang::get('accountlogins.accountlogins_user_agent')); ?></th>
					<th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'login_browser', 'sort' => $next_sort), $except_querystring, null, \Lang::get('accountlogins.accountlogins_browser')); ?></th>
					<th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'login_ip', 'sort' => $next_sort), $except_querystring, null, \Lang::get('accountlogins.accountlogins_ip')); ?></th>
					<th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'account_timezone', 'sort' => $next_sort), $except_querystring, null, \Lang::get('accountlogins.accountlogins_date_time')); ?></th>
					<th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'login_attempt', 'sort' => $next_sort), $except_querystring, null, \Lang::get('accountlogins.accountlogins_result')); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th style="width: 50%;"><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'login_ua', 'sort' => $next_sort), $except_querystring, null, \Lang::get('accountlogins.accountlogins_user_agent')); ?></th>
					<th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'login_browser', 'sort' => $next_sort), $except_querystring, null, \Lang::get('accountlogins.accountlogins_browser')); ?></th>
					<th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'login_ip', 'sort' => $next_sort), $except_querystring, null, \Lang::get('accountlogins.accountlogins_ip')); ?></th>
					<th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'account_timezone', 'sort' => $next_sort), $except_querystring, null, \Lang::get('accountlogins.accountlogins_date_time')); ?></th>
					<th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'login_attempt', 'sort' => $next_sort), $except_querystring, null, \Lang::get('accountlogins.accountlogins_result')); ?></th>
				</tr>
			</tfoot>
			<tbody>
				<?php if (isset($list_logins['items']) && is_array($list_logins['items']) && !empty($list_logins['items'])) { ?> 
				<?php foreach ($list_logins['items'] as $row) { ?> 
				<tr>
					<td><?php echo $row->login_ua; ?></td>
					<td><?php echo $row->login_browser; ?></td>
					<td><?php echo $row->login_ip; ?></td>
					<td><?php echo \Extension\Date::gmtDate('', $row->login_time, $account->account_timezone); ?></td>
					<td><span class="glyphicon glyphicon-<?php echo ($row->login_attempt == '1' ? 'ok' : 'remove'); ?>"></span> <?php echo \Lang::get('account.' . $row->login_attempt_text); ?></td>
				</tr>
				<?php } // endofreach; ?> 
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
			<?php if (\Model_AccountLevelPermission::checkAdminPermission('account.account_perm', 'account.account_deletelogin_log_perm')) { ?> 
			<select name="act" class="form-control select-inline chosen-select">
				<option value="" selected="selected"></option>
				<option value="del"><?php echo \Lang::get('admin.admin_delete'); ?></option>
				<option value="truncate"><?php echo \Lang::get('account.account_delete_all_user_logins'); ?></option>
			</select>
			<button type="submit" class="bb-button btn btn-warning"><?php echo \Lang::get('admin.admin_submit'); ?></button>
			<?php echo \Extension\Html::anchor('admin/account', \Lang::get('admin.admin_cancel'), array('class' => 'btn btn-default')); ?> 
			<?php } ?> 
		</div>
		<div class="col-sm-6">
			<?php if (isset($pagination)) {echo $pagination->render();} ?> 
		</div>
	</div>
<?php echo \Form::close(); ?> 