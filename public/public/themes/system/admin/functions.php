<?php
/**
 * System admin theme. - functions
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
 * @return string
 */
function languageSwitchAdminBootstrapNavbar() 
{
    $languages = \Config::get('locales');

    ksort($languages);

    $current_lang = \Lang::get_lang();
    $output = '<a href="#" onclick="return false;" class="non-link-navbar dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-globe"></span> ' . $languages[$current_lang]['name'] . '</a>';

    if (is_array($languages) && !empty($languages)) {
        $lang_options = '';
        foreach ($languages as $language => $item) {
            if ($language != $current_lang) {
                $lang_options .= "\t" . '<li>' . \Html::anchor(\Uri::createNL($language . '/admin'), $item['name']) . '</li>' . "\n";
            }
        }
    }

    if (isset($lang_options) && $lang_options != null) {
        $lang_options = "\n" . '<ul class="dropdown-menu">' . "\n"
            . $lang_options
            . '</ul>' . "\n\t\t\t\t\t\t\t\t";

        $output .= $lang_options;

        unset($lang_options);
    }

    unset($current_lang, $item, $languages, $language);

    return $output;
}// languageSwitchAdminBootstrapNavbar


/**
 * language switch for admin page based on generic navbar.
 * 
 * @return string
 */
function languageSwitchAdminNavbar()
{
    $languages = \Config::get('locales');
    
    ksort($languages);

    $current_lang = \Lang::get_lang();
    $output = '<a href="#" onclick="return false;"><span class="glyphicon glyphicon-globe"></span></a>';

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
            $lang_options .= '>' . \Html::anchor(\Uri::createNL($language . '/admin'), 
                $item['name'], 
                array('class' => (isset($active_class) ? $active_class : ''))
            ) . '</li>' . "\n";
            
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

    unset($current_lang, $item, $languages, $language);

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
