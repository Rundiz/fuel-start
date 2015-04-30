<?php
/**
 * System 2 admin theme. - functions
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */


/**
 * check admin permission
 * 
 * @param string $page_name
 * @param string $action
 * @param integer $account_id
 * @return boolean
 */
function checkAdminPermission($page_name = '', $action = '', $account_id = '') 
{
    return \Model_AccountLevelPermission::checkAdminPermission($page_name, $action, $account_id);
}// checkAdminPermission


/**
 * generate breadcrumb from array.
 * 
 * @param array $page_breadcrumb
 * @return string
 */
function generateBreadCrumb(array $page_breadcrumb = array())
{
    if (!is_array($page_breadcrumb)) {
        return null;
    }
    
    $total_breadcrumbs = count($page_breadcrumb);
    $output = '';
    
    if ($total_breadcrumbs > 0) {
        $output .= '<ol class="breadcrumb">'."\n";
        for ($i = 1; $i <= $total_breadcrumbs; $i++) {
            if (isset($page_breadcrumb[($i-1)]) && is_array($page_breadcrumb[($i-1)]) && array_key_exists('name', $page_breadcrumb[($i-1)])) {
                $output .= "\t".'<li';
                if ($i == $total_breadcrumbs) {
                    $output .= ' class="active"';
                }
                $output .= '>';
                if ($i != $total_breadcrumbs || !array_key_exists('url', $page_breadcrumb[($i-1)])) {
                    $output .= '<a href="'.$page_breadcrumb[($i-1)]['url'].'">';
                }
                $output .= $page_breadcrumb[($i-1)]['name'];
                if ($i != $total_breadcrumbs || !array_key_exists('url', $page_breadcrumb[($i-1)])) {
                    $output .= '</a>';
                }
                $output .= '</li>'."\n";
            }
        }// end for;
        $output .= '</ol>'."\n";
    }
    
    unset($i, $total_breadcrumbs);
    return $output;
}// generateBreadCrumb


/**
 * get admin's avatar picture.
 * 
 * @param integer $account_id
 * @return string return element ready for display avatar.
 */
function getAdminAvatar($account_id)
{
    $theme = \Theme::instance();
    $default_no_avatar = $theme->asset->img('default-avatar.jpg', array('alt' => 'user avatar', 'class' => 'img-user-avatar img-circle'));
    unset($theme);
    if (!is_numeric($account_id) || intval($account_id) === intval(0)) {
        return $default_no_avatar;
    }
    
    $cache_name = 'public.themes.sys2.getAdminAvatar-'
            . \Model_Sites::getSiteId(false) . '-'
            . $account_id;
    $cache_data = \Extension\Cache::getSilence($cache_name);
    
    if (false === $cache_data) {
        // if never cached or cache expired.
        $result = \DB::select()
            ->as_object()
            ->from('accounts')
            ->where('account_id', $account_id)
            ->execute();
        
        if (count($result) > 0) {
            $row = $result->current();
            if ($row->account_avatar != null) {
                $return_val = \Html::img($row->account_avatar, array('alt' => 'user avatar', 'class' => 'img-user-avatar img-circle'));
                \Cache::set($cache_name, $return_val, 86400);
                unset($cache_name);
                return $return_val;
            }
        }
        
        if (!isset($return_val) || (isset($return_val) && $return_val == null)) {
            // not found account or not found avatar.
            \Cache::set($cache_name, $default_no_avatar, 86400);
            unset($cache_name);
            return $default_no_avatar;
        }
    }
    
    unset($cache_name);
    return $cache_data;
}// getAdminAvatar


/**
 * get root site url
 * it is up to configuration with {lang} in url or not. if there is no {lang} in url, the root web may contain // at the end.
 * 
 * @return string
 */
function getRootSiteURL()
{
    $root_url = \Uri::create('/');
    
    if (mb_substr($root_url, -2) == '//') {
        // this case is http://domain/fuelstart//
        return mb_substr($root_url, 0, -1);
    }
    
    // this case is http://domain/th/fuelstart/
    return $root_url;
}// getRootSiteURL


/**
 * language switch for admin page based on Bootstrap navbar.
 * 
 * @param boolean $change_on_current_page change language on current page not go to root? false = change language and go to admin home, true = change language on current page.
 * @return string
 */
function languageSwitchAdminBootstrapNavbar($change_on_current_page = false) 
{
    $languages = \Config::get('locales');

    ksort($languages);

    $current_lang = \Lang::get_lang();
    $output = '<a href="#" onclick="return false;" class="non-link-navbar dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-globe"></i></a>';
    
    $url_querystring = str_replace('&', '&amp;', \Input::server('QUERY_STRING'));
    if ($url_querystring != null) {
        $url_querystring = '?' . $url_querystring;
    }

    if (is_array($languages) && !empty($languages)) {
        $lang_options = '';
        foreach ($languages as $language => $item) {
            $lang_options .= "\t" . '<li'.($language == $current_lang ? ' class="active"' : '').'>';
            if ($change_on_current_page === true) {
                $lang_options .= \Html::anchor(\Uri::createNL($language . '/' . \Uri::string() . $url_querystring), 
                    $item['name'], 
                    array('class' => (isset($active_class) ? $active_class : ''))
                );
            } else {
                $lang_options .= \Html::anchor(\Uri::createNL($language . '/admin'), 
                    $item['name'], 
                    array('class' => (isset($active_class) ? $active_class : ''))
                );
            }
            $lang_options .= '</li>' . "\n";
        }
    }

    if (isset($lang_options) && $lang_options != null) {
        $lang_options = "\n" . '<ul class="dropdown-menu">' . "\n"
            . $lang_options
            . '</ul>' . "\n\t\t\t\t\t\t\t\t";

        $output .= $lang_options;

        unset($lang_options);
    }

    unset($current_lang, $item, $languages, $language, $url_querystring);

    return $output;
}// languageSwitchAdminBootstrapNavbar


/**
 * language switch for admin page based on generic navbar.
 * 
 * @param boolean $change_on_current_page change language on current page not go to root? false = change language and go to admin home, true = change language on current page.
 * @return string
 */
function languageSwitchAdminNavbar($change_on_current_page = false)
{
    $languages = \Config::get('locales');
    
    ksort($languages);

    $current_lang = \Lang::get_lang();
    $output = '<a href="#" onclick="return false;"><span class="glyphicon glyphicon-globe"></span></a>';
            
    $url_querystring = str_replace('&', '&amp;', \Input::server('QUERY_STRING'));
    if ($url_querystring != null) {
        $url_querystring = '?' . $url_querystring;
    }

    if (is_array($languages) && !empty($languages)) {
        $lang_options = '';
        foreach ($languages as $language => $item) {
            if ($language == $current_lang) {
                $active_class = 'current';
            }
            
            $lang_options .= "\t" . '<li';
            if (isset($active_class) && $active_class != null) {
                $lang_options .= ' class="' . $active_class . '"';
            }
            if ($change_on_current_page === true) {
                $lang_options .= '>' . \Html::anchor(\Uri::createNL($language . '/' . \Uri::string() . $url_querystring), 
                    $item['name'], 
                    array('class' => (isset($active_class) ? $active_class : ''))
                );
            } else {
                $lang_options .= '>' . \Html::anchor(\Uri::createNL($language . '/admin'), 
                    $item['name'], 
                    array('class' => (isset($active_class) ? $active_class : ''))
                );
            }
            $lang_options .= '</li>' . "\n";
            
            unset($active_class);
        }
    }

    if (isset($lang_options) && $lang_options != null) {
        $lang_options = "\n" . '<ul>' . "\n"
            . $lang_options
            . '</ul>' . "\n\t\t\t\t\t\t\t\t";

        $output .= $lang_options;

        unset($lang_options);
    }

    unset($current_lang, $item, $languages, $language, $url_querystring);

    return $output;
}// languageSwitchAdminNavbar


/**
 * language switch for admin page. display as select box.
 * 
 * @return string
 */
function languageSwitchAdminSelectBox() 
{
    $languages = \Config::get('locales');

    ksort($languages);

    $current_lang = \Lang::get_lang();
    $output = "\n" . '<select name="admin_language" onchange="change_redirect($(this));" class="form-control chosen-select">' . "\n";
    if (is_array($languages) && !empty($languages)) {
        foreach ($languages as $language => $item) {
            $output .= "\t" . '<option value="' . \Uri::createNL($language . '/admin'). '"';
            if ($language == $current_lang) {
                $output .= ' selected="selected"';
            }
            $output .= '>' . $item['name'] . '</option>' . "\n";
        }
    } else {
        $output .= "\t" . '<option></option>' . "\n";
    }
    $output .= '</select>' . "\n";

    unset($current_lang, $languages);

    return $output;
}// languageSwitchAdminSelectBox
