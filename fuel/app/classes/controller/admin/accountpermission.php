<?php
/** 
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

class Controller_Admin_AccountPermission extends \Controller_AdminController 
{
	
	
	public function __construct()
	{
		parent::__construct();

		// load language
		\Lang::load('acperm', 'acperm');
	}// __construct
	
	
	/**
	 * define permissions for this app/controller.
	 * 
	 * @return array
	 */
	public function _define_permission() 
	{
		// return array('controller page name' => array('action 1', 'action 2', 'action 3', 'a lot more action. up to you...'));
		return array('acperm.acperm_perm' => array('acperm.acperm_manage_perm'));
	}// _define_permission
	
	
	public function action_index() 
	{
		// check permission
		if (\Model_AccountLevelPermission::checkAdminPermission('acperm.acperm_perm', 'acperm.acperm_manage_perm') == false) {
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
		\Lang::load('acperm', 'acperm');
		
		// read flash message for display errors.
		$form_status = \Session::get_flash('form_status');
		if (isset($form_status['form_status']) && isset($form_status['form_status_message'])) {
			$output['form_status'] = $form_status['form_status'];
			$output['form_status_message'] = $form_status['form_status_message'];
		}
		unset($form_status);
		
		// list modules that has permission for admin click to edit permission.
		$output['list_modules_perm'] = \Library\Modules::forge()->listModulesWithPermission();
		
		// set to make sure these are core controllers permissions
		$output['permission_core'] = 1;
		
		// list permissions from app/classes/controller (core controllers)
		$output['list_permissions'] = \Model_AccountLevelPermission::fetchPermissionsFile();
		$output['list_permissions_check'] = \Model_AccountLevelPermission::listPermissionChecked();
		$output['list_levels'] = \Model_AccountLevelGroup::listLevels();
		
		// <head> output ----------------------------------------------------------------------------------------------
		$output['page_title'] = $this->generateTitle(\Lang::get('acperm.acperm_permission'));
		// <head> output ----------------------------------------------------------------------------------------------
		
		return $this->generatePage('admin/templates/accountpermission/accountpermission_v', $output, false);
	}// action_index
	
	
	public function action_module($module_system_name = '') 
	{
		// check permission
		if (\Model_AccountLevelPermission::checkAdminPermission('acperm.acperm_perm', 'acperm.acperm_manage_perm') == false) {
			\Session::set_flash(
				'form_status',
				array(
					'form_status' => 'error',
					'form_status_message' => \Lang::get('admin.admin_permission_denied', array('page' => \Uri::string()))
				)
			);
			\Response::redirect(\Uri::create('admin'));
		}
		
		// check if this module really has permission.
		if (\Library\Modules::forge()->hasPermission($module_system_name) == false) {
			\Response::redirect(\Uri::create('admin/account-permission'));
		}
		
		// load language
		\Lang::load('account', 'account');
		\Lang::load('acperm', 'acperm');
		
		// read flash message for display errors.
		$form_status = \Session::get_flash('form_status');
		if (isset($form_status['form_status']) && isset($form_status['form_status_message'])) {
			$output['form_status'] = $form_status['form_status'];
			$output['form_status_message'] = $form_status['form_status_message'];
		}
		unset($form_status);
		
		// set to make sure these are NOT core controllers permissions
		$output['permission_core'] = 0;
		$output['module_system_name'] = $module_system_name;
		
		// list permissions, levels, checked permissions ------------------------------------------------------------
		$output['list_permissions'] = \Library\Modules::forge()->fetchPermissionModule($module_system_name);
		$output['list_permissions_check'] = \Model_AccountLevelPermission::listPermissionChecked(0, $module_system_name);
		$output['list_levels'] = \Model_AccountLevelGroup::listLevels();
		
		// read module data from file
		$output['module'] = \Library\Modules::forge()->readModuleMetadataFromModuleName($module_system_name);
		
		// <head> output ----------------------------------------------------------------------------------------------
		$output['page_title'] = $this->generateTitle(\Lang::get('acperm.acperm_permission'));
		// <head> output ----------------------------------------------------------------------------------------------
		
		return $this->generatePage('admin/templates/accountpermission/accountpermission_module_v', $output, false);
	}// action_module
	
	
	public function action_reset() 
	{
		// check permission
		if (\Model_AccountLevelPermission::checkAdminPermission('acperm.acperm_perm', 'acperm.acperm_manage_perm') == false) {
			\Session::set_flash(
				'form_status',
				array(
					'form_status' => 'error',
					'form_status_message' => \Lang::get('admin.admin_permission_denied', array('page' => \Uri::string()))
				)
			);
			\Response::redirect(\Uri::create('admin'));
		}
		
		// method post only 
		if (\Input::method() != 'POST') {
			\Response::redirect(\Uri::create('admin/account-permission'));
		}
		
		// ajax request only
		if (!\Input::is_ajax()) {
			\Response::redirect(\Uri::create('admin/account-permission'));
		}
		
		if (!\Extension\NoCsrf::check()) {
			$output['result'] = false;
		} else {
			$result = \Model_AccountLevelPermission::resetPermission();
			$output['result'] = $result;
		}
		
		$response = new \Response();
		$response->set_header('Content-Type', 'application/json');
		$response->body(json_encode($output));
		return $response;
	}// action_reset
	
	
	public function action_save() 
	{
		// check permission
		if (\Model_AccountLevelPermission::checkAdminPermission('acperm.acperm_perm', 'acperm.acperm_manage_perm') == false) {
			\Session::set_flash(
				'form_status',
				array(
					'form_status' => 'error',
					'form_status_message' => \Lang::get('admin.admin_permission_denied', array('page' => \Uri::string()))
				)
			);
			\Response::redirect(\Uri::create('admin'));
		}
		
		// if form submitted
		if (\Input::method() == 'POST') {
			if (\Extension\NoCsrf::check()) {
				$data['permission_core'] = (int) trim(\Input::post('permission_core'));
					if ($data['permission_core'] != '1') {$data['permission_core'] = '0';}
				$data['module_system_name'] = \Security::strip_tags(trim(\Input::post('module_system_name')));
					if ($data['module_system_name'] == null || $data['permission_core'] == '1') {$data['module_system_name'] = null;}

				$data['level_group_id'] = \Input::post('level_group_id');
				$data['permission_page'] = \Input::post('permission_page');
				$data['permission_action'] = \Input::post('permission_action');

				\Model_AccountLevelPermission::savePermissions($data);
			}
		}
		
		// set success message
		\Session::set_flash(
			'form_status',
			array(
				'form_status' => 'success',
				'form_status_message' => \Lang::get('admin.admin_saved')
			)
		);
		
		// go back
		if (\Input::referrer() != null && \Input::referrer() != \Uri::main()) {
			\Response::redirect(\Input::referrer());
		} else {
			\Response::redirect('admin/account-permission');
		}
	}// action_save
	
	
}

