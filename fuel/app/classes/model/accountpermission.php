<?php
/** 
 * account_permission ORM and reusable functions
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

class Model_AccountPermission extends \Orm\Model 
{
	
	
    protected static $_table_name = 'account_permission';
    protected static $_primary_key = array('permission_id');
    
    // relations
    protected static $_belongs_to = array(
        'accounts' => array(
            'model_to' => 'Model_Accounts',
            'key_from' => 'account_id',
            'key_to' => 'account_id',
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
     * check account permission.
     * This will be check permission per user.
     * 
     * @param string $page_name
     * @param string $action
     * @param integer $account_id
     * @return boolean
     */
    public static function checkAccountPermission($page_name = '', $action = '', $account_id = '')
    {
        // check for required attribute
        if (!is_numeric($account_id) || $page_name == null || $action == null) {
            return false;
        }
        
        if ($account_id == '1') {return true;}// permanent owner's account
        
        $site_id = \Model_Sites::getSiteId(false);
        $cache_name = 'model.accountPermission-checkAccountPermission-' 
                . $site_id . '-'
                . \Extension\Security::formatString($page_name, 'alphanum_dash_underscore') . '-'
                . \Extension\Security::formatString($action, 'alphanum_dash_underscore') . '-'
                . $account_id;
        $cached = \Extension\Cache::getSilence($cache_name);
        
        if (false === $cached) {
            // get current user from db.
            $result = \DB::select()->as_object()->from('accounts')->where('account_id', $account_id)->execute();
            
            if (count($result) > 0) {
                $row = $result->current();
                
                // check this account in permission db.
                $result2 = \DB::select()
                    ->from(static::$_table_name)
                    ->where('account_id', $row->account_id)
                    ->where('permission_page', $page_name)
                    ->where('permission_action', $action)
                    ->execute();

                if (count($result2) > 0) {
                    // found.
                    unset($result, $result2, $row);

                    \Cache::set($cache_name, true, 2592000);
                    return true;
                }
                
                unset($result, $result2, $row);
            }// endif not found account.
            // not found this user or not found permission in db.
            unset($result);

            \Cache::set($cache_name, 'false', 2592000);
            return false;
        }// endif cached
        
        if ('false' === $cached) {
            return false;
        } else {
            return $cached;
        }
    }// checkAccountPermission


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
        return \Model_AccountLevelPermission::checkAdminPermission($page_name, $action, $account_id);
    }// checkAdminPermission
    
    
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
        return \Model_AccountLevelPermission::checkMemberPermission($page_name, $action, $account_id);
    }// checkMemberPermission


    /**
     * fetch permissions from core files (app/classes/controller/admin)
     *
     * @return array
     */
    public static function fetchPermissionsFile()
    {
        return \Model_AccountLevelPermission::fetchPermissionsFile();
    }// fetchPermissionsFile


    /**
     * list permissions that checked
     *
     * @param integer $account_id
     * @param integer $core
     * @param string $module_system_name
     * @return array
     */
    public static function listPermissionChecked($account_id = '', $core = 1, $module_system_name = '')
    {
        $output = array();
        $query = static::query();

        $query->where('account_id', $account_id);
        
        if ($core === 1) {
            $query->where('permission_core', '1');
        } else {
            $query->where('permission_core', '0');
            $query->where('module_system_name', $module_system_name);
        }

        if ($query->count() > 0) {
            foreach ($query->get() as $row) {
                $output[$row->permission_id][$row->permission_page][$row->permission_action] = $row->account_id;
            }
        }

        unset($query, $row);

        return $output;
    }// listPermissionChecked


    /**
     * reset permission
     *
     * @param integer $account_id
     * @param null|integer $core
     * @return boolean
     */
    public static function resetPermission($account_id = '', $core = '')
    {
        if ($core == null) {
            // delete all of this user permission.
            \DB::delete(static::$_table_name)->where('account_id', $account_id)->execute();
            return true;
        } elseif ($core === 1) {
            // delete this user permission that is core permission
            \DB::delete(static::$_table_name)->where('account_id', $account_id)->where('permission_core', '1')->execute();
            return true;
        } elseif ($core === 0) {
            // delete this user module permission
            \DB::delete(static::$_table_name)->where('account_id', $account_id)->where('permission_core', '0')->execute();
            return true;
        }
        
        // clear cache
        \Extension\Cache::deleteCache('model.accountPermission-checkAccountPermission-' . \Model_Sites::getSiteId(false));

        return false;
    }// resetPermission


    /**
     * save permissions
     * save permission for one user.
     *
     * @param integer $account_id
     * @param array $data
     * @return boolean
     */
    public static function savePermissions($account_id = '', array $data = array())
    {
        // loop check permission is not in db, insert it.
        foreach ($data['permission_action'] as $key => $permission_action) {
            if (isset($data['account_id'][$key]) && $data['permission_page'][$key]) {
                // check if permission is in db or not.
                $result = \DB::select()
                    ->from(static::$_table_name)
                    ->where('account_id', $account_id)
                    ->where('permission_page', $data['permission_page'][$key])
                    ->where('permission_action', $permission_action)
                    ->execute();

                if (count($result) <= 0) {
                    // not in db. insert it.
                    \DB::insert(static::$_table_name)
                        ->set([
                            'account_id' => $account_id,
                            'permission_core' => $data['permission_core'],
                            'module_system_name' => $data['module_system_name'],
                            'permission_page' => $data['permission_page'][$key],
                            'permission_action' => $data['permission_action'][$key],
                        ])
                        ->execute();
                }
            }
        }

        // clear unused variables
        unset($entry, $key, $permission_action, $result);

        // now remove permission in db that was not checked.
        foreach ($data['permission_action'] as $key => $permission_action) {
            if (isset($data['permission_page'][$key])) {
                $result = \DB::select()
                    ->as_object()
                    ->from(static::$_table_name)
                    ->where('account_id', $account_id)// find permission on this account id.
                    ->where('permission_core', $data['permission_core'])
                    ->where('module_system_name', $data['module_system_name'])
                    ->where('permission_page', $data['permission_page'][$key])
                    ->where('permission_action', $permission_action)
                    ->execute();

                if (count($result) > 0) {
                    $row = $result->current();
                    
                    if (isset($data['account_id'][$key])) {
                       if (!in_array($row->account_id, array($data['account_id'][$key]))) {
                           \DB::delete(static::$_table_name)->where('permission_id', $row->permission_id)->execute();
                        }
                    } else {
                        // this account's permission was not ticked.
                        \DB::delete(static::$_table_name)->where('permission_id', $row->permission_id)->execute();
                    }
                }
            }
        }

        // clear unused variables
        unset($key, $permission_action, $result, $row);

        $data = array();
        
        // clear cache
        \Extension\Cache::deleteCache('model.accountPermission-checkAccountPermission-' . \Model_Sites::getSiteId(false));

        return true;
    }// savePermissions
	
	
}

