<?php
/**
 * Extend image class
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

namespace Extension;

class Image extends \Fuel\Core\Image 
{


	/**
	 * checkMemAvailbleForResize
	 * @author Klinky
	 * @link http://stackoverflow.com/a/4163548/128761, http://stackoverflow.com/questions/4162789/php-handle-memory-code-low-memory-usage
	 * @param string $filename
	 * @param integer $targetX
	 * @param integer $targetY
	 * @param boolean $returnRequiredMem
	 * @param float $gdBloat
	 * @return mixed 
	 */
	public static function checkMemAvailbleForResize($filename, $targetX, $targetY, $returnRequiredMem = false, $gdBloat = 1.68) 
	{
		$maxMem = ((int) ini_get('memory_limit') * 1024) * 1024;
		$imageSizeInfo = getimagesize($filename);
		$srcGDBytes = ceil((($imageSizeInfo[0] * $imageSizeInfo[1]) * 3) * $gdBloat);
		$targetGDBytes = ceil((($targetX * $targetY) * 3) * $gdBloat);
		$totalMemRequired = $srcGDBytes + $targetGDBytes + memory_get_usage();
		\Log::debug('File: '.$filename.'; MemLimit: '.$maxMem.'; MemRequired: '.$totalMemRequired.';');
		
		if ($returnRequiredMem) {
			return $srcGDBytes + $targetGDBytes;
		}
		
		if ($totalMemRequired > $maxMem) {
			return false;
		}
		
		return true;
	}// checkMemAvailbleForResize


}

