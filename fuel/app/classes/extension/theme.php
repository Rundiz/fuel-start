<?php
/**
 * Extend theme class
 *
 * @author Vee W.
 */

class Theme extends \Fuel\Core\Theme
{


    public function __construct(array $config = array()) {
        parent::__construct($config);
    }// __construct
    
    
    /**
     * find view file<br>
     * this method that extends fuelphp core theme is for re-arrange priority of theme and views.
     * 
     * @param string $view
     * @param string $themes
     * @return string
     */
    protected function find_file($view, $themes = null)
    {
        if ($themes === null) {
            $themes = array($this->active, $this->fallback);
        }

        // determine the path prefix and optionally the module path
        $path_prefix = '';
        $module_path = null;
        if ($this->config['use_modules'] and class_exists('Request', false) and $request = \Request::active() and $module = $request->module) {
            // we're using module name prefixing
            $path_prefix = $module.DS;

            // and modules are in a separate path
            is_string($this->config['use_modules']) and $path_prefix = trim($this->config['use_modules'], '\\/').DS.$path_prefix;

            // do we need to check the module too?
            $this->config['use_modules'] === true and $module_path = \Module::exists($module).'themes'.DS;
        }
        
        foreach ($themes as $theme) {
            $ext   = pathinfo($view, PATHINFO_EXTENSION) ?
                '.'.pathinfo($view, PATHINFO_EXTENSION) : $this->config['view_ext'];
            $file  = (pathinfo($view, PATHINFO_DIRNAME) ?
                    str_replace(array('/', DS), DS, pathinfo($view, PATHINFO_DIRNAME)).DS : '').
                pathinfo($view, PATHINFO_FILENAME);
            
            if (empty($theme['find_file'])) {
                if ($module_path and ! empty($theme['name']) and is_file($path = $module_path.$theme['name'].DS.$file.$ext)) {
                    // if use_modules is true then this $path will be /www/root/modules/<module name>/themes/<theme name>/<$view>.php
                    return $path;
                } elseif (is_file($path = $theme['path'].$path_prefix.$file.$ext)) {
                    // if use_modules is true then $path will be /www/root/<theme path>/<theme name>/<module name>/<$view>.php
                    // if use_modules is 'modules' then $path will be /www/root/<theme path>/<theme name>/modules/<module name>/<$view>.php
                    return $path;
                } elseif (is_file($path = \Module::exists($module) . 'views' . DS . $file . $ext)) {
                    /**
                     * this condition was added by Vee W.
                     * look directly in modules/module_name/views. this $path will be /www/root/<modules path>/<module name>/views/<$view>.php
                     * 
                     * @author Vee W.
                     */
                    return $path;
                } elseif (is_file($path = $theme['path'].$file.$ext)) {
                    // this will not look into module name anymore. $path will be /www/root/<theme path>/<theme name>/<$view>.php
                    return $path;
                }
            } else {
                if ($path = \Finder::search($theme['path'].$path_prefix, $file, $ext)) {
                    return $path;
                }
            }
        }
        
        // not found, return the viewname to fall back to the standard View processing
        return $view;
    }// find_file


}
