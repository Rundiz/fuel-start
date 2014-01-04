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
	 * if method is in array. get called method, arguments and set input type from method.
	 * 
	 * @param string $method
	 * @param array $arguments
	 * @return mixed
	 */
	public static function __callStatic($method, $arguments)
	{
		$html5_input_types = array('color', 'date', 'datetime', 'datetime-local', 'email', 'month', 'number', 'range', 'search', 'tel', 'time', 'url', 'week');
		
		if (!method_exists(self::forge(), $method) && in_array($method, $html5_input_types)) {
			unset($html5_input_types);
			
			// compare arguments to input(arguments)
			$field = '';
			if (isset($arguments[0])) {
				$field = $arguments[0];
			}
			
			$value = '';
			if (isset($arguments[1])) {
				$value = $arguments[1];
			}
			
			$attributes = '';
			if (isset($arguments[2])) {
				$attributes = $arguments[2];
			}
			
			// add input attribute type.
			$attributes['type'] = $method;
			
			// clear
			$method = '';
			$arguments = '';
			
			return parent::input($field, $value, $attributes);
		} else {
			\Error::error_handler(500, 'Call to undefined method', __FILE__, __LINE__);
		}
	}// __callStatic
	
	
	/**
	 * open enctype multipart form.
	 * 
	 * @param array $attributes
	 * @param array $hidden
	 * @return string
	 */
	public static function openMultipart($attributes = array(), $hidden = array()) 
	{
		$attributes['enctype'] = 'multipart/form-data';
		
		return \Form::open($attributes, $hidden);
	}// openMultipart


}

