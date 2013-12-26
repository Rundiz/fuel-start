<?php
/**
 * Extend cookie class to add cookie prefix.
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

namespace Extension;

class Cookie extends \Fuel\Core\Cookie 
{
	
	
	public static $config = array(
		'expiration'            => 0,
		'path'                  => '/',
		'domain'                => null,
		'secure'                => false,
		'http_only'             => false,
		'prefix' => '', // added prefix to cookie.
	);
	
	
	/**
	 * initialize class.
	 */
	public static function _init()
	{
		static::$config = array_merge(static::$config, \Fuel\Core\Config::get('cookie', array()));
	}// init.
	
	
	/**
	 * get cookie
	 * 
	 * @param string $name
	 * @param mixed $default default value if value in cookie not found.
	 * @return mixed
	 */
	public static function get($name = null, $default = null)
	{
		// add prefix to cookie.
		$prefix = static::$config['prefix'];
		$name = $prefix . $name;
		
		return parent::get($name, $default);
	}// get


	/**
	 * set cookie
	 * @param string $name
	 * @param mixed $value
	 * @param integer $expiration
	 * @param string $path
	 * @param string $domain
	 * @param boolean $secure
	 * @param boolean $http_only
	 * @return mixed
	 */
	public static function set($name, $value, $expiration = null, $path = null, $domain = null, $secure = null, $http_only = null)
	{
		// add prefix to cookie.
		$prefix = static::$config['prefix'];
		$name = $prefix . $name;
		
		return parent::set($name, $value, $expiration, $path, $domain, $secure, $http_only);
	}// set


}

