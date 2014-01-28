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
	 *disallowed edit or delete level_group_id
	 * 
	 * @var array array of disallowed ids
	 */
	public $disallowed_edit_delete = array(1, 2, 3, 4);
	
	
	/**
	 * add level groupo
	 * 
	 * @param array $data
	 * @return boolean
	 */
	public static function addLevel($data = array()) 
	{
		// get new priority
		$entry = self::query()->where('level_group_id', 'NOT IN', self::forge()->disallowed_edit_delete)->order_by('level_priority', 'DESC')->get_one();
		
		if ($entry == null) {
			$data['level_priority'] = 3;
		} else {
			$data['level_priority'] = ($entry->level_priority+1);
		}
		
		unset($entry);
		
		// add to db.
		$alg = self::forge($data);
		$alg->save();
		
		unset($alg);
		
		// done
		return true;
	}// addLevel
	
	
	/**
	 * delete level group.
	 * 
	 * @param integer $level_group_id
	 * @return boolean
	 */
	public static function deleteLevel($level_group_id = '') 
	{
		if (in_array($level_group_id, self::forge()->disallowed_edit_delete)) {
			return false;
		}
		
		// @todo [api] for delete level group or role here.
		
		// delete level group
		self::find($level_group_id)->delete();
		
		return true;
	}// deleteLevel
	
	
	/**
	 * edit level group
	 * 
	 * @param array $data
	 * @return boolean
	 */
	public static function editLevel($data = array()) 
	{
		// set level_group_id variable and unset it from $data to prevent update error PK
		$level_group_id = $data['level_group_id'];
		unset($data['level_group_id']);
		
		$alg = self::find($level_group_id);
		$alg->set($data);
		$alg->save();
		
		return true;
	}// editLevel
	
	
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
		
		$output['total'] = $query->count();
		
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
		
		$output['items'] = $query->order_by($orders, $sort)->get();
		
		unset($allowed_orders, $orders, $query, $sort);
		
		return $output;
	}// listLevels


}

