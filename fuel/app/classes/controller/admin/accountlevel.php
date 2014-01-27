<?php
/** 
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

class Controller_Admin_AccountLevel extends \Controller_AdminController 
{
	
	
	/**
	 *disallowed edit or delete level_group_id
	 * 
	 * @var array array of disallowed ids
	 */
	public $disallowed_edit_delete;
	
	
	public function __construct()
	{
		parent::__construct();
		
		// set disallowed ids
		$this->disallowed_edit_delete = \Model_AccountLevelGroup::forge()->disallowed_edit_delete;
	}// __construct
	
	
	/**
	 * define permissions for this app/controller.
	 * 
	 * @return array
	 */
	protected function _define_permission() 
	{
		// return array('controller page name' => array('action 1', 'action 2', 'action 3', 'a lot more action. up to you...'));
		return array('accountlv_perm' => array('accountlv_viewlevels_perm', 'accountlv_add_perm', 'accountlv_edit_perm', 'accountlv_delete_perm', 'accountlv_sort_perm'));
	}// _define_permission
	
	
	public function action_index() 
	{
		// check permission
		if (\Model_AccountLevelPermission::checkAdminPermission('accountlv_perm', 'accountlv_viewlevels_perm') == false) {
			\Session::set_flash(
				'form_status',
				array(
					'form_status' => 'error',
					'form_status_message' => \Lang::get('admin.admin_permission_denied', array('page' => \Uri::string()))
				)
			);
			\Response::redirect(\Uri::create('admin'));
		}
		
		// load language
		\Lang::load('account', 'account');
		\Lang::load('accountlv', 'accountlv');
		
		// read flash message for display errors.
		$form_status = \Session::get_flash('form_status');
		if (isset($form_status['form_status']) && isset($form_status['form_status_message'])) {
			$output['form_status'] = $form_status['form_status'];
			$output['form_status_message'] = $form_status['form_status_message'];
		}
		unset($form_status);
		
		// search query
		$output['q'] = trim(\Input::get('q'));
		
		// disallow edit, delete.
		$output['disallowed_edit_delete'] = $this->disallowed_edit_delete;
		
		// list level groups
		$output['list_levels'] = \Model_AccountLevelGroup::listLevels();
		
		// <head> output ----------------------------------------------------------------------------------------------
		$output['page_title'] = $this->generateTitle(\Lang::get('accountlv.accountlv_role'));
		// <head> output ----------------------------------------------------------------------------------------------
		
		return $this->generatePage('admin/templates/accountlevel/accountlevel_v', $output, false);
	}// action_index
	
	
}

