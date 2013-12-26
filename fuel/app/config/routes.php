<?php
return array(
	'_root_'  => 'index/index',  // The default route
	'_404_'   => 'error/404',    // The main 404 route
	
	'account/confirm-register(.*)' => 'account/ConfirmRegister$1',
	'account/resend-activate' => 'account/ResendActivate',
);