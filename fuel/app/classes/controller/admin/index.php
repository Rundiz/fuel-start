<?php
/** 
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

class Controller_Admin_Index extends \Controller_AdminController 
{
	
	
	public function action_index() 
	{
		// load language
		\Lang::load('index', 'index');
		
		// read flash message for display errors.
		$form_status = \Session::get_flash('form_status');
		if (isset($form_status['form_status']) && isset($form_status['form_status_message'])) {
			$output['form_status'] = $form_status['form_status'];
			$output['form_status_message'] = $form_status['form_status_message'];
		}
		unset($form_status);
		
		// get total accounts
		$output['total_accounts'] = \Model_Accounts::count();
		
		// <head> output ----------------------------------------------------------------------------------------------
		$output['page_title'] = $this->generateTitle(\Lang::get('admin.admin_administrator_dashbord'));
		// <head> output ----------------------------------------------------------------------------------------------
		
		// the admin views or theme should follow this structure. (admin/templates/controller/method) and follow with _v in the end.
		return $this->generatePage('admin/templates/index/index_v', $output, false);
	}// action_index
	
	
}

