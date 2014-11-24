<?php

namespace Tmd;

class Controller_Admin_Test extends \Controller_AdminController
{
    
    
    public function action_accordion()
    {
        $output['page_title'] = $this->generateTitle('jquery ui accordion');
        
        return $this->generatePage('admin/templates/test/accordion_v', $output, false);
    }// action_accordion
    
    
    public function action_datepicker()
    {
        $output['page_title'] = $this->generateTitle('jquery ui date picker');
        
        return $this->generatePage('admin/templates/test/datepicker_v', $output, false);
    }// action_datepicker


    public function action_index()
    {
        echo \Html::anchor('tmd/admin/test/accordion', 'jquery ui accordion').'<br>'."\n";
        echo \Html::anchor('tmd/admin/test/datepicker', 'jquery ui date picker').'<br>'."\n";
        echo \Html::anchor('tmd/admin/test/dialog', 'jquery ui dialog').'<br>'."\n";
        echo \Html::anchor('tmd/admin/test/tabs', 'jquery ui tabs').'<br>'."\n";
    }// action_index


    public function action_dialog()
    {
        $output['page_title'] = $this->generateTitle('jquery ui dialog');
        
        return $this->generatePage('admin/templates/test/dialog_v', $output, false);
    }// action_dialog


    public function action_tabs()
    {
        $output['page_title'] = $this->generateTitle('jquery ui tabs');
        
        return $this->generatePage('admin/templates/test/tabs_v', $output, false);
    }// action_tabs


}
