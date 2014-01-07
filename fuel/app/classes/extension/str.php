<?php
/**
 * Extend string class
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

namespace Extension;

class Str extends \Fuel\Core\Str 
{


	/**
	 * is json format
	 * check the string that it is valid json encoded format.
	 * example:
	 * original (int)123 decode=> (int)123 = original is valid json encoded
	 *            (str)123           => (int)123 = valid
	 *            (str)0123         => (int)123 = invalid!
	 *            (str)"0123"       => (str)0123 = valid
	 *            (str)null           => null = valid
	 *            (str)false         => (bool)false = valid
	 *            (str)true          => (bool)true = valid
	 * 
	 * @param string $string
	 * @return boolean
	 */
	public static function isJsonFormat($string) 
	{
		if (is_array($string) || is_object($string)) {
			return false;
		}
		
		$result = \Str::is_json($string);
		
		if ($result === true) {
			if (preg_match('/{([^}]*)}/', $string) === 1 || preg_match('/"([^"]+)"/', $string) === 1) {
				return true;
			} else {
				// if input value is null or boolean or boolean string (json encoded).
				if (
					is_null(json_decode($string)) || 
					(is_bool(json_decode($string)) || is_bool($string))
				) {
					return true;
				}
				
				// if input value is number. make very sure that it is equal string length. (0123 is not equal to 123 which 0123 (string) is not valid json encoded)
				if (is_numeric($string) && mb_strlen($string) == mb_strlen(json_decode($string))) {
					return true;
				}
				
				return false;
			}
		}
		
		return $result;
	}// isJsonFormat


}

