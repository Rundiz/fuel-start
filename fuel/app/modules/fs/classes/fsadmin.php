<?php
/**
 * Fuel Start updater
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

namespace Fs;

class Fsadmin
{


	public function __construct()
	{
		// load language
		\Lang::load('fs::fs');
	}// __construct
	
	
	/**
	 * define permission for administrators actions.
	 * @return array
	 */
	public function _define_permission() 
	{
		//return array('fsupdater_perm' => array('fs_update_perm'));
	}// _define_permission


}

