<?php
/** 
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

namespace Fs;

class Controller_Update extends \Controller_BaseController
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
		/*if (\Model_AccountLevelPermission::checkAdminPermission('fsupdater_perm', 'fs_update_perm') == false) {
			\Session::set_flash(
				'form_status',
				array(
					'form_status' => 'error',
					'form_status_message' => \Lang::get('admin.admin_permission_denied', array('page' => \Uri::string()))
				)
			);
			\Response::redirect(\Uri::create('admin'));
		}*/// do not need to check permission since update run from frontend.
		
		if (\Input::method() == 'POST') {
			if (!\Extension\NoCsrf::check()) {
				// validate token failed
				$output['form_status'] = 'error';
				$output['form_status_message'] = \Lang::get('fslang.fslang_invalid_csrf_token');
			} else {
				// update to 1.5 first time
				$result = \Fs\update0001::run();

				if ($result === true) {
					$output['hide_form'] = true;
					$output['form_status'] = 'success';
					$output['form_status_message'] = \Lang::get('fs_update_completed');
				} else {
					$output['form_status'] = 'error';
					$output['form_status_message'] = \Lang::get('fs_failed_to_update');
				}
			}
		}
		
		// <head> output ----------------------------------------------------------------------------------------------
		$output['page_title'] = $this->generateTitle(\Lang::get('fs_updater'));
		// <head> output ----------------------------------------------------------------------------------------------
		
		return $this->generatePage('update_v', $output, false);
	}// action_index
	
	
}

