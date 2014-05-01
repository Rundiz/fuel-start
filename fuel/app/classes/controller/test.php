<?php
/** 
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

class Controller_Test extends \Controller_BaseController
{
    
    
    public function action_1inside2column()
    {
        $output['name'] = 'FuelStart';
        
        // <head> output ---------------------------------------------------------------------
        $output['page_title'] = $this->generateTitle('Test');
        // <head> output ---------------------------------------------------------------------
        
        // generate first sub layout. ---------------------------------------------------------
        $layout = $this->generateLayout('front/templates/test/index_v', $output, false, '1column');
        $output = array_merge($output, $layout);
        $layout_file = 'front/templates/test/index_v';
        if (isset($layout['layout_file'])) {
            $layout_file = $layout['layout_file'];
        }
        unset($layout);
        
        // generate second sub layout -------------------------------------------------------
        $layout = $this->generateLayout($layout_file, $output, false, '2column');
        $output = array_merge($output, $layout);
        $layout_file = 'front/templates/test/index_v';
        if (isset($layout['layout_file'])) {
            $layout_file = $layout['layout_file'];
        }
        unset($layout);
        
        return $this->generatePage($layout_file, $output, false);
    }// action_1inside2column
    
    
    public function action_2column()
    {
        $output['name'] = 'FuelStart';
        
        // <head> output ---------------------------------------------------------------------
        $output['page_title'] = $this->generateTitle('Test');
        // <head> output ---------------------------------------------------------------------
        
        return $this->generateLayoutAndPage('front/templates/test/index_v', $output, false, '2column');
    }// action_2column
    
    
    public function action_index()
    {
        $output['name'] = 'FuelStart';
        
        // <head> output ---------------------------------------------------------------------
        $output['page_title'] = $this->generateTitle('Test');
        // <head> output ---------------------------------------------------------------------
        
        $layout = $this->generateLayout('front/templates/test/index_v', $output, false, '1column');
        
        $output = array_merge($layout, $output);
        $layout_file = 'front/templates/test/index_v';
        if (isset($layout['layout_file'])) {
            $layout_file = $layout['layout_file'];
        }
        unset($layout);
        
        return $this->generatePage($layout_file, $output, false);
    }// action_index
    
    
}

