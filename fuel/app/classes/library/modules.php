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


    /**
     * store modules container path.
     * @var array 
     */
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
                            if (is_array(call_user_func_array(array($class, '_define_permission'), array()))) {
                                $output = array_merge($output, call_user_func_array(array($class, '_define_permission'), array()));
                            }
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
     * easily include module files from module paths.
     * 
     * @param string|array $files
     * @return boolean
     */
    public static function includeModuleFiles($files = array())
    {
        $module_path = \Config::get('module_paths');
        
        foreach ($module_path as $a_path) {
            if (is_array($files)) {
                foreach ($files as $file) {
                    if (file_exists($a_path . $file)) {
                        include_once $a_path . $file;
                    }
                }
            } elseif (is_string($files)) {
                if (file_exists($a_path . $files)) {
                    include_once $a_path . $files;
                }
            } else {
                $files = '';
                unset($a_path, $module_path);
                return false;
            }
        }
        
        $files = '';
        unset($a_path, $file, $module_path);
        return true;
    }// includeModuleFiles


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
                $config['basedir'] = $module_path;
                $file_area = \File::forge($config);
                unset($config);
                
                $files = \File::read_dir($module_path, 1,
                    array(
                        '!^\.',
                    ),
                    $file_area
                );
                unset($file_area);

                foreach ($files as $file => $subs) {
                    $file = mb_substr($file, 0, mb_strlen($file)-1);
                    if (is_dir($module_path . $file)) {
                            if (file_exists($module_path . $file . DS . 'classes' . DS . $file . 'admin.php') && is_file($module_path . $file . DS . 'classes' . DS . $file . 'admin.php')) {
                                $class_name_with_namespace = '\\' . ucfirst($file) . '\\' . ucfirst($file) . 'Admin' ;

                                // load module to check class exists, method exists.
                                \Module::load($file);

                                if (class_exists($class_name_with_namespace)) {
                                    if (method_exists($class_name_with_namespace, 'admin_navbar')) {
                                        $obj = new $class_name_with_namespace;
                                        $output .= "\t" . call_user_func_array(array($obj, 'admin_navbar'), array()) . "\n";
                                    }
                                }
                        }// endif file exists.
                    }// endif is dir
                }// endforaech;
                unset($class_name_with_namespace, $file, $files, $obj, $subs);
            }// endforaech;

            unset($module_path);

            if ($output != null) {
                $output = "\n" . '<ul>' . "\n" . $output . '</ul>' . "\n";
            }

            return $output;
        }

        return false;
    }// listAdminNavbar


    /**
     * list modules that enabled.
     * 
     * @todo [fuelstart][module] this will be change to list modules that enabled from module manager later.
     * @return array return array of modules folder name.
     */
    public function listEnabledModules()
    {
        return $this->listModulesFromFileSys();
    }// listEnabledModules


    /**
     * list all modules from file system.
     * 
     * @return array return array of modules folder name.
     */
    public function listModulesFromFileSys()
    {
        $output = [];
        $i = 0;

        if (is_array($this->module_paths) && !empty($this->module_paths)) {
            // loop module paths
            foreach ($this->module_paths as $module_path) {
                $config['basedir'] = $module_path;
                $file_area = \File::forge($config);
                unset($config);
                
                $files = \File::read_dir($module_path, 1,
                    array(
                        '!^\.',
                    ),
                    $file_area
                );
                unset($file_area);
                
                foreach ($files as $file => $subs) {
                    // remove back slash trail.
                    $file = mb_substr($file, 0, mb_strlen($file)-1);
                    
                    if (is_dir($module_path . $file)) {
                        if (file_exists($module_path . $file . DS . $file . '_module.php') && is_file($module_path . $file . DS . $file . '_module.php')) {
                            $output[$i]['module_path'] = $module_path;
                            $output[$i]['module_system_name'] = $file;
                            
                            $i++;
                        }
                    }
                }
                unset($file, $files, $subs);
            }// endforeach;
            unset($module_path);
        }// endif $this->module_paths

        unset($i);
        return $output;
    }// listModulesFromFileSys


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
                $config['basedir'] = $module_path;
                $file_area = \File::forge($config);
                unset($config);
                
                $files = \File::read_dir($module_path, 1,
                    array(
                        '!^\.',
                    ),
                    $file_area
                );
                unset($file_area);

                foreach ($files as $file => $subs) {
                    // remove back slash trail.
                    $file = mb_substr($file, 0, mb_strlen($file)-1);
                    
                    if (is_dir($module_path . $file)) {
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
                        }// endif file exists.
                    }// endif is dir
                }// endforaech;
                unset($class_name_with_namespace, $file, $files, $subs);
            }// endforaech;

            unset($module_path);

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
