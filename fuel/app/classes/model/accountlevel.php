<?php
/**
 * account_level ORM
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
			'key_from' => 'account_id',
			'model_to' => 'Model_Accounts',
			'key_to' => 'account_id',
		)
	);


}

