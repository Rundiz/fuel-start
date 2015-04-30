<?php
/**
 *
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 *
 */

class Controller_Admin_AccountLevel extends \Controller_AdminController
{


    /**
     *disallowed edit or delete level_group_id
     *
     * @var array array of disallowed ids
     */
    public $disallowed_edit_delete;


    public function __construct()
    {
        parent::__construct();

        // load language
        \Lang::load('accountlv');

        // set disallowed ids
        $this->disallowed_edit_delete = \Model_AccountLevelGroup::forge()->disallowed_edit_delete;
    }// __construct


    /**
     * define permissions for this app/controller.
     *
     * @return array
     */
    public function _define_permission()
    {
        // return array('controller page name' => array('action 1', 'action 2', 'action 3', 'a lot more action. up to you...'));
        return array('accountlv_perm' => array('accountlv_viewlevels_perm', 'accountlv_add_perm', 'accountlv_edit_perm', 'accountlv_delete_perm', 'accountlv_sort_perm'));
    }// _define_permission


    public function action_add()
    {
        // set redirect url
        $redirect = $this->getAndSetSubmitRedirection();
        
        // check permission
        if (\Model_AccountLevelPermission::checkAdminPermission('accountlv_perm', 'accountlv_add_perm') == false) {
            \Session::set_flash(
                'form_status',
                array(
                    'form_status' => 'error',
                    'form_status_message' => \Lang::get('admin_permission_denied', array('page' => \Uri::string()))
                )
            );
            \Response::redirect($redirect);
        }

        // load language
        \Lang::load('account');

        // read flash message for display errors.
        $form_status = \Session::get_flash('form_status');
        if (isset($form_status['form_status']) && isset($form_status['form_status_message'])) {
            $output['form_status'] = $form_status['form_status'];
            $output['form_status_message'] = $form_status['form_status_message'];
        }
        unset($form_status);

        // if form submitted
        if (\Input::method() == 'POST') {
            // store data for save in db
            $data['level_name'] = \Security::htmlentities(trim(\Input::post('level_name')));
            $data['level_description'] = \Security::htmlentities(trim(\Input::post('level_description')));

            // validate form.
            $validate = \Validation::forge();
            $validate->add('level_name', \Lang::get('accountlv_role'), array(), array('required'));

            if (!\Extension\NoCsrf::check()) {
                // validate token failed
                $output['form_status'] = 'error';
                $output['form_status_message'] = \Lang::get('fslang_invalid_csrf_token');
            } elseif (!$validate->run()) {
                // validate failed
                $output['form_status'] = 'error';
                $output['form_status_message'] = $validate->show_errors();
            } else {
                // save
                $result = \Model_AccountLevelGroup::addLevel($data);

                if ($result === true) {
                    if (\Session::get_flash('form_status', null, false) == null) {
                        \Session::set_flash(
                            'form_status',
                            array(
                                'form_status' => 'success',
                                'form_status_message' => \Lang::get('admin_saved')
                            )
                        );
                    }

                    \Response::redirect($redirect);
                } else {
                    $output['form_status'] = 'error';
                    $output['form_status_message'] = $result;
                }
            }

            // re-populate form
            $output['level_name'] = $data['level_name'];
            $output['level_description'] = $data['level_description'];
        }

        // <head> output ----------------------------------------------------------------------------------------------
        $output['page_title'] = $this->generateTitle(\Lang::get('accountlv_role'));
        // <head> output ----------------------------------------------------------------------------------------------
        
        // breadcrumb -------------------------------------------------------------------------------------------------
        $page_breadcrumb = [];
        $page_breadcrumb[0] = ['name' => \Lang::get('admin_admin_home'), 'url' => \Uri::create('admin')];
        $page_breadcrumb[1] = ['name' => \Lang::get('accountlv_role'), 'url' => \Uri::create('admin/account-level')];
        $page_breadcrumb[2] = ['name' => \Lang::get('accountlv_add_role'), 'url' => \Uri::main()];
        $output['page_breadcrumb'] = $page_breadcrumb;
        unset($page_breadcrumb);
        // breadcrumb -------------------------------------------------------------------------------------------------

        return $this->generatePage('admin/templates/accountlevel/form_v', $output, false);
    }// action_add


    public function action_ajaxsort()
    {
        // set redirect url
        $redirect = $this->getAndSetSubmitRedirection();

        // if not ajax
        if (!\Input::is_ajax()) {
            \Response::redirect($redirect);
        }
        
        // check permission
        if (\Model_AccountLevelPermission::checkAdminPermission('accountlv_perm', 'accountlv_sort_perm') == false) {
            \Session::set_flash(
                'form_status',
                array(
                    'form_status' => 'error',
                    'form_status_message' => \Lang::get('admin_permission_denied', array('page' => \Uri::string()))
                )
            );
            return null;
        }

        $output['result'] = false;

        if (\Input::method() == 'POST') {
            $lvg_ids = \Input::post('listItem');

            if (is_array($lvg_ids)) {
                $level_priority = 3;
                foreach ($lvg_ids as $level_group_id) {
                    $alg = \Model_AccountLevelGroup::find($level_group_id);
                    $alg->level_priority = $level_priority;
                    $alg->save();

                    $level_priority++;
                }

                $output['result'] = true;

                if (\Session::get_flash('form_status', null, false) == null) {
                    \Session::set_flash(
                        'form_status',
                        array(
                            'form_status' => 'success',
                            'form_status_message' => \Lang::get('admin_saved')
                        )
                    );
                }
            }

            unset($alg, $lvg_ids, $level_group_id, $level_priority);
        }

        $response = new \Response();
        $response->set_header('Content-Type', 'application/json');
        $response->body(json_encode($output));

        return $response;
    }// action_ajaxsort


    public function action_edit($level_group_id = '')
    {
        // set redirect url
        $redirect = $this->getAndSetSubmitRedirection();
        
        // check permission
        if (\Model_AccountLevelPermission::checkAdminPermission('accountlv_perm', 'accountlv_edit_perm') == false) {
            \Session::set_flash(
                'form_status',
                array(
                    'form_status' => 'error',
                    'form_status_message' => \Lang::get('admin_permission_denied', array('page' => \Uri::string()))
                )
            );
            \Response::redirect($redirect);
        }

        // force $level_group_id to be integer
        $level_group_id = (int) $level_group_id;

        // load language
        \Lang::load('account');

        // read flash message for display errors.
        $form_status = \Session::get_flash('form_status');
        if (isset($form_status['form_status']) && isset($form_status['form_status_message'])) {
            $output['form_status'] = $form_status['form_status'];
            $output['form_status_message'] = $form_status['form_status_message'];
        }
        unset($form_status);

        // get data to edit
        $alg = \Model_AccountLevelGroup::find($level_group_id);

        // if not found
        if ($alg == null) {
            \Response::redirect($redirect);
        }

        // set output data for form
        foreach ($alg as $key => $value) {
            $output[$key] = $value;
        }

        unset($alg, $key, $value);

        // if form submitted
        if (\Input::method() == 'POST') {
            // store data for save in db
            $data['level_group_id'] = $level_group_id;
            $data['level_name'] = \Security::htmlentities(trim(\Input::post('level_name')));
            $data['level_description'] = \Security::htmlentities(trim(\Input::post('level_description')));

            // validate form.
            $validate = \Validation::forge();
            $validate->add('level_name', \Lang::get('accountlv_role'), array(), array('required'));

            if (!\Extension\NoCsrf::check()) {
                // validate token failed
                $output['form_status'] = 'error';
                $output['form_status_message'] = \Lang::get('fslang_invalid_csrf_token');
            } elseif (!$validate->run()) {
                // validate failed
                $output['form_status'] = 'error';
                $output['form_status_message'] = $validate->show_errors();
            } else {
                // save
                $result = \Model_AccountLevelGroup::editLevel($data);

                if ($result === true) {
                    if (\Session::get_flash('form_status', null, false) == null) {
                        \Session::set_flash(
                            'form_status',
                            array(
                                'form_status' => 'success',
                                'form_status_message' => \Lang::get('admin_saved')
                            )
                        );
                    }

                    \Response::redirect($redirect);
                } else {
                    $output['form_status'] = 'error';
                    $output['form_status_message'] = $result;
                }
            }

            // re-populate form
            $output['level_name'] = $data['level_name'];
            $output['level_description'] = $data['level_description'];
        }

        // <head> output ----------------------------------------------------------------------------------------------
        $output['page_title'] = $this->generateTitle(\Lang::get('accountlv_role'));
        // <head> output ----------------------------------------------------------------------------------------------
        
        // breadcrumb -------------------------------------------------------------------------------------------------
        $page_breadcrumb = [];
        $page_breadcrumb[0] = ['name' => \Lang::get('admin_admin_home'), 'url' => \Uri::create('admin')];
        $page_breadcrumb[1] = ['name' => \Lang::get('accountlv_role'), 'url' => \Uri::create('admin/account-level')];
        $page_breadcrumb[2] = ['name' => \Lang::get('accountlv_edit_role'), 'url' => \Uri::main()];
        $output['page_breadcrumb'] = $page_breadcrumb;
        unset($page_breadcrumb);
        // breadcrumb -------------------------------------------------------------------------------------------------

        return $this->generatePage('admin/templates/accountlevel/form_v', $output, false);
    }// action_edit


    public function action_index()
    {
        // clear redirect referrer
        \Session::delete('submitted_redirect');
        
        // check permission
        if (\Model_AccountLevelPermission::checkAdminPermission('accountlv_perm', 'accountlv_viewlevels_perm') == false) {
            \Session::set_flash(
                'form_status',
                array(
                    'form_status' => 'error',
                    'form_status_message' => \Lang::get('admin_permission_denied', array('page' => \Uri::string()))
                )
            );
            \Response::redirect(\Uri::create('admin'));
        }

        // load language
        \Lang::load('account');

        // read flash message for display errors.
        $form_status = \Session::get_flash('form_status');
        if (isset($form_status['form_status']) && isset($form_status['form_status_message'])) {
            $output['form_status'] = $form_status['form_status'];
            $output['form_status_message'] = $form_status['form_status_message'];
        }
        unset($form_status);

        // search query
        $output['q'] = trim(\Input::get('q'));

        // disallow edit, delete.
        $output['disallowed_edit_delete'] = $this->disallowed_edit_delete;

        // list level groups
        $output['list_levels'] = \Model_AccountLevelGroup::listLevels();

        // <head> output ----------------------------------------------------------------------------------------------
        $output['page_title'] = $this->generateTitle(\Lang::get('accountlv_role'));
        // <head> output ----------------------------------------------------------------------------------------------
        
        // breadcrumb -------------------------------------------------------------------------------------------------
        $page_breadcrumb = [];
        $page_breadcrumb[0] = ['name' => \Lang::get('admin_admin_home'), 'url' => \Uri::create('admin')];
        $page_breadcrumb[1] = ['name' => \Lang::get('accountlv_role'), 'url' => \Uri::create('admin/account-level')];
        $output['page_breadcrumb'] = $page_breadcrumb;
        unset($page_breadcrumb);
        // breadcrumb -------------------------------------------------------------------------------------------------

        return $this->generatePage('admin/templates/accountlevel/index_v', $output, false);
    }// action_index


    public function action_multiple()
    {
        $ids = \Input::post('id');
        $act = trim(\Input::post('act'));
        // set redirect url
        $redirect = $this->getAndSetSubmitRedirection();

        if (\Extension\NoCsrf::check()) {
            if ($act == 'del') {
                // check permission.
                if (\Model_AccountLevelPermission::checkAdminPermission('accountlv_perm', 'accountlv_delete_perm') == false) {\Response::redirect($redirect);}

                if (is_array($ids)) {
                    foreach ($ids as $id) {
                        if (in_array($id, $this->disallowed_edit_delete)) {
                            continue;
                        }

                        \Model_AccountLevelGroup::deleteLevel($id);
                    }
                }
            }
        }

        // go back
        \Response::redirect($redirect);
    }// action_multiple
    
    
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
                $redirect_uri = 'admin/account-level';
                $session->set('submitted_redirect', $redirect_uri);
                return $redirect_uri;
            }
        } else {
            return $session->get('submitted_redirect');
        }
    }// getAndSetRedirection


}
