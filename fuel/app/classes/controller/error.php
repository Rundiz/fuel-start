<?php
/** 
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

class Controller_Error extends \Controller 
{
	
	
	public function action_404() 
	{
		Lang::load('error', 'error');
		
		$output['error_head'] = Lang::get('error.404_error_head');
		$output['error_content'] = Lang::get('error.404_error_content', array('home_link' => Uri::base()));
		
		// <head> output ----------------------------------------------------------------------------------------------
		$output['page_title'] = Lang::get('error.404_page_title');
		// <head> output ----------------------------------------------------------------------------------------------
		
		return Response::forge(Theme::instance()->view('error/404_v', $output)->auto_filter(false), 404);
	}// action_404
	
	
}

