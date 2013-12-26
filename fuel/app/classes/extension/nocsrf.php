<?php
/**
 * CSRF Protection
 * This class create for all NoCsrf class easier with configured values.
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

namespace Extension;

session_start();
include_once APPPATH . 'vendor' . DS . 'nocsrf' . DS . 'nocsrf.php';

class NoCsrf extends \NoCsrf 
{


	protected static $doOriginCheck = true;
	
	
	/**
	 * check token
	 * 
	 * @param string|null $key not required
	 * @param array|null $origin leave null for auto origin values
	 * @param boolean|null $throwException throw exception or not. leave null for not.
	 * @param integer|null $timespan time span. leave null for get from config.
	 * @param boolean|null $multiple multiple use or not. true if yes, false if not allow multiple use.
	 * @return boolean
	 */
	public static function check($key = '', $origin = '', $throwException=false, $timespan=null, $multiple=true) 
	{
		if ($key == null) {
			$key = \Config::get('security.csrf_token_key');
		}
		
		if ($origin == null) {
			if (isset($_GET[$key])) {
				$origin = array($key => $_GET[$key]);
			} elseif (isset($_POST[$key])) {
				$origin = array($key => $_POST[$key]);
			} else {
				$origin = array();
			}
		}
		
		// check null value, set default value
		if (!is_bool($throwException)) {
			$throwException = false;
		}
		
		if ($timespan == null) {
			$timespan = \Config::get('security.csrf_expiration');
		}
		
		if (!is_bool($multiple)) {
			$multiple = true;
		}
		
		// check value
		$check = parent::check($key, $origin, $throwException, $timespan, $multiple);
		
		// if fail, unset csrf value.
		if ($check == false) {
			unset($_SESSION['csrf_' . $key]);
		}
		
		return $check;
	}// check
	
	
	/**
	 * generate token
	 * add ability to generate input hidden for form.
	 * 
	 * @param string|null $key not required
	 * @param boolean $noinput if true, this function will return only generated token. if false, this function will return input hidden with generated token.
	 * @return string
	 */
	public static function generate($key = '', $noinput = false) 
	{
		if ($key == null) {
			$key = \Config::get('security.csrf_token_key');
		}
		
		// if csrf session exists, return old value. if not, return new value.
		if (!isset($_SESSION['csrf_' . $key]) || (isset($_SESSION['csrf_' . $key]) && $_SESSION['csrf_' . $key] == null)) {
			if ($noinput === false) {
				return '<input type="hidden" name="' . $key . '" value="' . parent::generate($key) . '" />';
			} else {
				return parent::generate($key);
			}
		} else {
			if ($noinput === false) {
				return '<input type="hidden" name="' . $key . '" value="' . $_SESSION['csrf_' . $key] . '" />';
			} else {
				return $_SESSION['csrf_' . $key];
			}
		}
	}// generate


}

