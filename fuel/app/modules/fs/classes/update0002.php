<?php
/**
 * updater
 * this update for update to 1.5.4
 */

namespace Fs;

class update0002
{
    
    public static function run()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `" . \DB::table_prefix('account_permission') . "` (
            `permission_id` int(11) NOT NULL AUTO_INCREMENT,
            `account_id` int(11) NOT NULL COMMENT 'refer to accounts.account_id',
            `permission_core` int(1) NOT NULL DEFAULT '0' COMMENT '1=core permission, 0=modules permission',
            `module_system_name` varchar(255) DEFAULT NULL COMMENT 'module system name',
            `permission_page` varchar(255) NOT NULL,
            `permission_action` varchar(255) DEFAULT NULL,
            PRIMARY KEY (`permission_id`),
            KEY `account_id` (`account_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='contain user''s permission for each admin page and action.' AUTO_INCREMENT=1 ;";
        \DB::query($sql)->execute();
        
        unset($sql);
        
        return true;
    }// run
    
    
}
