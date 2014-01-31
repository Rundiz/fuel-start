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
 * language switch for admin page.
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
 * language switch for admin page. display as select box.
 * 
 * @return string
 */
function languageSwitchAdminSelectBox() 
{
	$languages = \Config::get('locales');
	
	ksort($languages);
	
	$current_lang = \Lang::get_lang();
	$output = "\n" . '<select name="admin_language" onchange="change_redirect($(this));" class="form-control">' . "\n";
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
