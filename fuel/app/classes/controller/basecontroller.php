<?php
/** 
 * Base Controller
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

abstract class Controller_BaseController extends \Controller 
{
	
	
	public function __construct()
	{
		// fix changed current language but autoload not reload
		\Lang::load('fslang', 'fslang');
		
		// call web cron to run tasks (including purge old login history)
		\Library\WebCron::forge()->init();
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
		return $theme->view('front/template', $output, $auto_filter);
	}// generatePage
	
	
	public function generateTitle($title, $name_position = 'last') 
	{
		$cfg_values = array('site_name', 'page_title_separator');
		$config = Model_Config::getvalues($cfg_values);
		unset($cfg_values);
		
		// @todo [api] generate title if condition here.
		
		if ($name_position == 'first') {
			$output = $config['site_name']['value'];
			$output .= $config['page_title_separator']['value'];
		} else {
			$output = '';
		}
			
		if (is_array($title)) {
			if ($name_position == 'last') {
				$title = array_reverse($title);
			}
			
			foreach ($title as $a_title) {
				$output .= $a_title;
				if ($a_title != end($title)) {
					$output .= $config['page_title_separator']['value'];
				}
			}
		} else {
			$output .= $title;
		}
			
		if ($name_position == 'last') {
			$output .= $config['page_title_separator']['value'];
			$output .= $config['site_name']['value'];
		}
		
		unset($a_title, $config);
		
		return $output;
	}// generateTitle
	
	
}

