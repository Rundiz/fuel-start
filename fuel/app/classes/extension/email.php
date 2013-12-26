<?php
/**
 * Extends email for auto load configured mail value.
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

namespace Extension;

class Email 
{
	
	
	/**
	 * get config
	 * get email config values from db and set it to ready for FuelPHP email configuration array.
	 * 
	 * @author Vee Winch.
	 * @return array all email configuration in db.
	 */
	public static function getConfig() 
	{
		$cfg_email = \Model_Config::getvalues(array('mail_protocol', 'mail_mailpath', 'mail_smtp_host', 'mail_smtp_user', 'mail_smtp_pass', 'mail_smtp_port'));
		
		$config['driver'] = $cfg_email['mail_protocol']['value'];
		$config['sendmail_path'] = $cfg_email['mail_mailpath']['value'];
		$config['smtp']['host'] = $cfg_email['mail_smtp_host']['value'];
		$config['smtp']['port'] = (int) $cfg_email['mail_smtp_port']['value'];
		$config['smtp']['username'] = $cfg_email['mail_smtp_user']['value'];
		$config['smtp']['password'] = $cfg_email['mail_smtp_pass']['value'];
		$config['smtp']['timeout'] = 20;
		
		$config['newline'] = "\r\n";
		
		unset($cfg_email);
		
		return $config;
	}// getConfig
	
	
	/**
	 * set multiple email array for FuelPHP emails array.
	 * 
	 * @author Vee Winch.
	 * @param string $emails
	 * @return array
	 */
	public static function setEmails($emails) 
	{
		if (is_string($emails)) {
			$emails = str_replace(', ', ',', $emails);
		}
		
		if (strpos($emails, ',') === false) {
			// not found multiple email.
			return $emails;
		} else {
			$emails_arr = explode(',', $emails);
			
			return $emails_arr;
		}
	}// setEmails


}


?>