<?php
/** 
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

namespace Fs;

class Controller_Admin_Update extends \Controller_AdminController
{
	
	
	public function __construct()
	{
		parent::__construct();

		// load language
		\Lang::load('fs::fs');
	}// __construct
	
	
	public function action_index()
	{
		// check permission
		if (\Model_AccountLevelPermission::checkAdminPermission('fsupdater_perm', 'fs_update_perm') == false) {
			\Session::set_flash(
				'form_status',
				array(
					'form_status' => 'error',
					'form_status_message' => \Lang::get('admin.admin_permission_denied', array('page' => \Uri::string()))
				)
			);
			\Response::redirect(\Uri::create('admin'));
		}
		
		// update to 1.5 first time
		$result = \Fs\update0001::run();
		
		$output['result'] = $result;
		
		// <head> output ----------------------------------------------------------------------------------------------
		$output['page_title'] = $this->generateTitle(\Lang::get('fs_updater'));
		// <head> output ----------------------------------------------------------------------------------------------
		
		return $this->generatePage('admin/update_v', $output, false);
	}// action_index
	
	
}

