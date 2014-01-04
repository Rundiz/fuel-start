<?php
/** 
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

class Controller_Account_Forgotpw extends \Controller_BaseController
{
	
	
	public function action_index() 
	{
		// load language
		\Lang::load('account', 'account');
		
		// form submitted
		if (\Input::method() == 'POST') {
			$data['account_email'] = \Security::strip_tags(trim(\Input::post('account_email')));
			
			// validate form.
			$validate = \Validation::forge();
			$validate->add('account_email', \Lang::get('account.account_email'), array(), array('required', 'valid_email'));
			
			if (!\Extension\NoCsrf::check()) {
				// validate token failed
				$output['form_status'] = 'error';
				$output['form_status_message'] = \Lang::get('fslang.fslang_invalid_csrf_token');
			} elseif (!$validate->run()) {
				// validate failed
				$output['form_status'] = 'error';
				$output['form_status_message'] = $validate->show_errors();
			} else {
				// validate pass
				include APPPATH . 'vendor' . DS . 'securimage' . DS . 'securimage.php';
				$securimage = new \Securimage();
				if ($securimage->check(\Input::post('captcha')) == false) {
					$output['form_status'] = 'error';
					$output['form_status_message'] = \Lang::get('account.account_wrong_captcha_code');
				} else {
					$continue_form = true;
				}
				
				if (isset($continue_form) && $continue_form === true) {
					// try to send reset password email
					$result = \Model_Accounts::sendResetPasswordEmail($data);
					
					if ($result === true) {
						$output['hide_form'] = true;
						$output['form_status'] = 'success';
						$output['form_status_message'] = \Lang::get('account.account_please_check_your_email_to_confirm_reset_password');
					} else {
						if (is_string($result)) {
							$output['form_status'] = 'error';
							$output['form_status_message'] = $result;
						}
					}
				}
			}
			
			// re-populate form
			$output['account_email'] = trim(\Input::post('account_email'));
		}
		
		// <head> output ----------------------------------------------------------------------------------------------
		$output['page_title'] = $this->generateTitle(\Lang::get('account.account_forgot_username_or_password'));
		// <head> output ----------------------------------------------------------------------------------------------
		
		return $this->generatePage('front/templates/account/forgotpw_v', $output, false);
	}// action_index
	
	
}

