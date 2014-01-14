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
		// load language
		\Lang::load('fslang', 'fslang');
		
		// <head> output ----------------------------------------------------------------------------------------------
		$output['page_title'] = 'Fuel Start';
		
		// example for asset and theme asset
		//\Asset::css('bootstrap.min.css', array(), 'fuelstart');
		//\Theme::instance()->asset->css('main.css', array(), 'fuelstart');
		//$output['page_meta'][] = \Html::meta('description', 'test-fuel-start-description');
		//$output['page_link'][] = html_tag('link', array('rel' => 'stylesheet', 'href' => Uri::createNL(\Theme::instance()->asset_path('css/main.css'))));
		// end example
		// <head> output ----------------------------------------------------------------------------------------------
		
		$theme = \Theme::instance();
		
		return $theme->view('front/templates/index_v', $output, false);
	}// action_index
	
	
}

