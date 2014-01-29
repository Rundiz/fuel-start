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
	
	
	public function __construct()
	{
		parent::__construct();
		
		$this->app_admin_path = APPPATH . 'classes' . DS . 'controller' . DS . 'admin' . DS;
	}// __construct
	
	
	/**
	 * check admin permission
	 * check permission match to user'sgroup_id page_name and action
	 * @param integer $account_id
	 * @param string $page_name
	 * @param string $action
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
		
		// check for required attribute
		if (!is_numeric($account_id) || $page_name == null || $action == null) {
			return false;
		}
		
		if ($account_id == '1') {return true;}// permanent owner's account
		
		// get current user levels from db.
		$query = \Model_AccountLevel::query()->where('account_id', $account_id);
		
		if ($query->count() > 0) {
			// loop each level of this user.
			foreach ($query->get() as $row) {
				if ($row->level_group_id == '1') {
					// this user is in super admin group.
					unset($query, $row);
					
					return true;
				}
				
				// check this level group in permission db.
				$query2 = self::query()
							->where('level_group_id', $row->level_group_id)
							->where('permission_page', $page_name)
							->where('permission_action', $action);
				
				if ($query2->count() > 0) {
					// found.
					unset($query2, $row);
					
					return true;
				}
				
				unset($query2);
			}// endforeach;
			// not found in permission db. did not given any permission.
			unset($query, $row);
			
			return false;
		}
		// not found this user role?
		unset($query);
		
		return false;
	}// checkAdminPermission
	
	
	public static function fetchPermissionsFile() 
	{
		
		$permission_array = array();
		$self = self::forge();
		$controller_prefix = 'Controller_Admin_';
		
		if (is_dir($self->app_admin_path)) {
			/*if ($dh = opendir($self->app_admin_path)) {
				while ($file = readdir($dh) !== false) {
					if ($file != '.' && $file != '..' && is_file($self->app_admin_path . $file)) {
						
					}
				}
			}*/
			$files = \Extension\File::readDir2D($self->app_admin_path);
			natsort($files);
			
			foreach ($files as $file) {
				$file_name = str_replace($self->app_admin_path, '', $file);
				if (is_file($file)) {
					// prevent re-declare self class.
					if ($file_name != 'accountpermission') {
						include_once $file;
					}
					
					$file_to_class = $controller_prefix . str_replace(array('.php', DS), array('', '_'), $file_name);
					$obj = new $file_to_class;
					
					if (method_exists($obj, '_define_permission')) {
						$permission_array = array_merge($permission_array, $obj->_define_permission());
					}
				}
			}
		}
		
		unset($controller_prefix, $files, $file, $file_name, $file_to_class, $obj, $self);
		
		return $permission_array;
	}// fetchPermissionsFile


}

