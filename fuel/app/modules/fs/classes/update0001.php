<?php
/**
 * updater
 * this update for the first time to update from 1.0.x to 1.5
 */

namespace Fs;

class update0001
{


	public static function run()
	{
		// get site name
		$site_name = \Model_Config::getval('site_name');
		
		// get domain
		if (isset($_SERVER['HTTP_HOST'])) {
			$site_domain = $_SERVER['HTTP_HOST'];
		} elseif (isset($_SERVER['SERVER_NAME'])) {
			$site_domain = $_SERVER['SERVER_NAME'];
		} else {
			$site_domain = 'localhost';
		}
		
		$sql = "CREATE TABLE IF NOT EXISTS `" . \DB::table_prefix('sites') . "` (
			`site_id` int(11) NOT NULL AUTO_INCREMENT,
			`site_name` varchar(255) DEFAULT NULL,
			`site_domain` varchar(255) DEFAULT NULL COMMENT 'ex. domain.com, sub.domain.com with out http://',
			`site_status` int(1) NOT NULL DEFAULT '0' COMMENT '0=disable, 1=enable',
			`site_create` bigint(20) DEFAULT NULL,
			`site_create_gmt` bigint(20) DEFAULT NULL,
			`site_update` bigint(20) DEFAULT NULL,
			`site_update_gmt` bigint(20) DEFAULT NULL,
			PRIMARY KEY (`site_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;";
		  \DB::query($sql)->execute();
		  
		  // check if table already created before insert.
		  $result = \DB::count_records('sites');
		  if ($result <= 0) {
			$sql = "INSERT INTO `" . \DB::table_prefix('sites') . "` (`site_id`, `site_name`, `site_domain`, `site_status`, `site_create`, `site_create_gmt`, `site_update`, `site_update_gmt`) VALUES
			(1, '" . $site_name . "', '" . $site_domain . "', 1, " . time() . ", " . \Extension\Date::localToGmt() . ", " . time() . ", " . \Extension\Date::localToGmt() . ");";
			\DB::query($sql)->execute();
		  }
		
		unset($sql);
		
		return true;
	}// run


}

