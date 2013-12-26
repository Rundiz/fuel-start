<?php
/**
 * account_fields ORM
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

class Model_AccountFields extends \Orm\Model 
{


	protected static $_table_name = 'account_fields';
	protected static $_primary_key = array();
	
	// relations
	protected static $_belongs_to = array(
		'accounts' => array(
			'model_to' => 'Model_Accounts',
			'key_from' => 'account_id',
		)
	);


}

