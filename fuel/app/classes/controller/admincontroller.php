<?php
/** 
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

class Controller_AdminController extends \Controller_BaseController 
{
	
	
	public function __construct()
	{
		parent::__construct();
		
		// validate admin logged in
		if (\Model_Accounts::isAdminLogin() == false) {
			\Response::redirect(\Uri::create('admin/login') . '?rdr=' . urlencode(\Uri::main()));
		}
		
		// load global admin language
		\Lang::load('admin', 'admin');
	}// __construct
	
	
	/**
	 * generate whole page
	 * 
	 * @param string $view path to view of current controller.
	 * @param array $output
	 * @param boolean $auto_filter
	 * @return view
	 */
	public function generatePage($view = null, $output = array(), $auto_filter = null) 
	{
		if (!is_array($output)) {
			$output = array();
		}
		
		// start theme class
		$theme = \Theme::instance();
		$theme->active('system');
		
		// load requested controller theme into page_content variable.
		$output['page_content'] = $theme->view($view, $output, $auto_filter);
		
		// load main template and put page_content variable in it.
		return $theme->view('admin/template', $output, $auto_filter);
	}// generatePage
	
	
}

