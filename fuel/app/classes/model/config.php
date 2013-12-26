<?php
/**
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

class Model_Config extends \Orm\Model 
{


	protected static $_table_name = 'config';
	//protected static $_properties = array('config_name', 'config_value', 'config_core', 'config_description');
	protected static $_primary_key = array();// no PK, need to set PK to empty array.
	
	
	/**
	 * get config value from config_name field in config table
	 * 
	 * @param string $config_name config name
	 * @return mixed
	 */
	public static function getval($config_name = '', $return_field = 'config_value') 
	{
		if ($config_name == null) {
			return null;
		}
		
		$query = self::query()->where('config_name', '=', $config_name)->get_one();
		
		if ($return_field == null) {
			return $query;
		} else {
			return $query->$return_field;
		}
	}// getval
	
	
	/**
	 * alias name of getval
	 * 
	 * @return mixed
	 */
	public static function getvalue($config_name = '', $return_field = 'config_vlue') 
	{
		return self::getval($config_name, $return_field);
	}// getvalue
	
	
	/**
	 * get multiple config values from config_name field in config table
	 * 
	 * @param array $config_name
	 * @return array|null array if exists, null if not exists.
	 */
	public static function getvalues($config_name = array()) 
	{
		if (!is_array($config_name) || (is_array($config_name) && empty($config_name))) {
			return null;
		}
		
		// because FuelPHP ORM cannot get multiple results if that table has no primary key.
		// we will use array loop to get a single value.
		$output = array();
		
		foreach ($config_name as $a_config_name) {
			$a_cfg = self::getval($a_config_name, null);
			$output[$a_cfg->config_name]['value'] = $a_cfg->config_value;
			$output[$a_cfg->config_name]['core'] = $a_cfg->config_core;
			$output[$a_cfg->config_name]['description'] = $a_cfg->config_description;
		}
		
		return $output;
		// end get values by array loop.
	}// getvalues


}

