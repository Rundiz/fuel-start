<?php

namespace TestMod;

class Controller_Admin_Index extends \Controller_AdminController
{


    public function __construct()
    {
        parent::__construct();

        // load languages
        \Lang::load('testmod::testmod');
    }// __construct


    public function action_account()
    {
        $output['account_model'] = new \Model_Accounts();

        // <head> output -------------------------------------------
        $output['page_title'] = $this->generateTitle('Test module plugin');
        // <head> output -------------------------------------------

        // breadcrumb -------------------------------------------------------------------------------------------------
        $page_breadcrumb = [];
        $page_breadcrumb[0] = ['name' => \Lang::get('admin_admin_home'), 'url' => \Uri::create('admin')];
        $page_breadcrumb[1] = ['name' => 'Test module plugin', 'url' => \Uri::create('testmod/admin/index')];
        $page_breadcrumb[2] = ['name' => 'Account apis', 'url' => \Uri::main()];
        $output['page_breadcrumb'] = $page_breadcrumb;
        unset($page_breadcrumb);
        // breadcrumb -------------------------------------------------------------------------------------------------

        return $this->generatePage('admin/templates/index/account_v', $output, false);
    }// action_account


    public function action_accountMultisite()
    {
        $act = trim(\Input::post('act'));
        $output = [];
        
        if (strtolower(\Fuel\Core\Input::method()) == 'post') {
            if ($act == 'createmaintable') {
                $create_table = \Fuel\Core\DBUtil::create_table(
                    'testmultisiteaccount',
                    [
                        'id' => ['constraint' => 11, 'type' => 'int', 'auto_increment' => true],
                        'account_id' => ['constraint' => 11, 'type' => 'int', 'null' => true, 'comment' => 'refer to accounts.account_id'],
                        'actdate' => ['type' => 'bigint', 'null' => true, 'comment' => 'date/time of record date.'],
                    ],
                    ['id'],
                    true
                );
                $output['create_table_result'] = $create_table;
                $output['result'] = true;
            } elseif ($act == 'insertdemodata') {
                // get accounts that is not guest
                $account_result = \DB::select('account_id')->as_object()->from('accounts')->where('account_id', '!=', '0')->execute();
                // get all sites from site table
                $sites_result = \DB::select('site_id')->as_object()->from('sites')->execute();
                $output['tables_data'] = [];
                if ($sites_result != null) {
                    foreach ($sites_result as $site) {
                        if ($site->site_id == '1') {
                            $test_table = 'testmultisiteaccount';
                        } else {
                            $test_table = $site->site_id.'_testmultisiteaccount';
                        }
                        if (\DBUtil::table_exists($test_table)) {
                            \DBUtil::truncate_table($test_table);
                            if ($account_result != null) {
                                foreach ($account_result as $account) {
                                    \DB::insert($test_table)
                                        ->set([
                                            'account_id' => $account->account_id,
                                            'actdate' => time(),
                                        ])
                                        ->execute();
                                }// endforeach; $account_result
                            }// endif; $account_result
                            
                            // finished insert get data from this table.
                            $this_table_result = \DB::select()->as_object('stdClass')->from($test_table)->limit(10)->order_by('id', 'DESC')->execute()->as_array();
                            $output['tables_data'][$test_table] = $this_table_result;
                            unset($this_table_result);
                        }
                        unset($test_table);
                    }// endforeach; $sites_result
                    $output['result'] = true;
                }// endif; $sites_result
                unset($account, $account_result, $site, $sites_result);
            } elseif ($act == 'loaddemodata') {
                // get all sites from site table
                $sites_result = \DB::select('site_id')->as_object()->from('sites')->execute();
                $output['tables_data'] = [];
                if ($sites_result != null) {
                    foreach ($sites_result as $site) {
                        if ($site->site_id == '1') {
                            $test_table = 'testmultisiteaccount';
                        } else {
                            $test_table = $site->site_id.'_testmultisiteaccount';
                        }
                        if (\DBUtil::table_exists($test_table)) {
                            $this_table_result = \DB::select()->as_object('stdClass')->from($test_table)->limit(10)->order_by('id', 'DESC')->execute()->as_array();
                            $output['tables_data'][$test_table] = $this_table_result;
                            unset($this_table_result);
                        }
                    }// endforeach; $sites_result
                    $output['result'] = true;
                }// endif; $sites_result
                unset($site, $sites_result);
            } elseif ($act == 'droptable') {
                // get all sites from site table
                $sites_result = \DB::select('site_id')->as_object()->from('sites')->execute();
                if ($sites_result != null) {
                    foreach ($sites_result as $site) {
                        if ($site->site_id == '1') {
                            $test_table = 'testmultisiteaccount';
                        } else {
                            $test_table = $site->site_id.'_testmultisiteaccount';
                        }
                        if (\DBUtil::table_exists($test_table)) {
                            \DBUtil::drop_table($test_table);
                        }
                    }// endforeach; $sites_result
                    $output['result'] = true;
                }// endif; $sites_result
                unset($site, $sites_result);
            }// endif; $act
            
            if (\Input::is_ajax()) {
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
            }
        }

        // <head> output -------------------------------------------
        $output['page_title'] = $this->generateTitle('Test module plugin');
        // <head> output -------------------------------------------

        // breadcrumb -------------------------------------------------------------------------------------------------
        $page_breadcrumb = [];
        $page_breadcrumb[0] = ['name' => \Lang::get('admin_admin_home'), 'url' => \Uri::create('admin')];
        $page_breadcrumb[1] = ['name' => 'Test module plugin', 'url' => \Uri::create('testmod/admin/index')];
        $page_breadcrumb[2] = ['name' => 'Test delete account on multisite table', 'url' => \Uri::main()];
        $output['page_breadcrumb'] = $page_breadcrumb;
        unset($page_breadcrumb);
        // breadcrumb -------------------------------------------------------------------------------------------------

        return $this->generatePage('admin/templates/index/accountMultisite_v', $output, false);
    }// action_accountMultisite


    public function action_index()
    {
        // <head> output -------------------------------------------
        $output['page_title'] = $this->generateTitle('Test module plugin');
        // <head> output -------------------------------------------

        // breadcrumb -------------------------------------------------------------------------------------------------
        $page_breadcrumb = [];
        $page_breadcrumb[0] = ['name' => \Lang::get('admin_admin_home'), 'url' => \Uri::create('admin')];
        $page_breadcrumb[1] = ['name' => 'Test module plugin', 'url' => \Uri::create('testmod/admin/index')];
        $output['page_breadcrumb'] = $page_breadcrumb;
        unset($page_breadcrumb);
        // breadcrumb -------------------------------------------------------------------------------------------------

        return $this->generatePage('admin/templates/index/index_v', $output, false);
    }// action_index


}