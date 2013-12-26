<?php
/**
 * Extend form class.
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

namespace Extension;

class Form extends \Fuel\Core\Form 
{


	/**
	 * generate input type email
	 * 
	 * @author Vee Winch.
	 * @param string $field
	 * @param string $value
	 * @param array $attributes
	 * @return string
	 */
	public static function email($field, $value = null, array $attributes = array()) 
	{
		$attributes['type'] = 'email';
		
		return parent::input($field, $value, $attributes);
	}// email


}

