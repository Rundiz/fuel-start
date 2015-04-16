<?php
/**
 *
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 *
 */

class Model_Sites extends \Orm\Model
{


    protected static $_table_name = 'sites';
    protected static $_primary_key = array('site_id');

    // relations
    protected static $_has_many = array(
        'account_logins' => array(
            'key_from' => 'site_id',
            'model_to' => 'Model_AccountLogins',
            'key_to' => 'site_id',
            'cascade_delete' => true,
        ),
        'account_sites' => array(
            'key_from' => 'site_id',
            'model_to' => 'Model_AccountSites',
            'key_to' => 'site_id',
            'cascade_delete' => true,
        ),
    );


    /**
     * list tables that *must copy* when create new site.
     *
     * @var array $multisite_tables
     * @todo [multisite] developers have to add *must copy* tables here when you create table that need to use differently in multi-site.
     */
    public $multisite_tables = array(
        'account_fields',
        'account_level',// this table require data.
        'account_level_group', // this table require base level data.
        'account_level_permission',
        'account_permission',

        'config', // this table must copy "core" config data
    );


    /**
     * add new site. new site in db and create, copy tables to contain site id prefix.
     *
     * @param array $data
     * @return boolean
     */
    public static function addSite(array $data = array())
    {
        // additional data for inserting
        $data['site_create'] = time();
        $data['site_create_gmt'] = \Extension\Date::localToGmt();
        $data['site_update'] = time();
        $data['site_update_gmt'] = \Extension\Date::localToGmt();

        // insert into db.
        list($site_id) = \DB::insert(static::$_table_name)
            ->set($data)
            ->execute();

        // start copy tables
        static::forge()->copyNewSiteTable($site_id);

        // @todo [theme][multisite] for any theme management that get config from db from each site. you need to add set default theme for each created site here.

        // set config for new site. this step should reset core config values in new site for security reason.
        // @todo [core_config] when developers add new core config names and values, you have to add those default values here.
        $cfg_data['site_name'] = $data['site_name'];
        $cfg_data['page_title_separator'] = ' | ';
        $cfg_data['site_timezone'] = 'Asia/Bangkok';
        $cfg_data['simultaneous_login'] = '0';
        $cfg_data['allow_avatar'] = '1';
        $cfg_data['avatar_size'] = '200';
        $cfg_data['avatar_allowed_types'] = 'gif|jpg|png';
        $cfg_data['avatar_path'] = 'public/upload/avatar/';
        $cfg_data['member_allow_register'] = '1';
        $cfg_data['member_register_notify_admin'] = '1';
        $cfg_data['member_verification'] = '1';
        $cfg_data['member_admin_verify_emails'] = 'admin@localhost';
        $cfg_data['member_disallow_username'] = 'admin, administrator, administrators, root, system';
        $cfg_data['member_max_login_fail'] = '10';
        $cfg_data['member_login_fail_wait_time'] = '30';
        $cfg_data['member_login_remember_length'] = '30';
        $cfg_data['member_confirm_wait_time'] = '10';
        $cfg_data['member_email_change_need_confirm'] = '1';
        $cfg_data['mail_protocol'] = 'mail';
        $cfg_data['mail_mailpath'] = '/usr/sbin/sendmail';
        $cfg_data['mail_smtp_host'] = '';
        $cfg_data['mail_smtp_user'] = '';
        $cfg_data['mail_smtp_pass'] = '';
        $cfg_data['mail_smtp_port'] = '25';
        $cfg_data['mail_sender_email'] = 'no-reply@localhost';
        $cfg_data['content_items_perpage'] = '10';
        $cfg_data['content_admin_items_perpage'] = '20';
        $cfg_data['media_allowed_types'] = '7z|aac|ace|ai|aif|aifc|aiff|avi|bmp|css|csv|doc|docx|eml|flv|gif|gz|h264|h.264|htm|html|jpeg|jpg|js|json|log|mid|midi|mov|mp3|mpeg|mpg|pdf|png|ppt|psd|swf|tar|text|tgz|tif|tiff|txt|wav|webm|word|xls|xlsx|xml|xsl|zip';
        $cfg_data['ftp_host'] = '';
        $cfg_data['ftp_username'] = '';
        $cfg_data['ftp_password'] = '';
        $cfg_data['ftp_port'] = '21';
        $cfg_data['ftp_passive'] = 'true';
        $cfg_data['ftp_basepath'] = '/public_html/';
        foreach ($cfg_data as $cfg_name => $cfg_value) {
            \DB::update($site_id . '_config')
                    ->where('config_name', $cfg_name)
                    ->value('config_value', $cfg_value)
                    ->execute();
        }
        unset($cfg_data, $cfg_name, $cfg_value);

        // clear cache
        \Extension\Cache::deleteCache('model.sites-getSiteId');
        \Extension\Cache::deleteCache('model.sites-isSiteEnabled');
        \Extension\Cache::deleteCache('controller.AdminController-generatePage-fs_list_sites');

        // done.
        return true;
    }// addSite


    /**
     * copy new site tables and set default values for some table.
     *
     * @param integer $site_id
     * @return boolean
     */
    public function copyNewSiteTable($site_id = '')
    {
        if (!is_numeric($site_id)) {
            return false;
        }

        // copy tables
        foreach ($this->multisite_tables as $table) {
            $table_withprefix = \DB::table_prefix($table);
            $table_site_withprefix = \DB::table_prefix($site_id . '_' . $table);

            if ($table == 'config') {
                $sql = 'CREATE TABLE IF NOT EXISTS ' . $table_site_withprefix . ' SELECT * FROM ' . $table_withprefix . ' WHERE config_core = 1';
            } else {
                $sql = 'CREATE TABLE IF NOT EXISTS ' . $table_site_withprefix . ' LIKE ' . $table_withprefix;
            }

            \DB::query($sql)->execute();

            // create default values
            if ($table == 'account_level_group') {
                $sql = "INSERT INTO `" . $table_site_withprefix . "` (`level_group_id`, `level_name`, `level_description`, `level_priority`) VALUES
                    (1, 'Super administrator', 'For site owner or super administrator.', 1),
                    (2, 'Administrator', NULL, 2),
                    (3, 'Member', 'For registered user.', 999),
                    (4, 'Guest', 'For non register user.', 1000);";
                \DB::query($sql)->execute();
            }
        }

        unset($sql, $table, $table_site_withprefix, $table_withprefix);

        // loop get account and add default levels
        $exist_account_id = array();
        $result = \DB::select('*')
                ->from('account_level')
                ->as_object()
                ->execute();
        foreach ($result as $row) {
            // check and set level group id
            $lvg = \Model_AccountLevelGroup::getHighestPriorityAccountLevel($row->account_id);
            if ($lvg !== false && $lvg->level_group_id == '1') {
                $level_group_id = '1';
            } else {
                $level_group_id = '3';// 3 is just member. always set to 3 for non super-administrator for safety.
            }
            
            if (!in_array($row->account_id, $exist_account_id)) {
                \DB::insert($site_id . '_account_level')->set(array(
                    'level_group_id' => $level_group_id,
                    'account_id' => $row->account_id
                ))->execute();
                
                $exist_account_id = array_merge($exist_account_id, array($row->account_id));
            }
        }

        // done
        return true;
    }// copyNewSiteTable


    /**
     * delete site tables and site data in sites table.
     *
     * @param integer $site_id
     * @return boolean
     */
    public static function deleteSite($site_id = '')
    {
        // prevent delete site 1
        if ($site_id == '1') {
            return false;
        }

        // delete related _sites tables
        // this can be done by ORM relation itself. I have nothing to do here except something to remove more than just in db, example file, folder

        // drop [site_id]_tables
        foreach (static::forge()->multisite_tables as $table) {
            \DBUtil::drop_table($site_id . '_' . $table);
        }
        
        // delete data in related tables
        \DB::delete(\Model_AccountLogins::getTableName())->where('site_id', $site_id)->execute();
        \DB::delete(\Model_AccountSites::getTableName())->where('site_id', $site_id)->execute();

        // delete this site from sites table
        \DB::delete(static::$_table_name)->where('site_id', $site_id)->execute();
        
        // clear cache
        \Extension\Cache::deleteCache('model.accounts-checkAccount-' . $site_id);
        \Extension\Cache::deleteCache('model.accountLevelPermission-checkLevelPermission-' . $site_id);
        \Extension\Cache::deleteCache('model.accountPermission-checkAccountPermission-' . $site_id);
        \Extension\Cache::deleteCache('model.config-getval-' . $site_id);
        \Extension\Cache::deleteCache('model.config-getvalues-' . $site_id);
        \Extension\Cache::deleteCache('model.sites-getSiteId');
        \Extension\Cache::deleteCache('model.sites-isSiteEnabled');
        \Extension\Cache::deleteCache('controller.AdminController-generatePage-fs_list_sites');

        // done
        return true;
    }// deleteSite


    /**
     * edit site. update site name to config table too.
     *
     * @param array $data
     * @return boolean
     */
    public static function editSite(array $data = array())
    {
        // check site_domain not exists in other site_id
        $match_sites = \DB::select()
            ->from(static::$_table_name)
            ->where('site_id', '!=', $data['site_id'])
            ->where('site_domain', $data['site_domain'])
            ->execute();
        if (count($match_sites) > 0) {
            unset($match_sites);
            return \Lang::get('siteman_domain_currently_exists');
        }
        unset($match_sites);

        // additional data for updating
        $data['site_update'] = time();
        $data['site_update_gmt'] = \Extension\Date::localToGmt();

        // filter data before update
        if ($data['site_id'] == '1') {
            // site 1 always enabled.
            $data['site_status'] = '1';
        }

        $site_id = $data['site_id'];
        unset($data['site_id']);

        // update to db
        \DB::update(static::$_table_name)
            ->where('site_id', $site_id)
            ->set($data)
            ->execute();

        // set config for new site.
        $cfg_data['site_name'] = $data['site_name'];

        if ($site_id == '1') {
            $config_table = 'config';
        } else {
            $config_table = $site_id . '_config';
        }

        foreach ($cfg_data as $cfg_name => $cfg_value) {
            \DB::update($config_table)
                    ->where('config_name', $cfg_name)
                    ->value('config_value', $cfg_value)
                    ->execute();
        }
        unset($cfg_data, $cfg_name, $cfg_value);
        
        // clear cache
        \Extension\Cache::deleteCache('model.sites-getSiteId');
        \Extension\Cache::deleteCache('model.sites-isSiteEnabled');
        \Extension\Cache::deleteCache('controller.AdminController-generatePage-fs_list_sites');

        // done
        return true;
    }// editSite


    /**
     * get current site id
     *
     * @param boolean $enabled_only
     * @param boolean $real_id_only set true to return real site id only, if not found then return false.
     * @return integer
     */
    public static function getSiteId($enabled_only = true, $real_id_only = false)
    {
        // get domain
        if (isset($_SERVER['HTTP_HOST'])) {
            $site_domain = $_SERVER['HTTP_HOST'];
        } elseif (isset($_SERVER['SERVER_NAME'])) {
            $site_domain = $_SERVER['SERVER_NAME'];
        } else {
            $site_domain = 'localhost';
        }
        
        $cache_name = 'model.sites-getSiteId-' 
                . ($enabled_only == true ? 'true' : 'false') . '-'
                . ($real_id_only == true ? 'true' : 'false') . '-'
                . \Extension\Security::formatString($site_domain, 
                    'alphanum_dash_underscore');
        $cached = \Extension\Cache::getSilence($cache_name);

        if (false === $cached) {
            $query = \DB::select()
                ->as_object()
                ->from(static::$_table_name)
                ->where('site_domain', $site_domain);
            if ($enabled_only === true) {
                $query->where('site_status', 1);
            }
            $result = $query->execute();
            unset($query);

            if (count($result) > 0) {
                // found.
                $row = $result->current();

                unset($result, $site_domain);

                \Cache::set($cache_name, $row->site_id, 2592000);
                return $row->site_id;
            }
            // not found, always return 1.
            unset($result, $row, $site_domain);

            if ($real_id_only == false) {
                \Cache::set($cache_name, 1, 2592000);
                return 1;
            } else {
                \Cache::set($cache_name, 'false', 2592000);
                return false;
            }
        }
        
        if ('false' === $cached) {
            return false;
        } else {
            return $cached;
        }
    }// getSiteId
    
    
    /**
     * get table name based on current site.
     * 
     * @return string
     */
    public static function getTableName()
    {
        return static::$_table_name;
    }// getTableName
    
    
    /**
     * get table name with site id prefix
     * 
     * @param string $table_name
     * @return string
     */
    public function getTableSiteId($table_name = '')
    {
        $site_id = static::getSiteId(false);
        
        if ($site_id == '1') {
            return $table_name;
        } else {
            return $site_id . '_' . $table_name;
        }
    }// getTableSiteId
    
    
    /**
     * check if current site is enabled
     * 
     * @return boolean
     */
    public static function isSiteEnabled()
    {
        // always return true if it is main site. (site id 1).
        $site_id = static::getSiteId(false);
        if (1 == $site_id) {
            return true;
        }
        
        // get domain
        if (isset($_SERVER['HTTP_HOST'])) {
            $site_domain = $_SERVER['HTTP_HOST'];
        } elseif (isset($_SERVER['SERVER_NAME'])) {
            $site_domain = $_SERVER['SERVER_NAME'];
        } else {
            $site_domain = 'localhost';
        }
        
        $cache_name = 'model.sites-isSiteEnabled-' 
                . \Extension\Security::formatString($site_domain, 
                    'alphanum_dash_underscore');
        $cached = \Extension\Cache::getSilence($cache_name);
        
        if (false === $cached) {
            $result = \DB::select()
                ->from(static::$_table_name)
                ->where('site_domain', $site_domain)
                ->where('site_status', 1)
                ->execute();
            $total = count($result);

            unset($result, $site_domain);

            if ($total > 0) {
                \Cache::set($cache_name, true, 2592000);
                return true;
            }

            \Cache::set($cache_name, 'false', 2592000);
            return false;
        }
        
        if ('false' === $cached) {
            return false;
        } else {
            return $cached;
        }
    }// isSiteEnabled


    /**
     * list websites from db
     *
     * @param array $option available options: [list_for], [filter_], [orders], [sort], [offset], [limit], [list_for], [unlimit]
     * @return array
     */
    public static function listSites($option = array())
    {

        $query = static::query();
        // where conditions
        if (!isset($option['list_for']) || (isset($option['list_for']) && $option['list_for'] == 'front')) {
            $query->where('site_status', 1);
        }
        
        // filters --------------------------------------------------------------------------------------------------------------------------------------------
        if (isset($option['filter_site_id'])) {
            $query->where('site_id', 'LIKE', '%'.$option['filter_site_id'].'%');
        }
        if (isset($option['filter_site_name'])) {
            $query->where('site_name', 'LIKE', '%'.\Security::htmlentities($option['filter_site_name']).'%');
        }
        if (isset($option['filter_site_domain'])) {
            $query->where('site_domain', 'LIKE', '%'.mb_strtolower(\Security::strip_tags($option['filter_site_domain'])).'%');
        }
        if (isset($option['filter_site_status'])) {
            $query->where('site_status', $option['filter_site_status']);
        }
        // end filters --------------------------------------------------------------------------------------------------------------------------------------

        $output['total'] = $query->count();

        // sort and order
        $allowed_orders = array('site_id', 'site_name', 'site_domain', 'site_status', 'site_create', 'site_update');
        if (!isset($option['orders']) || (isset($option['orders']) && !in_array($option['orders'], $allowed_orders))) {
            $option['orders'] = 'site_id';
        }
        unset($allowed_orders);
        if (!isset($option['sort'])) {
            $option['sort'] = 'ASC';
        }

        // offset and limit
        if (!isset($option['offset'])) {
            $option['offset'] = 0;
        }
        if (!isset($option['limit'])) {
            if (isset($option['list_for']) && $option['list_for'] == 'admin') {
                $option['limit'] = \Model_Config::getval('content_admin_items_perpage');
            } else {
                $option['limit'] = \Model_Config::getval('content_items_perpage');
            }
        }

        // get the results from sort, order, offset, limit.
        $query->order_by($option['orders'], $option['sort']);
        if (!isset($option['unlimit']) || (isset($option['unlimit']) && $option['unlimit'] == false)) {
            $query->offset($option['offset'])->limit($option['limit']);
        }
        $output['items'] = $query->get();

        unset($query);

        return $output;
    }// listSites


}
