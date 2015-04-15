<?php
/**
 * Extend date class
 *
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 *
 */

namespace Extension;

class Date extends \Date
{


    /**
     * get real timezone value (example Asia/Bangkok) from timezone number (example (UTC+07:00) Bangkok)<br>
     * by match timezone number to timezone configuration.
     * 
     * @param string $timezone_num
     * @return string timezone value for php.
     */
    public static function getRealTimezoneValue($timezone_num)
    {
        \Config::load('timezone', 'timezone');
        $timezone_list = \Config::get('timezone.timezone', array());
                    
        if (is_array($timezone_list) && array_key_exists($timezone_num, $timezone_list)) {
            $timezone = $timezone_list[$timezone_num];
            unset($timezone_list);
            return $timezone;
        }
        
        unset($timezone, $timezone_list);
        // in case that it cannot found any value matched. use UTC (+0.00).
        return 'UTC';
    }// getRealTimezoneValue


    /**
     * gmt date. the timezone up to current user data.
     *
     * @param string $date_format date format can use both date() function or strftime() function
     * @param integer $timestamp localtime timestamp.
     * @param type $timezone php timezone (http://www.php.net/manual/en/timezones.php)
     * @return null
     */
    public static function gmtDate($date_format = '%Y-%m-%d %H:%M:%S', $timestamp = '', $timezone = '')
    {
        // check empty date format
        if (empty($date_format)) {
            $date_format = '%Y-%m-%d %H:%M:%S';
        }

        // check timestamp
        if (empty($timestamp)) {
            $timestamp = time();
        } else {
            if (!self::isValidTimeStamp($timestamp)) {
                $timestamp = strtotime($timestamp);
            }
        }
        
        // make very sure that selected timezone is in the timezone list or converted to real timezone.
        if ($timezone != null) {
            $timezone = static::isValidTimezone($timezone);
        }

        // check timezone
        if ($timezone == null) {
            $account_model = new \Model_Accounts();
            $cookie = $account_model->getAccountCookie();
            $site_timezone = static::getRealTimezoneValue(\Model_Config::getval('site_timezone'));

            if (!isset($cookie['account_id'])) {
                // not member or not log in. use default config timezone.
                $timezone = $site_timezone;
            } else {
                // find timezone for current user.
                $row = \Model_Accounts::find($cookie['account_id']);

                if (!empty($row)) {
                    $timezone = static::getRealTimezoneValue($row->account_timezone);
                } else {
                    $timezone = $site_timezone;
                }
            }

            unset($account_model, $cookie, $row, $site_timezone);
        }

        // what format of the date_format (use date() value or strftime() value)
        if (strpos($date_format, '%') !== false) {
            // use strftime() format
            return \Date::forge($timestamp)->set_timezone($timezone)->format($date_format);
        } else {
            // use date() format
            return date($date_format, strtotime(\Date::forge($timestamp)->set_timezone($timezone)->format('%Y-%m-%d %H:%M:%S')));
        }
    }// gmtDate


    /**
    * is valid timestamp
    * @author sepehr
    * @link https://gist.github.com/sepehr/6351385
    * @param string $timestamp timestamp to validate
    * @return boolean
    */
    public static function isValidTimeStamp($timestamp) {
        $check = (is_int($timestamp) OR is_float($timestamp))
            ? $timestamp
            : (string) (int) $timestamp;

        return ($check === $timestamp)
            AND ((int) $timestamp <= PHP_INT_MAX)
            AND ((int) $timestamp >= ~PHP_INT_MAX); 
    }// isValidTimeStamp


    /**
     * is valid timezone.<br>
     * check and return real timezone value.
     * 
     * @param string $timezone check timezone
     * @return string return checked and get timezone value. if found that this is invalid then return null.
     */
    public static function isValidTimezone($timezone) {
        \Config::load('timezone', 'timezone');
        $timezone_list = \Config::get('timezone.timezone', array());
        if (array_key_exists($timezone, $timezone_list)) {
            // found in timezone list key. convert to timezone list value.
            $timezone = static::getRealTimezoneValue($timezone);
        } elseif (\Arr::search($timezone_list, $timezone) === null) {
            // not found in the timezone list value. this is not the timezone key and not even timezone value.!
            $timezone = null;
        }
        unset($timezone_list);
        
        return $timezone;
    }// isValidTimezone


    /**
     * get gmt timestamp from local timestamp
     *
     * @author Vee Winch.
     * @param integer $timestamp timestamp
     * @return integer
     */
    public static function localToGmt($timestamp = '')
    {
        if ($timestamp == null) {
            $timestamp = time();
        }

        return strtotime(\Date::forge($timestamp, 'GMT')->format('%Y-%m-%d %H:%M:%S'));
    }// localToGmt


}
