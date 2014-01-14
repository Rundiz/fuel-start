<?php
/**
 * account_sites ORM and reusable function
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */


class Model_AccountSites extends \Orm\Model 
{


	protected static $_table_name = 'account_sites';
	protected static $_primary_key = array('account_site_id');
	
	// relations
	protected static $_belongs_to = array(
		'accounts' => array(
			'model_to' => 'Model_Accounts',
			'key_from' => 'account_id',
			'key_to' => 'account_id',
		)
	);
	
	
	/**
	 * add login session
	 * 
	 * @param array $data
	 */
	public function addLoginSession($data = array()) 
	{
		// @todo [multisite] for multi site, add get site id here.
		$site_id = 1;
		
		// find exists last login on target site id.
		$account_sites = \Model_AccountSites::query()->where('account_id', $data['account_id'])->where('site_id', $site_id);
		
		if ($account_sites->count() <= 0) {
			// use insert
			$row = new \Model_AccountSites();
			$row->account_id = $data['account_id'];
			$row->site_id = $site_id;
			$row->account_last_login = time();
			$row->account_last_login_gmt = \Extension\Date::localToGmt();
			if (isset($data['session_id'])) {
				$row->account_online_code = $data['session_id'];
			}
			$row->save();
			
			unset($row);
		} else {
			// use update
			$row = $account_sites->get_one();
			$row->account_last_login = time();
			$row->account_last_login_gmt = \Extension\Date::localToGmt();
			if (isset($data['session_id'])) {
				$row->account_online_code = $data['session_id'];
			}
			$row->save();
			
			unset($row);
		}
		
		unset($account_sites, $site_id);
	}// addLoginSession


}

