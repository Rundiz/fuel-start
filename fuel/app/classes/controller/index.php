<?php
/** 
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

class Controller_Index extends \Controller 
{
	
	
	/**
	 * default method for this controller.
	 * you may replace code in this method with yours to start build your project.
	 */
	public function action_index() 
	{
		// <head> output ----------------------------------------------------------------------------------------------
		$output['page_title'] = 'Fuel Start';
		// <head> output ----------------------------------------------------------------------------------------------
		
		$theme = \Theme::instance();
		
		return $theme->view('front/templates/index_v', $output);
	}// action_index
	
	
}

