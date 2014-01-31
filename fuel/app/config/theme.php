<?php
/**
 * Theme configuration
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

if (!defined('THEMEDIR')) {
	define('THEMEDIR', DOCROOT . 'public' . DS . 'themes' . DS);
}

return array(
	'active' => 'system',
	'assets_folder' => 'assets',
	'fallback' => 'system',
	'paths' => array(
		THEMEDIR
	),
	'use_modules' => 'modules',
	'view_ext' => '.php',
);