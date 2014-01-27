<?php
/**
 * account_fields ORM and reusable function
 * 
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
			'key_to' => 'account_id',
		)
	);
	
	
	/**
	 * get data
	 * 
	 * @param integer $account_id
	 * @return object
	 */
	public static function getData($account_id = '') 
	{
		if (!is_numeric($account_id)) {
			return false;
		}
		
		$query = \DB::select()->from('account_fields')->where('account_id', $account_id)->as_object(__CLASS__)->execute();
		/**
		 * as_object('Model_Name') means you can foreach loop and access $row::method_of_this_class() as you accessing that model object.
		 * example:
		 * foreach ($query as $row) {
		 *     echo $row::testStaticMethod();
		 * }
		 * the 'testStaticMethod' must be in that model.
		 */
		
		return $query;
	}// getData
	
	
	/**
	 * update account fields
	 * 
	 * @param integer $account_id
	 * @param array $data_fields
	 * @return boolean
	 */
	public function updateAccountFields($account_id = '', array $data_fields = array()) 
	{
		if (!is_numeric($account_id)) {
			return false;
		}
		
		// delete not exists fields.
		$current_af = static::getData($account_id);
		
		if ($current_af->count() > 0) {
			foreach ($current_af as $af) {
				if (!isset($data_fields[$af->field_name])) {
					$entry = static::query()->where('account_id', $account_id)->where('field_name', $af->field_name)->delete();
				}
			}
		}
		unset($af, $current_af, $entry);
		
		// update or insert fields.
		if (is_array($data_fields) && !empty($data_fields)) {
			foreach ($data_fields as $field_name => $field_value) {
				$entry = static::query()->where('account_id', $account_id)->where('field_name', $field_name)->get_one();
				
				if (!is_array($entry) && !is_object($entry)) {
					// use insert
					$entry = new self;
					$entry->account_id = $account_id;
					$entry->field_name = $field_name;
					$entry->field_value = $field_value;
					$entry->save();
				} else {
					// use update
					// for update multiple rows as ORM style, please see http://www.fuelphp.com/forums/discussion/12798/how-to-update-via-orm-with-multiple-where-conditions
					// $objects = Model_AF::query()->where('field_name', $field_name)->get();
					// foreach ($objects as $object) {
					// $object->field_value = $field_value; 
					//  $object->save();
					// }
					// use update by db query. it is faster.
					\DB::update('account_fields')
						->value('field_value', $field_value)
						->where('account_id', '=', $account_id)
						->where('field_name', $field_name)
						->execute();
				}
				
				unset($entry);
			}
			unset($field_name, $field_value);
		}
		
		return true;
	}// updateAccountFields


}

