<?php
/**
 * account_level ORM and reusable function
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

class Model_AccountLevel extends \Orm\Model 
{


	protected static $_table_name = 'account_level';
	protected static $_primary_key = array('level_id');
	
	// relations
	protected static $_belongs_to = array(
		'account_level_group' => array(
			'model_to' => 'Model_AccountLevelGroup',
			'key_from' => 'level_group_id',
			'key_to' => 'level_group_id',
		),
		'accounts' => array(
			'model_to' => 'Model_Accounts',
			'key_from' => 'account_id',
			'key_to' => 'account_id',
		)
	);
	
	
	/**
	 * update account levels
	 * 
	 * @param integer $account_id
	 * @param array $data_level
	 * @return boolean
	 */
	public function updateLevels($account_id = '', $data_level = array()) 
	{
		// @todo [multisite] for multi site with table site id prefix, you need to modify and loop those [site id]_account_level to add level to user.
		// 
		// below is add level to user by use single site table structure.
		
		// delete not exists level
		$lvls = self::query()->where('account_id', $account_id);
		if ($lvls->count() > 0) {
			foreach ($lvls->get() as $lvl) {
				if (!in_array($lvl->level_group_id, $data_level)) {
					self::query()->where('account_id', $account_id)->where('level_id', $lvl->level_id)->delete();
				}
			}
		}
		unset($lvls, $lvl);
		
		// update or insert fields
		if (is_array($data_level) && !empty($data_level)) {
			foreach ($data_level as $level_group_id) {
				$entry = self::query()->where('account_id', $account_id)->where('level_group_id', $level_group_id)->get_one();
				
				if (!is_array($entry) && !is_object($entry)) {
					// not exists, use insert.
					$entry = new self;
					$entry->account_id = $account_id;
					$entry->level_group_id = $level_group_id;
					$entry->save();
				}
				
				unset($entry);
			}
		}
		
		return true;
	}// updateLevels


}

