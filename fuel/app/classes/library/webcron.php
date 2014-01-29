<?php
/**
 * web cron
 * work on schduled tasks without command line or real server cron job.
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

namespace Library;

class WebCron
{
	
	
	public function __construct()
	{
		// class constructor
	}// __construct
	
	
	/**
	 * check cron to run in time
	 * 
	 * @param array $option options avaliable: [name, second_expired, run_timestamp]
	 * @param array $callback_function
	 * @param array $callback_function_param
	 * @return boolean
	 */
	public function checkCron(array $option = array(), array $callback_function = array(), $callback_function_param = array()) 
	{
		// not set option name
		if (!isset($option['name'])) {
			return false;
		}
		
		// verify second expired.
		if (!isset($option['second_expired'])) {
			$option['second_expired'] = 86400;
		} else {
			$option['second_expired'] = (int) $option['second_expired'];
		}
		
		// verify run on date/time timestamp is valid and set (if not set, set to null)
		if (!isset($option['run_timestamp']) || (isset($option['run_timestamp']) && !\Extension\Date::isValidTimeStamp((string) $option['run_timestamp']))) {
			$option['run_timestamp'] = null;
		}
		
		// check that both expired and run timestamp has value.
		if ($option['second_expired'] == 0 && $option['run_timestamp'] == null) {
			return false;
		}
		
		// set callback function param to be array if it is not.
		if (!is_array($callback_function_param)) {
			$callback_function_param = array($callback_function_param);
		}
		
		// start checking ------------------------------------------------------------------------------------------------
		
		$run_task = false;
		
		// run from specific date/time timestamp.
		if ($option['run_timestamp'] != null && $option['run_timestamp'] <= time()) {
			$run_task = true;
		} elseif ($option['second_expired'] > 0) {
			// get cache of this task name
			try {
				// @todo [multisite] add site id to cache name on multi site code
				$cache = \Cache::get('webcron-' . $option['name']);
			} catch (\CacheNotFoundException $e) {
				$cache = false;
			}
			
			// if never cached or cache expired
			if ($cache === false) {
				$run_task = true;
				
				// @todo [multisite] add site id to cache name on multi site code
				\Cache::set('webcron-' . $option['name'], 'done', $option['second_expired']);
			}
		}
		
		// checked pass, run the task by call to callback function.
		if ($run_task === true) {
			call_user_func_array($callback_function, $callback_function_param);
			
			return true;
		}
		
		return false;
	}// checkCron


	/**
	 * forge
	 * 
	 * @return object
	 */
	public static function forge() 
	{
		return new self();
	}// forge
	
	
	/**
	 * initialize
	 * 
	 * @return boolean
	 */
	public function init() 
	{
		// check tasks from code -----------------------------------------------------------------
		// purge old login history
		$this->checkCron(array('name' => 'purge_login_history'), array('\Model_AccountLogins', 'purgeOldLogins'));
		
		// check tasks from db
		// @todo [api] add check tasks from db or create your tasks from db here.
		
		return true;
	}// init


}

