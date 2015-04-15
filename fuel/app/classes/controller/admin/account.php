<?php
/**
 *
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 *
 */

class Controller_Admin_Account extends \Controller_AdminController
{


    public function __construct()
    {
        parent::__construct();

        // load language
        \Lang::load('account');
    }// __construct


    /**
     * define permissions for this app/controller.
     *
     * @return array
     */
    public function _define_permission()
    {
        // return array('controller page name' => array('action 1', 'action 2', 'action 3', 'a lot more action. up to you...'));
        return array('account_perm' => array('account_viewusers_perm', 'account_add_perm', 'account_edit_perm', 'account_delete_perm', 'account_viewlogin_log_perm', 'account_deletelogin_log_perm'));
    }// _define_permission


    public function action_add()
    {
        // set redirect url
        $redirect = $this->getAndSetSubmitRedirection();
        
        // check permission
        if (\Model_AccountLevelPermission::checkAdminPermission('account_perm', 'account_add_perm') == false) {
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

        // load config from db.
        $cfg_values = array('allow_avatar', 'avatar_size', 'avatar_allowed_types', 'site_timezone');
        $config = \Model_Config::getvalues($cfg_values);
        $output['config'] = $config;
        // set config data to display in view file.
        $output['allow_avatar'] = $config['allow_avatar']['value'];
        $output['avatar_size'] = $config['avatar_size']['value'];
        $output['avatar_allowed_types'] = $config['avatar_allowed_types']['value'];
        unset($cfg_values);

        // read flash message for display errors.
        $form_status = \Session::get_flash('form_status');
        if (isset($form_status['form_status']) && isset($form_status['form_status_message'])) {
            $output['form_status'] = $form_status['form_status'];
            $output['form_status_message'] = $form_status['form_status_message'];
        }
        unset($form_status);

        // get timezone list to display.
        \Config::load('timezone', 'timezone');
        $output['timezone_list'] = \Config::get('timezone.timezone', array());
        $output['default_timezone'] = $config['site_timezone']['value'];

        // get levels to select
        $output['account_levels'] = \Model_AccountLevelGroup::listLevels(array('no_guest' => true));

        // set default level and status
        $output['level_group_id'] = array('3');
        $output['account_status'] = '1';

        // if form submitted
        if (\Input::method() == 'POST') {
            // store data for accounts table
            $data['account_username'] = trim(\Input::post('account_username'));
            $data['account_email'] = \Security::strip_tags(trim(\Input::post('account_email')));
            $data['account_password'] = trim(\Input::post('account_password'));
            $data['account_display_name'] = \Security::htmlentities(\Input::post('account_display_name'));
            $data['account_firstname'] = \Security::htmlentities(trim(\Input::post('account_firstname', null)));
                if ($data['account_firstname'] == null) {$data['account_firstname'] = null;}
            $data['account_middlename'] = \Security::htmlentities(trim(\Input::post('account_middlename', null)));
                if ($data['account_middlename'] == null) {$data['account_middlename'] = null;}
            $data['account_lastname'] = \Security::htmlentities(trim(\Input::post('account_lastname', null)));
                if ($data['account_lastname'] == null) {$data['account_lastname'] = null;}
            $data['account_birthdate'] = \Security::strip_tags(trim(\Input::post('account_birthdate', null)));
                if ($data['account_birthdate'] == null) {$data['account_birthdate'] = null;}
            $data['account_signature'] = \Security::htmlentities(trim(\Input::post('account_signature', null)));
                if ($data['account_signature'] == null) {$data['account_signature'] = null;}
            $data['account_timezone'] = \Security::strip_tags(trim(\Input::post('account_timezone')));
            $data['account_language'] = \Security::strip_tags(trim(\Input::post('account_language', null)));
                if ($data['account_language'] == null) {$data['account_language'] = null;}
            $data['account_status'] = (int) \Security::strip_tags(trim(\Input::post('account_status')));
            $data['account_status_text'] = \Security::htmlentities(trim(\Input::post('account_status_text')));
                if ($data['account_status'] == '1') {$data['account_status_text'] = null;}

            // store data for account_fields
            $data_field = array();
            if (is_array(\Input::post('account_field'))) {
                foreach (\Input::post('account_field') as $field_name => $field_value) {
                    if (is_string($field_name)) {
                        if (is_array($field_value)) {
                            $field_value = json_encode($field_value);
                        }

                        $data_field[$field_name] = $field_value;
                    }
                }
            }
            unset($field_name, $field_value);

            // store data for account_level table
            $data_level['level_group_id'] = \Input::post('level_group_id');

            // validate form.
            $validate = \Validation::forge();
            $validate->add_callable(new \Extension\FsValidate());
            $validate->add('account_username', \Lang::get('account_username'), array(), array('required', 'noSpaceBetweenText'));
            $validate->add('account_email', \Lang::get('account_email'), array(), array('required', 'valid_email'))->add_rule('uniqueDB', 'accounts.account_email');
            $validate->add('account_password', \Lang::get('account_password'), array(), array('required'));
            $validate->add('account_display_name', \Lang::get('account_display_name'), array(), array('required'));
            $validate->add('account_birthdate', \Lang::get('account_birthdate'))->add_rule('valid_date', 'Y-m-d');
            $validate->add('account_timezone', \Lang::get('account_timezone'), array(), array('required'));
            $validate->add('account_status', \Lang::get('account_status'), array(), array('required'));
            $validate->add('level_group_id', \Lang::get('account_role'), array(), array('required'));

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
                $result = \Model_Accounts::addAccount($data, $data_field, $data_level);

                if ($result === true) {
                    if (\Session::get_flash('form_status', null, false) == null) {
                        \Session::set_flash(
                            'form_status',
                            array(
                                'form_status' => 'success',
                                'form_status_message' => \Lang::get('account_created')
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
            $output['account_username'] = trim(\Input::post('account_username'));
            $output['account_email'] = trim(\Input::post('account_email'));
            $output['account_display_name'] = trim(\Input::post('account_display_name'));
            $output['account_firstname'] = trim(\Input::post('account_firstname'));
            $output['account_middlename'] = trim(\Input::post('account_middlename'));
            $output['account_lastname'] = trim(\Input::post('account_lastname'));
            $output['account_birthdate'] = trim(\Input::post('account_birthdate'));
            $output['account_signature'] = trim(\Input::post('account_signature'));
            $output['account_timezone'] = trim(\Input::post('account_timezone'));
            $output['account_language'] = trim(\Input::post('account_language'));
            $output['account_status'] = trim(\Input::post('account_status'));
            $output['account_status_text'] = trim(\Input::post('account_status_text'));
            $output['level_group_id'] = \Input::post('level_group_id');

            // re-populate form for account fields
            if (is_array(\Input::post('account_field'))) {
                foreach (\Input::post('account_field') as $field_name => $field_value) {
                    if (is_string($field_name)) {
                        $output['account_field'][$field_name] = $field_value;
                    }
                }
            }
            unset($field_name, $field_value);
        }

        // <head> output ----------------------------------------------------------------------------------------------
        $output['page_title'] = $this->generateTitle(\Lang::get('account_accounts'));
        $output['page_link'][] = html_tag('link', array('rel' => 'stylesheet', 'href' => Uri::createNL(\Theme::instance()->asset_path('css/datepicker.css'))));
        // <head> output ----------------------------------------------------------------------------------------------

        return $this->generatePage('admin/templates/account/form_v', $output, false);
    }// action_add


    public function action_delete_avatar()
    {
        if (!\Input::is_ajax()) {
            \Response::redirect(\Uri::create('admin/account'));
        }

        // check permission
        if (\Model_AccountLevelPermission::checkAdminPermission('account_perm', 'account_edit_perm') == false) {
            return false;
        }

        $account_id = (int) trim(\Input::post('account_id'));

        // if editing guest.
        if ($account_id == '0') {
            return false;
        }

        // load language
        \Lang::load('account');

        // get target user data
        $row = \Model_Accounts::find($account_id);
        if ($row == null) {
            return false;
        }

        // set target user levels
        foreach ($row->account_level as $lvl) {
            $output['level_group_id'][] = $lvl->level_group_id;
        }

        // check that this user can edit?
        if (\Model_Accounts::forge()->canIAddEditAccount($output['level_group_id']) == false) {
            // no
            $output = array(
                'form_status' => 'error',
                'form_status_message' => \Lang::get('account_you_cannot_edit_account_that_contain_role_higher_than_yours')
            );
            $output['result'] = false;
        } else {
            // yes
            unset($output);

            // delete avatar
            \Model_Accounts::forge()->deleteAccountAvatar($account_id);

            $output['result'] = true;
        }

        $response = new \Response();
        $response->set_header('Content-Type', 'application/json');
        $response->body(json_encode($output));
        return $response;
    }// delete_avatar


    public function action_delete_log($account_id = '')
    {
        // clear redirect referrer
        \Session::delete('submitted_redirect');
        
        // set redirect url
        $redirect = $this->getAndSetSubmitRedirection();
        
        // check permission
        if (\Model_AccountLevelPermission::checkAdminPermission('account_perm', 'account_deletelogin_log_perm') == false) {
            \Session::set_flash(
                'form_status',
                array(
                    'form_status' => 'error',
                    'form_status_message' => \Lang::get('admin_permission_denied', array('page' => \Uri::string()))
                )
            );
            \Response::redirect($redirect);
        }

        if (!is_numeric($account_id)) {
            \Response::redirect($redirect);
        }

        // load language
        \Lang::load('account');
        \Lang::load('accountlogins');

        $act = trim(\Input::post('act'));

        if (\Extension\NoCsrf::check()) {
            // if actions
            if ($act == 'del') {
                \Model_AccountLogins::query()->where('account_id', $account_id)->delete();
            } elseif ($act == 'truncate') {
                \DBUtil::truncate_table('account_logins');
            }
        }

        // go back
        \Response::redirect($redirect);
    }// action_delete_log


    public function action_edit($account_id = '')
    {
        // set redirect url
        $redirect = $this->getAndSetSubmitRedirection();
        
        // check permission
        if (\Model_AccountLevelPermission::checkAdminPermission('account_perm', 'account_edit_perm') == false) {
            \Session::set_flash(
                'form_status',
                array(
                    'form_status' => 'error',
                    'form_status_message' => \Lang::get('admin_permission_denied', array('page' => \Uri::string()))
                )
            );
            \Response::redirect($redirect);
        }

        // if editing guest.
        if ($account_id == '0') {
            \Response::redirect($redirect);
        }

        // if no account id, get current user's' account id
        if ($account_id == null) {
            $cookie = \Model_Accounts::forge()->getAccountCookie('admin');
            if (isset($cookie['account_id'])) {
                $account_id = $cookie['account_id'];
            } else {
                unset($cookie);

                \Response::redirect($redirect);
            }
            unset($cookie);
        }

        // load language
        \Lang::load('account');

        // load config from db.
        $cfg_values = array('allow_avatar', 'avatar_size', 'avatar_allowed_types', 'site_timezone');
        $config = \Model_Config::getvalues($cfg_values);
        $output['config'] = $config;
        // set config data to display in view file.
        $output['allow_avatar'] = $config['allow_avatar']['value'];
        $output['avatar_size'] = $config['avatar_size']['value'];
        $output['avatar_allowed_types'] = $config['avatar_allowed_types']['value'];
        unset($cfg_values);

        // read flash message for display errors.
        $form_status = \Session::get_flash('form_status');
        if (isset($form_status['form_status']) && isset($form_status['form_status_message'])) {
            $output['form_status'] = $form_status['form_status'];
            $output['form_status_message'] = $form_status['form_status_message'];
        }
        unset($form_status);

        // get timezone list to display.
        \Config::load('timezone', 'timezone');
        $output['timezone_list'] = \Config::get('timezone.timezone', array());
        $output['default_timezone'] = $config['site_timezone']['value'];

        // get levels to select
        $output['account_levels'] = \Model_AccountLevelGroup::listLevels(array('no_guest' => true));

        // get selected user data. -------------------------------------------------------------------------------------
        $row = \Model_Accounts::find($account_id);
        $output['account_id'] = $account_id;

        if ($row == null) {
            // not found selected user.
            unset($config, $output, $row);

            \Response::redirect($redirect);
        }

        // loop set form field.
        foreach ($row as $key => $value) {
            $output[$key] = $value;
        }
        foreach ($row->account_level as $lvl) {
            $output['level_group_id'][] = $lvl->level_group_id;
        }

        // check if editing account that has higher level
        if (\Model_Accounts::forge()->canIAddEditAccount($output['level_group_id']) == false) {
            \Session::set_flash(
                'form_status',
                array(
                    'form_status' => 'error',
                    'form_status_message' => \Lang::get('account_you_cannot_edit_account_that_contain_role_higher_than_yours')
                )
            );
            \Response::redirect($redirect);
        }

        // if form submitted --------------------------------------------------------------------------------------------
        if (\Input::method() == 'POST') {
            // store data for accounts table
            $data['account_id'] = $account_id;
            $data['account_username'] = $row->account_username; //trim(\Input::post('account_username'));//no, do not edit username.
            $data['account_old_email'] = $row->account_email;
            $data['account_email'] = \Security::strip_tags(trim(\Input::post('account_email')));
            $data['account_password'] = trim(\Input::post('account_password'));
            $data['account_new_password'] = trim(\Input::post('account_new_password'));
            $data['account_display_name'] = \Security::htmlentities(\Input::post('account_display_name'));
            $data['account_firstname'] = \Security::htmlentities(trim(\Input::post('account_firstname', null)));
                if ($data['account_firstname'] == null) {$data['account_firstname'] = null;}
            $data['account_middlename'] = \Security::htmlentities(trim(\Input::post('account_middlename', null)));
                if ($data['account_middlename'] == null) {$data['account_middlename'] = null;}
            $data['account_lastname'] = \Security::htmlentities(trim(\Input::post('account_lastname', null)));
                if ($data['account_lastname'] == null) {$data['account_lastname'] = null;}
            $data['account_birthdate'] = \Security::strip_tags(trim(\Input::post('account_birthdate', null)));
                if ($data['account_birthdate'] == null) {$data['account_birthdate'] = null;}
            $data['account_signature'] = \Security::htmlentities(trim(\Input::post('account_signature', null)));
                if ($data['account_signature'] == null) {$data['account_signature'] = null;}
            $data['account_timezone'] = \Security::strip_tags(trim(\Input::post('account_timezone')));
            $data['account_language'] = \Security::strip_tags(trim(\Input::post('account_language', null)));
                if ($data['account_language'] == null) {$data['account_language'] = null;}
            $data['account_status'] = (int) \Security::strip_tags(trim(\Input::post('account_status')));
            $data['account_status_text'] = \Security::htmlentities(trim(\Input::post('account_status_text')));
                if ($data['account_status'] == '1') {$data['account_status_text'] = null;}

            // store data for account_fields
            $data_field = array();
            if (is_array(\Input::post('account_field'))) {
                foreach (\Input::post('account_field') as $field_name => $field_value) {
                    if (is_string($field_name)) {
                        if (is_array($field_value)) {
                            $field_value = json_encode($field_value);
                        }

                        $data_field[$field_name] = $field_value;
                    }
                }
            }
            unset($field_name, $field_value);

            // store data for account_level table
            $data_level['level_group_id'] = \Input::post('level_group_id');

            // validate form.
            $validate = \Validation::forge();
            $validate->add_callable(new \Extension\FsValidate());
            $validate->add('account_username', \Lang::get('account_username'), array(), array('noSpaceBetweenText'));
            $validate->add('account_email', \Lang::get('account_email'), array(), array('required', 'valid_email'));
            $validate->add('account_display_name', \Lang::get('account_display_name'), array(), array('required'));
            $validate->add('account_birthdate', \Lang::get('account_birthdate'))->add_rule('valid_date', 'Y-m-d');
            $validate->add('account_timezone', \Lang::get('account_timezone'), array(), array('required'));
            $validate->add('account_status', \Lang::get('account_status'), array(), array('required'));
            $validate->add('level_group_id', \Lang::get('account_role'), array(), array('required'));

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
                $result = \Model_Accounts::editAccount($data, $data_field, $data_level);

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
            $output['account_username'] = trim(\Input::post('account_username'));
            $output['account_email'] = trim(\Input::post('account_email'));
            $output['account_display_name'] = trim(\Input::post('account_display_name'));
            $output['account_firstname'] = trim(\Input::post('account_firstname'));
            $output['account_middlename'] = trim(\Input::post('account_middlename'));
            $output['account_lastname'] = trim(\Input::post('account_lastname'));
            $output['account_birthdate'] = trim(\Input::post('account_birthdate'));
            $output['account_signature'] = trim(\Input::post('account_signature'));
            $output['account_timezone'] = trim(\Input::post('account_timezone'));
            $output['account_language'] = trim(\Input::post('account_language'));
            $output['account_status'] = trim(\Input::post('account_status'));
            $output['account_status_text'] = trim(\Input::post('account_status_text'));
            $output['level_group_id'] = \Input::post('level_group_id');

            // re-populate form for account fields
            if (is_array(\Input::post('account_field'))) {
                foreach (\Input::post('account_field') as $field_name => $field_value) {
                    if (is_string($field_name)) {
                        $output['account_field'][$field_name] = $field_value;
                    }
                }
            }
            unset($field_name, $field_value);
        }

        // <head> output ----------------------------------------------------------------------------------------------
        $output['page_title'] = $this->generateTitle(\Lang::get('account_accounts'));
        $output['page_link'][] = html_tag('link', array('rel' => 'stylesheet', 'href' => Uri::createNL(\Theme::instance()->asset_path('css/datepicker.css'))));
        // <head> output ----------------------------------------------------------------------------------------------

        return $this->generatePage('admin/templates/account/form_v', $output, false);
    }// action_edit


    public function action_index()
    {
        // clear redirect referrer
        \Session::delete('submitted_redirect');
        
        // check permission
        if (\Model_AccountLevelPermission::checkAdminPermission('account_perm', 'account_viewusers_perm') == false) {
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
        
        // get levels to select
        $account_levels = \Model_AccountLevelGroup::listLevels(array('no_guest' => false));
        $select_lvls = array();
        if (!empty($account_levels['items']) && is_array($account_levels)) {
            foreach ($account_levels['items'] as $lvr) {
                $select_lvls[$lvr->level_group_id] = $lvr->level_name;
            }
        }
        $output['account_levels'] = $select_lvls;
        unset($account_levels, $select_lvls);

        // set sort variable for sortable in views.
        $sort = \Security::strip_tags(trim(\Input::get('sort')));
        if ($sort == null || $sort == 'ASC') {
            $next_sort = 'DESC';
        } else {
            $next_sort = 'ASC';
        }
        $output['next_sort'] = $next_sort;
        unset($next_sort, $sort);

        // search query
        $output['q'] = trim(\Input::get('q'));
        // filters
        $output['filter_account_id'] = trim(\Input::get('filter_account_id'));
        $output['filter_account_username'] = trim(\Input::get('filter_account_username'));
        $output['filter_account_email'] = trim(\Input::get('filter_account_email'));
        $output['filter_level_group_id'] = trim(\Input::get('filter_level_group_id'));
        $output['filter_account_create'] = trim(\Input::get('filter_account_create'));
        $output['filter_account_last_login'] = trim(\Input::get('filter_account_last_login'));
        $output['filter_account_status'] = trim(\Input::get('filter_account_status'));

        // list accounts --------------------------------------------------------------------------------------------------
        $option['limit'] = \Model_Config::getval('content_admin_items_perpage');
        $option['offset'] = (trim(\Input::get('page')) != null ? ((int)\Input::get('page')-1)*$option['limit'] : 0);
        if (trim(\Input::get('q')) != null) {
            $option['search'] = trim(\Input::get('q'));
        }
        if ($output['filter_account_id'] != null) {
            $option['filter_account_id'] = $output['filter_account_id'];
        }
        if ($output['filter_account_username'] != null) {
            $option['filter_account_username'] = $output['filter_account_username'];
        }
        if ($output['filter_account_email'] != null) {
            $option['filter_account_email'] = $output['filter_account_email'];
        }
        if ($output['filter_level_group_id'] != null) {
            $option['filter_level_group_id'] = $output['filter_level_group_id'];
        }
        if ($output['filter_account_create'] != null) {
            $option['filter_account_create'] = $output['filter_account_create'];
        }
        if ($output['filter_account_last_login'] != null) {
            $option['filter_account_last_login'] = $output['filter_account_last_login'];
        }
        if ($output['filter_account_status'] != null) {
            $option['filter_account_status'] = $output['filter_account_status'];
        }
        if (\Security::strip_tags(trim(\Input::get('orders'))) != null) {
            $option['orders'] = \Security::strip_tags(trim(\Input::get('orders')));
        }
        if (\Security::strip_tags(trim(\Input::get('sort'))) != null) {
            $option['sort'] = \Security::strip_tags(trim(\Input::get('sort')));
        }
        $list_accounts = \Model_Accounts::listAccounts($option);

        // pagination config
        $config['pagination_url'] = \Uri::main() . \Uri::getCurrentQuerystrings(true, true, false);
        $config['total_items'] = $list_accounts['total'];
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
        $pagination = \Pagination::forge('viewlogins_pagination', $config);

        $output['list_accounts'] = $list_accounts;
        $output['pagination'] = $pagination;

        unset($config, $list_accounts, $option, $pagination);

        // <head> output ----------------------------------------------------------------------------------------------
        $output['page_title'] = $this->generateTitle(\Lang::get('account_accounts'));
        $output['page_link'][] = html_tag('link', array('rel' => 'stylesheet', 'href' => Uri::createNL(\Theme::instance()->asset_path('css/datepicker.css'))));
        // <head> output ----------------------------------------------------------------------------------------------

        return $this->generatePage('admin/templates/account/index_v', $output, false);
    }// action_index


    public function action_multiple()
    {
        $ids = \Input::post('id');
        $act = trim(\Input::post('act'));
        $redirect = $this->getAndSetSubmitRedirection();

        if (\Extension\NoCsrf::check()) {
            // if action is delete.
            if ($act == 'del') {
                // check permission.
                if (\Model_AccountLevelPermission::checkAdminPermission('account_perm', 'account_delete_perm') == false) {\Response::redirect($redirect);}

                if (is_array($ids)) {
                    foreach ($ids as $id) {
                        // get target level group id
                        $lvls = \Model_AccountLevel::query()->where('account_id', $id)->get();

                        // not found
                        if ($lvls == null) {
                            continue;
                        } else {
                            // format level group for check can i add, edit
                            $level_group = array();
                            foreach ($lvls as $lvl) {
                                $level_group[] = $lvl->level_group_id;
                            }
                        }

                        if (\Model_Accounts::forge()->canIAddEditAccount($level_group) == true) {
                            // delete account.
                            \Model_Accounts::deleteAccount($id);
                            
                            // clear cache
                            \Extension\Cache::deleteCache('model.accounts-checkAccount-'.\Model_Sites::getSiteId().'-'.$id);
                        }
                    }
                }
            } elseif ($act == 'enable') {
                // check permission.
                if (\Model_AccountLevelPermission::checkAdminPermission('account_perm', 'account_delete_perm') == false) {\Response::redirect($redirect);}

                if (is_array($ids)) {
                    foreach ($ids as $id) {
                        if ($id == '0') {
                            continue;
                        }

                        // get target level group id
                        $lvls = \Model_AccountLevel::query()->where('account_id', $id)->get();

                        // not found
                        if ($lvls == null) {
                            continue;
                        } else {
                            // format level group for check can i add, edit
                            $level_group = array();
                            foreach ($lvls as $lvl) {
                                $level_group[] = $lvl->level_group_id;
                            }
                        }

                        if (\Model_Accounts::forge()->canIAddEditAccount($level_group) == true) {
                            $entry = \Model_Accounts::find($id);
                            $entry->account_status = '1';
                            $entry->account_status_text = null;
                            $entry->save();

                            unset($entry);
                        }

                        // clear cache
                        \Extension\Cache::deleteCache('model.accounts-checkAccount-'.\Model_Sites::getSiteId().'-'.$id);
                    }
                }
            } elseif ($act == 'disable') {
                // check permission.
                if (\Model_AccountLevelPermission::checkAdminPermission('account_perm', 'account_delete_perm') == false) {\Response::redirect($redirect);}

                if (is_array($ids)) {
                    foreach ($ids as $id) {
                        if ($id == '0') {
                            continue;
                        }

                        // get target level group id
                        $lvls = \Model_AccountLevel::query()->where('account_id', $id)->get();

                        // not found
                        if ($lvls == null) {
                            continue;
                        } else {
                            // format level group for check can i add, edit
                            $level_group = array();
                            foreach ($lvls as $lvl) {
                                $level_group[] = $lvl->level_group_id;
                            }
                        }

                        if (\Model_Accounts::forge()->canIAddEditAccount($level_group) == true) {
                            $entry = \Model_Accounts::find($id);
                            $entry->account_status = '0';
                            $entry->account_status_text = null;
                            $entry->save();

                            unset($entry);
                        }

                        // clear cache
                        \Extension\Cache::deleteCache('model.accounts-checkAccount-'.\Model_Sites::getSiteId().'-'.$id);
                    }
                }
            }
        }

        // go back
        \Response::redirect($redirect);
    }// action_multiple


    public function action_viewlogins($account_id = '')
    {
        // set redirect url
        $redirect = $this->getAndSetSubmitRedirection();
        
        // check permission
        if (\Model_AccountLevelPermission::checkAdminPermission('account_perm', 'account_viewlogin_log_perm') == false) {
            \Session::set_flash(
                'form_status',
                array(
                    'form_status' => 'error',
                    'form_status_message' => \Lang::get('admin_permission_denied', array('page' => \Uri::string()))
                )
            );
            \Response::redirect($redirect);
        }

        // viewing guest logins?
        if ($account_id == '0') {
            \Response::redirect($redirect);
        }

        // load language
        \Lang::load('account');
        \Lang::load('accountlogins');

        // read flash message for display errors.
        $form_status = \Session::get_flash('form_status');
        if (isset($form_status['form_status']) && isset($form_status['form_status_message'])) {
            $output['form_status'] = $form_status['form_status'];
            $output['form_status_message'] = $form_status['form_status_message'];
        }
        unset($form_status);

        // get accounts data for this account.
        $account = \Model_Accounts::find($account_id);
        if ($account == null) {
            // not found account.
            \Response::redirect($redirect);
        }
        $output['account'] = $account;
        $output['account_id'] = $account_id;
        unset($account);

        // set sort variable for sortable in views.
        $next_sort = \Security::strip_tags(trim(\Input::get('sort')));
        if ($next_sort == null || $next_sort == 'DESC') {
            $next_sort = 'ASC';
        } else {
            $next_sort = 'DESC';
        }
        $output['next_sort'] = $next_sort;
        unset($next_sort);

        // list logins -----------------------------------------------------------------------------------------------------
        $option['limit'] = \Model_Config::getval('content_admin_items_perpage');
        $option['offset'] = (trim(\Input::get('page')) != null ? ((int)\Input::get('page')-1)*$option['limit'] : 0);
        if (\Security::strip_tags(trim(\Input::get('orders'))) != null) {
            $option['orders'] = \Security::strip_tags(trim(\Input::get('orders')));
        }
        if (\Security::strip_tags(trim(\Input::get('sort'))) != null) {
            $option['sort'] = \Security::strip_tags(trim(\Input::get('sort')));
        }
        $list_logins = \Model_AccountLogins::listLogins(array('account_id' => $account_id), $option);

        // pagination config
        $config['pagination_url'] = \Uri::main() . \Uri::getCurrentQuerystrings(true, true, false);
        $config['total_items'] = $list_logins['total'];
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
        $pagination = \Pagination::forge('viewlogins_pagination', $config);

        $output['list_logins'] = $list_logins;
        $output['pagination'] = $pagination;

        unset($config, $list_logins, $option, $pagination);

        // <head> output ----------------------------------------------------------------------------------------------
        $output['page_title'] = $this->generateTitle(\Lang::get('account_view_login_history'));
        // <head> output ----------------------------------------------------------------------------------------------

        return $this->generatePage('admin/templates/account/viewlogins_v', $output, false);
    }// action_viewlogins
    
    
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
                $redirect_uri = 'admin/account';
                $session->set('submitted_redirect', $redirect_uri);
                return $redirect_uri;
            }
        } else {
            return $session->get('submitted_redirect');
        }
    }// getAndSetRedirection


}
