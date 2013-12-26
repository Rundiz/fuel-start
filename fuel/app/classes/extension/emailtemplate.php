<?php
/**
 * Email template loader library.
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

namespace Extension;

class EmailTemplate
{


	/**
	 * read template
	 * 
	 * @author Vee Winch.
	 * @param string $email_file email file.
	 * @param string $template_path path to folder that store email file.
	 * @return mixed
	 */
	public static function readTemplate($email_file = '', $template_path = null) 
	{
		if ($email_file == null) {return null;}
		
		if ($template_path == null) {
			$template_path = APPPATH . 'lang' . DS . \Lang::get_lang() . DS . 'email' . DS;
		}
		
		if (file_exists($template_path . $email_file)) {
			$site_name = \Model_Config::getval('site_name');
			
			$output = file_get_contents($template_path.$email_file);
			$output = str_replace("%site_name%", $site_name, $output);
			$output = str_replace("%site_url%", \Uri::base(), $output);
			$output = str_replace("%site_admin%", \Uri::create('site-admin'), $output);
			
			unset($site_name, $template_path);
			
			return $output;
		} else {
			return false;
		}
	}// readTemplate


}

