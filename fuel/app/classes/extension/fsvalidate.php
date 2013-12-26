<?php
/**
 * My validation rules
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

namespace Extension;

class FsValidate
{


	/**
	 * validate no space between text.
	 * 
	 * @param mixed $val
	 * @return boolean
	 */
	public function _validation_noSpaceBetweenText($val)
	{
		\Validation::active()->set_message('noSpaceBetweenText', __('account.account_invalid_space_between_text'));
		
		if (preg_match('/\s/', $val) == false) {
			// not found space, return true.
			return true;
		} else {
			// found space, return false.
			return false;
		}
	}// _validation_noSpaceBetweenText
	
	
	/**
	 * validate unique data from db (table.field)
	 * this class copy from fuelphp document
	 * 
	 * @param mixed $val
	 * @param string $options table.field
	 * @return boolean
	 */
	public function _validation_uniqueDB($val, $options) 
	{
		list($table, $field) = explode('.', $options);
		
		$result = \DB::select("LOWER (\"$field\")")
			->where($field, '=', \Str::lower($val))
			->from($table)->execute();
		
		return ! ($result->count() > 0);
	}// _validation_uniqueDB


}

