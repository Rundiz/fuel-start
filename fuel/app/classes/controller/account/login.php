<?php
/** 
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

class Controller_Account_Login extends \Controller_BaseController 
{
	
	
	public $login_fail_time_show_captcha = 3;
	
	
	public function action_index() 
	{
		// load language
		\Lang::load('account', 'account');
		
		// load config from db.
		$cfg_values = array('member_max_login_fail', 'member_login_fail_wait_time');
		$config = Model_Config::getvalues($cfg_values);
		$output['config'] = $config;
		unset($cfg_values);
		
		// set login redirect referer.
		if (\Input::referrer() != null && \Input::referrer() != \Uri::main()) {
			$output['go_to'] = urlencode(\Input::referrer());
		}
		if (\Input::get('rdr') != null) {
			$output['go_to'] = urlencode(\Input::get('rdr'));
		}
		
		// read flash message for display errors. this is REQUIRED if you coding the check login with simultaneous login detection on.
		// this is REQUIRED in login page. because failed 'is login' check will redirect to here.
		$form_status = \Session::get_flash('form_status');
		if (isset($form_status['form_status']) && isset($form_status['form_status_message'])) {
			$output['form_status'] = $form_status['form_status'];
			$output['form_status_message'] = $form_status['form_status_message'];
		}
		unset($form_status);
		
		// count login fail and show captcha.
		if (\Session::get('login_all_fail_count', '0') >= $this->login_fail_time_show_captcha || \Session::get('show_captcha', false) === true) {
			$output['show_captcha'] = true;
			
			// if last time login failed is over wait time, reset it
			if ((time()-\Session::get('login_all_fail_time', time()))/60 > $config['member_login_fail_wait_time']['value']) {
				// reset captcha requirement and wait time.
				\Session::set('login_all_fail_count', (\Session::get('login_all_fail_count')-($this->login_fail_time_show_captcha+1)));// do not reset this, just reduce to fail time show captcha+1. doing this to prevent brute force attack.
				\Session::delete('login_all_fail_time');
				\Session::delete('show_captcha');
			}
		}
		
		// if form submitted --------------------------------------------------------------------------------------------
		if (\Input::method() == 'POST') {
			// store data for login
			$data['account_identity'] = trim(\Input::post('account_identity'));
			if (strpos($data['account_identity'], '@') === false) {
				$data['account_username'] = $data['account_identity'];
			} else {
				$data['account_email'] = $data['account_identity'];
			}
			$data['account_password'] = trim(\Input::post('account_password'));
			
			// validate form.
			$validate = \Validation::forge();
			// check username or email required
			$validate->add('account_identity', \Lang::get('account.account_username_or_email'), array(), array('required'));
			$validate->add('account_password', \Lang::get('account.account_password'), array(), array('required'));
			
			if (!\Extension\NoCsrf::check()) {
				// validate token failed
				$output['form_status'] = 'error';
				$output['form_status_message'] = \Lang::get('fslang.fslang_invalid_csrf_token');
			} elseif (!$validate->run()) {
				// validate failed
				$output['form_status'] = 'error';
				$output['form_status_message'] = $validate->show_errors();
				
				// if ajax request, return error message.
				if (\Input::is_ajax()) {
					unset($output['config']);
					
					$response = new \Response();
					$response->set_header('Content-Type', 'application/json');
					$response->body(json_encode($output));
					return $response;
				}
			} else {
				// logout to clear old cookie and login session before try login
				\Model_Accounts::logout();
				
				// count login failed and wait if it was exceed max failed allowed.
				if (
					\Session::get('login_all_fail_count', '0') > $config['member_max_login_fail']['value'] && 
					(time()-\Session::get('login_all_fail_time', time()))/60 <= $config['member_login_fail_wait_time']['value']
				) {
					// continuous login failed over max fail limit.
					$result = Lang::get('account.account_login_failed_too_many', array('wait_minute' => $config['member_login_fail_wait_time']['value'], 'wait_til_time' => date('d F Y H:i:s', time()+($config['member_login_fail_wait_time']['value']*60))));
				} else {
					// not reach maximum limit
					// check if show captcha
					if (isset($output['show_captcha']) && $output['show_captcha'] === true) {
						include APPPATH . 'vendor' . DS . 'securimage' . DS . 'securimage.php';
						$securimage = new \Securimage();
						if ($securimage->check(\Input::post('captcha')) == false) {
							$result = \Lang::get('account.account_wrong_captcha_code');
						}
					}
					
					// try to login. ---------------------------------------------
					if (!isset($result) || (isset($result) && $result == null)) {
						$result = \Model_Accounts::memberLogin($data);
					}
				}
				
				// check login result ----------------------------------------------
				if ($result === true) {
					// success
					$all_fail_count = 0;
					\Session::delete('login_all_fail_count');
					\Session::delete('login_all_fail_time');
					\Session::delete('show_captcha');
					
					if (\Input::is_ajax()) {
						unset($output['config']);

						$output['login_status'] = true;
						$output['form_status'] = 'success';
						$output['form_status_message'] = \Lang::get('account.account_login_success');
						
						if (!isset($output['go_to'])) {
							$output['go_to'] = \Uri::base();
						} else {
							$output['go_to'] = urldecode($output['go_to']);
						}
						
						$response = new \Response();
						$response->set_header('Content-Type', 'application/json');
						$response->body(json_encode($output));
						return $response;
					} else {
						if (isset($output['go_to'])) {
							\Response::redirect(urldecode($output['go_to']));
						} else {
							\Response::redirect(\Uri::base());
						}
					}
				} else {
					// failed
					$all_fail_count = (\Session::get('login_all_fail_count', '0')+1);
					\Session::set('login_all_fail_count', $all_fail_count);
					\Session::set('login_all_fail_time', time());
					
					// if login fail count more than or equal to fail time show captcha
					if ($all_fail_count >= ($this->login_fail_time_show_captcha)) {
						$output['show_captcha'] = true;
						\Session::set('show_captcha', true);
					}
					
					$output['form_status'] = 'error';
					$output['form_status_message'] = $result;
					
					if (\Input::is_ajax()) {
						unset($output['config']);

						$response = new \Response();
						$response->set_header('Content-Type', 'application/json');
						$response->body(json_encode($output));
						return $response;
					}
				}
			}
			
			// re-populate form
			$output['account_identity'] = $data['account_identity'];
		}
		
		// <head> output ----------------------------------------------------------------------------------------------
		$output['page_title'] = $this->generateTitle(\Lang::get('account.account_login'));
		// <head> output ----------------------------------------------------------------------------------------------
		
		return $this->generatePage('front/templates/account/login_v', $output, false);
	}// action_index
	
	
}

