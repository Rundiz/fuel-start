<?php
/**
 * Extend date class
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

namespace Extension;

class Date extends \Date
{
	
	
	/**
	 * gmt date. the timezone up to current user data.
	 * 
	 * @param string $date_format date format can use both date() function or strftime() function
	 * @param integer $timestamp localtime timestamp.
	 * @param type $timezone php timezone (http://www.php.net/manual/en/timezones.php)
	 * @return null
	 */
	public static function gmtDate($date_format = '%Y-%m-%d %H:%M:%S', $timestamp = '', $timezone = '') 
	{
		// check empty date format
		if (empty($date_format)) {
			$date_format = '%Y-%m-%d %H:%M:%S';
		}
		
		// check timestamp
		if (empty($timestamp)) {
			$timestamp = time();
		} else {
			if (!self::isValidTimeStamp($timestamp)) {
				$timestamp = strtotime($timestamp);
			}
		}
		
		// check timezone
		if ($timezone == null) {
			$account_model = new \Model_Accounts();
			$cookie = $account_model->getAccountCookie();
			$site_timezone = \Model_Config::getval('site_timezone');
			
			if (!isset($cookie['account_id'])) {
				// not member or not log in. use default config timezone.
				$timezone = $site_timezone;
			} else {
				// find timezone for current user.
				$row = \Model_Accounts::find($cookie['account_id']);
				
				if (!empty($row)) {
					$timezone = $row->account_timezone;
				} else {
					$timezone = $site_timezone;
				}
			}
			
			unset($account_model, $cookie, $row, $site_timezone);
		}
		
		// what format of the date_format (use date() value or strftime() value)
		if (strpos($date_format, '%') !== false) {
			// use strftime() format
			return \Date::forge($timestamp)->set_timezone($timezone)->format($date_format);
		} else {
			// use date() format
			return date($date_format, strtotime(\Date::forge($timestamp)->set_timezone($timezone)->format('%Y-%m-%d %H:%M:%S')));
		}
	}// gmtDate


	/**
	* is valid timestamp
	* @author Gordon
	* @link http://stackoverflow.com/questions/2524680/check-whether-the-string-is-a-unix-timestamp
	* @param string $timestamp timestamp needs to be string
	* @return boolean 
	*/
	public static function isValidTimeStamp($timestamp) {
		return ((string) (int) $timestamp === $timestamp)
			  && ($timestamp <= PHP_INT_MAX)
			  && ($timestamp >= ~PHP_INT_MAX);
	}// isValidTimeStamp
	
	
	/**
	 * get gmt timestamp from local timestamp
	 * 
	 * @author Vee Winch.
	 * @param integer $timestamp timestamp
	 * @return integer
	 */
	public static function localToGmt($timestamp = '') 
	{
		if ($timestamp == null) {
			$timestamp = time();
		}
		
		return strtotime(\Date::forge($timestamp, 'GMT')->format('%Y-%m-%d %H:%M:%S'));
	}// localToGmt


}

