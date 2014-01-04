<?php
/**
 * accounts ORM and reusable function
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

class Model_Accounts extends \Orm\Model 
{


	protected static $_table_name = 'accounts';
	protected static $_primary_key = array('account_id');
	
	// relations
	protected static $_has_many = array(
		'account_fields' => array(
			'model_to' => 'Model_AccountFields',
			'key_from' => 'account_id',
			'key_to' => 'account_id',
			'cascade_delete' => true,
		),
		'account_level' => array(
			'key_from' => 'account_id',
			'model_to' => 'Model_AccountLevel',
			'key_to' => 'account_id',
			'cascade_save' => true,
			'cascade_delete' => true,
		),
		'account_logins' => array(
			'key_from' => 'account_id',
			'model_to' => 'Model_AccountLogins',
			'key_to' => 'account_id',
			'cascade_delete' => true,
		),
		'account_sites' => array(
			'key_from' => 'account_id',
			'model_to' => 'Model_AccountSites',
			'key_to' => 'account_id',
			'cascade_delete' => true,
		),
	);
	
	
	protected $password_hash_level = 12;
	
	
	/**
	 * check account is logged in correctly and status is enabled. also call to check simultaneous login.
	 * 
	 * @param intger $account_id
	 * @param string $account_username
	 * @param string $account_email
	 * @param string $account_online_code
	 * @return boolean
	 */
	public function checkAccount($account_id = '', $account_username = '', $account_email = '', $account_online_code = '') 
	{
		// check all required data
		if ($account_id == null || $account_username == null || $account_email == null || $account_online_code == null) {
			return false;
		}
		
		// @todo write code to get site id for multisite coding
		$site_id = 1;
		
		// check for matches id username and email. ---------------------------------------------------------------
		$query = self::query()
				->where('account_id', $account_id)
				->where('account_username', $account_username)
				->where('account_email', $account_email)
				->where('account_status', 1);
		
		if ($query->count() > 0) {
			$row = $query->get_one();
			unset($query);
			
			// if not allow simultaneous login. (if not allow login from many places)
			if (\Model_Config::getval('simultaneous_login') == '0') {
				if ($this->isSimultaneousLogin($account_id, $account_online_code) == true) {
					unset($row);
					
					// log out
					static::logout(array('remove_online_code' => false));
					
					// set error message.
					\Session::set_flash(
						'form_status',
						array(
							'form_status' => 'error',
							'form_status_message' => \Lang::get('account.account_simultaneous_login_detected')
						)
					);
					
					return false;
				}
			}
			
			// check pass with or without simultaneous login check.
			unset($row);
			
			return true;
		}
		
		// not found account in db. or found but disabled
		unset($query);
		
		// log out
		static::logout();
		
		return false;
	}// checkAccount
	
	
	/**
	 * check password
	 * 
	 * @param string $entered_password
	 * @param string $hashed_password
	 * @return boolean
	 */
	public function checkPassword($entered_password = '', $hashed_password = '') 
	{
		// @todo any hash api for check password should be here.
		
		include_once APPPATH . DS . 'vendor' . DS . 'phpass' . DS . 'PasswordHash.php';
		$PasswordHash = new PasswordHash($this->password_hash_level, false);
		return $PasswordHash->CheckPassword($entered_password, $hashed_password);
	}// checkPassword
	
	
	/**
	 * confirm register
	 * 
	 * @param array $data
	 * @return boolean|string if passed return true. if failed return error text.
	 */
	public static function confirmRegister(array $data = array()) 
	{
		// check username and confirm code.
		$query = self::query()
				->where('account_username', $data['account_username'])
				->where('account_confirm_code', $data['account_confirm_code'])
				->where('account_status', '0')// newly registered user has status 0
				->where('account_last_login', null);// newly registered user never login
		if ($query->count() <= 0) {
			// not found.
			unset($query);
			return \Lang::get('account.account_your_confirm_register_code_is_invalid');
		} else {
			$row = $query->get_one();
			unset($query);
		}
		
		// found user with this confirm code. update account status to 1 to allow login.
		$account = self::find($row->account_id);
		$account->account_confirm_code = null;
		$account->account_status = 1;
		$account->account_status_text = null;
		$account->save();
		
		unset($account, $row);
		
		return true;
	}// confirmRegister
	
	
	/**
	 * count continueous login fail
	 * 
	 * @deprecated not use anymore
	 * @param array $data
	 * @return int|boolean return false on failed to get data, number for countable data
	 */
	/*public static function countContinuousLoginFail($data = array()) 
	{
		if (!isset($data['account_username']) && !isset($data['account_email'])) {
			// nothing set, return false.
			return false;
		} else {
			if (!isset($data['account_username'])) {
				$data['account_username'] = null;
			}
			if (!isset($data['account_email'])) {
				$data['account_email'] = null;
			}
		}
		
		// get account_id from username or email
		$query = self::query()
				->where('account_username', $data['account_username'])
				->or_where('account_email', $data['account_email']);
		
		if ($query->count() <= 0) {
			unset($query);
			// not found account, return false.
			return false;
		} else {
			$row = $query->get_one();
			$account_id = $row->account_id;
			unset($query, $row);
		}
		
		// get account logins data
		$query = \Model_AccountLogins::find('all', 
			array(
				'where' => array(array('account_id', $account_id)),
				'order_by' => array('account_login_id' => 'DESC')
			)
		);
		
		if (is_object($query) || is_array($query)) {
			$i = 0;
			foreach ($query as $row) {
				if ($row->login_attempt == '1') {
					unset($query, $row);
					return $i;
				}
				$i++;
			}
			
			unset($query, $row);
			return $i;
		}
		
		unset($query);
		return (int) 0;
	}// countContinuousLoginFail*/ // not use anymore
	
	
	/**
	 * get account cookie
	 * 
	 * @param string $level
	 * @return array|null
	 */
	public function getAccountCookie($level = 'member') 
	{
		if ($level != 'admin' && $level != 'member') {
			$level = 'member';
		}
		
		$cookie_account = \Security::xss_clean(\Extension\Cookie::get($level . '_account'));
		
		if ($cookie_account != null) {
			$cookie_account = \Crypt::decode($cookie_account);
			$cookie_account = unserialize($cookie_account);
		}
		
		return $cookie_account;
	}// getAccountCookie
	
	
	/**
	 * get login fail last time
	 * 
	 * @deprecated not use anymore
	 * @param array $data
	 * @return mixed return false on failed, true on success (not found last login, found but success), timestamp on found last failed login.
	 */
	/*public static function getLoginFailLastTime($data = array()) 
	{
		if (!isset($data['account_username']) && !isset($data['account_email'])) {
			// nothing set, return false.
			return false;
		} else {
			if (!isset($data['account_username'])) {
				$data['account_username'] = null;
			}
			if (!isset($data['account_email'])) {
				$data['account_email'] = null;
			}
		}
		
		// get account_id from username or email
		$query = self::query()
				->where('account_username', $data['account_username'])
				->or_where('account_email', $data['account_email']);
		
		if ($query->count() <= 0) {
			unset($query);
			// not found account, return false.
			return false;
		} else {
			$row = $query->get_one();
			$account_id = $row->account_id;
			unset($query, $row);
		}
		
		// get account login failed last time
		$account = \Model_AccountLogins::query()->where('account_id', $account_id)->order_by('account_login_id', 'DESC')->get_one();
		
		if (is_object($account)) {
			if ($account->login_attempt == '0') {
				// found last login and it was failed. return date/time in timestamp (data in db is timestamp)
				return $account->login_time;
			}
		}
		
		// not found last login (maybe never login before), found but succeed.
		unset($account);
		return true;
	}// getLoginFailLastTime*/ // not use anymore
	
	
	/**
	 * hash password
	 * @param string $password
	 * @return string
	 */
	public function hashPassword($password = '') 
	{
		// @todo any hash password api should be here with if condition.
		
		include_once APPPATH . DS . 'vendor' . DS . 'phpass' . DS . 'PasswordHash.php';
		$PasswordHash = new PasswordHash($this->password_hash_level, false);
		return $PasswordHash->HashPassword($password);
	}// hashPassword
	
	
	/**
	 * create instance to call non-static method from static method.
	 * @return \Model_Accounts
	 */
	private static function instance() 
	{
		return new Model_Accounts();
	}// instance
	
	
	/**
	 * is member logged in?
	 * 
	 * @return boolean return true for logged in. return false for not logged in.
	 */
	public static function isMemberLogin() 
	{
		$cookie_account = self::instance()->getAccountCookie();
		
		if (!isset($cookie_account['account_id']) || !isset($cookie_account['account_username']) || !isset($cookie_account['account_email']) || !isset($cookie_account['account_display_name']) || !isset($cookie_account['account_online_code'])) {
			return false;
		}
		
		return self::instance()->checkAccount($cookie_account['account_id'], $cookie_account['account_username'], $cookie_account['account_email'], $cookie_account['account_online_code']);
	}// isMemberLogin
	
	
	/**
	 * is simultaneous login (login from multiple places)
	 * 
	 * @param integer $account_id
	 * @param string $account_online_code
	 * @param integer $site_id site id for multisite code.
	 * @return boolean return true if detected simultaneous login, false if logged in only one place.
	 */
	private function isSimultaneousLogin($account_id = '', $account_online_code = '', $site_id = '') 
	{
		// check required data
		if (!is_numeric($account_id) || $account_online_code == null) {
			return true;
		}
		
		if ($site_id == null) {
			// @todo write code to get current site id for multisite coding.
			$site_id = 1;
		}
		
		// find this account id and their online code on selected site.
		$query = \Model_AccountSites::query()
				->where('account_id', $account_id)
				->where('site_id', $site_id)
				->where('account_online_code', $account_online_code);
		
		if ($query->count() > 0) {
			unset($query);
			
			// not found logged in from other place (online code in db matched with this user's cookie online code).
			return false;
		}
		
		// not found account on this site. or found but online code does not match (null online code in db means logged out, so it is not match this user that still logged in).
		unset($query);
		
		return true;
	}// isSimultaneousLogin
	
	
	/**
	 * logout
	 * 
	 * @param array $data
	 * @return boolean
	 */
	public static function logout($data = array()) 
	{
		if (!isset($data['site_id']) || (isset($data['site_id']) && $data['site_id'] == null)) {
			// @todo add code to get current site id for multisite code.
			$data['site_id'] = 1;
		}
		
		// get account id if not set
		if (!isset($data['account_id']) || (isset($data['account_id']) && !is_numeric($data['account_id']))) {
			$cookie = self::instance()->getAccountCookie();
			
			if (isset($cookie['account_id'])) {
				$data['account_id'] = $cookie['account_id'];
			} else {
				$data['account_id'] = 0;
			}
		}
		
		\Extension\Cookie::delete('member_account');
		\Extension\Cookie::delete('admin_account');
		
		if (!isset($data['remove_online_code']) || (isset($data['remove_online_code']) && $data['remove_online_code'] == true)) {
			// delete online code for certain site, so when program check for logged in or simultaneous it will return false.
			$account_sites = \Model_AccountSites::query()->where('account_id', $data['account_id'])->where('site_id', $data['site_id']);
			$row = $account_sites->get_one();
			$row->account_online_code = null;
			$row->save();

			unset($account_sites, $row);
		}
		
		return true;
	}// logout
	
	
	public static function memberEditProfile(array $data = array(), array $data_field = array()) 
	{
		if (empty($data)) {
			return false;
		}
		
		// check email change?
		if ($data['account_old_email'] == $data['account_email']) {
			$email_change = false;
		} else {
			$email_change = true;
			
			//check for already in use email
			$query = self::query()->where('account_id', '!=', $data['account_id'])->where('account_email', $data['account_email']);
			if ($query->count() > 0) {
				// found email already in use.
				unset($email_change, $query);
				
				return \Lang::get('account.account_email_already_exists');
			} else {
				$data['account_new_email'] = $data['account_email'];
			}
			unset($query);
		}
		
		// if email change, send confirm link to old email
		if ($email_change === true) {
			$data['confirm_code'] = Extension\Str::random('alnum', 5);
			$data['confirm_code_since'] = time();
			$send_email_change_confirmation = self::instance()->sendEmailChangeConfirmation($data);
			
			if ($send_email_change_confirmation === true) {
				$data['account_confirm_code'] = $data['confirm_code'];
				$data['account_confirm_code_since'] = $data['confirm_code_since'];
			} else {
				return $send_email_change_confirmation;
			}
			
			unset($data['confirm_code'], $data['confirm_code_since'], $send_email_change_confirmation);
		}
		unset($email_change);
		
		// check avatar upload
		if (\Model_Config::getval('allow_avatar') == '1' && (isset($_FILES['account_avatar']['name']) && $_FILES['account_avatar']['name'] != null)) {
			// @todo need to upload avatar code here.
			echo 'do upload coding <br>';
			echo __FILE__ . ' line: ' . __LINE__;
			exit;
		}
		
		// check password change and set new password data for update in db.
		if (!empty($data['account_password'])) {
			// there is current password input.
			if ($data['account_new_password'] != null) {
				// check current password match in db.
				$query = self::query()->where('account_id', $data['account_id'])->where('account_username', $data['account_username']);
				if ($query->count() > 0) {
					$row = $query->get_one();
					
					if (self::instance()->checkPassword($data['account_password'], $row->account_password)) {
						$data['account_password'] = self::instance()->hashPassword($data['account_new_password']);
						
						unset($query, $row);
						
						// @todo api for changed password here.
						
						// flash message for changed password please login again.
						\Session::set_flash(
							'form_status',
							array(
								'form_status' => 'success',
								'form_status_message' => \Lang::get('account.account_your_password_changed_please_login_again')
							)
						);
						
						$password_changed = true;
					} else {
						unset($query, $row);
						
						return \Lang::get('account.account_wrong_password');
					}
				} else {
					unset($query);
					
					return \Lang::get('account.account_not_found_account_in_db');
				}
			} else {
				return \Lang::get('account.account_please_enter_your_new_password');
			}
		} else {
			// no password change
			// remove password data to prevent db update password field to null
			unset($data['account_password']);
		}
		
		// remove un-necessary variables to prevent error when update db.
		unset($data['account_old_email'], $data['account_email'], $data['account_new_password']);
		
		// update to db.
		$datasave = $data;
		unset($datasave['account_id']);
		
		$accounts = self::find($data['account_id']);
		$accounts->set($datasave);
		$accounts->save();
		unset($datasave);
		
		// update account fields
		$af = new \Model_AccountFields();
		$af->updateAccountFields($data['account_id'], $data_field);
		unset($af);
		
		// @todo api edit account should be here.
		
		// done
		if (isset($password_changed) && $password_changed === true) {
			self::logout();
		}
		
		return true;
	}// memberEditProfile
	
	
	/**
	 * member login.
	 * 
	 * @param array $data
	 * @return mixed return true on success, return error message on failed.
	 */
	public static function memberLogin($data = array()) 
	{
		if (!isset($data['account_password']) || (!isset($data['account_username']) && !isset($data['account_email']))) {
			return false;
		} else {
			if (!isset($data['account_username'])) {
				$data['account_username'] = null;
			}
			if (!isset($data['account_email'])) {
				$data['account_email'] = null;
			}
		}
		
		$query = self::query()
				->where('account_username', $data['account_username'])
				->or_where('account_email', $data['account_email']);
		
		if ($query->count() > 0) {
			// found
			$row = $query->get_one();
			
			// check enabled account.
			if ($row->account_status == '1') {
				// enabled
				// check password
				if (self::instance()->checkPassword($data['account_password'], $row->account_password) === true) {
					// check password passed
					// generate session id for check simultaneous login
					$session_id = \Session::key('session_id');
					
					// if login set to remember, set expires.
					if (\Input::post('remember') == 'yes') {
						$expires = (\Model_Config::getval('member_login_remember_length')*24*60*60);
					} else {
						$expires = 0;
					}
					
					// set cookie
					$cookie_account['account_id'] = $row->account_id;
					$cookie_account['account_username'] = $row->account_username;
					$cookie_account['account_email'] = $row->account_email;
					$cookie_account['account_display_name'] = $row->account_display_name;
					$cookie_account['account_online_code'] = $session_id;
					$cookie_account = \Crypt::encode(serialize($cookie_account));
					Extension\Cookie::set('member_account', $cookie_account, $expires);
					unset($cookie_account, $expires);
					
					// update last login in accounts table
					$accounts = self::find($row->account_id);
					$accounts->account_last_login = time();
					$accounts->account_last_login_gmt = \Extension\Date::localToGmt();
					$accounts->save();
					unset($accounts);
					
					// add/update last login session.
					$account_session['account_id'] = $row->account_id;
					$account_session['session_id'] = $session_id;
					
					$account_site = new \Model_AccountSites();
					$account_site->addLoginSession($account_session);
					unset($account_session);
					
					// record login
					$account_logins = new Model_AccountLogins();
					$account_logins->recordLogin($row->account_id, 1, 'account_login_success');
					
					// @todo any login api should be here.
					
					unset($query, $row, $session_id);
					
					// login success
					return true;
				} else {
					// check password failed, wrong password
					$account_logins = new Model_AccountLogins();
					$account_logins->recordLogin($row->account_id, 0, 'account_wrong_username_or_password');
					
					unset($query, $row);
					
					return \Lang::get('account.account_wrong_username_or_password');
				}
			} else {
				// account disabled
				$account_logins = new Model_AccountLogins();
				$account_logins->recordLogin($row->account_id, 0, 'account_was_disabled');
				
				unset($query);
				
				return \Lang::get('account.account_was_disabled') . ' : ' . $row->account_status_text;
			}
		}
		
		// not found account. login failed
		unset($query);
		
		return \Lang::get('account.account_wrong_username_or_password');
	}// memberLogin
	
	
	/**
	 * register new account
	 * 
	 * @param array $data
	 * @param array $data_fields additional fields to store in account_fields table.
	 * @return boolean|string return true when completed and return error text when error occured.
	 */
	public static function registerAccount($data = array(), $data_fields = array()) 
	{
		// check required data.
		if (empty($data) || !is_array($data)) {return false;}
		
		// get configurations db
		$cfg = \Model_Config::getvalues(array('member_verification', 'member_disallow_username'));
		
		// verify disallow username.
		if (isset($cfg['member_disallow_username']['value'])) {
			$cfg['member_disallow_username']['value'] = str_replace(', ', ',', $cfg['member_disallow_username']['value']);
			$disallow_usernames = explode(',', $cfg['member_disallow_username']['value']);
			foreach ($disallow_usernames as $disallow_username) {
				if ($data['account_username'] == trim($disallow_username)) {
					unset($cfg, $disallow_username, $disallow_usernames);
					return \Lang::get('account.account_username_disallowed');
				}
			}
		}
		
		// check duplicate username.
		$query = self::query()->select('account_username')->where('account_username', $data['account_username']);
		if ($query->count() > 0) {
			unset($query);
			return \Lang::get('account.account_username_already_exists');
		}
		unset($query);
		
		// check duplicate email.
		$query = self::query()->select('account_email')->where('account_email', $data['account_email']);
		if ($query->count() > 0) {
			unset($query);
			return \Lang::get('account.account_email_already_exists');
		}
		unset($query);
		
		if ($cfg['member_verification']['value'] != '0') {
			// admin config need to verify.
			// generate confirm code
			$data['account_confirm_code'] = \Str::random('alnum', 6);
		}
		
		// send register email
		$send_result = self::instance()->sendRegisterEmail($data);
		if ($send_result !== true) {
			return $send_result;
		}
		unset($send_result);
		
		$data['account_password'] = self::instance()->hashPassword($data['account_password']);
		$data['account_create'] = time();
		$data['account_create_gmt'] = \Extension\Date::localToGmt();
		if ($cfg['member_verification']['value'] == '0') {
			// admin config to no need to verify.
			$data['account_status'] = '1';
		} else {
			$data['account_status'] = '0';
			if ($cfg['member_verification']['value'] == '2') {
				$data['account_status_text'] = \Lang::get('account.account_waiting_for_admin_verification');
			} else {
				$data['account_status_text'] = \Lang::get('account.account_please_confirm_registration_from_your_email');
			}
		}
		
		// add account to db. ----------------------------------------
		//list($account_id) = \DB::insert('accounts')->set($data); // query builder style.
		$account = self::forge($data);
		
		// add level to user by use single site table structure.
		$account->account_level[0] = new Model_AccountLevel();
		$account->account_level[0]->level_group_id = 3;
		
		$account->save();
		$account_id = $account->account_id;
		unset($account);
		// end add account to db -------------------------------------
		
		// add level to user.
		// @todo for multi site with table site id prefix, you need to modify and loop those [site id]_account_level to add level to user.
		/*
		//foreach ($list_site['items'] as $site) {
		$account_level = new \Model_AccountLevel;
		$account_level->level_group_id = 3;
		$account_level->account_id = $account_id;
		$account_level->save();
		unset($account_level);
		// }
		 */
		
		// add account fields if there is any value.
		// to add account fields data structure shoud be like this...
		// array(array('field_name' => 'website', 'field_value' => 'http://domain.tld'), array('field_name' => 'fb', 'field_value' => 'http://fb.com/myprofile'));
		// or
		// $af[0]['field_name'] = 'website';
		// $af[0]['field_value'] = 'http://domain.tld';
		// $sf[1]['field_name'] = 'fb';
		// $sf[1]['field_value'] = 'http://fb.com/myprofile';
		if (!empty($data_fields) && is_array($data_fields)) {
			foreach ($data_fields as $field) {
				$account_fields = self::forge($field);
				$account_fields->account_id = $account_id;
				$account_fields->save();
			}
			unset($account_fields, $field);
		}
		
		// @todo register account api should be here.
		
		return true;
	}// registerAccount.
	
	
	/**
	 * send email change confirmation for require user to confirm changed action.
	 * 
	 * @param array $data
	 * @return mixed
	 */
	public function sendEmailChangeConfirmation(array $data = array()) 
	{
		if (!isset($data['confirm_code']) || !isset($data['confirm_code_since']) || !isset($data['account_username']) || !isset($data['account_email'])) {
			return false;
		}
		
		$cfg_member_confirm_wait_time = \Model_Config::getval('member_confirm_wait_time')*60;
		
		// email content
		$email_content = \Extension\EmailTemplate::readTemplate('email_change1.html');
		$email_content = str_replace("%username%", \Security::htmlentities($data['account_username']), $email_content);
		$email_content = str_replace("%newemail%", \Security::htmlentities($data['account_email']), $email_content);
		$email_content = str_replace("%link_confirm%", \Uri::create('account/confirm-change-email/' . $data['account_id'] . '/' . $data['confirm_code'] . '/confirm'), $email_content);
		$email_content = str_replace("%link_cancel%", \Uri::create('account/confirm-change-email/' . $data['account_id'] . '/' . $data['confirm_code'] . '/cancel'), $email_content);
		$email_content = str_replace("%confirm_until%", date('d F Y H:i:s', (time()+$cfg_member_confirm_wait_time)), $email_content);
		
		\Package::load('email');
		$config = \Extension\Email::getConfig();
		$email = \Email::forge($config);
		$email->from(\Model_Config::getval('mail_sender_email'));
		$email->to($data['account_old_email']);
		$email->subject(\Lang::get('account.account_please_confirm_change_email'));
		$email->html_body($email_content);
		$email->alt_body(str_replace("\t", '', strip_tags($email_content)));
		if ($email->send() == false) {
			unset($cfg_member_confirm_wait_time, $config, $email, $email_content);
			return \Lang::get('account.account_email_could_not_send');
		}

		unset($cfg_member_confirm_wait_time, $config, $email, $email_content);
		
		return true;
	}// sendEmailChangeConfirmation
	
	
	/**
	 * send register email
	 * @param array $data
	 * @return boolean|string return true when send register email was done and return error text when error occured.
	 */
	public function sendRegisterEmail($data = array(), $options = array()) 
	{
		if (!isset($data['account_username']) || !isset($data['account_email']) || !isset($data['account_confirm_code'])) {return false;}
		
		$cfg = \Model_Config::getvalues(array('member_verification', 'mail_sender_email', 'member_register_notify_admin', 'member_admin_verify_emails'));
		
		// email content
		$member_verification = $cfg['member_verification']['value'];
		if ($member_verification == '0') {
			// not verify.
			$not_verify_register = true;
		} elseif ($member_verification == '1') {
			// verify by email. (user verify)
			$email_content = \Extension\EmailTemplate::readTemplate('register_user_verify_account.html');
		} elseif ($member_verification == '2') {
			// verify by admin. (admin allow or not)
			$email_content = \Extension\EmailTemplate::readTemplate('register_admin_verify_account.html');
		}
		
		// modify email content for ready to send.
		if (isset($email_content) && $email_content != null) {
			$email_content = str_replace("%username%", \Security::htmlentities($data['account_username']), $email_content);
			$email_content = str_replace('%register_confirm_link%', \Uri::create('account/confirm-register/'.urlencode($data['account_username']).'/'.urlencode($data['account_confirm_code'])), $email_content);
		} elseif (isset($email_content) && $email_content == null) {
			return \Lang::get('account.account_unable_to_load_email_template');
		}
		
		// if need to send verify register
		if (!isset($not_verify_register) || (isset($not_verify_register) && $not_verify_register == false)) {
			// send email to notify user, admin to verify registration
			\Package::load('email');
			$config = \Extension\Email::getConfig();
			$email = \Email::forge($config);
			$email->from($cfg['mail_sender_email']['value']);
			$email->to($data['account_email']);
			if ($member_verification == '1') {
				$email->subject(\Lang::get('account.account_please_confirm_your_account'));
			} elseif ($member_verification == '2') {
				$email->subject(\Lang::get('account.account_please_verify_user_registration'));
			}
			$email->html_body($email_content);
			$email->alt_body(str_replace("\t", '', strip_tags($email_content)));
			if ($email->send() == false) {
				// email could not sent.
				unset($cfg, $config, $email, $email_content, $member_verification, $not_verify_register);
				return \Lang::get('account.account_email_could_not_send');
			}
			unset($email, $email_content, $not_verify_register);
		}
		
		// if member verification need admin to verify OR register needs to notify admin.
		if (($member_verification == '2' || $cfg['member_register_notify_admin']['value'] == '1') && (!isset($options['not_notify_admin']) || (isset($options['not_notify_admin']) && $options['not_notify_admin'] == false))) {
			// email content
			$email_content = \Extension\EmailTemplate::readTemplate('register_notify_admin.html');
			$email_content = str_replace("%username%", \Security::htmlentities($data['account_username']), $email_content);
			
			\Package::load('email');
			$config = \Extension\Email::getConfig();
			$email = \Email::forge($config);
			$email->from($cfg['mail_sender_email']['value']);
			$email->to(\Extension\Email::setEmails($cfg['member_admin_verify_emails']['value']));
			$email->subject(\Lang::get('account.account_notify_admin_new_register_account', array('username' => $data['account_username'])));
			$email->html_body($email_content);
			$email->alt_body(str_replace("\t", '', strip_tags($email_content)));
			if ($email->send() == false) {
				// email could not sent.
				unset($cfg, $config, $email, $email_content, $member_verification, $not_verify_register);
				return \Lang::get('account.account_email_could_not_send');
			}
		}
		
		unset($cfg, $config, $member_verification, $not_verify_register);
		
		return true;
	}// sendRegisterEmail
	
	
	public static function sendResetPasswordEmail(array $data = array()) 
	{
		if (!isset($data['account_email'])) {
			return false;
		}
		
		$query = self::query()->where('account_email', $data['account_email']);
		
		if ($query->count() > 0) {
			$row = $query->get_one();
			unset($query);
			
			if ($row->account_status == '0') {
				return \Lang::get('account.account_was_disabled') . ' : ' . $row->account_status_text;
			}
			
			$cfg_member_confirm_wait_time = \Model_Config::getval('member_confirm_wait_time')*60;
			
			// check confirm wait time. you need to wait until 'wait time' passed to send reset password request again.
			if ($row->account_confirm_code != null && time()-$row->account_confirm_code_since <= $cfg_member_confirm_wait_time) {
				return \Lang::get('account.account_reset_password_please_wait_until', array('wait_til_time' => date('d F Y H:i:s', ($row->account_confirm_code_since+(\Model_Config::getval('member_confirm_wait_time')*60)))));
			}
			
			$account_new_password = \Str::random('alnum', 10);
			$account_confirm_code = \Str::random('alnum', 5);
			$account_confirm_code_since = time();
			
			$email_content = \Extension\EmailTemplate::readTemplate('reset_password1.html');
			$email_content = str_replace("%username%", \Security::htmlentities($row->account_username), $email_content);
			$email_content = str_replace("%link_confirm%", \Uri::create('account/resetpw/' . $row->account_id . '/' . $account_confirm_code . '/reset'), $email_content);
			$email_content = str_replace("%link_cancel%", \Uri::create('account/resetpw/' . $row->account_id . '/' . $account_confirm_code . '/cancel'), $email_content);
			$email_content = str_replace("%confirm_until%", date('d F Y H:i:s', (time()+$cfg_member_confirm_wait_time)), $email_content);
			
			\Package::load('email');
			$config = \Extension\Email::getConfig();
			$email = \Email::forge($config);
			$email->from(\Model_Config::getval('mail_sender_email'));
			$email->to($data['account_email']);
			$email->subject(\Lang::get('account.account_email_reset_password_request'));
			$email->html_body($email_content);
			$email->alt_body(str_replace("\t", '', strip_tags($email_content)));
			if ($email->send() == false) {
				unset($account_confirm_code, $account_confirm_code_since, $account_new_password, $cfg_member_confirm_wait_time, $config, $email, $email_content, $query, $row);
				return \Lang::get('account.account_email_could_not_send');
			}
			
			unset($cfg_member_confirm_wait_time, $config, $email, $email_content);
			
			// update to db.
			//$row->account_new_password = self::instance()->hashPassword($account_new_password);
			$row->account_confirm_code = $account_confirm_code;
			$row->account_confirm_code_since = $account_confirm_code_since;
			$row->save();
			
			unset($account_confirm_code, $account_confirm_code_since, $account_new_password, $row);
			
			return true;
		}
		
		// account not found.
		return \Lang::get('account.account_didnot_found_entered_email');
	}// sendResetPasswordEmail


}

