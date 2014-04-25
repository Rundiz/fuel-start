<?php
/**
 * account_level_permission ORM and reusable functions
 *
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 *
 */

class Model_AccountLevelPermission extends \Orm\Model
{


    protected static $_table_name = 'account_level_permission';
    protected static $_primary_key = array('permission_id');

    // relations
    protected static $_belongs_to = array(
        'account_level_group' => array(
            'model_to' => 'Model_AccountLevelGroup',
            'key_from' => 'level_group_id',
            'key_to' => 'level_group_id',
        )
    );


    public $app_admin_path;


    public function __construct(array $data = array(), $new = true, $view = null, $cache = true)
    {
        parent::__construct($data, $new, $view, $cache);

        $this->app_admin_path = APPPATH . 'classes' . DS . 'controller' . DS . 'admin' . DS;
    }// __construct


    /**
     * run before initialize the class
     * use this method to set new table prefix with multisite.
     */
    public static function _init()
    {
        // get current site id
        $site_id = \Model_Sites::getSiteId(false);

        if ($site_id != '1') {
            static::$_table_name = $site_id . '_' . static::$_table_name;
        }
    }// _init


    /**
     * check admin permission
     * if account id is not set, get it from admin cookie.
     * 
     * @param string $page_name
     * @param string $action
     * @param integer $account_id
     * @return boolean
     */
    public static function checkAdminPermission($page_name = '', $action = '', $account_id = '')
    {
        if ($account_id == null) {
            // account id is empty, get it from cookie.
            $model_accounts = new \Model_Accounts();
            $ca_account = $model_accounts->getAccountCookie('admin');
            $account_id = (isset($ca_account['account_id']) ? $ca_account['account_id'] : '0');

            unset($ca_account, $model_accounts);
        }
        
        // check level or role's permission.
        $permission_result = static::checkLevelPermission($page_name, $action, $account_id);
        
        if ($permission_result === true) {
            return true;
        } else {
            // level or role's permission return false. check user's permission.
            return \Model_AccountPermission::checkAccountPermission($page_name, $action, $account_id);
        }
    }// checkAdminPermission
    
    
    /**
     * check level permission
     * check permission based on user's level group id and page name and action.
     * 
     * @param string $page_name
     * @param string $action
     * @param integer $account_id
     * @return boolean
     */
    private static function checkLevelPermission($page_name = '', $action = '', $account_id = '')
    {
        // check for required attribute
        if (!is_numeric($account_id) || $page_name == null || $action == null) {
            return false;
        }

        if ($account_id == '1') {return true;}// permanent owner's account
        
        $site_id = \Model_Sites::getSiteId(false);
        $cache_name = 'model.accountLevelPermission-checkLevelPermission-' 
                . $site_id . '-'
                . \Extension\Security::formatString($page_name, 'alphanum_dash_underscore') . '-'
                . \Extension\Security::formatString($action, 'alphanum_dash_underscore') . '-'
                . $account_id;
        $cached = \Extension\Cache::getSilence($cache_name);

        if (false === $cached) {
            // get current user levels from db.
            $query = \Model_AccountLevel::query()->where('account_id', $account_id);

            if ($query->count() > 0) {
                // loop each level of this user.
                foreach ($query->get() as $row) {
                    if ($row->level_group_id == '1') {
                        // this user is in super admin group.
                        unset($query, $row);

                        \Cache::set($cache_name, true, 2592000);
                        return true;
                    }

                    // check this level group in permission db.
                    $query2 = static::query()
                                ->where('level_group_id', $row->level_group_id)
                                ->where('permission_page', $page_name)
                                ->where('permission_action', $action);

                    if ($query2->count() > 0) {
                        // found.
                        unset($query2, $row);

                        \Cache::set($cache_name, true, 2592000);
                        return true;
                    }

                    unset($query2);
                }// endforeach;
                // not found in permission db. did not given any permission.
                unset($query, $row);

                \Cache::set($cache_name, 'false', 2592000);
                return false;
            }
            // not found this user role?
            unset($query);

            \Cache::set($cache_name, 'false', 2592000);
            return false;
        }
        
        if ('false' === $cached) {
            return false;
        } else {
            return $cached;
        }
    }// checkLevelPermission
    
    
    /**
     * check member permission.
     * if account id is not set, get it from member cookie.
     * 
     * @param string $page_name
     * @param string $action
     * @param integer $account_id
     * @return boolean
     */
    public static function checkMemberPermission($page_name = '', $action = '', $account_id = '')
    {
        if ($account_id == null) {
            // account id is empty, get it from cookie.
            $model_accounts = new \Model_Accounts();
            $cm_account = $model_accounts->getAccountCookie('member');
            $account_id = (isset($cm_account['account_id']) ? $cm_account['account_id'] : '0');

            unset($cm_account, $model_accounts);
        }
        
        return static::checkAdminPermission($page_name, $action, $account_id);
    }// checkMemberPermission


    /**
     * fetch permissions from core files (app/classes/controller/admin)
     *
     * @return array
     */
    public static function fetchPermissionsFile()
    {
        $permission_array = array();
        $self = static::forge();
        $controller_prefix = 'Controller_Admin_';

        if (is_dir($self->app_admin_path)) {
            $files = \Extension\File::readDir2D($self->app_admin_path);
            natsort($files);

            foreach ($files as $file) {
                $file_name = str_replace($self->app_admin_path, '', $file);
                if (is_file($file)) {
                    // prevent re-declare self class.
                    if ($file_name != 'accountpermission') {
                        include_once $file;
                    }

                    $file_to_class = $controller_prefix . ucwords(str_replace(array('.php', DS), array('', '_'), $file_name));

                    if (class_exists($file_to_class)) {
                        $obj = new $file_to_class;

                        if (method_exists($obj, '_define_permission')) {
                            $permission_array = array_merge($permission_array, $obj->_define_permission());
                        }
                    }
                }
            }
        }

        unset($controller_prefix, $files, $file, $file_name, $file_to_class, $obj, $self);

        return $permission_array;
    }// fetchPermissionsFile


    /**
     * list permissions that checked
     *
     * @param integer $core
     * @param string $module_system_name
     * @return array
     */
    public static function listPermissionChecked($core = 1, $module_system_name = '')
    {
        $output = array();
        $query = static::query();

        if ($core === 1) {
            $query->where('permission_core', '1');
        } else {
            $query->where('permission_core', '0');
            $query->where('module_system_name', $module_system_name);
        }

        if ($query->count() > 0) {
            foreach ($query->get() as $row) {
                $output[$row->permission_id][$row->permission_page][$row->permission_action] = $row->level_group_id;
            }
        }

        unset($query, $row);

        return $output;
    }// listPermissionChecked


    /**
     * reset permission
     *
     * @param null|integer $core
     * @return boolean
     */
    public static function resetPermission($core = '')
    {
        if ($core == null) {
            // reset all permissions
            \DBUtil::truncate_table(static::$_table_name);
            return true;
        } elseif ($core === 1) {
            // reset core permissions
            static::query()->where('permission_core', '1')->delete();
            return true;
        } elseif ($core === 0) {
            // reset modules permissions
            static::query()->where('permission_core', '0')->delete();
            return true;
        }
        
        // clear cache
        \Extension\Cache::deleteCache('model.accountLevelPermission-checkLevelPermission-' . \Model_Sites::getSiteId(false));

        return false;
    }// resetPermission


    /**
     * save permissions
     *
     * @param array $data
     * @return boolean
     */
    public static function savePermissions(array $data = array())
    {
        // loop check permission is not in db, insert it.
        foreach ($data['level_group_id'] as $key => $lv_groups) {
            foreach ($lv_groups as $level_group_id) {
                // check if permission is in db or not.
                $query = static::query()
                        ->where('level_group_id', $level_group_id)
                        ->where('permission_page', $data['permission_page'][$key])
                        ->where('permission_action', $data['permission_action'][$key]);

                if ($query->count() <= 0) {
                    // not in db. insert it.
                    $entry = static::forge();
                    $entry->level_group_id = $level_group_id;
                    $entry->permission_core = $data['permission_core'];
                    $entry->module_system_name = $data['module_system_name'];
                    $entry->permission_page = $data['permission_page'][$key];
                    $entry->permission_action = $data['permission_action'][$key];
                    $entry->save();
                }
            }
        }

        // clear unused variables
        unset($entry, $key, $level_group_id, $lv_groups, $query);

        // now remove permission in db that was not checked.
        foreach ($data['permission_action'] as $key => $permission_action) {
            if (isset($data['permission_page'][$key])) {
                $query = static::query()
                        ->where('permission_core', $data['permission_core'])
                        ->where('module_system_name', $data['module_system_name'])
                        ->where('permission_page', $data['permission_page'][$key])
                        ->where('permission_action', $permission_action);

                if ($query->count() > 0) {
                    foreach ($query->get() as $row) {
                        if (isset($data['level_group_id'][$key])) {
                            if (!in_array($row->level_group_id, $data['level_group_id'][$key])) {
                                static::find($row->permission_id)->delete();
                            }
                        } else {
                            static::find($row->permission_id)->delete();
                        }
                    }
                }
            }
        }

        // clear unused variables
        unset($key, $permission_action, $query, $row);

        $data = array();
        
        // clear cache
        \Extension\Cache::deleteCache('model.accountLevelPermission-checkLevelPermission-' . \Model_Sites::getSiteId(false));

        return true;
    }// savePermission


}
