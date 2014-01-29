<?php
/** 
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

class Controller_Admin_Config extends \Controller_AdminController 
{
	
	
	public function __construct()
	{
		parent::__construct();

		// load language
		\Lang::load('config', 'config');
	}// __construct
	
	
	/**
	 * define permissions for this app/controller.
	 * 
	 * @return array
	 */
	public function _define_permission() 
	{
		// return array('controller page name' => array('action 1', 'action 2', 'action 3', 'a lot more action. up to you...'));
		return array('config.config_global' => array('config.config_global'));
	}// _define_permission
	
	
	public function action_ajax_test_ftp() 
	{
		// check permission
		if (\Model_AccountLevelPermission::checkAdminPermission('config.config_global', 'config.config_global') == false) {
			\Session::set_flash(
				'form_status',
				array(
					'form_status' => 'error',
					'form_status_message' => \Lang::get('admin.admin_permission_denied', array('page' => \Uri::string()))
				)
			);
			\Response::redirect(\Uri::create('admin'));
		}
		
		// is ajax
		if (! \Input::is_ajax()) {
			\Response::redirect(\Uri::create('admin'));
		}
		
		// load language
		\Lang::load('config', 'config');
		
		if (\Input::method() == 'POST') {
			// get post value and test connection
			$config['hostname'] = trim(\Input::post('hostname'));
			$config['username'] = trim(\Input::post('username'));
			$config['password'] = trim(\Input::post('password'));
			$config['port'] = (int) trim(\Input::post('port'));
			$config['passive'] = (trim(\Input::post('passive')) == 'true' ? true : false);
			$config['ssl_mode'] = false;
			$config['debug'] = false;
			$basepath = trim(\Input::post('basepath'));
			
			// connect to ftp
			$ftp = \Ftp::forge($config);
			$ftp->connect();
			$ftp->change_dir($basepath);
			$files = $ftp->list_files();
			$ftp->close();
			
			$output = array();
			
			if ($files !== false) {
				$output['form_status'] = 'success';
				$output['form_status_message'] = \Lang::get('config.config_ftp_connected_check_basepath_from_dir_structure_below');
				
				natsort($files);
				$output['list_files'] = '<ul>';
				foreach ($files as $file) {
					$output['list_files'] .= '<li>' . $file . '</li>';
				}
				$output['list_files'] .= '</ul>';
			} else {
				// got false from list_files means cannot connect
				$output['form_status'] = 'error';
				$output['form_status_message'] = \Lang::get('config.config_ftp_could_not_connect_to_server');
			}
			
			// clear no use variables
			unset($basepath, $config, $file, $files, $ftp);
			
			// send out json values
			$response = new \Response();
			$response->set_header('Content-Type', 'application/json');
			$response->body(json_encode($output));
			return $response;
		}
	}// action_ajax_test_ftp
	
	
	public function action_index() 
	{
		// check permission
		if (\Model_AccountLevelPermission::checkAdminPermission('config.config_global', 'config.config_global') == false) {
			\Session::set_flash(
				'form_status',
				array(
					'form_status' => 'error',
					'form_status_message' => \Lang::get('admin.admin_permission_denied', array('page' => \Uri::string()))
				)
			);
			\Response::redirect(\Uri::create('admin'));
		}
		
		// load language
		\Lang::load('config', 'config');
		
		// get timezone list for select box
		\Config::load('timezone', 'timezone');
		$output['timezone_list'] = \Config::get('timezone.timezone', array());
		
		// read flash message for display errors.
		$form_status = \Session::get_flash('form_status');
		if (isset($form_status['form_status']) && isset($form_status['form_status_message'])) {
			$output['form_status'] = $form_status['form_status'];
			$output['form_status_message'] = $form_status['form_status_message'];
		}
		unset($form_status);
		
		$allowed_field = array();
		
		// load config to form.
		$result = \DB::select('*')->from('config')->as_object('Model_Config')->where('config_core', '1')->execute();
		if ((is_array($result) || is_object($result)) && !empty($result)) {
			foreach ($result as $row) {
				$allowed_field[] = $row->config_name;
				$output[$row->config_name] = $row->config_value;
			}
		}
		unset($result, $row);
		
		// if form submitted
		if (\Input::method() == 'POST') {
			// store data to variable for update to db.
			$data = array();
			foreach (\Input::post() as $key => $value) {
				if (in_array($key, $allowed_field)) {
					$data[$key] = $value;
				}
			}
			unset($allowed_field);
			
			// check again for some required default value config data.
			// tab website
			$data['site_name'] = \Security::htmlentities($data['site_name']);
			$data['page_title_separator'] = \Security::htmlentities($data['page_title_separator']);
			
			// tab account
			if (!isset($data['member_allow_register']) || $data['member_allow_register'] != '1') {$data['member_allow_register'] = '0';}
			if (!isset($data['member_register_notify_admin']) || $data['member_register_notify_admin'] != '1') {$data['member_register_notify_admin'] = '0';}
			if (!isset($data['simultaneous_login']) || $data['simultaneous_login'] != '1') {$data['simultaneous_login'] = '0';}
			if (!is_numeric($data['member_max_login_fail'])) {$data['member_max_login_fail'] = '10';}
			if (!is_numeric($data['member_login_fail_wait_time'])) {$data['member_login_fail_wait_time'] = '30';}
			if (!is_numeric($data['member_login_remember_length'])) {$data['member_login_remember_length'] = '30';}
			if (!is_numeric($data['member_confirm_wait_time'])) {$data['member_confirm_wait_time'] = '10';}
			if (!isset($data['member_email_change_need_confirm']) || $data['member_email_change_need_confirm'] != '1') {$data['member_email_change_need_confirm'] = '0';}
			if (!isset($data['allow_avatar']) || $data['allow_avatar'] != '1') {$data['allow_avatar'] = '0';}
			if (!is_numeric($data['avatar_size'])) {$data['avatar_size'] = '200';}
			if (empty($data['avatar_allowed_types'])) {$data['avatar_allowed_types'] = 'jpg|jpeg';}
			if ($data['avatar_path'] == null) {unset($data['avatar_path']);}
			
			// tab email
			if ($data['mail_protocol'] == null) {$data['mail_protocol'] = 'mail';}
			if (!is_numeric($data['mail_smtp_port'])) {$data['mail_smtp_port'] = '0';}
			
			// tab content
			if (!is_numeric($data['content_items_perpage'])) {$data['content_items_perpage'] = '10';}
			if (!is_numeric($data['content_admin_items_perpage'])) {$data['content_admin_items_perpage'] = '10';}
			
			// tab media
			if (empty($data['media_allowed_types'])) {$data['media_allowed_types'] = 'avi|doc|docx|flv|gif|jpeg|jpg|mid|midi|mov|mp3|mpeg|mpg|pdf|png|swf|xls|xlsx|zip';}
			
			// tab ftp
			if (!is_numeric($data['ftp_port'])) {$data['ftp_port'] = '21';}
			if (!isset($data['ftp_passive']) || $data['ftp_passive'] != 'false') {$data['ftp_passive'] = 'true';}
			
			// validate form.
			$validate = \Validation::forge();
			
			if (!\Extension\NoCsrf::check()) {
				// validate token failed
				$output['form_status'] = 'error';
				$output['form_status_message'] = \Lang::get('fslang.fslang_invalid_csrf_token');
			} elseif (!$validate->run()) {
				// validate failed
				$output['form_status'] = 'error';
				$output['form_status_message'] = $validate->show_errors();
			} else {
				// try to save config.
				$result = \Model_Config::saveData($data);
				
				if ($result === true) {
					\Session::set_flash(
						'form_status',
						array(
							'form_status' => 'success',
							'form_status_message' => \Lang::get('admin.admin_saved')
						)
					);
					
					\Response::redirect(\Uri::main());
				} else {
					$output['form_status'] = 'error';
					$output['form_status_message'] = $result;
				}
			}
			
			// re-populate form.
			foreach ($data as $key => $value) {
				$output[$key] = html_entity_decode($value);
			}
		}
		
		// <head> output ----------------------------------------------------------------------------------------------
		$output['page_title'] = $this->generateTitle(\Lang::get('config.config_global_configuration'));
		// <head> output ----------------------------------------------------------------------------------------------
		
		return $this->generatePage('admin/templates/config/config_v', $output, false);
	}// action_index
	
	
}

