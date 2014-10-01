<?php

namespace Dbhelper;

class Controller_Admin_Index extends \Controller_AdminController
{
    
    
    public function __construct()
    {
        parent::__construct();
        
        // language
        \Lang::load('dbhelper::dbhelper');
    }// __construct
    
    
    public function action_index()
    {
        // clear redirect referrer
        \Session::delete('submitted_redirect');
        
        // read flash message for display errors.
        $form_status = \Session::get_flash('form_status');
        if (isset($form_status['form_status']) && isset($form_status['form_status_message'])) {
            $output['form_status'] = $form_status['form_status'];
            $output['form_status_message'] = $form_status['form_status_message'];
        }
        unset($form_status);
        
        // list tables
        $output['list_tables'] = \DB::list_tables();
        
        // if form submitted
        if (\Input::method() == 'POST') {
            $table_name = trim(\Input::post('table_name'));
            
            if (!\Extension\NoCsrf::check()) {
                // validate token failed
                $output['form_status'] = 'error';
                $output['form_status_message'] = \Lang::get('fslang_invalid_csrf_token');
            } elseif ($table_name == null) {
                $output['form_status'] = 'error';
                $output['form_status_message'] = \Lang::get('dbhelper_please_select_db_table');
            } else {
                $output['list_columns'] = \DB::list_columns(\DB::expr('`' . $table_name . '`'));
            }
        }// endif; form submitted
        
        // <head> output ---------------------------------------------------------------------
        $output['page_title'] = $this->generateTitle(\Lang::get('dbhelper'));
        // <head> output ---------------------------------------------------------------------

        return $this->generatePage('admin/templates/index/index_v', $output, false);
    }// action_index
    
    
    /**
     * get and set submit redirection url
     * 
     * @return string
     */
    private function getAndSetSubmitRedirection()
    {
        $session = \Session::forge();
        
        if ($session->get('submitted_redirect') == null) {
            if (\Input::referrer() != null && \Input::referrer() != \Uri::main()) {
                $session->set('submitted_redirect', \Input::referrer());
                return \Input::referrer();
            } else {
                $redirect_uri = 'dbhelper/admin';
                $session->set('submitted_redirect', $redirect_uri);
                return $redirect_uri;
            }
        } else {
            return $session->get('submitted_redirect');
        }
    }// getAndSetRedirection


}
