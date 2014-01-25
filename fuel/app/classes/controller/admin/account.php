<?php
/** 
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

class Controller_Admin_Account extends \Controller_AdminController 
{
	
	
	/**
	 * define permissions for this app/controller.
	 * 
	 * @return array
	 */
	protected function _define_permission() 
	{
		// return array('controller page name' => array('action 1', 'action 2', 'action 3', 'a lot more action. up to you...'));
		return array('account_perm' => array('account_viewusers_perm', 'account_add_perm', 'account_edit_perm', 'account_delete_perm', 'account_viewlogin_log_perm', 'account_deletelogin_log_perm'));
	}// _define_permission
	
	
	public function action_edit($account_id = '') 
	{
		// check permission
		if (\Model_AccountLevelPermission::checkAdminPermission('account_perm', 'account_edit_perm') == false) {
			\Session::set_flash(
				'form_status',
				array(
					'form_status' => 'error',
					'form_status_message' => \Lang::get('admin.admin_permission_denied', array('page' => \Uri::string()))
				)
			);
			\Response::redirect(\Uri::create('admin/account'));
		}
		
		// if editing guest.
		if ($account_id == '0') {
			\Response::redirect(\Uri::create('admin/account'));
		}
		
	}// action_edit
	
	
	public function action_index() 
	{
		// check permission
		if (\Model_AccountLevelPermission::checkAdminPermission('account_perm', 'account_viewusers_perm') == false) {
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
		
		// set sort variable for sortable in views.
		$next_sort = \Security::strip_tags(trim(\Input::get('sort')));
		if ($next_sort == null || $next_sort == 'ASC') {
			$next_sort = 'DESC';
		} else {
			$next_sort = 'ASC';
		}
		$output['next_sort'] = $next_sort;
		unset($next_sort);
		
		// search query
		$output['q'] = trim(\Input::get('q'));
		
		// list accounts --------------------------------------------------------------------------------------------------
		$option['limit'] = \Model_Config::getval('content_admin_items_perpage');
		$option['offset'] = (trim(\Input::get('page')) != null ? ((int)\Input::get('page')-1)*$option['limit'] : 0);
		
		$list_accounts = \Model_Accounts::listAccounts($option);
		
		// pagination config
		$config['pagination_url'] = \Uri::main() . \Uri::getCurrentQuerystrings(true, true, false);
		$config['total_items'] = $list_accounts['total'];
		$config['per_page'] = $option['limit'];
		$config['uri_segment'] = 'page';
		$config['num_links'] = 3;
		$config['show_first'] = true;
		$config['show_last'] = true;
		$config['first-inactive'] = "\n\t\t<li class=\"disabled\">{link}</li>";
		$config['first-inactive-link'] = '<a href="#">{page}</a>';
		$config['first-marker'] = '&laquo;';
		$config['last-inactive'] = "\n\t\t<li class=\"disabled\">{link}</li>";
		$config['last-inactive-link'] = '<a href="#">{page}</a>';
		$config['last-marker'] = '&raquo;';
		$config['previous-marker'] = '&lsaquo;';
		$config['next-marker'] = '&rsaquo;';
		$pagination = \Pagination::forge('viewlogins_pagination', $config);
		
		$output['list_accounts'] = $list_accounts;
		$output['pagination'] = $pagination;
		
		unset($config, $list_accounts, $option, $pagination);
		
		// <head> output ----------------------------------------------------------------------------------------------
		$output['page_title'] = $this->generateTitle(\Lang::get('account.account_accounts'));
		// <head> output ----------------------------------------------------------------------------------------------
		
		return $this->generatePage('admin/templates/account/account_v', $output, false);
	}// action_index
	
	
}

