<?php
/**
 * account_level_group ORM and reusable functions
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

class Model_AccountLevelGroup extends \Orm\Model 
{


	protected static $_table_name = 'account_level_group';
	protected static $_primary_key = array('level_group_id');
	
	// relations
	protected static $_has_many = array(
		'account_level' => array(
			'model_to' => 'Model_AccountLevel',
			'key_from' => 'level_group_id',
			'key_to' => 'level_group_id',
			'cascade_delete' => true,
		),
		'account_level_permission' => array(
			'model_to' => 'Model_AccountLevelPermission',
			'key_from' => 'level_group_id',
			'key_to' => 'level_group_id',
			'cascade_delete' => true,
		)
	);
	
	
	/**
	 * list level groups
	 * 
	 * @param array $option
	 * @return mixed
	 */
	public static function listLevels($option = array()) 
	{
		$query = self::query();
		
		if (isset($option['no_guest']) && $option['no_guest'] == true) {
			$query->where_open();
			$query->where('level_group_id', '!=', '4');
			$query->or_where('level_priority', '!=', '1000');
			$query->where_close();
		}
		
		// sort order
		$allowed_orders = array('level_group_id', 'level_name', 'level_description', 'level_priority');
		if (!isset($option['orders']) || (isset($option['orders']) && !in_array($option['orders'], $allowed_orders))) {
			$orders = 'level_priority';
		} else {
			$orders = $option['orders'];
		}
		if (!isset($option['sort']) || (isset($option['sort']) && $option['sort'] != 'DESC')) {
			$sort = 'ASC';
		} else {
			$sort = $option['sort'];
		}
		
		return $query->order_by($orders, $sort)->get();
	}// listLevels


}

