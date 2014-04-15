<?php
/**
 *
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 *
 */

class Controller_Admin_Siteman extends \Controller_AdminController
{


    public function __construct()
    {
        parent::__construct();

        // load language
        \Lang::load('siteman');
    }// __construct


    public function _define_permission()
    {
        return array('siteman_perm' => array('siteman_viewsites_perm', 'siteman_add_perm', 'siteman_edit_perm', 'siteman_delete_perm'));
    }// _define_permission


    public function action_add()
    {
        // check permission
        if (\Model_AccountLevelPermission::checkAdminPermission('siteman_perm', 'siteman_add_perm') == false) {
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

        // set default form value
        $output['site_status'] = '1';

        // if form submitted
        if (\Input::method() == 'POST') {
            // store data for save
            $data['site_name'] = \Security::htmlentities(trim(\Input::post('site_name')));
            $data['site_domain'] = mb_strtolower(\Security::strip_tags(trim(\Input::post('site_domain'))));
            $data['site_status'] = (int) trim(\Input::post('site_status'));

            $validate = \Validation::forge();
            $validate->add_callable(new \Extension\FsValidate());
            $validate->add('site_name', \Lang::get('siteman_site_name'), array(), array('required'));
            $validate->add('site_domain', \Lang::get('siteman_site_domain'), array(), array('required'))->add_rule('uniqueDB', 'sites.site_domain');

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
                $result = \Model_Sites::addSite($data);

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

                    \Response::redirect(\Uri::create('admin/siteman'));
                } else {
                    $output['form_status'] = 'error';
                    $output['form_status_message'] = $result;
                }
            }

            // re-populate form
            $output['site_name'] = \Input::post('site_name');
            $output['site_domain'] = \Input::post('site_domain');
            $output['site_status'] = \Input::post('site_status');
        }

        // <head> output ----------------------------------------------------------------------------------------------
        $output['page_title'] = $this->generateTitle(\Lang::get('siteman_multisite_manager'));
        // <head> output ----------------------------------------------------------------------------------------------

        return $this->generatePage('admin/templates/siteman/siteman_form_v', $output, false);
    }// action_add


    public function action_edit($site_id = '')
    {
        // check permission
        if (\Model_AccountLevelPermission::checkAdminPermission('siteman_perm', 'siteman_edit_perm') == false) {
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

        // get selected site data
        $row = \Model_Sites::find($site_id);
        $output['site_id'] = $site_id;

        if ($row == null) {
            unset($output, $row);

            \Response::redirect(\Uri::create('admin/siteman'));
        }

        // loop set form field.
        foreach ($row as $key => $value) {
            $output[$key] = $value;
        }

        // if form submitted
        if (\Input::method() == 'POST') {
            // store data for save
            $data['site_id'] = $site_id;
            $data['site_name'] = \Security::htmlentities(trim(\Input::post('site_name')));
            $data['site_domain'] = mb_strtolower(\Security::strip_tags(trim(\Input::post('site_domain'))));
            $data['site_status'] = (int) trim(\Input::post('site_status'));

            $validate = \Validation::forge();
            $validate->add_callable(new \Extension\FsValidate());
            $validate->add('site_name', \Lang::get('siteman_site_name'), array(), array('required'));
            $validate->add('site_domain', \Lang::get('siteman_site_domain'), array(), array('required'));

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
                $result = \Model_Sites::editSite($data);

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

                    \Response::redirect(\Uri::create('admin/siteman'));
                } else {
                    $output['form_status'] = 'error';
                    $output['form_status_message'] = $result;
                }
            }

            // re-populate form
            $output['site_name'] = \Input::post('site_name');
            $output['site_domain'] = \Input::post('site_domain');
            $output['site_status'] = \Input::post('site_status');
        }

        // <head> output ----------------------------------------------------------------------------------------------
        $output['page_title'] = $this->generateTitle(\Lang::get('siteman_multisite_manager'));
        // <head> output ----------------------------------------------------------------------------------------------

        return $this->generatePage('admin/templates/siteman/siteman_form_v', $output, false);
    }// action_edit


    public function action_index()
    {
        // check permission
        if (\Model_AccountLevelPermission::checkAdminPermission('siteman_perm', 'siteman_viewsites_perm') == false) {
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

        // set sort variable for sortable in views.
        $next_sort = \Security::strip_tags(trim(\Input::get('sort')));
        if ($next_sort == null || $next_sort == 'ASC') {
            $next_sort = 'DESC';
        } else {
            $next_sort = 'ASC';
        }
        $output['next_sort'] = $next_sort;
        unset($next_sort);

        // list sites ------------------------------------------------------------------------------------------------------
        $option['list_for'] = 'admin';
        $option['limit'] = \Model_Config::getval('content_admin_items_perpage');
        $option['offset'] = (trim(\Input::get('page')) != null ? ((int)\Input::get('page')-1)*$option['limit'] : 0);

        $list_sites = \Model_Sites::listSites($option);

        // pagination config
        $config['pagination_url'] = \Uri::main() . \Uri::getCurrentQuerystrings(true, true, false);
        $config['total_items'] = $list_sites['total'];
        $config['per_page'] = $option['limit'];
        $config['uri_segment'] = 'page';
        $config['num_links'] = 3;
        $config['show_first'] = true;
        $config['show_last'] = true;
        $config['first-inactive'] = "\n\t\t<li class=\"disabled\">{link}</li>";
        $config['first-inactive-link'] = '<a href="#">{page}</a>';
        $config['first-marker'] = '&laquo;';
        $config['last-inactive'] = "\n\t\t<li class=\"disabled\">{link}</li>";
        $config['last-inactive-link'] = '<a href="#">{page}</a>';
        $config['last-marker'] = '&raquo;';
        $config['previous-marker'] = '&lsaquo;';
        $config['next-marker'] = '&rsaquo;';
        $pagination = \Pagination::forge('default', $config);

        $output['list_sites'] = $list_sites;
        $output['pagination'] = $pagination;

        unset($config, $list_accounts, $option, $pagination);

        // <head> output ----------------------------------------------------------------------------------------------
        $output['page_title'] = $this->generateTitle(\Lang::get('siteman_multisite_manager'));
        // <head> output ----------------------------------------------------------------------------------------------

        return $this->generatePage('admin/templates/siteman/siteman_v', $output, false);
    }// action_index


    public function action_multiple()
    {
        $ids = \Input::post('id');
        $act = trim(\Input::post('act'));

        if (\Extension\NoCsrf::check()) {
            if ($act == 'del') {
                // check permission.
                if (\Model_AccountLevelPermission::checkAdminPermission('siteman_perm', 'siteman_delete_perm') == false) {
                    \Session::set_flash(
                        'form_status',
                        array(
                            'form_status' => 'error',
                            'form_status_message' => \Lang::get('admin_permission_denied', array('page' => \Uri::string()))
                        )
                    );
                    \Response::redirect(\Uri::create('admin/siteman'));
                }

                if (is_array($ids)) {
                    foreach ($ids as $id) {
                        \Model_Sites::deleteSite($id);
                    }
                }
            } elseif ($act == 'enable') {
                // check permission.
                if (\Model_AccountLevelPermission::checkAdminPermission('siteman_perm', 'siteman_edit_perm') == false) {
                    \Session::set_flash(
                        'form_status',
                        array(
                            'form_status' => 'error',
                            'form_status_message' => \Lang::get('admin_permission_denied', array('page' => \Uri::string()))
                        )
                    );
                    \Response::redirect(\Uri::create('admin/siteman'));
                }

                if (is_array($ids)) {
                    foreach ($ids as $id) {
                        if ($id == '1') {
                            continue;
                        }

                        $entry = \Model_Sites::find($id);
                        $entry->site_status = 1;
                        $entry->save();
                    }

                    unset($entry);
                }
            } elseif ($act == 'disable') {
                // check permission.
                if (\Model_AccountLevelPermission::checkAdminPermission('siteman_perm', 'siteman_edit_perm') == false) {
                    \Session::set_flash(
                        'form_status',
                        array(
                            'form_status' => 'error',
                            'form_status_message' => \Lang::get('admin_permission_denied', array('page' => \Uri::string()))
                        )
                    );
                    \Response::redirect(\Uri::create('admin/siteman'));
                }

                if (is_array($ids)) {
                    foreach ($ids as $id) {
                        if ($id == '1') {
                            continue;
                        }

                        $entry = \Model_Sites::find($id);
                        $entry->site_status = 0;
                        $entry->save();
                    }

                    unset($entry);
                }
            }
        }

        // go back
        if (\Input::referrer() != null && \Input::referrer() != \Uri::main()) {
            \Response::redirect(\Input::referrer());
        } else {
            \Response::redirect('admin/siteman');
        }
    }// action_multiple


}
