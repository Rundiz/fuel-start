<?php
/**
 * Extend security class
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

namespace Extension;

class Security extends \Fuel\Core\Security
{
    
    
    /**
     * format string to filter allowed characters
     * 
     * @param string $str
     * @param string $pattern_name
     * @param string $replace_with
     * @param string $patterm_custom
     * @return string
     */
    public static function formatString($str, 
        $pattern_name = 'alpha', 
        $replace_with = '',
        $patterm_custom = ''
    )
    {
        switch ($pattern_name) {
            case 'alphanum':
                $pattern = '/[^a-z0-9]+/iuD';
                break;
            case 'alphanum_dash':
                $pattern = '/[^a-z0-9\-]+/iuD';
                break;
            case 'alphanum_dash_underscore':
                $pattern = '/[^a-z0-9\-_]+/iuD';
                break;
            case 'num':
                $pattern = '/[^0-9]+/iuD';
                break;
            case 'custom';
                return preg_replace($patterm_custom, $replace_with, $str);
            default:
                // 'alpha'
                $pattern = '/[^a-z]+/iuD';
                break;
        }
        
        return preg_replace($pattern, $replace_with, $str);
    }// formatString
}
