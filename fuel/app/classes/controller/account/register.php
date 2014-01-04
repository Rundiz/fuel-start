<?php
/** 
 * Account register
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

class Controller_Account_Register extends \Controller_BaseController 
{
	
	
	public function action_index() 
	{
		// load language
		\Lang::load('account', 'account');
		
		// load config from db.
		$cfg_values = array('member_allow_register', 'member_verification');
		$config = \Model_Config::getvalues($cfg_values);
		$output['config'] = $config;
		unset($cfg_values);
		
		// pre-set form values
		$output['account_username'] = null;
		$output['account_email'] = null;
		$output['account_password'] = null;
		$output['account_confirm_password'] = null;
		$output['captcha'] = null;
		
		if (\Input::method() == 'POST' && $config['member_allow_register']['value'] == '1') {
			// store data to array for send to model with add/register method.
			$data['account_username'] = trim(\Input::post('account_username'));
			$data['account_display_name'] = \Security::htmlentities($data['account_username']);
			$data['account_email'] = \Security::strip_tags(trim(\Input::post('account_email')));
			$data['account_password'] = trim(\Input::post('account_password'));
			
			// validate form.
			$validate = \Validation::forge();
			$validate->add_callable(new \Extension\FsValidate());
			$validate->add('account_username', \Lang::get('account.account_username'), array(), array('required', 'noSpaceBetweenText'));
			$validate->add('account_email', \Lang::get('account.account_email'), array(), array('required', 'valid_email'));
			$validate->add('account_password', \Lang::get('account.account_password'), array(), array('required'));
			$validate->add('account_confirm_password', \Lang::get('account.account_confirm_password'), array(), array('required'))->add_rule('match_field', 'account_password');
			
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
					$continue_register = true;
				}
				
				// if captcha pass
				if (isset($continue_register) && $continue_register === true) {
					// register action
					$result = \Model_Accounts::registerAccount($data);
					
					if ($result === true) {
						$output['hide_register_form'] = true;
						
						// if member verification is need, show those message. if no need, just show success message.
						if ($config['member_verification']['value'] == '0') {
							$output['form_status'] = 'success';
							$output['form_status_message'] = \Lang::get('account.account_registration_complted');
						} elseif ($config['member_verification']['value'] == '1') {
							$output['form_status'] = 'success';
							$output['form_status_message'] = \Lang::get('account.account_registration_completed_need_confirm');
						} elseif ($config['member_verification']['value'] == '2') {
							$output['form_status'] = 'success';
							$output['form_status_message'] = \Lang::get('account.account_registration_completed_need_admin_verify');
						}
					} else {
						$output['form_status'] = 'error';
						$output['form_status_message'] = $result;
					}
				}
			}
			
			// re-populate form
			$output['account_username'] = trim(\Input::post('account_username'));
			$output['account_email'] = trim(\Input::post('account_email'));
			//$output['account_password'] = trim(\Input::post('account_password'));
			//$output['account_confirm_password'] = trim(\Input::post('account_confirm_password'));
			//$output['captcha'] = \Input::post('captcha');
		}
		
		// <head> output ----------------------------------------------------------------------------------------------
		$output['page_title'] = $this->generateTitle(\Lang::get('account.account_register'));
		// <head> output ----------------------------------------------------------------------------------------------
		
		return $this->generatePage('front/templates/account/register_v', $output, false);
	}// action_index
	
	
}

