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

