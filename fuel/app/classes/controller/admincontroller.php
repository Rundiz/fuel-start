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
        
        // list sites to display links in admin page
        $list_sites_option['list_for'] = 'admin';
        $list_sites_option['unlimit'] = true;
        $list_sites = \Model_Sites::listSites($list_sites_option);
        
        if (isset($list_sites['total']) && $list_sites['total'] > 1) {
            if (isset($list_sites['items']) && is_array($list_sites['items']) && !empty($list_sites['items'])) {
                $output['fs_list_sites'] = $list_sites['items'];
            } else {
                $output['fs_list_sites'] = null;
            }
        }
        unset($list_sites, $list_sites_option);

        // start theme class
        $theme = \Theme::instance();
        $theme->active('system');

        // load requested controller theme into page_content variable.
        $output['page_content'] = $theme->view($view, $output, $auto_filter);

        // load main template and put page_content variable in it.
        return $theme->view('admin/template', $output, $auto_filter);
    }// generatePage


}
