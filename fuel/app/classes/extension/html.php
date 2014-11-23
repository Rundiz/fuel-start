<?php
/**
 * Extend html class
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 *
 */

namespace Extension;

class Html extends \Fuel\Core\Html
{


    /**
     * generate Fuel Start sortable link. it can generate any querystring url.
     *
     * @param array $sortable_data
     * @param array $except_querystring
     * @param string $link
     * @param string $link_text
     * @param array $attributes
     * @param boolean $secure
     * @return string
     */
    public static function fuelStartSortableLink(array $sortable_data = array(), array $except_querystring = array(), $link = null, $link_text = '', array $attributes = array(), $secure = null)
    {
        if ($link == null) {
            $link = \Uri::main();
        }

        if (!is_array($except_querystring)) {
            $except_querystring = array();
        }

        $querystring = array();

        // build querystring of sortable_data
        if (!empty($sortable_data) && is_array($sortable_data)) {
            foreach ($sortable_data as $name => $value) {
                if (!empty($name) || !empty($value)) {
                    $querystring_array[] = $name . '=' . $value;
                    $except_querystring = array_merge($except_querystring, array($name));
                }
            }
            unset($name, $value);
        }

        // build querystring of exists querystring except except_querystring
        $all_querystring = \Uri::getAllQuerystring(true);
        foreach ($all_querystring as $q_name => $q_value) {
            if (!empty($q_name) || !empty($q_value)) {
                if (!in_array(urldecode($q_name), $except_querystring)) {
                    $querystring_array[] = $q_name . '=' . $q_value;
                }
            }
        }// endforeach
        unset($all_querystring, $q_name, $q_value);
        
        if (isset($querystring_array)) {
            $querystring[1] = implode('&amp;', $querystring_array);
        }
        $querystring_str = implode('&amp;', $querystring);

        // if there is querystring. build it as string (name=val&amp;name2=val2...)
        if (!empty($querystring)) {

            $link .= '?' . $querystring_str;

            unset($i, $key, $querystring, $querystring_str, $value);
        }

        // add sorted icons.
        if (isset($sortable_data['orders']) && $sortable_data['orders'] == \Input::get('orders')) {
            if (strtoupper(\Input::get('sort')) == 'ASC') {
                $link_text .= ' <span class="glyphicon glyphicon-sort-by-attributes"></span>';
            } elseif (strtoupper(\Input::get('sort')) == 'DESC') {
                $link_text .= ' <span class="glyphicon glyphicon-sort-by-attributes-alt"></span>';
            }
        }

        return \Html::anchor($link, $link_text, $attributes, $secure);
    }// fuelStartSortableLink


}
