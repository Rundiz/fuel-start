<?php
/**
 * Base Controller of Fuel Start
 *
 * @package FuelStart
 * @version 1.6.1
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * @link http://okvee.net/th/web-resources/download/fuel-start
 *
 */

abstract class Controller_BaseController extends \Controller
{
    
    
    public $theme_system_name;


    public function __construct()
    {
        // check that site was enabled.
        if (!\Model_Sites::isSiteEnabled()) {
            $request = \Request::forge('error/403')->execute();
            $response = new \Response($request, 403);
            $response->set_status(403);
            $response->send(true);
            
            unset($request, $response);
            exit;
        }

        // fix changed current language but autoload not reload
        \Lang::load('fslang');

        // call web cron to run tasks (including purge old login history)
        \Library\WebCron::forge()->init();
        
        // set default theme name
        // @todo [fuelstart][theme] for theme management. you should get default theme setting from db here.
        \Config::load('theme', true);
        $theme_active = \Config::get('theme.active');
        $this->theme_system_name = $theme_active;
        unset($theme_active);
    }// __construct
    
    
    /**
     * Generate the page layout.
     * Use this method if you want to generate sub layout (sub layout is not the whole page template.)
     * You can use this method to generate sub layout multiple time and you have to call generatePage.
     * 
     * @param string $view Path to view of current controller.
     * @param array $output The data that will be send to view file.
     * @param boolean $auto_filter Auto filter html?
     * @param string $layout The layout file.
     * @return array The generate content put in [layout_content] and the layout file that ready for create view put in [layout_file]
     */
    public function generateLayout($view = null, $output = array(), $auto_filter = null, $layout = null)
    {
        if (!is_array($output)) {
            $output = array();
        }
        
        // start theme class
        $theme = \Theme::instance();
        $theme->active($this->theme_system_name);

        $output['layout_content'] = $theme->view($view, $output, $auto_filter);
        $output['layout_file'] = 'front/layout/' . $layout;

        return $output;
    }// generateLayout
    
    
    /**
     * Generate the page layout and then generate page.
     * Use this method if you want to generate sub layout and then generate page immediately.
     * 
     * @param string $view Path to view of current controller.
     * @param array $output The data that will be send to view file.
     * @param boolean $auto_filter Auto filter html?
     * @param string $layout The layout file.
     * @return view
     */
    public function generateLayoutAndPage($view = null, $output = array(), $auto_filter = null, $layout = null)
    {
        $layout = $this->generateLayout($view, $output, $auto_filter, $layout);
        
        $output = array_merge($layout, $output);
        
        $layout_file = $view;
        if (isset($layout['layout_file'])) {
            $layout_file = $layout['layout_file'];
        }
        unset($layout);
        
        return $this->generatePage($layout_file, $output, false);
    }// generateLayoutAndPage


    /**
     * Generate the whole page template.
     *
     * @param string $view Path to view of current controller.
     * @param array $output The data that will be send to view file.
     * @param boolean $auto_filter Auto filter html?
     * @return view
     */
    public function generatePage($view = null, $output = array(), $auto_filter = null)
    {
        if (!is_array($output)) {
            $output = array();
        }

        // start theme class
        $theme = \Theme::instance();
        $theme->active($this->theme_system_name);

        // load requested controller theme into page_content variable.
        $output['page_content'] = $theme->view($view, $output, $auto_filter);

        // load main template and put page_content variable in it.
        return $theme->view('front/template', $output, $auto_filter);
    }// generatePage


    /**
     * Generate title by set title name and name separator position.
     * 
     * @param string|array $title Title name. This can be array that the title will be generate respectively.
     * @param string $name_position Position of name to generate. if first, the site name will come first then title name. example: site name | title 1 | title 2
     * @return string Generated title.
     */
    public function generateTitle($title, $name_position = 'last')
    {
        $cfg_values = array('site_name', 'page_title_separator');
        $config = Model_Config::getvalues($cfg_values);
        unset($cfg_values);

        // @todo [fuelstart][basecontroller][plug] generate title plug.
        $plugin = new \Library\Plugins();
        if ($plugin->hasFilter('BaseControllerGenTitle')) {
            $generated_title = $plugin->doFilter('BaseControllerGenTitle', $title, $name_position, $config);
            if (is_string($generated_title) && ($generated_title != null || !empty($generated_title))) {
                return $generated_title;
            }
            unset($generated_title);
        }
        unset($plugin);

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


    public function getMyAccountId()
    {
        $account_id = 0;
        $ca = \Model_Accounts::forge()->getAccountCookie('admin');
        if (isset($ca['account_id'])) {
            $account_id = $ca['account_id'];
        }
        
        unset($ca);
        
        return $account_id;
    }// getMyAccountId


    public function responseJson($output)
    {
        $response = new \Response();
        // no cache
        $response->set_header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
        $response->set_header('Cache-Control', 'post-check=0, pre-check=0', false);
        $response->set_header('Expires', 'Sat, 26 Jul 1997 05:00:00 GMT');
        $response->set_header('Pragma', 'no-cache');
        // content type
        $response->set_header('Content-Type', 'application/json');
        // set body
        if ($output == null) {
            $output = [];
        }
        $response->body(json_encode($output));
        return $response;
    }// responseJson


}
