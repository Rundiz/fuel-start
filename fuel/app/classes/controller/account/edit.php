<?php
/** 
 * Account edit
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

class Controller_Account_Edit extends \Controller_BaseController 
{
	
	
	public function action_deleteAvatar() 
	{
		// get account id from cookie
		$account = new \Model_Accounts();
		$cookie = $account->getAccountCookie();
		
		if (\Input::method() == 'POST') {
			if (!\Extension\NoCsrf::check()) {
				// validate token failed
				$output['form_status'] = 'error';
				$output['form_status_message'] = \Lang::get('fslang.fslang_invalid_csrf_token');
				$output['result'] = false;
			} else {
				if (!isset($cookie['account_id']) || \Model_Accounts::isMemberLogin() == false) {
					$output['result'] = false;
				} else {
					$output['result'] = true;

					$account->deleteAccountAvatar($cookie['account_id']);
				}
			}
		}
		
		unset($account, $cookie);
		
		if (\Input::is_ajax()) {
			// re-generate csrf token for ajax form to set new csrf.
			$output['csrf_html'] = \Extension\NoCsrf::generate();
			
			$response = new \Response();
			$response->set_header('Content-Type', 'application/json');
			$response->body(json_encode($output));
			return $response;
		} else {
			if (\Input::referrer() != null && \Input::referrer() != \Uri::main()) {
				\Response::redirect(\Input::referrer());
			} else {
				\Response::redirect(\Uri::base());
			}
		}
	}// action_deleteAvatar
	
	
	public function action_index() 
	{
		// load language
		\Lang::load('account', 'account');
		
		// is user logged in?
		if (\Model_Accounts::isMemberLogin() == false) {
			\Response::redirect(\Uri::create('account/login') . '?rdr=' . urlencode(\Uri::main()));
		}
		
		// load config from db.
		$cfg_values = array('allow_avatar', 'avatar_size', 'avatar_allowed_types');
		$config = \Model_Config::getvalues($cfg_values);
		$output['config'] = $config;
		// set config data to display in view file.
		$output['allow_avatar'] = $config['allow_avatar']['value'];
		$output['avatar_size'] = $config['avatar_size']['value'];
		$output['avatar_allowed_types'] = $config['avatar_allowed_types']['value'];
		unset($cfg_values);
		
		// read flash message for display errors. this is REQUIRED if you coding the check login with simultaneous login detection on.
		$form_status = \Session::get_flash('form_status');
		if (isset($form_status['form_status']) && isset($form_status['form_status_message'])) {
			$output['form_status'] = $form_status['form_status'];
			$output['form_status_message'] = $form_status['form_status_message'];
		}
		unset($form_status);
		
		// get account id
		$cookie_account = \Model_Accounts::forge()->getAccountCookie();
		
		// get account data
		$query = \Model_Accounts::query()
				->where('account_id', $cookie_account['account_id'])
				->where('account_username', $cookie_account['account_username'])
				->where('account_email', $cookie_account['account_email']);
		
		if ($query->count() > 0) {
			// found
			$row = $query->get_one();
			
			$output['row'] = $row;
			// loop set data for display in form.
			foreach ($row as $key => $field) {
				$output[$key] = $field;
			}
			
			// get account_fields data of current user and send to views form
			// to access data from view, use $account_field['field_name']. for example: the field_name is phone, just use $account_field['phone'];
			$account_fields = \Model_AccountFields::getData($cookie_account['account_id']);
			if ($account_fields->count() > 0) {
				foreach ($account_fields as $af) {
					$output['account_field'][$af->field_name] = (\Extension\Str::isJsonFormat($af->field_value) ? json_decode($af->field_value, true) : $af->field_value);
				}
			}
			unset($account_fields, $af);
			
			// get timezone list to display.
			\Config::load('timezone', 'timezone');
			$output['timezone_list'] = \Config::get('timezone.timezone', array());
			
			unset($query);
		} else {
			// not found account.
			unset($cookie_account, $query);
			
			\Model_Accounts::logout();
			\Response::redirect(\Uri::create('account/login') . '?rdr=' . urlencode(\Uri::main()));
		}
		
		// if form submitted
		if (\Input::method() == 'POST') {
			// store data for save to db.
			$data['account_id'] = $cookie_account['account_id'];
			$data['account_username'] = $cookie_account['account_username'];//trim(\Input::post('account_username'));//no, do not edit username.
			$data['account_old_email'] = $cookie_account['account_email'];
			$data['account_email'] = \Security::strip_tags(trim(\Input::post('account_email')));
			$data['account_password'] = trim(\Input::post('account_password'));
			$data['account_new_password'] = trim(\Input::post('account_new_password'));
			$data['account_display_name'] = \Security::htmlentities(\Input::post('account_display_name'));
			$data['account_firstname'] = \Security::htmlentities(trim(\Input::post('account_firstname', null)));
				if ($data['account_firstname'] == null) {$data['account_firstname'] = null;}
			$data['account_middlename'] = \Security::htmlentities(trim(\Input::post('account_middlename', null)));
				if ($data['account_middlename'] == null) {$data['account_middlename'] = null;}
			$data['account_lastname'] = \Security::htmlentities(trim(\Input::post('account_lastname', null)));
				if ($data['account_lastname'] == null) {$data['account_lastname'] = null;}
			$data['account_birthdate'] = \Security::strip_tags(trim(\Input::post('account_birthdate', null)));
				if ($data['account_birthdate'] == null) {$data['account_birthdate'] = null;}
			$data['account_signature'] = \Security::htmlentities(trim(\Input::post('account_signature', null)));
				if ($data['account_signature'] == null) {$data['account_signature'] = null;}
			$data['account_timezone'] = \Security::strip_tags(trim(\Input::post('account_timezone')));
			$data['account_language'] = \Security::strip_tags(trim(\Input::post('account_language', null)));
				if ($data['account_language'] == null) {$data['account_language'] = null;}
			
			// store data for account_fields
			$data_field = array();
			if (is_array(\Input::post('account_field'))) {
				foreach (\Input::post('account_field') as $field_name => $field_value) {
					if (is_string($field_name)) {
						if (is_array($field_value)) {
							$field_value = json_encode($field_value);
						}

						$data_field[$field_name] = $field_value;
					}
				}
			}
			unset($field_name, $field_value);
			
			// validate form.
			$validate = \Validation::forge();
			$validate->add_callable(new \Extension\FsValidate());
			//$validate->add('account_username', \Lang::get('account.account_username'), array(), array('required', 'noSpaceBetweenText'));//no, do not edit username.
			$validate->add('account_email', \Lang::get('account.account_email'), array(), array('required', 'valid_email'));
			$validate->add('account_display_name', \Lang::get('account.account_display_name'), array(), array('required'));
			$validate->add('account_birthdate', \Lang::get('account.account_birthdate'))->add_rule('valid_date', 'Y-m-d');
			$validate->add('account_timezone', \Lang::get('account.account_timezone'), array(), array('required'));
			
			if (!\Extension\NoCsrf::check()) {
				// validate token failed
				$output['form_status'] = 'error';
				$output['form_status_message'] = \Lang::get('fslang.fslang_invalid_csrf_token');
			} elseif (!$validate->run()) {
				// validate failed
				$output['form_status'] = 'error';
				$output['form_status_message'] = $validate->show_errors();
			} else {
				// save
				$result = \Model_accounts::memberEditProfile($data, $data_field);
				
				if ($result === true) {
					if (\Session::get_flash('form_status', null, false) == null) {
						\Session::set_flash(
							'form_status',
							array(
								'form_status' => 'success',
								'form_status_message' => \Lang::get('account.account_saved')
							)
						);
					}
					
					\Response::redirect(\Uri::main());
				} else {
					$output['form_status'] = 'error';
					$output['form_status_message'] = $result;
				}
			}
			
			// re-populate form
			//$output['account_username'] = trim(\Input::post('account_username'));//no, do not edit username.
			$output['account_email'] = trim(\Input::post('account_email'));
			$output['account_display_name'] = trim(\Input::post('account_display_name'));
			$output['account_firstname'] = trim(\Input::post('account_firstname'));
			$output['account_middlename'] = trim(\Input::post('account_middlename'));
			$output['account_lastname'] = trim(\Input::post('account_lastname'));
			$output['account_birthdate'] = trim(\Input::post('account_birthdate'));
			$output['account_signature'] = trim(\Input::post('account_signature'));
			$output['account_timezone'] = trim(\Input::post('account_timezone'));
			$output['account_language'] = trim(\Input::post('account_language'));
			
			// re-populate form for account fields
			if (is_array(\Input::post('account_field'))) {
				foreach (\Input::post('account_field') as $field_name => $field_value) {
					if (is_string($field_name)) {
						$output['account_field'][$field_name] = $field_value;
					}
				}
			}
			unset($field_name, $field_value);
		}
		
		// clear variables
		unset($cookie_account, $data, $result);
		
		// <head> output ----------------------------------------------------------------------------------------------
		$output['page_title'] = $this->generateTitle(\Lang::get('account.account_edit'));
		// <head> output ----------------------------------------------------------------------------------------------
		
		return $this->generatePage('front/templates/account/edit_v', $output, false);
	}// action_index
	
	
}

