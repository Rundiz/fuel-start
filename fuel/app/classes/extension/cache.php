<?php
/**
 * Extend cache class
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

namespace Extension;

class Cache extends \Fuel\Core\Cache
{
    
    
    /**
     * delete cache with partial name.
     * 
     * @param string $partial_name some part of cache name. This parameter can be ALL to clear all cache
     * @return boolean
     */
    public static function deleteCache($partial_name = '')
    {
        if ($partial_name == null) {
            return true;
        }
        
        if ($partial_name == 'ALL') {
            \Cache::delete_all();
        }
        
        $cache_driver = \Config::get('cache.driver');
        
        if ($cache_driver == 'file') {
            // this site using cache file driver.
            $cache_subfolder = '';
            //if partial name has dot, means cache is in sub folders.
            if (strpos($partial_name, '.') !== false) {
                $partial_name_exp = explode('.', $partial_name);

                foreach ($partial_name_exp as $folder) {
                    if (end($partial_name_exp) != $folder) {
                        $cache_subfolder .= $folder . '/';
                    }
                }

                $partial_name = $partial_name_exp[count($partial_name_exp)-1];

                unset($folder, $partial_name_exp);
            }

            // read cache directory and delete cache.
            $files = \Extension\File::readDir2D(APPPATH . 'cache/' . $cache_subfolder);
            if (is_array($files) && !empty($files)) {
                foreach ($files as $file) {
                    if (strpos($file, '.cache') !== false && strpos($file, $partial_name) !== false) {
                        if (is_writable($file)) {
                            unlink($file);
                        }
                    }
                }// endforeach;
            }

            unset($file, $files);
        } else {
            // this site using other cache driver.
            // there is no way to delete cache with partial name. use clear cache
            \Cache::delete_all();
        }
        
        return true;
    }// deleteCache
    
    
    /**
     * get cache without throw ugly error message but return false if not found.
     * 
     * @param string $identifier
     * @param boolean $use_expiration
     * @return mixed return false if not found.
     */
    public static function getSilence($identifier, $use_expiration = true)
    {
        try {
            $cache = static::get($identifier, $use_expiration);
        } catch (\CacheNotFoundException $e) {
            $cache = false;
        }
        
        return $cache;
    }// getSilence
}
