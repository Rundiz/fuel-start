<?php
/**
 *
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 *
 */

class Model_Config extends \Orm\Model
{


    protected static $_table_name = 'config';
    //protected static $_properties = array('config_name', 'config_value', 'config_core', 'config_description');
    protected static $_primary_key = array();// no PK, need to set PK to empty array.


    /**
     * run before initialize the class
     * use this method to set new table prefix with multisite.
     */
    public static function _init()
    {
        // get current site id
        $site_id = \Model_Sites::getSiteId(false);

        if ($site_id != '1') {
            static::$_table_name = $site_id . '_' . static::$_table_name;
        }
    }// _init
    
    
    /**
     * get table name based on current site.
     * 
     * @return string
     */
    public static function getTableName()
    {
        return static::$_table_name;
    }// getTableName


    /**
     * get config value from config_name field in config table
     *
     * @param string $config_name config name
     * @return mixed
     */
    public static function getval($config_name = '', $return_field = 'config_value')
    {
        if ($config_name == null) {
            return null;
        }

        $cache_name = 'model.config-getval-'
                . \Model_Sites::getSiteId(false) . '-'
                . \Extension\Security::formatString($config_name, 
                    'alphanum_dash_underscore')
                . '-'
                . $return_field;
        $cache_cfg = \Extension\Cache::getSilence($cache_name);

        if (false === $cache_cfg) {
            $result = \DB::select()
                ->as_object()
                ->from(static::$_table_name)
                ->where('config_name', '=', $config_name)
                ->execute();
            
            if (count($result) > 0) {
                $row = $result->current();
            } else {
                return $result;
            }

            if ($return_field == null) {
                \Cache::set($cache_name, $row, 2592000);
                return $row;
            } else {
                \Cache::set($cache_name, $row->$return_field, 2592000);
                return $row->$return_field;
            }
        }
        
        return $cache_cfg;
    }// getval


    /**
     * alias name of getval
     *
     * @return mixed
     */
    public static function getvalue($config_name = '', $return_field = 'config_vlue')
    {
        return static::getval($config_name, $return_field);
    }// getvalue


    /**
     * get multiple config values from config_name field in config table
     *
     * @param array $config_name
     * @return array|null array if exists, null if not exists.
     */
    public static function getvalues($config_name = array())
    {
        if (!is_array($config_name) || (is_array($config_name) && empty($config_name))) {
            return null;
        }
        
        $cache_name = 'model.config-getvalues-'
                . \Model_Sites::getSiteId(false) . '-'
                . \Extension\Security::formatString(md5(json_encode($config_name)), 
                    'alphanum_dash_underscore');
        $cached = \Extension\Cache::getSilence($cache_name);

        if (false === $cached) {
            // because FuelPHP ORM cannot get multiple results if that table has no primary key.
            // we will use DB class
            $output = array();

            $result = \DB::select('*')->from(static::$_table_name)->as_object()->where('config_name', 'IN', $config_name)->execute();
            if ((is_array($result) || is_object($result)) && !empty($result)) {
                foreach ($result as $row) {
                    $output[$row->config_name]['value'] = $row->config_value;
                    $output[$row->config_name]['core'] = $row->config_core;
                    $output[$row->config_name]['description'] = $row->config_description;
                }// endforeach;
            }// endif;
            unset($result, $row);

            \Cache::set($cache_name, $output, 2592000);
            return $output;
            // end get values by array loop.
        }
        
        return $cached;
    }// getvalues


    /**
     * save
     *
     * @param array $data
     * @return boolean
     */
    public static function saveData(array $data = array())
    {
        if (empty($data)) {return false;}

        foreach ($data as $key => $value) {
            \DB::update(static::$_table_name)
                ->value('config_value', $value)
                ->where('config_name', $key)
                ->execute();
        }
        
        // clear cache
        \Extension\Cache::deleteCache('model.config-getval-'.\Model_Sites::getSiteId(false));
        \Extension\Cache::deleteCache('model.config-getvalues-'.\Model_Sites::getSiteId(false));

        return true;
    }// saveData


}
