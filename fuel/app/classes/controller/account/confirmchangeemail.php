<?php
/** 
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

class Controller_Account_ConfirmChangeEmail extends \Controller_BaseController 
{
	
	
	public function router($account_id = '', $params = '') 
	{
		$confirm_code = '';
		$action_url = '';
		
		if (isset($params[0]) && $params[0] != null) {
			$confirm_code = $params[0];
		}
		if (isset($params[1]) && $params[1] != null) {
			$action_url = $params[1];
		}
		
		$action = 'action_index';
		return $this->$action($account_id, $confirm_code, $action_url);
	}// router
	
	
	public function action_index($account_id = '', $confirm_code = '', $action = '') 
	{
		// load language
		\Lang::load('account', 'account');
		
		// get config
		$cfg_values = array('member_confirm_wait_time');
		$config = Model_Config::getvalues($cfg_values);
		$output['config'] = $config;
		unset($cfg_values);
		
		// re-modify confirm code by form submitted
		if (\Input::method() == 'POST') {
			$confirm_code = trim(\Input::post('confirm_code'));
		}
		
		$output['change_action'] = $action;
		$output['confirm_code'] = $confirm_code;
		
		// check account id and confirm code.
		$query = \Model_Accounts::query()->where('account_id', $account_id)->where('account_confirm_code', $confirm_code);
		if ($query->count() <= 0) {
			$output['hide_form'] = true;
			
			$output['form_status'] = 'error';
			$output['form_status_message'] = \Lang::get('account.account_invalid_confirm_change_email_code');
		}
		
		// if cancel reset password
		if ($action == 'cancel' && $query->count() > 0) {
			// cancel no need to use form, hide it.
			$output['hide_form'] = true;
			
			// empty confirm code.
			$row = $query->get_one();
			$row->account_new_email = null;
			$row->account_confirm_code = null;
			$row->account_confirm_code_since = null;
			$row->save();

			$output['form_status'] = 'success';
			$output['form_status_message'] = \Lang::get('account.account_your_email_change_was_cancelled');
		}
		
		// form submitted
		if (\Input::method() == 'POST' && $action == 'confirm') {
			$data['account_confirm_code'] = trim(\Input::post('confirm_code'));
			
			// validate form.
			$validate = \Validation::forge();
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
				$row = $query->get_one();
				$cfg_member_confirm_wait_time = $config['member_confirm_wait_time']['value']*60;
				
				if (time()-$row->account_confirm_code_since > $cfg_member_confirm_wait_time) {
					// confirm wait time is too long than limit.
					$output['form_status'] = 'error';
					$output['form_status_message'] = \Lang::get('account.account_change_email_time_expired');
					
					// empty confirm code.
					$row->account_new_email = null;
					$row->account_confirm_code = null;
					$row->account_confirm_code_since = null;
					$row->save();
				} else {
					// confirmed change email. update new email.
					$row->account_email = $row->account_new_email;
					$row->account_new_email = null;
					$row->account_confirm_code = null;
					$row->account_confirm_code_since = null;
					$row->save();
					
					$output['hide_form'] = true;
					$output['form_status'] = 'success';
					$output['form_status_message'] = \Lang::get('account.account_change_email_successfully');
				}
			}
			
			// re-populate form
			$output['confirm_code'] = trim(\Input::post('confirm_code'));
		}
		
		unset($config, $query, $row);
		
		// <head> output ----------------------------------------------------------------------------------------------
		$output['page_title'] = $this->generateTitle(\Lang::get('account.account_confirm_change_email'));
		// <head> output ----------------------------------------------------------------------------------------------
		
		return $this->generatePage('front/templates/account/confirmchangeemail_v', $output, false);
	}// action_index
	
	
}

