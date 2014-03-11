<?php
/**
 *
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 *
 */

namespace Fs;

class Controller_Update extends \Controller
{


    public function __construct(\Request $request)
    {
        parent::__construct($request);

        // load language
        \Lang::load('fs::fs');
    }// __construct


    public function action_index()
    {
        if (\Input::method() == 'POST') {
            if (!\Extension\NoCsrf::check()) {
                // validate token failed
                $output['form_status'] = 'error';
                $output['form_status_message'] = \Lang::get('fslang.fslang_invalid_csrf_token');
            } else {
                // update to 1.5 first time
                $result = \Fs\update0001::run();

                if ($result === true) {
                    $output['hide_form'] = true;
                    $output['form_status'] = 'success';
                    $output['form_status_message'] = \Lang::get('fs_update_completed');
                } else {
                    $output['form_status'] = 'error';
                    $output['form_status_message'] = \Lang::get('fs_failed_to_update');
                }
            }
        }

        // <head> output ----------------------------------------------------------------------------------------------
        $output['page_title'] = \Lang::get('fs_updater');
        // <head> output ----------------------------------------------------------------------------------------------

        $theme = \Theme::instance();
        return $theme->view('update_v', $output, false);
    }// action_index


}
