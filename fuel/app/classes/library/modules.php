<?php
/**
 * Modules
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

namespace Library;

class Modules
{
	
	
	public $module_paths;


	public function __construct()
	{
		// class constructor
		
		// set module paths
		$this->module_paths = \Config::get('module_paths');
	}// __construct
	
	
	/**
	 * fetch permission from specific module
	 * 
	 * @param string $module_system_name
	 * @return mixed
	 */
	public function fetchPermissionModule($module_system_name = '') 
	{
		if ($module_system_name == null) {return false;}
		
		$output = array();
		
		if (is_array($this->module_paths) && !empty($this->module_paths)) {
			// loop module paths
			foreach ($this->module_paths as $module_path) {
				if (file_exists($module_path . $module_system_name . DS . 'classes' . DS . $module_system_name . 'admin.php') && is_file($module_path . $module_system_name . DS . 'classes' . DS . $module_system_name . 'admin.php')) {
					$class_name_with_namespace = '\\' . ucfirst($module_system_name) . '\\' . ucfirst($module_system_name) . 'Admin' ;
					
					// load module to check class exists, method exists.
					\Module::load($module_system_name);
					
					if (class_exists($class_name_with_namespace)) {
						if (method_exists($class_name_with_namespace, '_define_permission')) {
							$class = new $class_name_with_namespace;
							$output = array_merge($output, call_user_func_array(array($class, '_define_permission'), array()));
						}
					}
				}
			}
		}
		
		return $output;
	}// fetchPermissionModule


	/**
	 * forge
	 * 
	 * @return object
	 */
	public static function forge() 
	{
		return new self();
	}// forge
	
	
	/**
	 * check that module has defined permission
	 * 
	 * @param string $module_system_name
	 * @return boolean
	 */
	public function hasPermission($module_system_name = '') 
	{
		if ($module_system_name == null) {
			return false;
		}
		
		if (is_array($this->module_paths) && !empty($this->module_paths)) {
			// loop module paths
			foreach ($this->module_paths as $module_path) {
				if (file_exists($module_path . $module_system_name . DS . 'classes' . DS . $module_system_name . 'admin.php') && is_file($module_path . $module_system_name . DS . 'classes' . DS . $module_system_name . 'admin.php')) {
					$class_name_with_namespace = '\\' . ucfirst($module_system_name) . '\\' . ucfirst($module_system_name) . 'Admin' ;
					
					// load module to check class exists, method exists.
					\Module::load($module_system_name);
					
					// check if class exists
					if (class_exists($class_name_with_namespace)) {
						if (method_exists($class_name_with_namespace, '_define_permission')) {
							unset($class_name_with_namespace, $module_path);
							
							return true;
						}
					}
				}
			}
		}
		unset($class_name_with_namespace, $module_path);
		
		return false;
	}// hasPermission
	
	
	/**
	 * list admin navbar from module's admin file.
	 * 
	 * @return string|boolean
	 */
	public function listAdminNavbar() 
	{
		if (is_array($this->module_paths) && !empty($this->module_paths)) {
			$output = '';
			
			// loop module paths
			foreach ($this->module_paths as $module_path) {
				if ($handle = opendir($module_path)) {
					while (false != ($file = readdir($handle))) {
						if ($file != '.' && $file != '..' && is_dir($module_path . $file)) {
							if (file_exists($module_path . $file . DS . 'classes' . DS . $file . 'admin.php') && is_file($module_path . $file . DS . 'classes' . DS . $file . 'admin.php')) {
								$class_name_with_namespace = '\\' . ucfirst($file) . '\\' . ucfirst($file) . 'Admin' ;
								
								// load module to check class exists, method exists.
								\Module::load($file);
								
								if (class_exists($class_name_with_namespace)) {
									if (method_exists($class_name_with_namespace, '_define_permission')) {
										$obj = new $class_name_with_namespace;
										$output .= "\t" . call_user_func_array(array($obj, 'admin_navbar'), array()) . "\n";
									}
								}
							}
						}
					}
					
					closedir($handle);
				}
			}
			
			unset($class_name_with_namespace, $file, $handle, $module_path, $obj);
			
			if ($output != null) {
				$output = '<ul>' . "\n" . $output . '</ul>' . "\n";
			}
			
			return $output;
		}
		
		return false;
	}// listAdminNavbar
	
	
	/**
	 * list modules that has permission
	 * 
	 * @return mixed
	 */
	public function listModulesWithPermission() 
	{
		if (is_array($this->module_paths) && !empty($this->module_paths)) {
			$output = array();
			$i = 0;
			
			// loop module paths
			foreach ($this->module_paths as $module_path) {
				if ($handle = opendir($module_path)) {
					while (false != ($file = readdir($handle))) {
						if ($file != '.' && $file != '..' && is_dir($module_path . $file)) {
							if (file_exists($module_path . $file . DS . 'classes' . DS . $file . 'admin.php') && is_file($module_path . $file . DS . 'classes' . DS . $file . 'admin.php')) {
								$class_name_with_namespace = '\\' . ucfirst($file) . '\\' . ucfirst($file) . 'Admin' ;
								
								// load module to check class exists, method exists.
								\Module::load($file);
								
								if (class_exists($class_name_with_namespace)) {
									if (method_exists($class_name_with_namespace, '_define_permission')) {
										// get module name.
										$info = $this->readModuleMetadata($module_path . $file . DS . $file . '_module.php');
										if ($info['name'] == null) {
											$output[$i]['module_name'] = $file;
											$output[$i]['module_system_name'] = $file;
										} else {
											$output[$i]['module_name'] = $info['name'];
											$output[$i]['module_system_name'] = $file;
										}
										
										$i++;
									}
								}
							}
						}
					}
					
					closedir($handle);
				}
			}
			
			unset($class_name_with_namespace, $file, $handle, $i, $info, $module_path);
			
			return $output;
		}
		
		return false;
	}// listModulesWithPermission
	
	
	/**
	 * read module metadata
	 * @param string $module_item file path to [module name]_module.php
	 * @return mixed 
	 */
	public function readModuleMetadata($module_item = '') 
	{
		if (empty($module_item)) {return null;}
		
		// get module info.
		if (file_exists($module_item)) {
			$p_data = \File::read($module_item, true);
			preg_match ('|Module Name:(.*)$|mi', $p_data, $name);
			preg_match ('|Module URL:(.*)$|mi', $p_data, $url);
			preg_match ('|Version:(.*)|i', $p_data, $version);
			preg_match ('|Description:(.*)$|mi', $p_data, $description);
			preg_match ('|Author:(.*)$|mi', $p_data, $author_name);
			preg_match ('|Author URL:(.*)$|mi', $p_data, $author_url);
		}
		
		$output['name'] = (isset($name[1]) ? trim($name[1]) : '');
		$output['url'] = (isset($url[1]) ? trim($url[1]) : '');
		$output['version'] = (isset($version[1]) ? trim($version[1]) : '');
		$output['description'] = (isset($description[1]) ? trim($description[1]) : '');
		$output['author_name'] = (isset($author_name[1]) ? trim($author_name[1]) : '');
		$output['author_url'] = (isset($author_url[1]) ? trim($author_url[1]) : '');
		
		unset($p_data, $name, $url, $version, $description, $author_name, $author_url);
		
		return $output;
	}// readModuleMetadata
	
	
	/**
	 * read module metadata from just module name.
	 * 
	 * @param string $module module system name (module folder name)
	 * @return mixed
	 */
	public function readModuleMetadataFromModuleName($module = '') 
	{
		if ($module == null) {return null;}
		
		if (is_array($this->module_paths) && !empty($this->module_paths)) {
			// loop module paths
			foreach ($this->module_paths as $module_path) {
				if (file_exists($module_path . $module . DS . $module . '_module.php') && is_file($module_path . $module . DS . $module . '_module.php')) {
					return $this->readModuleMetadata($module_path . $module . DS . $module . '_module.php');
				}
			}
		}
	}// readModuleMetadataFromModuleName


}

