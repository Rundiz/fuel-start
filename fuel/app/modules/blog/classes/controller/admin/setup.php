<?php
/** 
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

namespace Blog;

class Controller_Admin_Setup extends \Controller_AdminController 
{
	
	
	public function action_index() 
	{
		echo \Extension\Html::anchor('blog/admin/setup/install', 'Install') . '<br />';
		echo \Extension\Html::anchor('blog/admin/setup/uninstall', 'Uninstall') . '<br />';
		
		echo 'Warning! The link action as you click. No confirm.';
	}// action_index
	
	
	public function action_install() 
	{
		$sql = "CREATE TABLE IF NOT EXISTS `" . \DB::table_prefix('blog') . "` (
			`post_id` int(11) NOT NULL AUTO_INCREMENT,
			`post_name` varchar(255) DEFAULT NULL,
			`post_body` longtext,
			`post_date` bigint(20) DEFAULT NULL,
			PRIMARY KEY (`post_id`)
		  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
		\DB::query($sql)->execute();
		
		$sql = "CREATE TABLE IF NOT EXISTS `" . \DB::table_prefix('blog_comment') . "` (
			`comment_id` int(11) NOT NULL AUTO_INCREMENT,
			`post_id` int(11) DEFAULT NULL,
			`comment_name` varchar(255) DEFAULT NULL,
			`comment_body` text,
			`comment_date` bigint(20) DEFAULT NULL,
			PRIMARY KEY (`comment_id`)
		  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
		\DB::query($sql)->execute();
		
		unset($sql);
		
		echo 'Install db tables for blog module completed.';
	}// action_install
	
	
	public function action_uninstall() 
	{
		if (\DBUtil::table_exists('blog')) {
			\DBUtil::drop_table('blog');
		}
		
		if (\DBUtil::table_exists('blog_comment')) {
			\DBUtil::drop_table('blog_comment');
		}
		
		echo 'Uninstall db tables for blog module completed.';
	}// action_uninstall
	
	
}

