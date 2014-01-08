<?php
/**
 * Extend html class
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

namespace Extension;

class Html extends \Fuel\Core\Html 
{


	/**
	 * generate Fuel Start sortable link. it can generate any querystring url.
	 * 
	 * @param array $sortable_data
	 * @param array $except_querystring
	 * @param string $link
	 * @param string $link_text
	 * @param array $attributes
	 * @param boolean $secure
	 * @return string
	 */
	public static function fuelStartSortableLink($sortable_data = array(), $except_querystring = array(), $link = null, $link_text = '', $attributes = array(), $secure = null) 
	{
		if ($link == null) {
			$link = \Uri::main();
		}
		
		if (!is_array($except_querystring)) {
			$except_querystring = array();
		}
		
		$querystring = array();
		
		// build querystring of sortable_data
		if (!empty($sortable_data) && is_array($sortable_data)) {
			foreach ($sortable_data as $name => $value) {
				$querystring[$name] = $value;
			}
		}
		
		// build querystring of exists querystring except sortable_data and except_querystring
		foreach ($_GET as $key => $value) {
			if (!in_array($key, $except_querystring) && !isset($querystring[$key])) {
				$querystring[$key] = $value;
			}
		}
		unset($key, $value);
		
		// if there is querystring. build it as string (name=val&amp;name2=val2...)
		if (!empty($querystring)) {
			$querystring_str = '';
			
			foreach ($querystring as $key => $value) {
				$querystring_str .= $key . '=' . $value;
				
				if (end($querystring) != $value) {
					$querystring_str .= '&amp;';
				}
			}
			
			$link .= '?' . $querystring_str;
			
			unset($key, $querystring, $querystring_str, $value);
		}
		
		return \Html::anchor($link, $link_text, $attributes, $secure);
	}// fuelStartSortableLink


}

