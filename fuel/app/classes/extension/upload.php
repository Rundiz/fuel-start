<?php
/**
 * Upload class use codeguy/upload
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/codeguy/Upload URL of Codeguy/Upload
 * @package FuelStart
 * 
 */

namespace Extension;

class Upload
{
	
	
	// validation rules. (ext, mime, size)
	// allowed extension. (array of extensions)
	public $allowed_ext;
	
	// allowed mimt type. (array of mime types)
	// MimeType List => http://www.webmaster-toolkit.com/mime-types.shtml
	public $allowed_mime;
	// automatic validate mime match extension or not. true if yes.
	// for auto validate mime, please set allowed_mime to null and set auto_validate_mime to true.
	public $auto_validate_mime = true;
	
	// allowed min and max file size. unit is B, K, M, G. example: 5M means 5 Megabytes
	public $allowed_min_size = 0;
	public $allowed_max_size;
	
	public $directory = '.';
	
	// new file name if you want to set new name. (name only. not extension)
	public $new_name;
	public $overwrite = false;
	// automatic add number to file if not allowed overwrite and file exists.
	public $name_auto_number = true;
	// if safe file name only. the file name that is not in the safe case (non english, symbols, etc..) will be set new name as random aplha.+md5()
	public $safe_name_only = true;
	
	protected $u_file;
	protected $u_storage;
	protected $upload_data; // uploaded data.
	
	
	public function __construct()
	{
		include_once APPPATH . 'vendor' . DS . 'codeguy' . DS . 'upload' . DS . 'src' . DS . 'Upload' . DS . 'Autoloader.php';
		
		$autoloader = new \Upload\Autoloader();
		$autoloader->register();
	}// __construct
	
	
	public function __destruct()
	{
		$this->unregisterAutoload();
		
		$this->u_file = '';
		$this->u_storage = '';
		$this->upload_data = '';
	}// __destruct
	
	
	/**
	 * create directory if not exists.
	 */
	public function createDirectoryIfNotExists() 
	{
		if (!file_exists($this->directory) || (file_exists($this->directory) && !is_dir($this->directory))) {
			// folder is not exists, or exists but is not folder (it is a file)
			$target_directory = str_replace('\\', '/', $this->directory);
			$target_directory = rtrim($target_directory, '/');
			$directory_exp = explode('/', $target_directory);
			
			$directory_before_target = '';
			foreach ($directory_exp as $a_dir) {
				if (end($directory_exp) != $a_dir) {
					$directory_before_target .= $a_dir . '/';
				}
			}
			
			\File::create_dir($directory_before_target, end($directory_exp), 0777);
			
			unset($directory_before_target, $directory_exp, $target_directory);
		}
	}// createDirectoryIfNotExists
	
	
	/**
	 * check safe name.
	 * 
	 * @param string $name
	 * @return string
	 */
	public function checkSafeName($name = '') 
	{
		if ($name != null) {
			if (!preg_match("/^[A-Za-z 0-9~_\-.+={}\"'()]+$/", $name)) {
				return md5(\Str::random('alnum', 5) . time()) . '.' . $this->u_file->getExtension();
			} else {
				return $name;
			}
		}
		
		return null;
	}// checkSafeName
	
	
	/**
	 * loop display errors with specific html tag.
	 * 
	 * @param string $open_tag
	 * @param string $close_tag
	 * @param boolean $match_fuelphp_error
	 * @return string
	 */
	public function displayErrors($open_tag = '<div>', $close_tag = '</div>', $match_fuelphp_error = true) 
	{
		if (!is_bool($match_fuelphp_error)) {
			$match_fuelphp_error = true;
		}
		
		$errors = $this->getErrors($match_fuelphp_error);
		$output = '';
		
		if (is_array($errors) && !empty($errors)) {
			foreach ($errors as $error) {
				$output .= $open_tag;
				$output .= $error;
				$output .= $close_tag;
			}
		}
		
		unset($error, $errors);
		
		return $output;
	}// displayErrors
	
	
	/**
	 * get all uploaded data
	 * 
	 * @return array
	 */
	public function getData() 
	{
		return $this->upload_data;
	}// getData
	
	
	/**
	 * get all errors
	 * 
	 * @return array
	 */
	public function getErrors($match_fuelphp_error = true) 
	{
		if ($match_fuelphp_error === true) {
			$fuelphp_upload_errors = \Lang::load('upload', 'upload');
			
			// number of message array below must match in FuelPHP upload language to use it translated properly.
			$codeguy_upload_errors = array(
				UPLOAD_ERR_OK,
				'The uploaded file exceeds the upload_max_filesize directive in php.ini', // \src\Upload\File.php
				'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form', // \src\Upload\File.php
				'The uploaded file was only partially uploaded', // \src\Upload\File.php
				'No file was uploaded', // \src\Upload\File.php
				'Missing a temporary folder', // \src\Upload\File.php
				'Failed to write file to disk', // \src\Upload\File.php
				'A PHP extension stopped the file upload', // \src\Upload\File.php
				'File size is too large', // \src\Upload\Validation\Size.php
				sprintf('Invalid file extension. Must be one of: %s', (is_array($this->allowed_ext) ? implode(', ', $this->allowed_ext) : $this->allowed_ext)), // \src\Upload\Validation\Extension.php
				sprintf('Invalid file extension. Must be one of: %s', (is_array($this->allowed_ext) ? implode(', ', $this->allowed_ext) : $this->allowed_ext)), // \src\Upload\Validation\Extension.php
				'type not allowed', // none in codeguy/upload
				'type not allowed', // none in codeguy/upload
				'Invalid mimetype', // \src\Upload\Validation\Mimetype.php
				'Invalid mimetype', // \src\Upload\Validation\Mimetype.php
				'max file name length', // none in codeguy/upload
				'Directory does not exist', // \src\Upload\Storage\FileSystem.php
				'File already exists', // \src\Upload\Storage\FileSystem.php
				'Directory is not writable', // \src\Upload\Storage\FileSystem.php
			);
			
			return str_replace($codeguy_upload_errors, $fuelphp_upload_errors, $this->u_file->getErrors());
		} else {
			return $this->u_file->getErrors();
		}
	}// getErrors
	
	/**
	 * get mime types list from extension
	 * 
	 * @param string $ext file extension without dot.
	 * @return array mime types of this extension.
	 */
	public function getMimeTypesListFromExtension($ext = '') 
	{
		if ($ext == null) {
			return array();
		}
		
		\Config::load('mimes', 'mimes');
		$mimes = \Config::get('mimes');
		
		if (isset($mimes[$ext])) {
			return $mimes[$ext];
		}
		
		unset($mimes);
		
		return array();
	}// getMimeTypesListFromExtension
	
	
	/**
	 * get new name with numbering if file is already exists.
	 * 
	 * @return string
	 */
	public function getNewFileName() 
	{
		if ($this->new_name != null) {
			$file_name = $this->new_name;
		} else {
			$file_name = $this->u_file->getName();
		}
		
		$file_ext = $this->u_file->getExtension();
		
		$i = 0;
		$found = true;
		
		do {
			$new_name = $file_name . ($i > 0 ? '(' . $i . ')' : '');
			
			if (file_exists($this->directory . $new_name . '.' . $file_ext) && is_file($this->directory . $new_name . '.' . $file_ext)) {
				$found = true;
				// prevent too many loop.
				if ($i > 1000) {
					$found = false;
					$file_name = $file_name . '-' . \Str::random('alnum');
				}
			} else {
				$file_name = $new_name;
				$found = false;
			}
			
			$i++;
		} while ($found === true);
		
		unset($file_ext, $found, $i, $new_name);
		
		return $file_name;
	}// getNewFileName
	
	
	/**
	 * unregister autoload
	 * 
	 * @return boolean boolean of spl autoload unregister
	 */
	public function unregisterAutoload() 
	{
		return spl_autoload_unregister(array(new \Upload\Autoloader(), 'autoload'));
	}// unregisterAutoload
	
	
	/**
	 * upload. move uploaded file to target directory with validation.
	 * 
	 * @param string $input_field
	 * @return boolean
	 */
	public function upload($input_field = '') 
	{
		if ($input_field == null) {
			$input_field = key($_FILES);
		}
		
		// create directory if not exists.
		$this->createDirectoryIfNotExists();
		
		// new upload instances
		$this->u_storage = new \Upload\Storage\FileSystem($this->directory, $this->overwrite);
		$this->u_file = new \Upload\File($input_field, $this->u_storage);
		
		// check for safe name. (english, number, dash-, underscore_, and url safe characters only.)
		if ($this->safe_name_only === true) {
			$name = $this->checkSafeName($this->u_file->getNameWithExtension());
			
			if ($name != $this->u_file->getNameWithExtension()) {
				$this->new_name = str_replace('.' . $this->u_file->getExtension(), '', $name);
				$this->u_file->setName($this->new_name);
			}
			
			unset($name);
		}
		
		// check name and set new name. -------------------------------------------------------
		if ($this->overwrite === false && $this->name_auto_number == true) {
			if ($this->new_name != null) {
				$file_exist_check = $this->new_name . '.' . $this->u_file->getExtension();
			} else {
				$file_exist_check = $this->u_file->getNameWithExtension();
			}
			
			if ((file_exists($this->directory . $file_exist_check) && is_file($this->directory . $file_exist_check)) || $this->new_name != null) {
				$this->u_file->setName($this->getNewFileName());
			}
			
			unset($file_exist_check);
		}
		
		// validations. ---------------------------------------------------------------------------------
		if ($this->allowed_ext != null) {
			$validation[] = new \Upload\Validation\Extension($this->allowed_ext);
		}
		
		if ($this->allowed_max_size != null) {
			$validation[] = new \Upload\Validation\Size($this->allowed_max_size, $this->allowed_min_size);
		}
		
		if ($this->allowed_mime != null) {
			$validation[] = new \Upload\Validation\Mimetype($this->allowed_mime);
		} elseif ($this->auto_validate_mime === true) {
			$validation[] = new \Upload\Validation\Mimetype($this->getMimeTypesListFromExtension($this->u_file->getExtension()));
		}
		
		if (isset($validation) && is_array($validation)) {
			$this->u_file->addValidations($validation);
		}
		
		// we need to collect data before call upload() method. if call after upload() method, the error may occur.
		$upload_data['dimensions'] = ($this->u_file->getPathname() != null ? $this->u_file->getDimensions() : array('width' => '', 'height' => '')); // width, height (add @ to prevent error when reading false image, example text file with .jpg extension).
		$upload_data['extension'] = $this->u_file->getExtension(); // ext without dot.
		$upload_data['md5'] = ($this->u_file->getPathname() != null ? $this->u_file->getMd5() : ''); // md5 file.
		$upload_data['mimetype'] = ($this->u_file->getPathname() != null ? $this->u_file->getMimetype() : ''); // mime type
		$upload_data['nameonly'] = $this->u_file->getName(); // file name only. no extension.
		$upload_data['name'] = $this->u_file->getNameWithExtension(); // file name with .extension
		$upload_data['size'] = $this->u_file->getSize(); // size in byte.
		$this->upload_data = $upload_data;
		unset($upload_data);
		
		// try to upload and return upload status, if error occur return false.
		try {
			$result = $this->u_file->upload();
			
			// almost done, unregister autoload
			$this->unregisterAutoload();
			
			return $result;
		} catch (\Exception $e) {
			// almost done, unregister autoload
			$this->unregisterAutoload();
			
			return false;
		}
	}// upload


}

