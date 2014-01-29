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
	 * list login history.
	 * 
	 * @param array $data
	 * @param array $option
	 * @return mixed
	 */
	public static function listLogins(array $data = array(), array $option = array()) 
	{
		if (!isset($data['account_id']) || (isset($data['account_id']) && !is_numeric($data['account_id']))) {
			return null;
		}
		
		// get total logins of current user
		$query = self::query()->where('account_id', $data['account_id']);
		
		$output['total'] = $query->count();
		
		// sort and order
		$orders = \Security::strip_tags(trim(\Input::get('orders')));
		$allowed_orders = array('account_login_id', 'login_ua', 'login_os', 'login_browser', 'login_ip', 'login_time', 'login_time_gmt', 'login_attempt', 'login_attempt_text');
		if ($orders == null || !in_array($orders, $allowed_orders)) {
			$orders = 'account_login_id';
		}
		unset($allowed_orders);
		$sort = \Security::strip_tags(trim(\Input::get('sort')));
		if ($sort == null) {
			$sort = 'DESC';
		}
		
		// offset and limit
		if (!isset($option['offset'])) {
			$option['offset'] = 0;
		}
		if (!isset($option['limit'])) {
			if (isset($option['list_for']) && $option['list_for'] == 'admin') {
				$option['limit'] = \Model_Config::getval('content_admin_items_perpage');
			} else {
				$option['limit'] = \Model_Config::getval('content_items_perpage');
			}
		}
		
		// get the results from sort, order, offset, limit.
		$output['items'] = $query->order_by($orders, $sort)->offset($option['offset'])->limit($option['limit'])->get();
		
		unset($orders, $query, $sort);
		
		return $output;
	}// listLogins
	
	
	/**
	 * purge old login history
	 * 
	 * @param integer $day_old
	 * @return boolean
	 */
	public static function purgeOldLogins($day_old = 90) 
	{
		if (!is_int($day_old)) {
			$day_old = 90;
		}
		
		$query = self::query()->where('login_time', '<', DB::expr('unix_timestamp(now() - interval '.$day_old.' day)'))->delete();
		
		// done.
		return true;
	}// purgeOldLogins
	
	
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
		
		// @todo [multisite] set site id for multiple site management
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

