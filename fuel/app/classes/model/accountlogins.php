<?php
/**
 * account_logins ORM and reusable function
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */


class Model_AccountLogins extends \Orm\Model 
{


	protected static $_table_name = 'account_logins';
	protected static $_primary_key = array('account_login_id');
	
	// relations
	protected static $_belongs_to = array(
		'accounts' => array(
			'model_to' => 'Model_Accounts',
			'key_from' => 'account_id',
			'key_to' => 'account_id',
		)
	);
	
	
	/**
	 * record login
	 * @param integer $account_id
	 * @param integer $attempt 0 for failed, 1 for success
	 * @param string $attempt_text attempt text
	 * @return boolean
	 */
	public function recordLogin($account_id = '', $attempt = '0', $attempt_text = '') 
	{
		if (!is_numeric($account_id) || !is_numeric($attempt)) {
			return false;
		}
		
		if ($attempt_text == null) {
			$attempt_text = null;
		}
		
		// @todo set site id for multiple site management
		$site_id = 1;
		
		// get browser class for use instead of fuelphp agent which is does not work.
		include_once APPPATH . 'vendor' . DS . 'browser' . DS . 'lib' . DS . 'Browser.php';
		$browser = new Browser();
		
		// set data for insertion
		$data['account_id'] = $account_id;
		$data['site_id'] = $site_id;
		$data['login_ua'] = \Input::user_agent();
		$data['login_os'] = $browser->getPlatform();
		$data['login_browser'] = $browser->getBrowser() . ' ' . $browser->getVersion();
		$data['login_ip'] = \Input::real_ip();
		$data['login_time'] = time();
		$data['login_time_gmt'] = \Extension\Date::localToGmt();
		$data['login_attempt'] = $attempt;
		$data['login_attempt_text'] = $attempt_text;
		
		$account_logins = new Model_AccountLogins($data);
		$account_logins->save();
		
		unset($account_logins, $browser, $data);
		
		return true;
	}// recordLogin


}

