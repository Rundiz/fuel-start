<?php
/** 
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

class Controller_Account_ConfirmRegister extends \Controller_BaseController 
{
	
	
	public function router($username = '', $params = '') 
	{
		if (isset($params[0]) && $params[0] != null) {
			$confirm_code = $params[0];
		} else {
			$confirm_code = '';
		}
		
		$action = 'action_index';
		return $this->$action($username, $confirm_code);
	}// router
	
	
	public function action_index($account_username = '', $confirm_code = '') 
	{
		// load language
		\Lang::load('account', 'account');
		
		// store username and confirm code from url to form and require the form to submit.
		$output['account_username'] = $account_username;
		$output['confirm_code'] = $confirm_code;
		
		if (\Input::method() == 'POST') {
			// store data for validate and update account status.
			$data['account_username'] = trim(\Input::post('account_username'));
			$data['account_confirm_code'] = trim(\Input::post('confirm_code'));
			
			// validate form.
			$validate = \Validation::forge();
			$validate->add('account_username', \Lang::get('account.account_username'), array(), array('required'));
			$validate->add('confirm_code', \Lang::get('account.account_confirm_code'), array(), array('required'));
			
			if (!\Extension\NoCsrf::check()) {
				// validate token failed
				$output['form_status'] = 'error';
				$output['form_status_message'] = \Lang::get('fslang.fslang_invalid_csrf_token');
			} elseif (!$validate->run()) {
				// validate failed
				$output['form_status'] = 'error';
				$output['form_status_message'] = $validate->show_errors();
			} else {
				// confirm register.
				$result = \Model_Accounts::confirmRegister($data);
				
				if ($result === true) {
					$output['hide_register_form'] = true;
					$output['form_status'] = 'success';
					$output['form_status_message'] = \Lang::get('account.account_confirm_register_completed');
					
					// @todo [api] confirm register passed should be here.
				} else {
					$output['form_status'] = 'error';
					$output['form_status_message'] = $result;
				}
			}
			
			// re-populate form
			$output['account_username'] = trim(\Input::post('account_username'));
			$output['confirm_code'] = trim(\Input::post('confirm_code'));
		}
		
		// <head> output ----------------------------------------------------------------------------------------------
		$output['page_title'] = $this->generateTitle(\Lang::get('account.account_confirm_register'));
		// <head> output ----------------------------------------------------------------------------------------------
		
		return $this->generatePage('front/templates/account/confirmregister_v', $output, false);
	}// action_index
	
	
}

