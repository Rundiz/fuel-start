<?php
/** 
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

class Controller_Account_ViewLogins extends \Controller_BaseController 
{
	
	
	public function action_index() 
	{
		// is user logged in?
		if (\Model_Accounts::isMemberLogin() == false) {
			\Response::redirect(\Uri::create('account/login') . '?rdr=' . urlencode(\Uri::main()));
		}
		
		// load language
		\Lang::load('account', 'account');
		\Lang::load('accountlogins', 'accountlogins');
		
		// get account id
		$cookie_account = \Model_Accounts::forge()->getAccountCookie();
		
		// get account data
		$row = \Model_Accounts::find($cookie_account['account_id']);
		
		if ($row == null) {
			// not found user data.
			unset($row);
			
			\Response::redirect(\Uri::main());
		}
		
		$output['account'] = $row;
		
		// set sort variable for sortable in views.
		$next_sort = \Security::strip_tags(trim(\Input::get('sort')));
		if ($next_sort == null || $next_sort == 'DESC') {
			$next_sort = 'ASC';
		} else {
			$next_sort = 'DESC';
		}
		$output['next_sort'] = $next_sort;
		unset($next_sort);
		
		// list logins -----------------------------------------------------------------------------------------------------
		$option['limit'] = \Model_Config::getval('content_items_perpage');
		$option['offset'] = (trim(\Input::get('page')) != null ? ((int)\Input::get('page')-1)*$option['limit'] : 0);
		
		$list_logins = \Model_AccountLogins::listLogins(array('account_id' => $cookie_account['account_id']), $option);
		
		// pagination config
		$config['pagination_url'] = \Uri::main() . \Uri::getCurrentQuerystrings(true, true, false);
		$config['total_items'] = $list_logins['total'];
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
		
		$output['list_logins'] = $list_logins;
		$output['pagination'] = $pagination;
		
		unset($config, $list_logins, $option, $pagination);
		
		// <head> output ----------------------------------------------------------------------------------------------
		$output['page_title'] = $this->generateTitle(\Lang::get('account.account_login_history'));
		// <head> output ----------------------------------------------------------------------------------------------
		
		return $this->generatePage('front/templates/account/viewlogins_v', $output, false);
	}// action_index
	
	
}

