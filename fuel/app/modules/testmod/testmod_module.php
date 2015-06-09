<?php
/**
 * Module Name: TestModPlug
 * Module URL:
 * Version:
 * Description: Test module plugin.
 * Author: 
 * Author URL: 
 */


/**
 * Test module plugin to hook into filters/actions.
 * @todo [fuelstart][testmod] comment out this test module plug class if done testing.
 */
class TestMod_Module
{


    public function actionAccountCheckPassword($entered = '', $args = '')
    {
        if (strtolower(\Input::method()) == 'post') {
            // prevent working on real submit login. it is just test demo only.
            return null;
        }

        list($hashed, $account_obj) = $args;
        unset($args);
        
        $hash_entered = $this->filterAccountHashPassword($entered);
        
        if ($hash_entered === $hashed) {
            return true;
        } else {
            return false;
        }
    }// actionAccountCheckPassword


    public function actionAccountDeleteOnMultisiteTables($account_id = '', $args = '')
    {
        $test_table_name = 'testmultisiteaccount';
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
                    \DB::delete($test_table)->where('account_id', $account_id)->execute();
                }
            }
        }
        unset($site, $sites_result, $test_table, $test_table_name);
    }// actionAccountDeleteOnMultisiteTables


    public function actionAccountLoginSuccess($account_id = '', $args = '')
    {
        $testmod_path = \Module::exists('testmod');
        $log_file_name = 'login-success.txt';
        $log_file_path = $testmod_path.$log_file_name;
        
        // delete if exists.
        if (\File::exists($log_file_path) && !is_writable($log_file_path)) {
            return false;
        } elseif (\File::exists($log_file_path) && is_writable($log_file_path)) {
            \File::delete($log_file_path);
        }
        
        \File::create($testmod_path, $log_file_name, 'Account id '.$account_id.' log in success on '.\Extension\Date::gmtDate());
        
        return true;
    }// actionAccountLoginSuccess


    public function actionAccountMemberEditAccount($account_id = '', $args = '')
    {
        $testmod_path = \Module::exists('testmod');
        $log_file_name = 'member-edit-account.txt';
        $log_file_path = $testmod_path.$log_file_name;

        // delete if exists.
        if (\File::exists($log_file_path) && !is_writable($log_file_path)) {
            return false;
        } elseif (\File::exists($log_file_path) && is_writable($log_file_path)) {
            \File::delete($log_file_path);
        }

        \File::create($testmod_path, $log_file_name, 'Member has edit to made changed their account. Account id = '.$account_id.'. Arguments = '.json_encode($args));

        return true;
    }// actionAccountMemberEditAccount


    public function filterAccountHashPassword($password = '', $args = '')
    {
        if (strtolower(\Input::method()) == 'post') {
            // prevent working on real submit login. it is just test demo only.
            return null;
        }

        return sha1(md5($password));
    }// filterAccountHashPassword


    public function filterBaseControllerGenTitle($title = '', $args = '')
    {
        if (\Uri::string() == 'testmod/admin') {
            list($name_position, $config) = $args;
            $args = '';
            return $title.'::'.$name_position.'::'.mb_strimwidth(json_encode($config), 0, 9, '...').'::Title plugged';
        }
    }// filterBaseControllerGenTitle


    public function filterSitesGetDefaultConfigValueForAddSite()
    {
        // these are just example. it will not be reset into new site's config table because it is not exists.
        // these values are just for reset config value while adding new site.
        $output = [];
        $output['testmod_configname1'] = 'value1';
        $output['testmod_configname2'] = 'value2';
        
        return $output;
    }// filterSitesGetDefaultConfigValueForAddSite


    public function filterSitesGetModulesMultisiteTables()
    {
        return 'testmultisiteaccount';
    }// filterSitesGetModulesMultisiteTables


}