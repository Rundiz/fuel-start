<?php
/**
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

namespace Extension;

class File extends \Fuel\Core\File 
{


	/**
	 * read directory and sub directory recursive into 2d array
	 * @param string $path
	 * @return array
	 */
	public static function readDir2D($path) 
	{
		$output = array();
		
		if ($handle = opendir($path)) {
			while (false != ($file = readdir($handle))) {
				if ($file != '.' && $file != '..') {
					if (is_dir($path . DS . $file)) {
						$output = array_merge($output, self::readDir2D($path . $file . DS));
						$output[] = $path . $file;
					} else {
						$output[] = $path . $file;
					}
				}
			}
			
			closedir($handle);
		}
		
		return $output;
	}// readDir2D


}

