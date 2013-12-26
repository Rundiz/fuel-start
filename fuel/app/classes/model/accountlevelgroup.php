<?php
/**
 * account_level_group ORM
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
		)
	);


}

