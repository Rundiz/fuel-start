<?php
return array(
	'_root_'  => 'index/index',  // The default route
	'_404_'   => 'error/404',    // The main 404 route
	
	'account/confirm-register(.*)' => 'account/ConfirmRegister$1',
	'account/resend-activate' => 'account/ResendActivate',
	'account/edit/delete-avatar' => 'account/Edit/deleteAvatar',
	'account/confirm-change-email(.*)' => 'account/ConfirmChangeEmail$1',
	'account/view-logins' => 'account/ViewLogins',
	
	'admin' => 'admin/index',
	'admin/account-level(.*)' => 'admin/AccountLevel$1',
	'admin/account-permission(.*)' => 'admin/AccountPermission$1',
);