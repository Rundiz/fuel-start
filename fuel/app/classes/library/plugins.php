<?php

namespace Library;

/**
 * plugins library for running action and filter plugins to hook into main code without rewrite the main code.<br>
 * this plugin library work with module, it called "module plug".<br>
 * <br>
 * to create your module's plug or plugin. you must have [module folder name]/[module folder name]_module.php file.<br>
 * in this file you must have its class. for example. module: media, module file: media/media_module.php, module plug class: Media_Module {}<br>
 * inside module plugin class, write hook action/filter as method in it. for example. there is ModBody filter, write this method into your class. public function filterModBody() {}<br>
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 */
class Plugins extends \Library\Modules
{


    /**
     * store result of hooked actions
     * 
     * @var array array list of hooked actions. 
     */
    public $did_action_result = [];


    /**
     * store modules in the modules folders.
     * 
     * @todo [fuelstart][module_plug] this will be change to list modules that enabled from module manager later.
     * @var array array list of enabled modules. the key must have module_path and module_system_name of each enabled module
     */
    public $enabled_modules = [];


    /**
     * store filtered data from hook plugins. more filters more changes.
     * 
     * @var mixed 
     */
    public $filtered_data;


    /**
     * track changed of filters since original data.
     * 
     * @var array 
     */
    public $original_data = [];


    /**
     * track plugins that were called into hook actions/filters.
     * 
     * @var array 
     */
    public $track_hook_plugins = [];
    public $track_hook_actions = [];
    public $track_hook_filters = [];


    public function __construct() {
        parent::__construct();

        if (empty($this->enabled_modules)) {
            // if not list any enabled modules then, do it!
            $this->enabled_modules = $this->listEnabledModules();
        }
    }// __construct


    /**
     * hook action.
     * 
     * @param string $action action name. must be Studly Caps case.
     * @param mixed $data set data for plugin to work.
     * @param mixed $arg additional data as arguments
     * @return array if has action, it will return array with keys like this... [action name]['result'] and module files info is in key [action name]['module']
     */
    public function doAction($action = '', $data = '', $arg = '')
    {
        // pass arguments to module plug method.
        $args = array();

        if (is_array($arg) || is_object($arg)) {
            $args = $arg;
        } else {
            //$args[] = $arg;
            for ($i = 2; $i < func_num_args(); $i++) {
                $args[] = func_get_arg($i);
            }
        }

        // if arguments is empty, set to null
        if (empty($args)) {
            $args = null;
        }

        $i = 0;

        foreach ($this->enabled_modules as $item) {
            if (is_array($item) && array_key_exists('module_path', $item) && array_key_exists('module_system_name', $item)) {
                // check required array key exists.
                if (
                    file_exists($item['module_path'].$item['module_system_name'].DS.$item['module_system_name'].'_module.php') &&
                    is_file($item['module_path'].$item['module_system_name'].DS.$item['module_system_name'].'_module.php')
                ) {
                    // check module file path exists.
                    // include the module to check class
                    include_once $item['module_path'].$item['module_system_name'].DS.$item['module_system_name'].'_module.php';
                    
                    $mplug_class_name = ucfirst($item['module_system_name']).'_Module';
                    
                    if (class_exists($mplug_class_name)) {
                        // if class exists
                        $mplug_obj = new $mplug_class_name;
                        
                        if (method_exists($mplug_obj, 'action'.$action)) {
                            // if method in this class exists.
                            // run module plug.
                            $result = [];
                            $result[$action]['result'] = $mplug_obj->{'action'.$action}($data, $args);
                            $result[$action]['module']['module_system_name'] = $item['module_system_name'];
                            $result[$action]['module']['module_path'] = $item['module_path'];
                            $result[$action]['module']['module_plug_file'] = $item['module_system_name'].'_module.php';
                            $result[$action]['module']['module_plug_class'] = $mplug_class_name;
                            $this->did_action_result = array_merge($this->did_action_result, $result);
                            
                            // track hook
                            $track_hook = [];
                            $track_hook[$i][$action]['module']['module_system_name'] = $item['module_system_name'];
                            $track_hook[$i][$action]['module']['module_path'] = $item['module_path'];
                            $track_hook[$i][$action]['module']['module_plug_file'] = $item['module_system_name'].'_module.php';
                            $track_hook[$i][$action]['module']['module_plug_class'] = $mplug_class_name;
                            $this->track_hook_actions = array_merge($this->track_hook_actions, $track_hook);

                            $i++;

                            unset($result, $track_hook);
                        }// endif; method_exists
                        unset($mplug_obj);
                    }// endif; class_exists
                    unset($mplug_class_name);
                }// endif; file_exists
            }// endif; is_array $item
        }// endforeach; $this->enabled_modules
        
        $this->track_hook_plugins = array_merge($this->track_hook_plugins, ['doAction' => $this->track_hook_actions]);

        unset($args, $i, $item);
        return $this->did_action_result;
    }// doAction


    /**
     * hook filter.<br>
     * make changes or modify the displaying results. this will hook to filter action from filtered data.
     * 
     * @param string $filter filter name. must be Studly Caps case.
     * @param mixed $data set data for plugin to work.
     * @param mixed $arg additional data as arguments
     * @return mixed the result of filtered data.
     */
    public function doFilter($filter = '', $data = '', $arg = '')
    {
        // set data to property
        $this->filtered_data = $data;
        $this->original_data = array_merge([], [$this->filtered_data]);

        // pass arguments to module plug method.
        $args = array();

        if (is_array($arg) || is_object($arg)) {
            $args = $arg;
        } else {
            //$args[] = $arg;
            for ($i = 2; $i < func_num_args(); $i++) {
                $args[] = func_get_arg($i);
            }
        }

        // if arguments is empty, set to null
        if (empty($args)) {
            $args = null;
        }

        $i = 0;

        foreach ($this->enabled_modules as $item) {
            if (is_array($item) && array_key_exists('module_path', $item) && array_key_exists('module_system_name', $item)) {
                // check required array key exists.
                if (
                    file_exists($item['module_path'].$item['module_system_name'].DS.$item['module_system_name'].'_module.php') &&
                    is_file($item['module_path'].$item['module_system_name'].DS.$item['module_system_name'].'_module.php')
                ) {
                    // check module file path exists.
                    // include the module to check class
                    include_once $item['module_path'].$item['module_system_name'].DS.$item['module_system_name'].'_module.php';
                    
                    $mplug_class_name = ucfirst($item['module_system_name']).'_Module';
                    
                    if (class_exists($mplug_class_name)) {
                        // if class exists
                        $mplug_obj = new $mplug_class_name;
                        
                        if (method_exists($mplug_obj, 'filter'.$filter)) {
                            // if method in this class exists.
                            $this->filtered_data = $mplug_obj->{'filter'.$filter}($this->filtered_data, $args);
                            // add to original data to track how many filters and how they changed.
                            $this->original_data = array_merge($this->original_data, [$this->filtered_data]);
                            
                            // track hook
                            $track_hook = [];
                            $track_hook[$i][$filter]['module']['module_system_name'] = $item['module_system_name'];
                            $track_hook[$i][$filter]['module']['module_path'] = $item['module_path'];
                            $track_hook[$i][$filter]['module']['module_plug_file'] = $item['module_system_name'].'_module.php';
                            $track_hook[$i][$filter]['module']['module_plug_class'] = $mplug_class_name;
                            $this->track_hook_filters = array_merge($this->track_hook_filters, $track_hook);

                            $i++;

                            unset($track_hook);
                        }// endif; method_exists
                        unset($mplug_obj);
                    }// endif; class_exists
                    unset($mplug_class_name);
                }// endif; file_exists
            }// endif; is_array $item
        }// endforeach; $this->enabled_modules
        
        $this->track_hook_plugins = array_merge($this->track_hook_plugins, ['doFilter' => $this->track_hook_filters]);

        unset($args, $i, $item);
        return $this->filtered_data;
    }// doFilter


    /**
     * check if module's plugins has any action will be hook into exists action.
     * 
     * @param string $filter action name. must be Studly Caps case.
     * @return integer|boolean return number of action exists or return false if there is nothing.
     */
    public function hasAction($filter = '')
    {
        return $this->hasFilter($filter);
    }// hasAction


    /**
     * check if module's plugins has any filter will be hook into exists filter.
     * 
     * @param string $filter filter name. must be Studly Caps case.
     * @return integer|boolean return number of filter exists or return false if there is nothing.
     */
    public function hasFilter($filter = '')
    {
        if (is_array($filter) || is_object($filter) || is_bool($filter)) {
            return false;
        }

        $i = 0;

        foreach ($this->enabled_modules as $item) {
            if (is_array($item) && array_key_exists('module_path', $item) && array_key_exists('module_system_name', $item)) {
                // check required array key exists.
                if (
                    file_exists($item['module_path'].$item['module_system_name'].DS.$item['module_system_name'].'_module.php') &&
                    is_file($item['module_path'].$item['module_system_name'].DS.$item['module_system_name'].'_module.php')
                ) {
                    // check module file path exists.
                    // include the module to check class
                    include_once $item['module_path'].$item['module_system_name'].DS.$item['module_system_name'].'_module.php';
                    
                    $mplug_class_name = ucfirst($item['module_system_name']).'_Module';
                    
                    if (class_exists($mplug_class_name)) {
                        // if class exists
                        $mplug_obj = new $mplug_class_name;
                        
                        if (method_exists($mplug_obj, 'action'.$filter) || method_exists($mplug_obj, 'filter'.$filter)) {
                            // if method in this class exists. count that has filter.
                            $i++;
                        }// endif; method_exists
                        unset($mplug_obj);
                    }// endif; class_exists
                    unset($mplug_class_name);
                }// endif; file_exists
            }// endif; is_array $item
        }// endforeach; $this->enabled_modules
        unset($item);

        if (intval($i) > 0) {
            return $i;
        } else {
            return false;
        }
    }// hasFilter


}