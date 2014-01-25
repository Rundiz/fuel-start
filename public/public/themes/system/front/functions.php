<?php
/**
 * System front end theme function
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */


function languageSwitchDropdown() 
{
	$languages = \Config::get('locales');
	
	// no languages, language is empty, there is only just one language
	if (empty($languages) || !is_array($languages) || count($languages) <= 1) {
		return null;
	}
	
	ksort($languages);
	
	$current_lang = \Lang::get_lang();
	$output = "\n" . '<div class="dropdown">' . "\n";
	$output .= "\t" . '<button class="btn dropdown-toggle" type="button" id="language-switch-dropdown" data-toggle="dropdown">';
	$output .= $languages[$current_lang]['name'];
	$output .= '<span class="caret"></span>';
	$output .= '</button>' . "\n";
	
	if (is_array($languages) && !empty($languages) && count($languages) > 1) {
		$output .= '<ul class="dropdown-menu" role="menu" aria-labelledby="language-switch-dropdown">' . "\n";
		foreach ($languages as $language => $item) {
			if ($language != $current_lang) {
				$output .= "\t" . '<li>' . \Html::anchor(\Uri::createNL($language), $item['name']) . '</li>' . "\n";
			}
		}
		$output .= '</ul>' . "\n";
	}
	
	$output .= '</div>' . "\n";
	
	return $output;
}// languageSwitchDropdown

