<?php

/**
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

namespace Okvee\Vimage;

// it is required PHP >= 5.3
if (phpversion() < 5.3) {
	die('PHP 5.3 or newer is required.');
}

/**
 * Vimage is the image manipulation class. It is the version 2 of "v image class" (http://okvee.net/th/web-resources/download/v-image-class)
 * Vimage class support GD only.
 *
 * @author Vee W.
 */

class Vimage
{
	
	
	// allow resize to larger than original image or not.
	public $allow_resize_larger = false;
	
	// class constructor status.
	public $construct_status = false;
	
	// jpeg resize quality (0-100)
	public $jpg_quality = 100;
	
	// last modified width & height
	private $last_mod_height;
	private $last_mod_width;
	
	// master dimension (auto, width, height)
	public $master_dim = 'auto';
	
	// new image content (image_read from v.1)
	private $new_img_content;
	// new image height
	private $new_img_height;
	// new image object (new_image from v.1)
	private $new_img_object;
	// new image width
	private $new_img_width;
	
	// store image extension of original image file.
	public $original_img_ext;
	// store array data from getimagesize of original image file.
	public $original_img_getdata;
	// store image height of original image file.
	public $original_img_height;
	public $original_img_mime;
	// store original image path.
	public $original_img_path;
	// store image width of original image file.
	public $original_img_width;
	
	// png resize quality (0-9) 0 is no compression.
	public $png_quality = 0;
	
	// store status (boolean) and status message for something error.
	public $status = false;
	public $status_msg;
	
	// watermark image content
	private $wm_img_content;
	// watermark image extension
	private $wm_img_ext;
	// watermark image height
	private $wm_img_height;
	// watermark image width
	private $wm_img_width;


	/**
	 * class constructor
	 * @param string $original_img_path full path to image.
	 */
	public function __construct($original_img_path) 
	{
		if (file_exists($original_img_path)) {
			$this->original_img_path = $original_img_path;
			
			// get image size and type
			$img_type = getimagesize($original_img_path);
			if ($img_type !== false) {
				$this->original_img_getdata = $img_type;
				$this->original_img_ext = $img_type[2]; // array(2) is IMAGETYPE_XXX which XXX is number and can convert to image extension use image_type_to_extension(XXX);
				$this->original_img_height = $img_type[1];
				$this->original_img_mime = $img_type['mime'];
				$this->original_img_width = $img_type[0];
				
				// unset un-use variable.
				unset($img_type);
				
				// set new image object
				$this->new_img_object = imagecreatetruecolor($this->original_img_width, $this->original_img_height);
				
				$this->construct_status = true;
			} else {
				$this->construct_status = false;
			}
		} else {
			$this->construct_status = false;
		}
	}// __construct
	
	
	public function __destruct()
	{
		$this->clear();
	}// __destruct
	
	
	/**
	 * calculate startX position of center
	 * @param integer $obj_width
	 * @param integer $canvas_width
	 * @return integer
	 */
	public function calculateStartXOfCenter($obj_width = '', $canvas_width = '') 
	{
		if (!is_numeric($obj_width) || !is_numeric($canvas_width)) {
			return 0;
		}
		
		return round(($canvas_width/2)-($obj_width/2));
	}// calculateStartXOfCenter
	
	
	/**
	 * calculate image size by keeping aspect ratio
	 * @param integer $width
	 * @param integer $height
	 * @return boolean
	 */
	public function calculateImageSizeRatio($width = '', $height = '') 
	{
		// if constructor failed to load.
		if (!$this->checkConstructorLoad()) {
			return false;
		}
		
		// convert width, height value to integer.
		$width = (int) $width;
		$height = (int) $height;
		
		// check if width, height value is integer or larger than 0.
		if (!is_int($width) || $width <= 0) {
			$width = 100;
		}
		if (!is_int($height) || $height <= 0) {
			$height = 100;
		}
		
		// check for last modified width
		$original_img_width = $this->original_img_width;
		if ($this->last_mod_width != null) {
			$original_img_width = $this->last_mod_width;
		}
		
		// check for last modified height
		$original_img_height = $this->original_img_height;
		if ($this->last_mod_height != null) {
			$original_img_height = $this->last_mod_height;
		}
		
		$original_img_orientation = $this->getOriginalImageOrientation();
		// find height from original image size by master dimension = width
		$find_h = round(($original_img_height/$original_img_width)*$width);
		// find width from original image size by master dimension = height
		$find_w = round(($original_img_width/$original_img_height)*$height);
		
		// make sure master dimension is set correctly.
		$this->checkMasterDimension();
		
		switch ($this->master_dim) {
			case 'width':
				$new_width = $width;
				$new_height = $find_h;
				
				// if not allow resize larger.
				if ($this->allow_resize_larger == false) {
					// if new width larger than original image width
					if ($width > $original_img_width) {
						$new_width = $original_img_width;
						$new_height = $original_img_height;
					}
				}
				break;
			case 'height':
				$new_width = $find_w;
				$new_height = $height;
				
				// if not allow resize larger.
				if ($this->allow_resize_larger == false) {
					// if new height is larger than original image height
					if ($height > $original_img_height) {
						$new_width = $original_img_width;
						$new_height = $original_img_height;
					}
				}
				break;
			case 'auto':
			default:
				// master dimension auto.
				switch ($original_img_orientation) {
					case 'P':
						// image orientation portrait
						$new_width = $find_w;
						$new_height = $height;

						// if not allow resize larger
						if ($this->allow_resize_larger == false) {
							// determine new image size must not larger than original image size.
							if ($height > $original_img_height && $width <= $original_img_width) {
								// if new height larger than original image height and width smaller or equal to original image width
								$new_width = $width;
								$new_height = $find_h;
							} else {
								if ($height > $original_img_height) {
									$new_width = $original_img_width;
									$new_height = $original_img_height;
								}
							}
						}
						break;
					case 'L':
						// image orientation landscape
					case 'S':
						// image orientation square
					default:
						// image orientation landscape and square
						$new_width = $width;
						$new_height = $find_h;

						// if not allow resize larger
						if ($this->allow_resize_larger == false) {
							// determine new image size must not larger than original image size.
							if ($width > $original_img_width && $height <= $original_img_height) {
								// if new width larger than original image width and height smaller or equal to original image height
								$new_width = $find_w;
								$new_height = $height;
							} else {
								if ($width > $original_img_width) {
									$new_width = $original_img_width;
									$new_height = $original_img_height;
								}
							}
						}
						break;
				}
				break;
		}
		
		$this->new_img_height = $new_height;
		$this->new_img_width = $new_width;
		
		// unset no use variables
		unset($find_h, $find_w, $height, $new_height, $new_width, $original_img_height, $original_img_orientation, $original_img_width, $width);
		
		return true;
	}// calculateImageSizeRatio
	
	
	/**
	 * check if class constructor loaded successfully.
	 * @return boolean
	 */
	public function checkConstructorLoad() 
	{
		// if constructor failed to load.
		if ($this->construct_status == false) {
			return false;
		}
		
		return true;
	}// checkConstructorLoad
	
	
	/**
	 * check master dimension value must be correctly.
	 */
	private function checkMasterDimension() 
	{
		$this->master_dim = strtolower($this->master_dim);
		
		if ($this->master_dim != 'auto' && $this->master_dim != 'width' && $this->master_dim != 'height') {
			$this->master_dim = 'auto';
		}
	}// checkMasterDimension
	
	
	/**
	 * clear image functions and value using imagedestroy
	 */
	public function clear() 
	{
		if ($this->new_img_content != null) {
			@imagedestroy($this->new_img_content);// imagedestroy can return error when not call show() or save().
			$this->new_img_content = null;
		}
		
		if ($this->new_img_object != null) {
			@imagedestroy($this->new_img_object);// imagedestroy can return error when not call show() or save().
			$this->new_img_object = null;
		}
	}// clear
	
	
	/**
	 * 
	 * @param integer $width
	 * @param integer $height
	 * @param mixed $start_x integer or center for automatically find center.
	 * @param mixed $start_y integer or middle for automatically find middle.
	 * @param string $fill fill canvas background with transparent, white, black (for gif and png).
	 * @return boolean
	 */
	public function crop($width = '', $height = '', $start_x = '0', $start_y = '0', $fill = 'transparent') 
	{
		// if constructor failed to load.
		if (!$this->checkConstructorLoad()) {
			return false;
		}
		
		// convert width, height value to integer.
		$width = (int) $width;
		$height = (int) $height;
		
		// check if width, height value is integer or larger than 0.
		if (!is_int($width) || $width <= 0) {
			$width = 100;
		}
		if (!is_int($height) || $height <= 0) {
			$height = 100;
		}
		
		$new_img_object = imagecreatetruecolor($width, $height);
		
		// if image content not set, set it up (this case just call crop without resize).
		if ($this->new_img_content == null) {
			$result = $this->setImageContent();
			
			if ($result === false) {
				$this->status = false;
				$this->status_msg = 'Failed to set image content.';
				return false;
			}
			
			$new_img_content = $this->new_img_content;
			unset($result);
		} else {
			$new_img_content = $this->new_img_object;
		}
		
		// check if start x is center or position number
		if ($start_x == 'center') {
			$canvas_width = imagesx($new_img_content);
			$object_width = imagesx($new_img_object);
			
			$start_x = $this->calculateStartXOfCenter($object_width, $canvas_width);
			
			unset($canvas_width, $object_width);
		} else {
			$start_x = (int) $start_x;
		}
		
		// check if start y is middle or position number
		if ($start_y == 'middle') {
			$canvas_height = imagesy($new_img_content);
			$object_height = imagesy($new_img_object);
			
			$start_y = $this->calculateStartXOfCenter($object_height, $canvas_height);
			
			unset($canvas_height, $object_height);
		} else {
			$start_y = (int) $start_y;
		}
		
		// set color
		$black = imagecolorallocate($new_img_object, 0, 0, 0);
		$white = imagecolorallocate($new_img_object, 255, 255, 255);
		$transwhite = imagecolorallocatealpha($new_img_object, 255, 255, 255, 127);// set color transparent white
		
		if ($fill != 'transparent' && $fill != 'white' && $fill != 'black') {
			$fill = 'transparent';
		}
		
		if ($this->original_img_ext == '1') {
			// gif image
			// fill background canvas
			if ($fill == 'transparent') {
				imagefill($new_img_object, 0, 0, $transwhite);
				imagecolortransparent($new_img_object, $transwhite);
			} else {
				imagefill($new_img_object, 0, 0, $$fill);
			}
			
			imagecopy($new_img_object, $new_img_content, 0, 0, $start_x, $start_y, $width, $height);
			
			// fill background canvas
			if ($fill == 'transparent') {
				imagefill($new_img_object, 0, 0, $transwhite);
				imagecolortransparent($new_img_object, $transwhite);
			} else {
				imagefill($new_img_object, 0, 0, $$fill);
			}
		} elseif ($this->original_img_ext == '2') {
			// jpeg image
			imagecopy($new_img_object, $new_img_content, 0, 0, $start_x, $start_y, $width, $height);
			
			// fill background canvas
			imagefill($new_img_object, 0, 0, $white);
		} elseif ($this->original_img_ext == '3') {
			// png image
			if ($fill == 'transparent') {
				imagefill($new_img_object, 0, 0, $transwhite);
				imagecolortransparent($new_img_object, $black);
				imagealphablending($new_img_object, false);
				imagesavealpha($new_img_object, true);
			} else {
				imagefill($new_img_object, 0, 0, $$fill);
			}
			
			imagecopy($new_img_object, $new_img_content, 0, 0, $start_x, $start_y, $width, $height);
			
			// fill background canvas
			if ($fill == 'transparent') {
				imagefill($new_img_object, 0, 0, $transwhite);
				imagecolortransparent($new_img_object, $black);
				imagealphablending($new_img_object, false);
				imagesavealpha($new_img_object, true);
			} else {
				imagefill($new_img_object, 0, 0, $$fill);
			}
		} else {
			// not allowed image type.
			$this->status = false;
			$this->status_msg = 'Unable to crop this type of image.';
			return false;
		}
		
		$this->last_mod_height = $height;
		$this->last_mod_width = $width;
		$this->new_img_object = $new_img_object;
		$this->new_img_content = $new_img_content;
		$this->new_img_height = $height;
		$this->new_img_width = $width;
		
		// clear un-use variables
		unset($black, $new_img_content, $new_img_object, $white, $start_x, $start_y, $transwhite);
	}// crop
	
	
	/**
	 * debug calculated image size by keeping aspect ratio.
	 * @param integer $width
	 * @param integer $height
	 * @return string
	 */
	public function debugResizeRatio($width = '', $height = '') 
	{
		// convert width, height value to integer.
		$width = (int) $width;
		$height = (int) $height;
		
		// check if width, height value is integer or larger than 0.
		if (!is_int($width) || $width <= 0) {
			$width = 100;
		}
		if (!is_int($height) || $height <= 0) {
			$height = 100;
		}
		
		$this->calculateImageSizeRatio($width, $height);
		
		return $this->new_img_width . 'x' . $this->new_img_height;
	}// debugResizeRatio
	
	
	/**
	 * get file extension from file name or file full path.
	 * @param string $file_name
	 * @return string
	 */
	public function getFileExt($file_name = '') 
	{
		$file_path = explode('.', $file_name);
		
		if (is_array($file_path)) {
			return $file_path[count($file_path)-1];
		}
		
		unset($file_path);
		
		return null;
	}// getFileExt
	
	
	/**
	 * get image orientation from specified width, height
	 * @param integer $width
	 * @param integer $height
	 * @return boolean|string return L, P, S for landscape, portrait, square or false if width, height is 0.
	 */
	public function getImageOrientation($width = '', $height = '') 
	{
		$width = (int) $width;
		$height = (int) $height;
		
		if ($height <= 0 || $width <= 0) {
			return false;
		}
		
		if ($width == $height) {
			return 'S';
		} elseif ($width > $height) {
			return 'L';
		} else {
			return 'P';
		}
	}// getImageOrientation
	
	
	/**
	 * get original image size (width and height)
	 * @return array
	 */
	public function getImageSize() 
	{
		$output['width'] = $this->original_img_width;
		$output['height'] = $this->original_img_height;
		
		return $output;
	}// getImageSize
	
	
	/**
	 * get original image orientation
	 * @return boolean|string return L, P, S for landscape, portrait, square or false if failed to load image.
	 */
	public function getOriginalImageOrientation() 
	{
		// if constructor failed to load.
		if (!$this->checkConstructorLoad()) {
			return false;
		}
		
		if ($this->original_img_width == $this->original_img_height) {
			// square image.
			return 'S';
		} elseif ($this->original_img_width > $this->original_img_height) {
			// landscape image.
			return 'L';
		} else {
			return 'P';
		}
	}// getOriginalImageOrientation
	
	
	/**
	 * alias method of resizeRatio().
	 */
	public function resize($width, $height) 
	{
		return $this->resizeRatio($width, $height);
	}// resize
	
	
	/**
	 * resize and not keep aspect ratio.
	 * @param integer $width
	 * @param integer $height
	 */
	public function resizeNoRatio($width, $height) 
	{
		// if constructor failed to load.
		if (!$this->checkConstructorLoad()) {
			return false;
		}
		
		// convert width, height value to integer.
		$width = (int) $width;
		$height = (int) $height;
		
		// check if width, height value is integer or larger than 0.
		if (!is_int($width) || $width <= 0) {
			$width = 100;
		}
		if (!is_int($height) || $height <= 0) {
			$height = 100;
		}
		
		// check for last modified width
		$original_img_width = $this->original_img_width;
		if ($this->last_mod_width != null) {
			$original_img_width = $this->last_mod_width;
		}
		
		// check for last modified height
		$original_img_height = $this->original_img_height;
		if ($this->last_mod_height != null) {
			$original_img_height = $this->last_mod_height;
		}
		
		// if new image content is empty or not.
		if ($this->new_img_content == null) {
			$result = $this->setImageContent();
			
			if ($result === false) {
				$this->status = false;
				$this->status_msg = 'Failed to set image content.';
				return false;
			}
			
			$new_img_content = $this->new_img_content;
			unset($result);
		} else {
			$new_img_content = $this->new_img_object;
		}
		
		// create new image color again with specify width, height. (created once in constructor)
		$this->new_img_object = imagecreatetruecolor($width, $height);
		
		if ($this->original_img_ext == '1') {
			// gif image
			$transwhite = imagecolorallocatealpha($this->new_img_object, 255, 255, 255, 127);// set color transparent white
			imagefill($this->new_img_object, 0, 0, $transwhite);
			imagecolortransparent($this->new_img_object, $transwhite);
			imagecopyresampled($this->new_img_object, $new_img_content, 0, 0, 0, 0, $width, $height, $original_img_width, $original_img_height);// resize
			imagesavealpha($new_img_content, true);
			
			unset($transwhite);
		} elseif ($this->original_img_ext == '2') {
			// jpeg image
			imagecopyresampled($this->new_img_object, $new_img_content, 0, 0, 0, 0, $width, $height, $original_img_width, $original_img_height);// resize
		} elseif ($this->original_img_ext == '3') {
			// png image
			imagealphablending($this->new_img_object, false);
			imagesavealpha($this->new_img_object, true);
			imagecopyresampled($this->new_img_object, $new_img_content, 0, 0, 0, 0, $width, $height, $original_img_width, $original_img_height);// resize
		} else {
			// not allowed image type.
			$this->status = false;
			$this->status_msg = 'Unable to resize this type of image.';
			return false;
		}
		
		$this->last_mod_height = $height;
		$this->last_mod_width = $width;
		$this->new_img_content = $new_img_content;
		$this->new_img_object = $this->new_img_object;
		$this->new_img_height = $height;
		$this->new_img_width = $width;
		
		// clear
		imagedestroy($new_img_content);
		unset($original_img_height, $original_img_width);
	}// resizeNoRatio
	
	
	/**
	 * resize by keeping aspect ratio
	 * @param integer $width
	 * @param integer $height
	 */
	public function resizeRatio($width, $height) 
	{
		// if constructor failed to load.
		if (!$this->checkConstructorLoad()) {
			return false;
		}
		
		$this->calculateImageSizeRatio($width, $height);
		
		return $this->resizeNoRatio($this->new_img_width, $this->new_img_height);
	}// resizeRatio
	
	
	/**
	 * rotate image
	 * @param integer|string $degree degree of rotation. (0, 90, 180, 270). for php < 5.5, you cannot use flip hor, vrt, horvrt
	 * @return boolean
	 */
	public function rotate($degree = '90') 
	{
		// if constructor failed to load.
		if (!$this->checkConstructorLoad()) {
			return false;
		}
		
		// check degree
		$allowed_degree = array(0, 90, 180, 270, 'hor', 'vrt', 'horvrt');
		if (!in_array($degree, $allowed_degree)) {
			$degree = 90;
		}
		
		if (!is_int($degree) && $degree != 'hor' && $degree != 'vrt' && $degree != 'horvrt') {
			$degree = (int) $degree;
		}
		unset($allowed_degree);
		
		// if image content not set, set it up (this case just call crop without resize).
		if ($this->new_img_content == null) {
			$result = $this->setImageContent();
			
			if ($result === false) {
				$this->status = false;
				$this->status_msg = 'Failed to set image content.';
				return false;
			}
			
			$new_img_content = $this->new_img_content;
			unset($result);
		} else {
			$new_img_content = $this->new_img_object;
		}
		
		// rotate.
		if (is_int($degree)) {
			// rotate by degrees number
			switch ($this->original_img_ext) {
				case '1':
					// gif
				case '2':
					// jpg
					$rotate = imagerotate($new_img_content, $degree, 0);
					break;
				case '3':
					// png
					$rotate = imagerotate($new_img_content, $degree, imageColorAllocateAlpha($new_img_content, 0, 0, 0, 127));
					imagealphablending($rotate, false);
					imagesavealpha($rotate, true);
					break;
				default:
					// not allowed image type.
					$this->status = false;
					$this->status_msg = 'Unable to rotate this type of image.';
					return false;
					break;
			}
			
			$this->new_img_object = $rotate;
			$this->new_img_height = imagesy($rotate);
			$this->new_img_width = imagesx($rotate);
			
			// re-set original image height & width. because image was rotate
			$this->original_img_height = $this->new_img_height;
			$this->original_img_width = $this->new_img_width;
		} else {
			// if php version lower than 5.5
			if (phpversion() < 5.5) {
				$this->status = false;
				$this->status_msg = 'Unable to flip image using PHP older than 5.5.';
				return false;
			}
			
			if ($degree == 'hor') {
				$mode = IMG_FLIP_HORIZONTAL;
			} elseif ($degree == 'vrt') {
				$mode = IMG_FLIP_VERTICAL;
			} else {
				$mode = IMG_FLIP_BOTH;
			}
			
			// flip image.
			imageflip($new_img_content, $mode);
			
			unset($mode);
			
			$this->new_img_object = $new_img_content;
			$this->new_img_height = imagesy($new_img_content);
			$this->new_img_width = imagesx($new_img_content);
			
			// re-set original image height & width. because image was rotate
			$this->original_img_height = $this->new_img_height;
			$this->original_img_width = $this->new_img_width;
		}
		
		return true;
	}// rotate
	
	
	/**
	 * set image content (imagecreatefrom...();)
	 * @return boolean
	 */
	private function setImageContent() 
	{
		$new_image = imagecreatetruecolor($this->original_img_width, $this->original_img_height);
		
		if ($this->original_img_ext == '1') {
			// gif image
			// set color
			$transwhite = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
			imagefill($new_image, 0, 0, $transwhite);
			imagecolortransparent($new_image, $transwhite);
			
			$new_img_content = imagecreatefromgif($this->original_img_path);
			
			// copy original image to canvas to maintain transparent
			imagecopyresampled($new_image, $new_img_content, 0, 0, 0, 0, $this->original_img_width, $this->original_img_height, $this->original_img_width, $this->original_img_height);
			imagesavealpha($new_img_content, true);
		} elseif ($this->original_img_ext == '2') {
			// jpeg image
			$new_img_content = imagecreatefromjpeg($this->original_img_path);
		} elseif ($this->original_img_ext == '3') {
			// png image
			$new_img_content = imagecreatefrompng($this->original_img_path);
			
			imagealphablending($new_img_content, false);
			imagesavealpha($new_img_content, true);
		} else {
			// not allowed image type.
			$this->status = false;
			$this->status_msg = 'Unable to resize this type of image.';
			return false;
		}
		
		$this->new_img_content = $new_img_content;
		
		//imagedestroy( $new_image);
		
		unset($new_image, $new_img_content, $transwhite);
		
		return true;
	}// setImageContent
	
	
	/**
	 * set watermark image content
	 * @param type $wm_img_path
	 * @return boolean
	 */
	private function setWatermarkImageContent($wm_img_path = '') 
	{
		// get watermark size.
		list($wm_width, $wm_height, $wm_ext) = getimagesize($wm_img_path);
		
		if ($wm_width == null || $wm_height == null || $wm_ext == null) {
			return false;
		}
		
		// switch case of watermark file ext
		switch ($wm_ext) {
			case '1':
				// gif
				$watermark_read = imagecreatefromgif($wm_img_path);
				break;
			case '2':
				// jpeg
				$watermark_read = imagecreatefromjpeg($wm_img_path);
				break;
			case '3':
				// png
				$watermark_read = imagecreatefrompng($wm_img_path);
				break;
			default:
				// not allowed this file ext.
				$this->status = false;
				$this->status_msg = 'Unable to use this watermark image type.';
				return false;
				break;
		}
		
		$this->wm_img_content = $watermark_read;
		$this->wm_img_ext = $wm_ext;
		$this->wm_img_height = $wm_height;
		$this->wm_img_width = $wm_width;
		
		unset($wm_ext, $wm_height, $wm_width);
	}// setWatermarkImageContent
	
	
	/**
	 * set watermark image
	 * @param string $wm_img_path full path of watermark image file
	 * @param integer|string $wm_img_start_x integer of position or text 'left', 'center', 'right'
	 * @param integer|string $wm_img_start_y integer of position or text 'top', 'middle', 'bottom'
	 * @param integer $wm_img_opacity // cannot use right now. there is no function to opacity the image, to do it pixels by pixels is very slow.
	 * @return boolean
	 */
	public function watermarkImage($wm_img_path = '', $wm_img_start_x = 0, $wm_img_start_y = 0, $wm_img_opacity = 100) 
	{
		// if watermark image path not empty
		if ($wm_img_path != null) {
			// if watermark image file is not exists or not really is file.
			if (!file_exists($wm_img_path) || (file_exists($wm_img_path) && !is_file($wm_img_path))) {
				return false;
			}
		} else {
			return false;
		}
		
		// convert from number (ex. '10') to integer (ex. 10)
		if (is_numeric($wm_img_start_x)) {
			$wm_img_start_x = (int) $wm_img_start_x;
		}
		if (is_numeric($wm_img_start_y)) {
			$wm_img_start_y = (int) $wm_img_start_y;
		}
		
		// if start x or start y is not number.
		if (!is_numeric($wm_img_start_x) || !is_numeric($wm_img_start_y)) {
			// if image content not set, set it up (this case just call crop without resize).
			if ($this->new_img_content == null) {
				$result = $this->setImageContent();

				if ($result === false) {
					$this->status = false;
					$this->status_msg = 'Failed to set image content.';
					return false;
				}

				$new_img_content = $this->new_img_content;
				unset($result);
			} else {
				$new_img_content = $this->new_img_object;
			}
			
			if ($this->wm_img_content == null) {
				// initiate watermark image resource
				$this->setWatermarkImageContent($wm_img_path);
			}
			
			// if start x is not number
			switch ($wm_img_start_x) {
				case 'center':
					$image_width = imagesx($new_img_content);
					$watermark_width = imagesx($this->wm_img_content);

					$wm_img_start_x = $this->calculateStartXOfCenter($watermark_width, $image_width);

					unset($image_width, $watermark_width);
					break;
				case 'right':
					if ($this->new_img_width > ($this->wm_img_width+5)) {
						$wm_img_start_x = $this->new_img_width-($this->wm_img_width+5);
					} else {
						$wm_img_start_x = $this->new_img_width-$this->wm_img_width;
					}
					break;
				case 'left':
					$wm_img_start_x = 5;
					break;
				default:
					if (!is_numeric($wm_img_start_x)) {
						$wm_img_start_x = 5;
					}
					break;
			}
			
			// if start y is not number
			switch ($wm_img_start_y) {
				case 'top':
					$wm_img_start_y = 5;
					break;
				case 'middle':
					$image_height = imagesy($new_img_content);
					$watermark_height = imagesy($this->wm_img_content);

					$wm_img_start_y = $this->calculateStartXOfCenter($watermark_height, $image_height);

					unset($image_height, $watermark_height);
					break;
				case 'bottom':
					if ($this->new_img_height-($this->wm_img_height+5) > '0') {
						$wm_img_start_y = $this->new_img_height-($this->wm_img_height+5);
					} else {
						$wm_img_start_y = $this->new_img_height-$this->wm_img_height;
					}
					break;
				default:
					if (!is_numeric($wm_img_start_y)) {
						$wm_img_start_y = 5;
					}
					break;
			}
		}
		
		return $this->watermarkImageProcess($wm_img_path, $wm_img_start_x, $wm_img_start_y, $wm_img_opacity);
	}// watermarkImage
	
	
	/**
	 * apply watermark image.
	 * @param string $wm_img_path
	 * @param integer $wm_img_start_x
	 * @param integer $wm_img_start_y
	 * @param integer $wm_img_opacity
	 * @return boolean
	 */
	private function watermarkImageProcess($wm_img_path = '', $wm_img_start_x = 0, $wm_img_start_y = 0, $wm_img_opacity = 100) 
	{
		// if constructor failed to load.
		if (!$this->checkConstructorLoad()) {
			return false;
		}
		
		// if image content not set, set it up (this case just call crop without resize).
		if ($this->new_img_content == null) {
			$result = $this->setImageContent();
			
			if ($result === false) {
				$this->status = false;
				$this->status_msg = 'Failed to set image content.';
				return false;
			}
			
			$new_img_content = $this->new_img_content;
			unset($result);
		} else {
			if ($this->new_img_height != null || $this->new_img_width != null) {
				$new_img_content = $this->new_img_object;
			} else {
				$new_img_content = $this->new_img_content;
			}
		}
		
		if ($this->wm_img_content == null) {
			// initiate watermark image resource
			$this->setWatermarkImageContent($wm_img_path);
		}
		
		// switch case of watermark file ext
		switch ($this->wm_img_ext) {
			case '1':
				// gif
				imagecopy($new_img_content, $this->wm_img_content, $wm_img_start_x, $wm_img_start_y, 0, 0, $this->wm_img_width, $this->wm_img_height);
				break;
			case '2':
				// jpeg
				imagecopy($new_img_content, $this->wm_img_content, $wm_img_start_x, $wm_img_start_y, 0, 0, $this->wm_img_width, $this->wm_img_height);
				break;
			case '3':
				// png
				imagealphablending($new_img_content, true);// add this for transparent watermark thru image. if not add transparent from watermark can see thru background under image.
				imagecopy($new_img_content, $this->wm_img_content, $wm_img_start_x, $wm_img_start_y, 0, 0, $this->wm_img_width, $this->wm_img_height);
				break;
			default:
				// not allowed this file ext.
				$this->status = false;
				$this->status_msg = 'Unable to use this watermark image type.';
				return false;
				break;
		}
		
		// clear
		if ($this->wm_img_content != null) {
			imagedestroy($this->wm_img_content);
		}
		
		unset($wm_ext, $wm_height, $wm_width);
		
		$this->new_img_object = $new_img_content;
		
		return true;
	}// watermarkImageProcess
	
	
	/**
	 * set watermark text
	 * @param string $wm_txt_text watermark text
	 * @param string $wm_txt_font_path true type font path
	 * @param integer|string $wm_txt_start_x start position x. number or [left, center, right]
	 * @param integer|string $wm_txt_start_y start position y. number or [top, middle, bottom]
	 * @param integer $wm_txt_font_size font size
	 * @param string $wm_txt_font_color font color [black, white, transwhitetext]
	 * @param integer $wm_txt_font_alpha alpha number (0-127)
	 * @return boolean
	 */
	public function watermarkText($wm_txt_text = '', $wm_txt_font_path = '', $wm_txt_start_x = 0, $wm_txt_start_y = 0, $wm_txt_font_size = 10, $wm_txt_font_color = 'transwhitetext', $wm_txt_font_alpha = 60) 
	{
		// if no text or font file
		if ($wm_txt_text == null || $wm_txt_font_path == null) {
			return false;
		}
		
		// if constructor failed to load.
		if (!$this->checkConstructorLoad()) {
			return false;
		}
		
		// if image content not set, set it up (this case just call crop without resize).
		if ($this->new_img_content == null) {
			$result = $this->setImageContent();
			
			if ($result === false) {
				$this->status = false;
				$this->status_msg = 'Failed to set image content.';
				return false;
			}
			
			$new_img_content = $this->new_img_content;
			unset($result);
		} else {
			if ($this->new_img_height != null || $this->new_img_width != null) {
				$new_img_content = $this->new_img_object;
			} else {
				$new_img_content = $this->new_img_content;
			}
		}
		
		// find text width and height
		// height must +5 to allow thai characters show full line
		$wm_txt_height = $wm_txt_font_size+5;
		$wm_txt_width = round((($wm_txt_font_size/2.5))*mb_strlen($wm_txt_text));
		
		// convert from number (ex. '10') to integer (ex. 10)
		if (is_numeric($wm_txt_start_x)) {
			$wm_txt_start_x = (int) $wm_txt_start_x;
		}
		if (is_numeric($wm_txt_start_y)) {
			$wm_txt_start_y = (int) $wm_txt_start_y;
		}
		
		// if start x or start y is not number
		if (!is_numeric($wm_txt_start_x) || !is_numeric($wm_txt_start_y)) {
			// if start x is not number
			switch ($wm_txt_start_x) {
				case 'center':
					$image_width = imagesx($new_img_content);
					$watermark_width = $wm_txt_width;

					$wm_txt_start_x = $this->calculateStartXOfCenter($watermark_width, $image_width);

					unset($image_width, $watermark_width);
					break;
				case 'right':
					$image_width = imagesx($new_img_content);
					$wm_txt_start_x = $image_width-$wm_txt_width;
					
					unset($image_width);
					break;
				case 'left':
					$wm_txt_start_x = 5;
					break;
				default:
					if (!is_numeric($wm_txt_start_x)) {
						$wm_txt_start_x = 5;
					}
					break;
			}
			
			// if start y is not number
			switch ($wm_txt_start_y) {
				case 'top':
					$wm_txt_start_y = 5;
					break;
				case 'middle':
					$image_height = imagesy($new_img_content);
					$watermark_height = $wm_txt_height;

					$wm_txt_start_y = $this->calculateStartXOfCenter($watermark_height, $image_height);

					unset($image_height, $watermark_height);
					break;
				case 'bottom':
					$image_height = imagesy($new_img_content);
					if ($image_height-($wm_txt_height+5) > '0') {
						$wm_txt_start_y = $image_height-($wm_txt_height+5);
					} else {
						$wm_txt_start_y = $image_height-($wm_txt_height+5);
					}
					unset($image_height);
					break;
				default:
					if (!is_numeric($wm_txt_start_y)) {
						$wm_txt_start_y = 5;
					}
					break;
			}
		}
		
		// create watermark text canvas
		$wm_txt = imagecreatetruecolor($wm_txt_width, $wm_txt_height);
		imagealphablending($wm_txt, false);
		imagesavealpha($wm_txt, true);
		
		// check watermark text font alpha must be 0-127
		$wm_txt_font_alpha = (int) $wm_txt_font_alpha;
		if ($wm_txt_font_alpha < 0 || $wm_txt_font_alpha > 127) {
			$wm_txt_font_alpha = 60;
		}
		
		// set color
		$black = imagecolorallocate($wm_txt, 0, 0, 0);
		$white = imagecolorallocate($wm_txt, 255, 255, 255);
		$transwhite = imagecolorallocatealpha($wm_txt, 255, 255, 255, 127);// set color transparent white
		$transwhitetext = imagecolorallocatealpha($wm_txt, 255, 255, 255, $wm_txt_font_alpha);
		
		// set text
		imagefill($wm_txt, 0, 0, $transwhite);
		// y coords below must -5 to allow something like p, g show full size
		imagettftext($wm_txt, $wm_txt_font_size, 0, 0, $wm_txt_height-5, $$wm_txt_font_color, $wm_txt_font_path, $wm_txt_text);
		//imagecolortransparent($wm_txt, $transwhite);
		
		// copy text to image
		imagecopy($new_img_content, $wm_txt, $wm_txt_start_x, $wm_txt_start_y, 0, 0, $wm_txt_width, $wm_txt_height);
		
		imagedestroy($wm_txt);
		
		$this->new_img_object = $new_img_content;
		
		return true;
	}// watermarkText
	
	
	// output methods ========================================================
	/**
	 * save image to file
	 * @param string $file_name
	 * @param string $file_ext
	 * @return boolean
	 */
	public function save($file_name = '', $file_ext = '') 
	{
		// if constructor failed to load.
		if (!$this->checkConstructorLoad()) {
			return false;
		}
		
		// check file name
		if ($file_name == null) {
			$this->status = false;
			$this->status_msg = 'Please enter file name.';
			return false;
		}
		
		$file_ext = strtolower($file_ext);
		
		// check file ext and determine automatically
		if ($file_ext == null) {
			$get_file_ext = strtolower($this->getFileExt($file_name));
			
			if ($get_file_ext == 'gif') {
				// gif image
				$file_ext = 'gif';
			} elseif ($get_file_ext == 'jpg' || $get_file_ext == 'jpeg') {
				// jpeg image
				$file_ext = 'jpg';
			} elseif ($get_file_ext == 'png') {
				// png image
				$file_ext = 'png';
			} else {
				// not allowed image type.
				$this->status = false;
				$this->status_msg = 'Unable to save this type of image.';
				return false;
			}
			
			unset($get_file_ext);
		}
		
		// if file ext is jpg or jpeg
		if ($file_ext == 'jpeg') {
			$file_ext = 'jpg';
		}
		
		// save difference in each file type.
		if ($file_ext == 'gif') {
			if ($this->original_img_ext == '3') {
				// in png out gif need special fill
				// create canvas
				$img = imagecreatetruecolor($this->new_img_width, $this->new_img_height);
				// set color
				$white = imagecolorallocate($img, 255, 255, 255);
				// fill canvas with color
				imagefill($img, 0, 0, $white);
				imagecopy($img, $this->new_img_object, 0, 0, 0, 0, $this->new_img_width, $this->new_img_height);
				
				// clear
				imagedestroy($img);
				
				unset($img, $white);
			}
			
			$result = imagegif($this->new_img_object, $file_name);
		} elseif ($file_ext == 'jpg') {
			if ($this->original_img_ext == '3') {
				// create canvas
				$img = imagecreatetruecolor($this->new_img_width, $this->new_img_height);
				// set color
				$white = imagecolorallocate($img, 255, 255, 255);
				// fill canvas with color
				imagefill($img, 0, 0, $white);
				imagecopy($img, $this->new_img_object, 0, 0, 0, 0, $this->new_img_width, $this->new_img_height);
				
				// clear
				imagedestroy($img);
				
				unset($img, $white);
			}
			
			// verify jpeg quality
			$this->jpg_quality = (int) $this->jpg_quality;
			if ($this->jpg_quality > 100 || $this->jpg_quality < 0) {
				$this->jpg_quality = 100;
			}
			
			$result = imagejpeg($this->new_img_object, $file_name, $this->jpg_quality);
		} elseif ($file_ext == 'png') {
			// verify png quality
			$this->png_quality = (int) $this->png_quality;
			if ($this->png_quality > 9 || $this->png_quality < 0) {
				$this->png_quality = 0;
			}
			
			$result = imagepng($this->new_img_object, $file_name, $this->png_quality);
		} else {
			// not allowed image type.
			$this->status = false;
			$this->status_msg = 'Unable to save this type of image.';
			return false;
		}
		
		$this->clear();
		
		if ($result !== false) {
			return true;
		}
		return false;
	}// save
	
	
	/**
	 * show image
	 * @param string $file_ext
	 * @return string image content
	 */
	public function show($file_ext = '') 
	{
		// if constructor failed to load.
		if (!$this->checkConstructorLoad()) {
			return false;
		}
		
		$file_ext = strtolower($file_ext);
		
		// check file ext and determine automatically
		if ($file_ext == null) {
			if ($this->original_img_ext == '1') {
				// gif image
				$file_ext = 'gif';
			} elseif ($this->original_img_ext == '2') {
				// jpeg image
				$file_ext = 'jpg';
			} elseif ($this->original_img_ext == '3') {
				// png image
				$file_ext = 'png';
			} else {
				// not allowed image type.
				$this->status = false;
				$this->status_msg = 'Unable to save this type of image.';
				return false;
			}
		}
		
		// if file ext is jpg or jpeg
		if ($file_ext == 'jpeg') {
			$file_ext = 'jpg';
		}

		if ($file_ext == 'gif') {
			if ($this->original_img_ext == "3") {
				// in png out gif need special fill
				$img = imagecreatetruecolor($this->new_img_width, $this->new_img_height);// create canvas
				$white = imagecolorallocate($img, 255, 255, 255);// set color
				imagefill($img, 0, 0, $white);// fill canvas with color
				imagecopy($img, $this->new_img_object, 0, 0, 0, 0, $this->new_img_width, $this->new_img_height);
				$this->new_img_object = $img;
			}

			imagegif($this->new_img_object);

			// clear
			if (isset($img)) {
				imagedestroy($img);
			}

			unset($img, $white);
		} elseif ($file_ext == 'jpg') {
			if ($this->original_img_ext == "3") {
				// in png out jpg need special fill
				// create canvas
				$img = imagecreatetruecolor($this->new_img_width, $this->new_img_height);
				// set color
				$white = imagecolorallocate($img, 255, 255, 255);
				// fill canvas with color
				imagefill($img, 0, 0, $white);
				imagecopy($img, $this->new_img_object, 0, 0, 0, 0, $this->new_img_width, $this->new_img_height);
				$this->new_img_object = $img;
			}
			
			// verify jpeg quality
			$this->jpg_quality = (int) $this->jpg_quality;
			if ($this->jpg_quality > 100 || $this->jpg_quality < 0) {
				$this->jpg_quality = 100;
			}

			imagejpeg($this->new_img_object, '', $this->jpg_quality);

			// clear
			if (isset($img)) {
				imagedestroy($img);
			}

			unset($img, $white);
		} elseif ($file_ext == 'png') {
			// verify png quality
			$this->png_quality = (int) $this->png_quality;
			if ($this->png_quality > 9 || $this->png_quality < 0) {
				$this->png_quality = 0;
			}
			
			imagepng($this->new_img_object, '', $this->png_quality);
		} else {
			// not allowed image type.
			$this->status = false;
			$this->status_msg = 'Unable to save this type of image.';
			return false;
		}
		
		$this->clear();
	}// show
	// output methods ========================================================


}


?>