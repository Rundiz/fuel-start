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
		// we will use DB class
		$output = array();
		
		$result = \DB::select('*')->from('config')->as_object()->where('config_name', 'IN', $config_name)->execute();
		if ((is_array($result) || is_object($result)) && !empty($result)) {
			foreach ($result as $row) {
				$output[$row->config_name]['value'] = $row->config_value;
				$output[$row->config_name]['core'] = $row->config_core;
				$output[$row->config_name]['description'] = $row->config_description;
			}// endforeach;
		}// endif;
		unset($result, $row);
		
		return $output;
		// end get values by array loop.
	}// getvalues
	
	
	/**
	 * save
	 * 
	 * @param array $data
	 * @return boolean
	 */
	public static function saveData(array $data = array()) 
	{
		if (empty($data)) {return false;}
		
		foreach ($data as $key => $value) {
			\DB::update('config')
				->value('config_value', $value)
				->where('config_name', $key)
				->execute();
		}
		
		return true;
	}// saveData


}

