<?php
/** 
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

class Controller_Account_ResendActivate extends \Controller_BaseController 
{
	
	
	public function action_index() 
	{
		// load language
		\Lang::load('account', 'account');
		
		if (\Input::method() == 'POST') {
			// store data for model
			$data['account_email'] = \Security::strip_tags(trim(\Input::post('account_email')));
			
			// validate form.
			$validate = \Validation::forge();
			$validate->add('account_email', \Lang::get('account.account_email'), array(), array('required', 'valid_email'));
			
			if (!\Extension\NoCsrf::check(null, null, null, null, false)) {
				// validate token failed
				$output['form_status'] = 'error';
				$output['form_status_message'] = \Lang::get('fslang.fslang_invalid_csrf_token');
			} elseif (!$validate->run()) {
				// validate failed
				$output['form_status'] = 'error';
				$output['form_status_message'] = $validate->show_errors();
			} else {
				// check registered emails with not confirm
				$query = \Model_Accounts::query()
						->select('account_id', 'account_username', 'account_email')
						->where('account_email', $data['account_email'])
						->where('account_last_login', null)
						->where('account_status', '0')
						->where('account_confirm_code', '!=', 'NULL');
				
				if ($query->count() <= 0) {
					$output['form_status'] = 'error';
					$output['form_status_message'] = \Lang::get('account.account_didnot_found_entered_email');
				} else {
					$row = $query->get_one();
					
					// generate confirm code
					$data['account_confirm_code'] = \Str::random('alnum', 6);
					$data['account_username'] = $row->account_username;
					
					$options['not_notify_admin'] = true;
					
					// send email to let user confirm registration
					$result = \Model_Accounts::forge()->sendRegisterEmail($data, $options);
					
					if ($result === true) {
						$account = \Model_Accounts::find($row->account_id);
						$account->account_confirm_code = $data['account_confirm_code'];
						$account->save();
						
						$output['form_status'] = 'success';
						$output['form_status_message'] = \Lang::get('account.account_registration_completed_need_confirm');
					} else {
						$output['form_status'] = 'error';
						$output['form_status_message'] = $result;
					}
				}
				
			}
			
			// re-populate form
			$output['account_email'] = trim(\Input::post('account_email'));
		}
		
		// <head> output ----------------------------------------------------------------------------------------------
		$output['page_title'] = $this->generateTitle(\Lang::get('account.account_resend_confirm_registration_email'));
		// <head> output ----------------------------------------------------------------------------------------------
		
		return $this->generatePage('front/templates/account/resendactivate_v', $output, false);
	}// action_index
	
	
}

