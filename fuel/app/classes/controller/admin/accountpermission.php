<?php
/** 
 * set user's permission.
 * This controller requires AccountLevelPermission controller to set the permission that who can change level's permission or user's permission.
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
        \Lang::load('acperm');
    }// __construct
    
    
    public function action_ajaxfindaccount()
    {
        // ajax only
        if (!\Input::is_ajax()) {
            \Response::redirect($this->getAndSetSubmitRedirection());
        }
        
        // check permission
        if (\Model_AccountLevelPermission::checkAdminPermission('acperm_perm', 'acperm_manage_user_perm') == false) {
            return null;
        }
        
        $output = array();
        
        // list account with search
        if (trim(\Input::get('term')) != null) {
            $option['search'] = trim(\Input::get('term'));
        }
        $option['list_for'] = 'admin';
        $list_account = \Model_Accounts::listAccounts($option);
        unset($option);
        
        // loop set result for search
        if (is_array($list_account) && !empty($list_account)) {
            $i = 0;
            foreach ($list_account['items'] as $row) {
                $output[$i]['value'] = $row->account_id;
                $output[$i]['label'] = $row->account_username;
                $i++;
            }
        }
        
        $response = new \Response();
        $response->set_header('Content-Type', 'application/json');
        $response->body(json_encode($output));
        return $response;
    }// action_ajaxfindaccount
    
    
    public function action_index($account_id = '')
    {
        // clear redirect referrer
        \Session::delete('submitted_redirect');
        
        // check permission
        if (\Model_AccountLevelPermission::checkAdminPermission('acperm_perm', 'acperm_manage_user_perm') == false) {
            \Session::set_flash(
                'form_status',
                array(
                    'form_status' => 'error',
                    'form_status_message' => \Lang::get('admin_permission_denied', array('page' => \Uri::string()))
                )
            );
            \Response::redirect(\Uri::create('admin'));
        }
        
        // if account id not set
        if (!is_numeric($account_id)) {
            $cookie_account = \Model_Accounts::forge()->getAccountCookie('admin');
            $account_id = 0;
            if (isset($cookie_account['account_id'])) {
                $account_id = $cookie_account['account_id'];
            }
            unset($cookie_account);
        }
        $output['account_id'] = $account_id;
        
        // check target account
        $account_check_result = $this->checkAccountData($account_id);
        $output['account_check_result'] = (is_object($account_check_result) || is_array($account_check_result) ? true : $account_check_result);
        $output['account_username'] = (is_object($account_check_result) || is_array($account_check_result) ? $account_check_result->account_username : null);
        if ($output['account_check_result'] != true) {
            $output['account_level'] = $account_check_result->account_level;
        }
        
        // set level group for check
        $level_group_check = array();
        if ($output['account_check_result'] != true) {
            foreach ($account_check_result->account_level as $lvl) {
                $level_group_check[] = $lvl->level_group_id;
            }
        }
        $output['level_group_check'] = $level_group_check;
        unset($level_group_check);
        
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
        $output['list_permissions'] = \Model_AccountPermission::fetchPermissionsFile();
        $output['list_permissions_check'] = \Model_AccountPermission::listPermissionChecked($account_id);
        
        // <head> output ----------------------------------------------------------------------------------------------
        $output['page_title'] = $this->generateTitle(\Lang::get('acperm_user_permission'));
        // <head> output ----------------------------------------------------------------------------------------------
        
        // breadcrumb -------------------------------------------------------------------------------------------------
        $page_breadcrumb = [];
        $page_breadcrumb[0] = ['name' => \Lang::get('admin_admin_home'), 'url' => \Uri::create('admin')];
        $page_breadcrumb[1] = ['name' => \Lang::get('acperm_user_permission'), 'url' => \Uri::create('admin/account-permission')];
        $output['page_breadcrumb'] = $page_breadcrumb;
        unset($page_breadcrumb);
        // breadcrumb -------------------------------------------------------------------------------------------------

        return $this->generatePage('admin/templates/accountpermission/index_v', $output, false);
    }// action_index


    public function action_module($account_id = '', $module_system_name = '')
    {
        // clear redirect referrer
        \Session::delete('submitted_redirect');
        
        // check permission
        if (\Model_AccountLevelPermission::checkAdminPermission('acperm_perm', 'acperm_manage_user_perm') == false) {
            \Session::set_flash(
                'form_status',
                array(
                    'form_status' => 'error',
                    'form_status_message' => \Lang::get('admin_permission_denied', array('page' => \Uri::string()))
                )
            );
            \Response::redirect(\Uri::create('admin'));
        }
        
        // load language
        \Lang::load('account');
        
        // if account id not set
        if (!is_numeric($account_id)) {
            $cookie_account = \Model_Accounts::forge()->getAccountCookie('admin');
            $account_id = 0;
            if (isset($cookie_account['account_id'])) {
                $account_id = $cookie_account['account_id'];
            }
            unset($cookie_account);
        }
        $output['account_id'] = $account_id;
        
        // check target account
        $account_check_result = $this->checkAccountData($account_id);
        $output['account_check_result'] = (is_object($account_check_result) || is_array($account_check_result) ? true : $account_check_result);
        $output['account_username'] = (is_object($account_check_result) || is_array($account_check_result) ? $account_check_result->account_username : null);
        $output['account_level'] = $account_check_result->account_level;
        // set level group for check
        $level_group_check = array();
        foreach ($account_check_result->account_level as $lvl) {
            $level_group_check[] = $lvl->level_group_id;
        }
        $output['level_group_check'] = $level_group_check;
        unset($level_group_check);
        
        // check if this module really has permission.
        if (\Library\Modules::forge()->hasPermission($module_system_name) == false) {
            \Response::redirect(\Uri::create('admin/account-permission/index/' . $account_id));
        }
        
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
        
        // list permissions from app/classes/controller (core controllers)
        $output['list_permissions'] = \Library\Modules::forge()->fetchPermissionModule($module_system_name);
        $output['list_permissions_check'] = \Model_AccountPermission::listPermissionChecked($account_id, 0, $module_system_name);
        
        // read module data from file
        $output['module'] = \Library\Modules::forge()->readModuleMetadataFromModuleName($module_system_name);
        
        // <head> output ----------------------------------------------------------------------------------------------
        $output['page_title'] = $this->generateTitle(\Lang::get('acperm_user_permission'));
        // <head> output ----------------------------------------------------------------------------------------------
        
        // breadcrumb -------------------------------------------------------------------------------------------------
        $page_breadcrumb = [];
        $page_breadcrumb[0] = ['name' => \Lang::get('admin_admin_home'), 'url' => \Uri::create('admin')];
        $page_breadcrumb[1] = ['name' => \Lang::get('acperm_user_permission'), 'url' => \Uri::create('admin/account-permission')];
        $page_breadcrumb[2] = ['name' => \Lang::get('acperm_module_permissison'), 'url' => \Uri::main()];
        $output['page_breadcrumb'] = $page_breadcrumb;
        unset($page_breadcrumb);
        // breadcrumb -------------------------------------------------------------------------------------------------

        return $this->generatePage('admin/templates/accountpermission/module_v', $output, false);
    }// action_module


    public function action_reset($account_id = '')
    {
        // set redirect url
        $redirect = $this->getAndSetSubmitRedirection();

        // ajax request only
        if (!\Input::is_ajax()) {
            \Response::redirect($redirect);
        }
        
        // check permission
        if (\Model_AccountLevelPermission::checkAdminPermission('acperm_perm', 'acperm_manage_user_perm') == false) {
            \Session::set_flash(
                'form_status',
                array(
                    'form_status' => 'error',
                    'form_status_message' => \Lang::get('admin_permission_denied', array('page' => \Uri::string()))
                )
            );
            return null;
        }

        // method post only
        if (\Input::method() != 'POST') {
            return null;
        }
        
        // if account id not set
        if (!is_numeric($account_id)) {
            $cookie_account = \Model_Accounts::forge()->getAccountCookie('admin');
            $account_id = 0;
            if (isset($cookie_account['account_id'])) {
                $account_id = $cookie_account['account_id'];
            }
            unset($cookie_account);
        }
        $output['account_id'] = $account_id;
        
        // check target account
        $account_check_result = $this->checkAccountData($account_id);
        $output['account_check_result'] = (is_object($account_check_result) || is_array($account_check_result) ? true : $account_check_result);
        unset($account_check_result);

        if (!\Extension\NoCsrf::check()) {
            $output['result'] = false;
        } else {
            if ($output['account_check_result'] === true) {
                $result = \Model_AccountPermission::resetPermission($account_id);
                $output['result'] = $result;
            } else {
                $output['result'] = false;
            }
        }

        $response = new \Response();
        $response->set_header('Content-Type', 'application/json');
        $response->body(json_encode($output));
        return $response;
    }// action_reset


    public function action_save($account_id = '')
    {
        // set redirect url
        $redirect = $this->getAndSetSubmitRedirection();
        
        // check permission
        if (\Model_AccountLevelPermission::checkAdminPermission('acperm_perm', 'acperm_manage_user_perm') == false) {
            \Session::set_flash(
                'form_status',
                array(
                    'form_status' => 'error',
                    'form_status_message' => \Lang::get('admin_permission_denied', array('page' => \Uri::string()))
                )
            );
            \Response::redirect($redirect);
        }
        
        // if account id not set
        if (!is_numeric($account_id)) {
            $cookie_account = \Model_Accounts::forge()->getAccountCookie('admin');
            $account_id = 0;
            if (isset($cookie_account['account_id'])) {
                $account_id = $cookie_account['account_id'];
            }
            unset($cookie_account);
        }
        $output['account_id'] = $account_id;
        
        // check target account
        $account_check_result = $this->checkAccountData($account_id);
        $output['account_check_result'] = (is_object($account_check_result) || is_array($account_check_result) ? true : $account_check_result);
        unset($account_check_result);
        
        if ($output['account_check_result'] === true) {
            // if form submitted
            if (\Input::method() == 'POST') {
                if (\Extension\NoCsrf::check()) {
                    $data['permission_core'] = (int) trim(\Input::post('permission_core'));
                        if ($data['permission_core'] != '1') {$data['permission_core'] = '0';}
                    $data['module_system_name'] = \Security::strip_tags(trim(\Input::post('module_system_name')));
                        if ($data['module_system_name'] == null || $data['permission_core'] == '1') {$data['module_system_name'] = null;}

                    $data['account_id'] = \Input::post('account_id');
                    $data['permission_page'] = \Input::post('permission_page');
                    $data['permission_action'] = \Input::post('permission_action');

                    \Model_AccountPermission::savePermissions($account_id, $data);
                    
                    // set success message
                    \Session::set_flash(
                        'form_status',
                        array(
                            'form_status' => 'success',
                            'form_status_message' => \Lang::get('admin_saved')
                        )
                    );
                } else {
                    // nocsrf error, set error msg.
                    \Session::set_flash(
                        'form_status',
                        array(
                            'form_status' => 'error',
                            'form_status_message' => \Lang::get('fslang_invalid_csrf_token')
                        )
                    );
                }// endif nocsrf check
            }// endif form submitted
            
            
        } else {
            // failed to check account. set error msg.
            \Session::set_flash(
                'form_status',
                array(
                    'form_status' => 'error',
                    'form_status_message' => $output['account_check_result']
                )
            );
        }// endif check account result.
        
        // go back
        \Response::redirect($redirect);
    }// action_save
    
    
    private function checkAccountData($account_id = '')
    {
        if ($account_id == null) {
            $cookie_account = \Model_Accounts::forge()->getAccountCookie('admin');
            $account_id = 0;
            if (isset($cookie_account['account_id'])) {
                $account_id = $cookie_account['account_id'];
            }
        }
        
        if ($account_id == 0 || !is_numeric($account_id)) {
            return \Lang::get('acperm_account_not_found');
        }
        
        $account = \Model_Accounts::find($account_id);
        
        // if not found account.
        if ($account == null) {
            unset($account);
            return \Lang::get('acperm_account_not_found');
        }
        
        // set level groups for check that this admin can set permission for this user.
        // lower admin level cannot add/edit/delete/change permission for admin that has higher level.
        $level_groups = array();
        foreach ($account->account_level as $lvl) {
            $level_groups[] = $lvl->level_group_id;
        }
        
        if (\Model_Accounts::forge()->canIAddEditAccount($level_groups) == false) {
            \Lang::load('account');
            return \Lang::get('account_you_cannot_edit_account_that_contain_role_higher_than_yours');
        }
        
        return $account;
    }// checkAccountData
    
    
    /**
     * get and set submit redirection url
     * 
     * @return string
     */
    private function getAndSetSubmitRedirection()
    {
        $session = \Session::forge();
        
        if ($session->get('submitted_redirect') == null) {
            if (\Input::referrer() != null && \Input::referrer() != \Uri::main()) {
                $session->set('submitted_redirect', \Input::referrer());
                return \Input::referrer();
            } else {
                $redirect_uri = 'admin/account-permission';
                $session->set('submitted_redirect', $redirect_uri);
                return $redirect_uri;
            }
        } else {
            return $session->get('submitted_redirect');
        }
    }// getAndSetRedirection
	
	
}

