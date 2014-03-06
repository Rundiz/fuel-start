<?php
/**
 * The development database settings. These get merged with the global settings.
 */

return array(
	'default' => array(
		'type' => 'mysqli',
		'connection'  => array(
			'hostname' => 'localhost',
			'database' => 'v_fuelstart_15',
			'username' => 'admin',
			'password' => 'pass',
		),
		'table_prefix' => 'ws_',
		'charset' => 'utf8',
	),
);
