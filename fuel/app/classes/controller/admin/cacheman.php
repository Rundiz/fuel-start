<?php
/** 
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

class Controller_Admin_Cacheman extends \Controller_AdminController
{
	
	
    public function __construct()
    {
        parent::__construct();
        
        // load language
        \Lang::load('cacheman');
    }// __construct
    
    
    public function _define_permission()
    {
        return array('cacheman_perm' => array('cacheman_clearcache_perm'));
    }// _define_permission
    
    
    public function action_index()
    {
        // check permission
        if (\Model_AccountLevelPermission::checkAdminPermission('cacheman_perm', 'cacheman_clearcache_perm') == false) {
            \Session::set_flash(
                'form_status',
                array(
                    'form_status' => 'error',
                    'form_status_message' => \Lang::get('admin_permission_denied', array('page' => \Uri::string()))
                )
            );
            \Response::redirect(\Uri::create('admin'));
        }
        
        // read flash message for display errors.
        $form_status = \Session::get_flash('form_status');
        if (isset($form_status['form_status']) && isset($form_status['form_status_message'])) {
            $output['form_status'] = $form_status['form_status'];
            $output['form_status_message'] = $form_status['form_status_message'];
        }
        unset($form_status);
        
        // if form submitted
        if (\Input::method() == 'POST') {
            $act = \Input::post('act');
            
            if ($act == 'clear') {
                \Extension\Cache::deleteCache('ALL');
                
                \Session::set_flash(
                    'form_status',
                    array(
                        'form_status' => 'success',
                        'form_status_message' => \Lang::get('cacheman_all_cleared')
                    )
                );
            }
            
            // go back
            \Response::redirect(\Uri::create('admin/cacheman'));
        }
        
        // <head> output ----------------------------------------------------------------------------------------------
        $output['page_title'] = $this->generateTitle(\Lang::get('cacheman'));
        // <head> output ----------------------------------------------------------------------------------------------

        return $this->generatePage('admin/templates/cache/index_v', $output, false);
    }// action_index
	
	
}

