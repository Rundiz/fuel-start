<article class="general-page-container">
	<h1><?php echo \Lang::get('account.account_view_login_history_of', array('name' => $account->account_display_name)); ?></h1>
	
	<table class="table table-bordered table-striped table-responsive list-logins-table">
		<thead>
			<tr>
				<?php 
				// except querystring to generate
				$except_querystring[] = 'page';
				?> 
				<th style="width: 50%;"><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'login_ua', 'sort' => $next_sort), $except_querystring, null, \Lang::get('accountlogins.accountlogins_user_agent')); ?></th>
				<th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'login_browser', 'sort' => $next_sort), $except_querystring, null, \Lang::get('accountlogins.accountlogins_browser')); ?></th>
				<th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'login_ip', 'sort' => $next_sort), $except_querystring, null, \Lang::get('accountlogins.accountlogins_ip')); ?></th>
				<th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'account_timezone', 'sort' => $next_sort), $except_querystring, null, \Lang::get('accountlogins.accountlogins_date_time')); ?></th>
				<th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'login_attempt', 'sort' => $next_sort), $except_querystring, null, \Lang::get('accountlogins.accountlogins_result')); ?></th>
			</tr>
		</thead>
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
	
	<?php echo $pagination->render(); ?> 
</article>