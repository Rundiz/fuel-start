<?php
/**
 * accounts ORM and reusable function
 *
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 *
 */

class Model_Accounts extends \Orm\Model
{


    protected static $_table_name = 'accounts';
    protected static $_primary_key = array('account_id');
    protected static $_properties = array('account_id', 'account_username', 'account_email', 'account_salt', 'account_password', 'account_display_name', 'account_firstname', 'account_middlename', 'account_lastname', 'account_birthdate', 'account_avatar', 'account_signature', 'account_timezone', 'account_language', 'account_create', 'account_create_gmt', 'account_last_login', 'account_last_login_gmt', 'account_status', 'account_status_text', 'account_new_email', 'account_new_password', 'account_confirm_code', 'account_confirm_code_since');

    // relations
    protected static $_has_many = array(
        'account_fields' => array(
            'model_to' => 'Model_AccountFields',
            'key_from' => 'account_id',
            'key_to' => 'account_id',
            'cascade_delete' => true,
        ),
        'account_level' => array(
            'key_from' => 'account_id',
            'model_to' => 'Model_AccountLevel',
            'key_to' => 'account_id',
            'cascade_delete' => true,
        ),
        'account_logins' => array(
            'key_from' => 'account_id',
            'model_to' => 'Model_AccountLogins',
            'key_to' => 'account_id',
            'cascade_delete' => true,
        ),
        'account_permission' => array(
            'model_to' => 'Model_AccountPermission',
            'key_from' => 'account_id',
            'key_to' => 'account_id',
            'cascade_delete' => true,
        ),
        'account_sites' => array(
            'key_from' => 'account_id',
            'model_to' => 'Model_AccountSites',
            'key_to' => 'account_id',
            'cascade_delete' => true,
        ),
    );


    protected $password_hash_level = 12;


    /**
     * add account
     * @param array $data
     * @param array $data_fields
     * @param array $data_level
     * @return mixed
     */
    public static function addAccount(array $data = array(), $data_fields = array(), $data_level = array())
    {
        if (empty($data) || empty($data_level)) {return false;}

        // check permission that can i add or edit this account
        if (static::instance()->canIAddEditAccount($data_level['level_group_id']) == false) {
            return \Lang::get('account_you_cannot_add_account_that_contain_role_higher_than_yours');
        }

        // check for duplicate account (username)
        $query = static::query()->where('account_username', $data['account_username']);
        if ($query->count() > 0) {
            unset($query);
            return \Lang::get('account_username_already_exists');
        }
        unset($query);

        // check for uploaded avatar
        if (\Model_Config::getval('allow_avatar') == '1' && isset($_FILES['account_avatar']['name']) && $_FILES['account_avatar']['name'] != null) {
            $result = static::instance()->uploadAvatar(array('input_field' => 'account_avatar'));

            if (isset($result['result']) && $result['result'] === true) {
                $data['account_avatar'] = $result['account_avatar'];
            } else {
                unset($config);

                return $result;
            }
        }

        // set values for insert to accounts table.
        $data['account_password'] = static::instance()->hashPassword($data['account_password']);
        $data['account_create'] = time();
        $data['account_create_gmt'] = \Extension\Date::localToGmt();

        // add account to db. ----------------------------------------
        //list($account_id) = \DB::insert(static::$_table_name)->set($data); // query builder style.
        $account = static::forge($data);

        // add level to user. -----------------------------------------
        $i = 0;
        foreach ($data_level['level_group_id'] as $level_group_id) {
            $account->account_level[$i] = new Model_AccountLevel();
            $account->account_level[$i]->level_group_id = $level_group_id;
            $i++;
        }

        $account->save();

        $account_id = $account->account_id;
        unset($account, $i);
        
        // loop sites to add level of this user to each site.
        $site_id = \Model_Sites::getSiteId(false);
        $list_site_option['list_for'] = 'admin';
        $list_site_option['unlimit'] = true;
        $sites = \Model_Sites::listSites($list_site_option);
        if (isset($sites['items']) && is_array($sites['items']) && !empty($sites['items'])) {
            foreach ($sites['items'] as $site) {
                if ($site->site_id != $site_id) {
                    if ($site->site_id == '1') {
                        $table_name = 'account_level';
                    } else {
                        $table_name = $site->site_id . '_account_level';
                    }
                    
                    \DB::insert($table_name)->set(array(
                        'account_id' => $account_id,
                        'level_group_id' => '3',// for other sites, set user level as member for security reason.
                    ))->execute();
                }
            }
        }
        unset($list_site_option, $site, $sites, $site_id, $table_name);

        // add account fields if there is any value. -----------------
        // to add account fields data structure shoud be like this...
        // array(array('field_name' => 'website', 'field_value' => 'http://domain.tld'), array('field_name' => 'fb', 'field_value' => 'http://fb.com/myprofile'));
        // or
        // $af[0]['field_name'] = 'website';
        // $af[0]['field_value'] = 'http://domain.tld';
        // $af[1]['field_name'] = 'fb';
        // $af[1]['field_value'] = 'http://fb.com/myprofile';
        if (!empty($data_fields) && is_array($data_fields)) {
            foreach ($data_fields as $field) {
                $account_fields = static::forge($field);
                $account_fields->account_id = $account_id;
                $account_fields->save();
            }
            unset($account_fields, $field);
        }

        return true;
    }// addAccount


    /**
     * admin login
     *
     * @param array $data
     * @return mixed
     */
    public static function adminLogin(array $data = array())
    {
        if (!isset($data['account_password']) || (!isset($data['account_username']) && !isset($data['account_email']))) {
            return false;
        }

        \Lang::load('account');

        // set required var.
        if (!isset($data['account_username'])) {
            $data['account_username'] = null;
        }
        if (!isset($data['account_email'])) {
            $data['account_email'] = null;
        }

        $result = \DB::select()
            ->as_object()
            ->from(static::$_table_name)
            ->where('account_username', $data['account_username'])
            ->or_where('account_email', $data['account_email'])
            ->execute();

        if (count($result) > 0) {
            // found
            $row = $result->current();
            // clear cache
            \Extension\Cache::deleteCache('model.accounts-checkAccount-' . \Model_Sites::getSiteId() . '-' . $row->account_id);

            // check enabled account.
            if ($row->account_status == '1') {
                // enabled
                // check password
                if (static::instance()->checkPassword($data['account_password'], $row->account_password, $row) === true) {
                    // check password passed
                    if (\Model_AccountLevelPermission::checkAdminPermission('account_admin_login', 'account_admin_login', $row->account_id) === true) {
                        // generate session id for check simultaneous login
                        $session_id = \Session::key('session_id');

                        // if login set to remember, set expires.
                        if (\Input::post('remember') == 'yes') {
                            $expires = (\Model_Config::getval('member_login_remember_length')*24*60*60);
                        } else {
                            $expires = 0;
                        }

                        // get member cookie to check if this user ever logged in at frontend.
                        $cookie_member = static::instance()->getAccountCookie();

                        if (isset($cookie_member['account_id']) && isset($cookie_member['account_username']) && isset($cookie_member['account_email']) && isset($cookie_member['account_display_name']) && isset($cookie_member['account_online_code'])) {
                            // already logged in at front end.
                            $session_id = $cookie_member['account_online_code'];
                        } else {
                            // never logged in at front end.
                            // set cookie (member cookie)
                            $cookie_account['account_id'] = $row->account_id;
                            $cookie_account['account_username'] = $row->account_username;
                            $cookie_account['account_email'] = $row->account_email;
                            $cookie_account['account_display_name'] = $row->account_display_name;
                            $cookie_account['account_online_code'] = $session_id;
                            $cookie_account = \Crypt::encode(serialize($cookie_account));
                            Extension\Cookie::set('member_account', $cookie_account, $expires);
                            unset($cookie_account);
                        }

                        // set cookie (admin cookie)
                        $cookie_account['account_id'] = $row->account_id;
                        $cookie_account['account_username'] = $row->account_username;
                        $cookie_account['account_email'] = $row->account_email;
                        $cookie_account['account_display_name'] = $row->account_display_name;
                        $cookie_account['account_online_code'] = $session_id;
                        $cookie_account = \Crypt::encode(serialize($cookie_account));
                        Extension\Cookie::set('admin_account', $cookie_account, 0);// admin cookie always expire when close browser. (set to 0)
                        unset($cookie_account, $expires);

                        // update last login in accounts table
                        \DB::update(static::$_table_name)
                            ->where('account_id', $row->account_id)
                            ->set([
                                'account_last_login' => time(),
                                'account_last_login_gmt' => \Extension\Date::localToGmt(),
                            ])
                            ->execute();

                        // add/update last login session.
                        $account_session['account_id'] = $row->account_id;
                        $account_session['session_id'] = $session_id;

                        $account_site = new \Model_AccountSites();
                        $account_site->addLoginSession($account_session);
                        unset($account_session);

                        // record login
                        $account_logins = new \Model_AccountLogins();
                        $account_logins->recordLogin($row->account_id, 1, 'account_login_success');

                        // @todo [fuelstart][account][plug] login success plug.
                        $plugin = new \Library\Plugins();
                        if ($plugin->hasAction('AccountLoginSuccess') !== false) {
                            $plugin->doAction('AccountLoginSuccess', $row->account_id, $row);
                        }

                        unset($account_logins, $account_site, $plugin, $result, $row, $session_id);

                        // login success
                        return true;
                    } else {
                        // permission deny. this user did not allowed to login admin page.
                        // record failed login
                        \Model_AccountLogins::forge()->recordLogin($row->account_id, 0, 'account_not_allow_to_login_to_admin_page');

                        return \Lang::get('admin_you_have_no_permission_to_access_this_page');
                    }
                } else {
                    // check password failed, wrong password
                    $account_logins = new \Model_AccountLogins();
                    $account_logins->recordLogin($row->account_id, 0, 'account_wrong_username_or_password');

                    unset($result, $row);

                    return \Lang::get('account_wrong_username_or_password');
                }
            } else {
                // account disabled
                $account_logins = new \Model_AccountLogins();
                $account_logins->recordLogin($row->account_id, 0, 'account_was_disabled');

                unset($result);

                return \Lang::get('account_was_disabled') . ' : ' . $row->account_status_text;
            }
        }

        // not found account. login failed
        unset($result, $row);

        return \Lang::get('account_wrong_username_or_password');
    }// adminLogin


    /**
     * can i add or edit account
     *
     * @param array $level_groups target level groups
     * @return boolean
     */
    public function canIAddEditAccount($level_groups)
    {
        if (!is_array($level_groups) || (is_array($level_groups) && empty($level_groups))) {
            return false;
        }

        // get account id
        $cookie = $this->getAccountCookie('admin');
        if (!isset($cookie['account_id'])) {
            return false;
        }
        $account_id = $cookie['account_id'];
        unset($cookie);
        
        // get site id and set table prefix for site
        $site_id = \Model_Sites::getSiteId(false);
        $table_site_prefix = '';
        if ($site_id != '1') {
            $table_site_prefix = $site_id . '_';
        }
        unset($site_id);

        // get current user level group priority
        $my_level = \DB::select()
                ->from($table_site_prefix . 'account_level')
                ->as_object('\Model_AccountLevel')
                ->join($table_site_prefix . 'account_level_group', 'LEFT')
                ->on($table_site_prefix . 'account_level_group.level_group_id', '=', $table_site_prefix . 'account_level.level_group_id')
                ->where('account_id', $account_id)
                ->order_by('level_priority', 'ASC')
                ->execute();
        $my_level = $my_level->current();

        if ($my_level == null || $my_level->count() == '0') {
            return false;
        }
        $my_level_priority = $my_level->account_level_group->level_priority;
        
        // loop check each target level group.
        foreach ($level_groups as $level_group_id) {
            // get target level group priority
            $target_level = \Model_AccountLevelGroup::query()->where('level_group_id', $level_group_id)->get_one();

            if ($target_level == null) {
                return false;
            }

            // check if target level is higher than current user level (priority of target is less than my)
            if ($target_level->level_priority < $my_level_priority) {
                return false;
            }
        }

        unset($level_group_id, $my_level, $my_level_priority, $target_level);

        // all checked pass!
        return true;
    }// canIAddEditAccount


    /**
     * check account is logged in correctly and status is enabled. also call to check simultaneous login.
     *
     * @param intger $account_id
     * @param string $account_username
     * @param string $account_email
     * @param string $account_online_code
     * @return boolean
     */
    public function checkAccount($account_id = '', $account_username = '', $account_email = '', $account_online_code = '')
    {
        // check all required data
        if ($account_id == null || $account_username == null || $account_email == null || $account_online_code == null) {
            return false;
        }

        $site_id = \Model_Sites::getSiteId(false);
        
        $cache_name = 'model.accounts-checkAccount-'
                . $site_id . '-'
                . $account_id . '-'
                . \Extension\Security::formatString($account_username, 
                    'alphanum_dash_underscore') . '-'
                . \Extension\Security::formatString($account_email, 
                    'alphanum_dash_underscore') . '-'
                . \Extension\Security::formatString($account_online_code, 
                    'alphanum_dash_underscore');
        $cached = \Extension\Cache::getSilence($cache_name);

        if (false === $cached) {
            // check for matches id username and email. ---------------------------------------------------------------
            $result = \DB::select()
                ->from(static::$_table_name)
                ->where('account_id', $account_id)
                ->where('account_username', $account_username)
                ->where('account_email', $account_email)
                ->where('account_status', 1)
                ->execute();

            if (count($result) > 0) {
                unset($result);

                // if not allow simultaneous login. (if not allow login from many places)
                if (\Model_Config::getval('simultaneous_login') == '0') {
                    if ($this->isSimultaneousLogin($account_id, $account_online_code, $site_id) == true) {

                        // log out
                        static::logout(array('remove_online_code' => false));

                        // load langauge for set error msg.
                        \Lang::load('account');

                        // set error message.
                        \Session::set_flash(
                            'form_status',
                            array(
                                'form_status' => 'error',
                                'form_status_message' => \Lang::get('account_simultaneous_login_detected')
                            )
                        );

                        return false;
                    }
                }

                // check account passed! with or without simultaneous login check.
                \Cache::set($cache_name, true, 2592000);
                return true;
            }

            // not found account in db. or found but disabled
            unset($result);

            // log out
            static::logout();

            return false;
        }
        
        return $cached;
    }// checkAccount


    /**
     * check password
     *
     * @link https://github.com/rchouinard/phpass phpass document
     * @param string $entered_password
     * @param string $hashed_password
     * @param object $account_obj
     * @return boolean return true if passed, return false if not passed.
     */
    public function checkPassword($entered_password = '', $hashed_password = '', $account_obj = '')
    {
        // @todo [fuelstart][account][plug] check password plug.
        $plugin = new \Library\Plugins();
        if ($plugin->hasAction('AccountCheckPassword') !== false) {
            $chk_pass_result = $plugin->doAction('AccountCheckPassword', $entered_password, $hashed_password, $account_obj);
            
            if (is_array($chk_pass_result) && array_key_exists('AccountCheckPassword', $chk_pass_result)) {
                $chk_pass_result = array_shift($chk_pass_result['AccountCheckPassword']);
            }
            
            if (is_bool($chk_pass_result)) {
                unset($plugin);
                return $chk_pass_result;
            }
        }
        unset($chk_pass_result, $plugin);

        include_once APPPATH . DS . 'vendor' . DS . 'phpass' . DS . 'PasswordHash.php';
        $PasswordHash = new PasswordHash($this->password_hash_level, false);
        return $PasswordHash->CheckPassword($entered_password, $hashed_password);
    }// checkPassword


    /**
     * confirm register
     *
     * @param array $data
     * @return boolean|string if passed return true. if failed return error text.
     */
    public static function confirmRegister(array $data = array())
    {
        // @todo [fuelstart][account][plug] confirm register process plug.
        // this will be override the system confirm register process. the hook result should be: true (boolean), error message (string).
        $plugin = new \Library\Plugins();
        if ($plugin->hasAction('AccountConfirmRegisterProcess') !== false) {
            $hook_result = $plugin->doAction('AccountConfirmRegisterProcess', $data);
            if (is_array($hook_result) && array_key_exists('AccountConfirmRegisterProcess', $hook_result)) {
                // if there is a result. shift to the result.
                $hook_result = array_shift($hook_result['AccountConfirmRegisterProcess']);
                if (
                    (is_bool($hook_result) && $hook_result === true) ||
                    (is_string($hook_result) && $hook_result != null)
                ) {
                    // expect the hook result to be true (boolean) or error message (string).
                    unset($hook_result, $plugin);
                    return $hook_result;
                }
            }
        }
        unset($hook_result, $plugin);
        
        // check username and confirm code.
        // confirm register has no time limitation.
        $query = static::query()
                ->where('account_username', $data['account_username'])
                ->where('account_confirm_code', $data['account_confirm_code'])
                ->where('account_status', '0')// newly registered user has status 0
                ->where('account_last_login', null);// newly registered user never login
        if ($query->count() <= 0) {
            // not found.
            unset($query);
            return \Lang::get('account_your_confirm_register_code_is_invalid');
        } else {
            $row = $query->get_one();
            unset($query);
        }

        // found user with this confirm code. update account status to 1 to allow login.
        $account = static::find($row->account_id);
        $account->account_confirm_code = null;
        $account->account_status = 1;
        $account->account_status_text = null;
        $account->save();

        unset($account, $row);

        return true;
    }// confirmRegister


    /**
     * delete account
     *
     * @param integer $account_id
     * @return boolean
     */
    public static function deleteAccount($account_id = '') {
        // check if it is guest or site owner (id = 1), not delete.
        if ($account_id === '0' || $account_id === '1') {return false;}

        // delete avatar
        static::instance()->deleteAccountAvatar($account_id, false);
        
        $plugin = new \Library\Plugins();

        // @todo [fuelstart][account][plug] before delete account plug.
        if ($plugin->hasAction('AccountBeforeDeleteAccount') !== false) {
            $plugin->doAction('AccountBeforeDeleteAccount', $account_id);
        }

        // loop sites to delete this account id related in multi-site tables.
        $site_id = \Model_Sites::getSiteId(false);
        $list_site_option['list_for'] = 'admin';
        $list_site_option['unlimit'] = true;
        $sites = \Model_Sites::listSites($list_site_option);
        
        if (isset($sites['items']) && is_array($sites['items']) && !empty($sites['items'])) {
            foreach ($sites['items'] as $site) {
                // skip delete from current site id table.
                if ($site->site_id != $site_id) {
                    if ($site->site_id == '1') {
                        $table_siteid_prefix = '';
                    } else {
                        $table_siteid_prefix = $site->site_id . '_';
                    }
                    
                    // delete from account_level table
                    \DB::delete($table_siteid_prefix . 'account_level')->where('account_id', $account_id)->execute();
                    
                    // delete from account_fields table
                    \DB::delete($table_siteid_prefix . 'account_fields')->where('account_id', $account_id)->execute();
                    
                    // delete from account_permission table
                    \DB::delete($table_siteid_prefix . 'account_permission')->where('account_id', $account_id)->execute();
                    
                    // @todo [fuelstart][account|multisite][plug] delete data in multisite tables that have account_id related. plug.
                    if ($plugin->hasAction('AccountDeleteOnMultisiteTables') !== false) {
                        $plugin->doAction('AccountDeleteOnMultisiteTables', $account_id, $site->site_id);
                    }
                }
            }
        }
        unset($list_site_option, $plugin, $site, $sites, $table_siteid_prefix);

        // delete account now.
        static::find($account_id)->delete();// needs to use ::find() to delete in related table
        
        // clear cache
        \Extension\Cache::deleteCache('model.accounts-checkAccount-'.$site_id.'-'.$account_id);
        \Extension\Cache::deleteCache('model.accountLevelPermission-checkLevelPermission-' . \Model_Sites::getSiteId(false));
        \Extension\Cache::deleteCache('model.accountPermission-checkAccountPermission-' . \Model_Sites::getSiteId(false));

        return true;
    }// deleteAccount


    /**
     * delete account avatar
     *
     * @param integer $account_id
     * @param boolean $update_db
     * @return boolean
     */
    public function deleteAccountAvatar($account_id = '', $update_db = true)
    {
        if (!is_numeric($account_id)) {
            return false;
        }

        $result = \DB::select()
            ->as_object()
            ->from(static::$_table_name)
            ->where('account_id', $account_id)
            ->execute();

        if (count($result) > 0) {
            $row = $result->current();

            if ($row->account_avatar != null && file_exists($row->account_avatar) && is_file($row->account_avatar)) {
                \File::delete($row->account_avatar);
            }

            // update db
            if ($update_db === true) {
                \DB::update(static::$_table_name)
                    ->set([
                        'account_avatar' => null,
                    ])
                    ->execute();
            }
        }
        
        // clear cache
        $site_id = \Model_Sites::getSiteId(false);
        \Extension\Cache::deleteCache('public.themes.sys2.getAdminAvatar-'.$site_id.'-'.$account_id);

        // @todo [fuelstart][account][plug] deleted avatar plug.
        $plugin = new \Library\Plugins();
        if ($plugin->hasAction('AccountDeleteAvatar') !== false) {
            $plugin->doAction('AccountDeleteAvatar', $account_id, $row);
        }

        unset($plugin, $result, $row, $site_id);
        return true;
    }// deleteAccountAvatar


    /**
     * edit account
     *
     * @param array $data
     * @param array $data_fields
     * @param array $data_level
     * @return boolean
     */
    public static function editAccount(array $data = array(), $data_fields = array(), $data_level = array())
    {
        if (empty($data) || empty($data_level)) {return false;}

        // get config
        $config = \Model_Config::getvalues(array('allow_avatar', 'member_email_change_need_confirm'));

        // check things -------------------------------------------------------------------------------------------------

        // check permission that can i add or edit this account
        if (static::instance()->canIAddEditAccount($data_level['level_group_id']) == false) {
            return \Lang::get('account_you_cannot_edit_account_that_contain_role_higher_than_yours');
        }

        // check for duplicate account (username)
        $query = static::query()->where('account_id', '!=', $data['account_id'])->where('account_username', $data['account_username']);
        if ($query->count() > 0) {
            unset($query);
            return \Lang::get('account_username_already_exists');
        }
        unset($query);

        // if email changed
        if (isset($data['account_old_email']) && $data['account_old_email'] != $data['account_email']) {
            $email_change = true;

            // check duplicate email
            $query = static::query()->where('account_id', '!=', $data['account_id'])->where('account_email', $data['account_email']);
            if ($query->count() > 0) {
                unset($query);
                return \Lang::get('account_email_already_exists');
            }
            unset($query);
        } else {
            $email_change = false;
        }

        // check password change and set new password data for update in db.
        if (!empty($data['account_password'])) {
            // there is current password input.
            if ($data['account_new_password'] != null) {
                // check current password match in db.
                $query = static::query()->where('account_id', $data['account_id'])->where('account_username', $data['account_username']);
                if ($query->count() > 0) {
                    $row = $query->get_one();

                    if (static::instance()->checkPassword($data['account_password'], $row->account_password, $row)) {
                        $data['account_password'] = static::instance()->hashPassword($data['account_new_password']);

                        unset($query, $row);

                        // @todo [fuelstart][account][plug] after changed password plug.
                        $plugin = new \Library\Plugins();
                        if ($plugin->hasAction('AccountAfterChangedPassword') !== false) {
                            $plugin->doAction(
                                'AccountAfterChangedPassword', 
                                $data['account_id'],
                                [
                                    'input_data' => $data,
                                    'input_data_fields' => $data_fields,
                                    'input_data_level' => $data_level,
                                    'inputs_post' => \Input::post(),// grab all input
                                    'email_change' => (isset($email_change) ? $email_change : false),
                                    'password_changed' => true,
                                ]
                            );
                        }
                        unset($plugin);

                        // flash message for changed password please login again.
                        \Session::set_flash(
                            'form_status',
                            array(
                                'form_status' => 'success',
                                'form_status_message' => \Lang::get('account_your_password_changed_please_login_again')
                            )
                        );

                        $password_changed = true;
                    } else {
                        unset($config, $query, $row);

                        return \Lang::get('account_wrong_password');
                    }
                } else {
                    unset($config, $query);

                    return \Lang::get('account_not_found_account_in_db');
                }
            } else {
                unset($config);

                return \Lang::get('account_please_enter_your_new_password');
            }
        } else {
            // no password change
            // remove password data to prevent db update password field to null
            unset($data['account_password']);
        }
        unset($data['account_new_password']);

        // action things -------------------------------------------------------------------------------------------------

        // check avatar upload and move if verified
        if ($config['allow_avatar']['value'] == '1' && (isset($_FILES['account_avatar']['name']) && $_FILES['account_avatar']['name'] != null)) {
            $result = static::instance()->uploadAvatar(array('account_id' => $data['account_id'], 'input_field' => 'account_avatar'));

            if (isset($result['result']) && $result['result'] === true) {
                $data['account_avatar'] = $result['account_avatar'];
            } else {
                unset($config);

                return $result;
            }
        }
        unset($result);

        // if email changed, send confirm
        if ($email_change === true) {
            if ($config['member_email_change_need_confirm']['value'] == '1') {
                // need to send email change confirmation.
                $data['confirm_code'] = Extension\Str::random('alnum', 5);
                $data['confirm_code_since'] = time();
                $send_email_change_confirmation = static::instance()->sendEmailChangeConfirmation($data);

                if ($send_email_change_confirmation === true) {
                    $data['account_confirm_code'] = $data['confirm_code'];
                    $data['account_confirm_code_since'] = $data['confirm_code_since'];
                } else {
                    unset($config);

                    return $send_email_change_confirmation;
                }

                unset($data['confirm_code'], $data['confirm_code_since'], $data['account_email'], $send_email_change_confirmation);
            }
        }
        unset($data['account_old_email']);

        // update account to db. ----------------------------------------
        $account_id = $data['account_id'];
        unset($data['account_id']);

        $accounts = static::find($account_id);
        $accounts->set($data);
        $accounts->save();

        // update level to user. -----------------------------------------
        if (isset($data_level['level_group_id']) && !empty($data_level['level_group_id'])) {
            $al = new \Model_AccountLevel();
            $al->updateLevels($account_id, $data_level['level_group_id']);
            unset($al);
        }

        // update account fields if there is any value. -----------------
        // if set data_field to null means not update account fields
        if (is_array($data_fields) && !empty($data_fields)) {
            $af = new \Model_AccountFields();
            $af->updateAccountFields($account_id, $data_fields);
            unset($af);
        }

        // @todo [fuelstart][account][plug] admin edit account plug.
        $plugin = new \Library\Plugins();
        if ($plugin->hasAction('AccountAdminEditAccount')) {
            $plugin->doAction(
                'AccountAdminEditAccount', 
                $account_id, 
                [
                    'input_data' => $data,
                    'input_data_fields' => $data_fields,
                    'input_data_level' => $data_level,
                    'inputs_post' => \Input::post(),// grab all input
                    'email_change' => (isset($email_change) ? $email_change : false),
                    'password_changed' => (isset($password_changed) ? $password_changed : false),
                ]
            );
        }
        unset($plugin);

        // done
        if (isset($password_changed) && $password_changed === true) {
            static::logout();
        }

        unset($config, $email_change, $password_changed);
        
        // clear cache
        \Extension\Cache::deleteCache('model.accounts-checkAccount-'.\Model_Sites::getSiteId().'-'.$account_id);
        \Extension\Cache::deleteCache('model.accountPermission-checkAccountPermission-' . \Model_Sites::getSiteId(false));

        return true;
    }// editAccount


    /**
     * get account cookie
     *
     * @param string $level
     * @return array|null
     */
    public function getAccountCookie($level = 'member')
    {
        if ($level != 'admin' && $level != 'member') {
            $level = 'member';
        }

        $cookie_account = \Security::xss_clean(\Extension\Cookie::get($level . '_account'));

        if ($cookie_account != null) {
            $cookie_account = \Crypt::decode($cookie_account);
            $cookie_account = @unserialize($cookie_account);// there is no way to check is serialize or unserializable so i have to suppress error.
        }

        return $cookie_account;
    }// getAccountCookie


    /**
     * get table name that already matched site id.
     * 
     * @return type
     */
    public static function getTableName()
    {
        return static::$_table_name;
    }// getTableName


    /**
     * hash password
     * 
     * @link https://github.com/rchouinard/phpass phpass document
     * @param string $password
     * @return string
     */
    public function hashPassword($password = '')
    {
        // @todo [fuelstart][account][plug] hash password plug.
        $plugin = new \Library\Plugins();
        if ($plugin->hasFilter('AccountHashPassword') !== false) {
            $returned_hash_password = $plugin->doFilter('AccountHashPassword', $password);
            
            if ($returned_hash_password != null) {
                unset($plugin);
                return $returned_hash_password;
            }
        }
        unset($plugin, $returned_hash_password);

        include_once APPPATH . DS . 'vendor' . DS . 'phpass' . DS . 'PasswordHash.php';
        $PasswordHash = new PasswordHash($this->password_hash_level, false);
        return $PasswordHash->HashPassword($password);
    }// hashPassword


    /**
     * create instance to call non-static method from static method.
     * @return \Model_Accounts
     */
    private static function instance()
    {
        return new Model_Accounts();
    }// instance


    /**
     * is admin logged in?
     * work same as is member logged in. but it get admin cookie to check not member cookie.
     *
     * @return boolean return true for logged in. return false for not logged in.
     */
    public static function isAdminLogin()
    {
        $cookie_account = static::instance()->getAccountCookie('admin');

        if (!isset($cookie_account['account_id']) || !isset($cookie_account['account_username']) || !isset($cookie_account['account_email']) || !isset($cookie_account['account_display_name']) || !isset($cookie_account['account_online_code'])) {
            return false;
        }

        return static::instance()->checkAccount($cookie_account['account_id'], $cookie_account['account_username'], $cookie_account['account_email'], $cookie_account['account_online_code']);
    }// isAdminLogin


    /**
     * is member logged in?
     *
     * @return boolean return true for logged in. return false for not logged in.
     */
    public static function isMemberLogin()
    {
        $cookie_account = static::instance()->getAccountCookie();

        if (!isset($cookie_account['account_id']) || !isset($cookie_account['account_username']) || !isset($cookie_account['account_email']) || !isset($cookie_account['account_display_name']) || !isset($cookie_account['account_online_code'])) {
            return false;
        }

        return static::instance()->checkAccount($cookie_account['account_id'], $cookie_account['account_username'], $cookie_account['account_email'], $cookie_account['account_online_code']);
    }// isMemberLogin


    /**
     * is simultaneous login (login from multiple places)
     *
     * @param integer $account_id
     * @param string $account_online_code
     * @param integer $site_id site id for multisite code.
     * @return boolean return true if detected simultaneous login, false if logged in only one place.
     */
    private function isSimultaneousLogin($account_id = '', $account_online_code = '', $site_id = '')
    {
        // check required data
        if (!is_numeric($account_id) || $account_online_code == null) {
            return true;
        }

        if ($site_id == null) {
            $site_id = \Model_Sites::getSiteId(false);
        }

        // find this account id and their online code on selected site.
        $result = \DB::select()
            ->from(\Model_AccountSites::getTableName())
            ->where('account_id', $account_id)
            ->where('site_id', $site_id)
            ->where('account_online_code', $account_online_code)
            ->execute();

        if (count($result) > 0) {
            unset($result);

            // not found logged in from other place (online code in db matched with this user's cookie online code).
            return false;
        }

        // not found account on this site. or found but online code does not match (null online code in db means logged out, so it is not match this user that still logged in).
        unset($result);

        return true;
    }// isSimultaneousLogin


    /**
     * list accounts
     *
     * @param array $option available options: [search], [orders], [sort], [offset], [limit], [list_for]
     * @return mixed
     */
    public static function listAccounts($option = array())
    {
        // get total logins of current user
        $query = static::query()
            ->related('account_level');

        // search
        if (isset($option['search']) && $option['search'] != null) {
            $search = $option['search'];

            $query->where_open()
                ->where('account_id', 'LIKE', '%' . $search . '%')
                ->or_where('account_username', 'LIKE', '%' . $search . '%')
                ->or_where('account_email', 'LIKE', '%' . $search . '%')
                ->or_where('account_display_name', 'LIKE', '%' . \Security::htmlentities($search) . '%')
                ->or_where('account_firstname', 'LIKE', '%' . \Security::htmlentities($search) . '%')
                ->or_where('account_middlename', 'LIKE', '%' . \Security::htmlentities($search) . '%')
                ->or_where('account_lastname', 'LIKE', '%' . \Security::htmlentities($search) . '%')
                ->or_where('account_birthdate', 'LIKE', '%' . $search . '%')
                ->or_where('account_avatar', 'LIKE', '%' . $search . '%')
                ->or_where('account_signature', 'LIKE', '%' . \Security::htmlentities($search) . '%')
                ->or_where('account_status_text', 'LIKE', '%' . $search . '%')
            ->where_close();

            unset($search);
        }
        
        // filters ------------------------------------------------------------------------------------------------
        if (isset($option['filter_account_id'])) {
            $query->where('account_id', 'LIKE', '%' . $option['filter_account_id'] . '%');
        }
        if (isset($option['filter_account_username'])) {
            $query->where('account_username', 'LIKE', '%' . $option['filter_account_username'] . '%');
        }
        if (isset($option['filter_account_email'])) {
            $query->where('account_email', 'LIKE', '%' . $option['filter_account_email'] . '%');
        }
        if (isset($option['filter_level_group_id'])) {
            $query->where('account_level.level_group_id', $option['filter_level_group_id']);
        }
        if (isset($option['filter_account_create'])) {
            $datetime = new \DateTime($option['filter_account_create']);
            $query->where('account_create', '>=', $datetime->getTimestamp());
            unset($datetime);
        }
        if (isset($option['filter_account_last_login'])) {
            $datetime = new \DateTime($option['filter_account_last_login']);
            $query->where('account_last_login', '>=', $datetime->getTimestamp());
            unset($datetime);
        }
        if (isset($option['filter_account_status'])) {
            $query->where('account_status', $option['filter_account_status']);
        }
        // filters ------------------------------------------------------------------------------------------------

        $output['total'] = $query->count();

        // sort and order
        $allowed_orders = array('account_id', 'account_username', 'account_email', 'account_display_name', 'account_firstname', 'account_middlename', 'account_lastname', 'account_birthdate', 'account_signature', 'account_timezone', 'account_language', 'account_create', 'account_create_gmt', 'account_last_login', 'account_last_login_gmt', 'account_status', 'account_status_text');
        if (!isset($option['orders']) || (isset($option['orders']) && !in_array($option['orders'], $allowed_orders))) {
            $option['orders'] = 'account_id';
        }
        unset($allowed_orders);
        if (!isset($option['sort'])) {
            $option['sort'] = 'ASC';
        }

        // offset and limit
        if (!isset($option['offset'])) {
            $option['offset'] = 0;
        }
        if (!isset($option['limit'])) {
            if (isset($option['list_for']) && $option['list_for'] == 'admin') {
                $option['limit'] = \Model_Config::getval('content_admin_items_perpage');
            } else {
                $option['limit'] = \Model_Config::getval('content_items_perpage');
            }
        }

        // get the results from sort, order, offset, limit.
        $output['items'] = $query->order_by($option['orders'], $option['sort'])->offset($option['offset'])->limit($option['limit'])->get();

        unset($query);

        return $output;
    }// listAccounts


    /**
     * logout
     *
     * @param array $data options: site_id for logout with specific site id., account_id for logout target account, remove_online_code for remove online code that use to check simultaneous login.
     * @return boolean
     */
    public static function logout($data = array())
    {
        if (!isset($data['site_id']) || (isset($data['site_id']) && $data['site_id'] == null)) {
            $data['site_id'] = \Model_Sites::getSiteId(false);
        }

        // get account id if not set
        if (!isset($data['account_id']) || (isset($data['account_id']) && !is_numeric($data['account_id']))) {
            $cookie = static::instance()->getAccountCookie();

            if (isset($cookie['account_id'])) {
                $data['account_id'] = $cookie['account_id'];
            } else {
                $data['account_id'] = 0;
            }
        }

        \Extension\Cookie::delete('member_account');
        \Extension\Cookie::delete('admin_account');

        if (!isset($data['remove_online_code']) || (isset($data['remove_online_code']) && $data['remove_online_code'] == true)) {
            // delete online code for certain site, so when program check for logged in or simultaneous it will return false.
            \DB::update(\Model_AccountSites::getTableName())
                ->where('account_id', $data['account_id'])
                ->where('site_id', $data['site_id'])
                ->set([
                    'account_online_code' => null,
                ])
                ->execute();
        }
        
        // clear cache
        \Extension\Cache::deleteCache('model.accounts-checkAccount-'.$data['site_id'] .'-'.$data['account_id']);

        return true;
    }// logout


    /**
     * member edit profile.
     *
     * @param array $data
     * @param array $data_field
     * @return mixed
     */
    public static function memberEditProfile(array $data = array(), $data_field = array())
    {
        if (empty($data)) {
            return false;
        }

        // get config
        $config = \Model_Config::getvalues(array('allow_avatar', 'member_email_change_need_confirm'));

        // check things -------------------------------------------------------------------------------------------------

        // check email change?
        if ($data['account_old_email'] == $data['account_email']) {
            $email_change = false;
        } else {
            $email_change = true;

            //check for already in use email
            $query = static::query()->where('account_id', '!=', $data['account_id'])->where('account_email', $data['account_email']);
            if ($query->count() > 0) {
                // found email already in use.
                unset($config, $email_change, $query);

                return \Lang::get('account_email_already_exists');
            } else {
                $data['account_new_email'] = $data['account_email'];
            }
            unset($query);
        }

        // check password change and set new password data for update in db.
        if (!empty($data['account_password'])) {
            // there is current password input.
            if ($data['account_new_password'] != null) {
                // check current password match in db.
                $query = static::query()->where('account_id', $data['account_id'])->where('account_username', $data['account_username']);
                if ($query->count() > 0) {
                    $row = $query->get_one();

                    if (static::instance()->checkPassword($data['account_password'], $row->account_password, $row)) {
                        $data['account_password'] = static::instance()->hashPassword($data['account_new_password']);

                        unset($query, $row);

                        // @todo [fuelstart][account][plug] after changed password plug.
                        $plugin = new \Library\Plugins();
                        if ($plugin->hasAction('AccountAfterChangedPassword') !== false) {
                            $plugin->doAction(
                                'AccountAfterChangedPassword', 
                                $data['account_id'],
                                [
                                    'input_data' => $data,
                                    'input_data_fields' => $data_field,
                                    'inputs_post' => \Input::post(),// grab all input
                                    'email_change' => (isset($email_change) ? $email_change : false),
                                    'password_changed' => true,
                                ]
                            );
                        }
                        unset($plugin);

                        // flash message for changed password please login again.
                        \Session::set_flash(
                            'form_status',
                            array(
                                'form_status' => 'success',
                                'form_status_message' => \Lang::get('account_your_password_changed_please_login_again')
                            )
                        );

                        $password_changed = true;
                    } else {
                        unset($config, $query, $row);

                        return \Lang::get('account_wrong_password');
                    }
                } else {
                    unset($config, $query);

                    return \Lang::get('account_not_found_account_in_db');
                }
            } else {
                unset($config);

                return \Lang::get('account_please_enter_your_new_password');
            }
        } else {
            // no password change
            // remove password data to prevent db update password field to null
            unset($data['account_password']);
        }
        unset($data['account_new_password']);

        // action things -------------------------------------------------------------------------------------------------

        // check avatar upload and move if verified.
        if ($config['allow_avatar']['value'] == '1' && (isset($_FILES['account_avatar']['name']) && $_FILES['account_avatar']['name'] != null)) {
            $result = static::instance()->uploadAvatar(array('account_id' => $data['account_id'], 'input_field' => 'account_avatar'));

            if (isset($result['result']) && $result['result'] === true) {
                $data['account_avatar'] = $result['account_avatar'];
            } else {
                unset($config);

                return $result;
            }
        }

        // if email change, send confirm link to old email
        if ($email_change === true) {
            if ($config['member_email_change_need_confirm']['value'] == '1') {
                // need to send email change confirmation.
                $data['confirm_code'] = Extension\Str::random('alnum', 5);
                $data['confirm_code_since'] = time();
                $send_email_change_confirmation = static::instance()->sendEmailChangeConfirmation($data);

                if ($send_email_change_confirmation === true) {
                    $data['account_confirm_code'] = $data['confirm_code'];
                    $data['account_confirm_code_since'] = $data['confirm_code_since'];
                } else {
                    unset($config);

                    return $send_email_change_confirmation;
                }

                unset($data['confirm_code'], $data['confirm_code_since'], $data['account_email'], $send_email_change_confirmation);
            } else {
                // no need to send email change confirmation. just change email.
                $data['account_email'] = $data['account_new_email'];

                unset($data['account_new_email']);
            }
        }
        unset($data['account_old_email']);

        // update to db.
        $datasave = $data;
        unset($datasave['account_id']);

        $accounts = static::find($data['account_id']);
        $accounts->set($datasave);
        $accounts->save();
        unset($datasave);

        // update account fields
        // if set data_field to null means not update account fields
        if (is_array($data_field)) {
            $af = new \Model_AccountFields();
            $af->updateAccountFields($data['account_id'], $data_field);
            unset($af);
        }

        // @todo [fuelstart][account][plug] member edit account plug.
        $plugin = new \Library\Plugins();
        if ($plugin->hasAction('AccountMemberEditAccount')) {
            $plugin->doAction(
                'AccountMemberEditAccount', 
                $data['account_id'], 
                [
                    'input_data' => $data,
                    'input_data_fields' => $data_field,
                    'inputs_post' => \Input::post(),// grab all input
                    'email_change' => (isset($email_change) ? $email_change : false),
                    'password_changed' => (isset($password_changed) ? $password_changed : false),
                ]
            );
        }
        unset($plugin);

        // done
        if (isset($password_changed) && $password_changed === true) {
            static::logout();
        }

        unset($config, $email_change, $password_changed);
        
        // clear cache
        \Extension\Cache::deleteCache('model.accounts-checkAccount-'.\Model_Sites::getSiteId().'-'.$data['account_id']);

        return true;
    }// memberEditProfile


    /**
     * member login.
     *
     * @param array $data
     * @return mixed return true on success, return error message on failed.
     */
    public static function memberLogin($data = array())
    {
        if (!isset($data['account_password']) || (!isset($data['account_username']) && !isset($data['account_email']))) {
            return false;
        } else {
            if (!isset($data['account_username'])) {
                $data['account_username'] = null;
            }
            if (!isset($data['account_email'])) {
                $data['account_email'] = null;
            }
        }

        $query = static::query()
                ->where('account_username', $data['account_username'])
                ->or_where('account_email', $data['account_email']);

        if ($query->count() > 0) {
            // found
            $row = $query->get_one();
            // clear cache
            \Extension\Cache::deleteCache('model.accounts-checkAccount-' . \Model_Sites::getSiteId() . '-' . $row->account_id);

            // check enabled account.
            if ($row->account_status == '1') {
                // enabled
                // check password
                if (static::instance()->checkPassword($data['account_password'], $row->account_password, $row) === true) {
                    // check password passed
                    // generate session id for check simultaneous login
                    $session_id = \Session::key('session_id');

                    // if login set to remember, set expires.
                    if (\Input::post('remember') == 'yes') {
                        $expires = (\Model_Config::getval('member_login_remember_length')*24*60*60);
                    } else {
                        $expires = 0;
                    }

                    // set cookie
                    $cookie_account['account_id'] = $row->account_id;
                    $cookie_account['account_username'] = $row->account_username;
                    $cookie_account['account_email'] = $row->account_email;
                    $cookie_account['account_display_name'] = $row->account_display_name;
                    $cookie_account['account_online_code'] = $session_id;
                    $cookie_account = \Crypt::encode(serialize($cookie_account));
                    Extension\Cookie::set('member_account', $cookie_account, $expires);
                    unset($cookie_account, $expires);

                    // update last login in accounts table
                    $accounts = static::find($row->account_id);
                    $accounts->account_last_login = time();
                    $accounts->account_last_login_gmt = \Extension\Date::localToGmt();
                    $accounts->save();
                    unset($accounts);

                    // add/update last login session.
                    $account_session['account_id'] = $row->account_id;
                    $account_session['session_id'] = $session_id;

                    $account_site = new \Model_AccountSites();
                    $account_site->addLoginSession($account_session);
                    unset($account_session);

                    // record login
                    $account_logins = new Model_AccountLogins();
                    $account_logins->recordLogin($row->account_id, 1, 'account_login_success');

                    // @todo [fuelstart][account][plug] login success plug.
                    $plugin = new \Library\Plugins();
                    if ($plugin->hasAction('AccountLoginSuccess') !== false) {
                        $plugin->doAction('AccountLoginSuccess', $row->account_id, $row);
                    }

                    unset($plugin, $query, $row, $session_id);

                    // login success
                    return true;
                } else {
                    // check password failed, wrong password
                    $account_logins = new Model_AccountLogins();
                    $account_logins->recordLogin($row->account_id, 0, 'account_wrong_username_or_password');

                    unset($query, $row);

                    return \Lang::get('account_wrong_username_or_password');
                }
            } else {
                // account disabled
                $account_logins = new Model_AccountLogins();
                $account_logins->recordLogin($row->account_id, 0, 'account_was_disabled');

                unset($query);

                return \Lang::get('account_was_disabled') . ' : ' . $row->account_status_text;
            }
        }

        // not found account. login failed
        unset($query);

        return \Lang::get('account_wrong_username_or_password');
    }// memberLogin


    /**
     * register new account
     *
     * @param array $data
     * @param array $data_fields additional fields to store in account_fields table.
     * @return boolean|string return true when completed and return error text when error occured.
     */
    public static function registerAccount($data = array(), $data_fields = array())
    {
        // check required data.
        if (empty($data) || !is_array($data)) {return false;}

        // get configurations db
        $cfg = \Model_Config::getvalues(array('member_verification', 'member_disallow_username'));

        // verify disallow username.
        if (isset($cfg['member_disallow_username']['value'])) {
            $cfg['member_disallow_username']['value'] = str_replace(', ', ',', $cfg['member_disallow_username']['value']);
            $disallow_usernames = explode(',', $cfg['member_disallow_username']['value']);
            foreach ($disallow_usernames as $disallow_username) {
                if ($data['account_username'] == trim($disallow_username)) {
                    unset($cfg, $disallow_username, $disallow_usernames);
                    return \Lang::get('account_username_disallowed');
                }
            }
        }

        // check duplicate username.
        $query = static::query()->select('account_username')->where('account_username', $data['account_username']);
        if ($query->count() > 0) {
            unset($query);
            return \Lang::get('account_username_already_exists');
        }
        unset($query);

        // check duplicate email.
        $query = static::query()->select('account_email')->where('account_email', $data['account_email']);
        if ($query->count() > 0) {
            unset($query);
            return \Lang::get('account_email_already_exists');
        }
        unset($query);

        if ($cfg['member_verification']['value'] != '0') {
            // admin config need to verify.
            // generate confirm code
            $data['account_confirm_code'] = \Str::random('alnum', 6);
        }

        // send register email
        $send_result = static::instance()->sendRegisterEmail($data);
        if ($send_result !== true) {
            return $send_result;
        }
        unset($send_result);

        $data['account_password'] = static::instance()->hashPassword($data['account_password']);
        $data['account_create'] = time();
        $data['account_create_gmt'] = \Extension\Date::localToGmt();
        if ($cfg['member_verification']['value'] == '0') {
            // admin config to no need to verify.
            $data['account_status'] = '1';
        } else {
            $data['account_status'] = '0';
            if ($cfg['member_verification']['value'] == '2') {
                $data['account_status_text'] = \Lang::get('account_waiting_for_admin_verification');
            } else {
                $data['account_status_text'] = \Lang::get('account_please_confirm_registration_from_your_email');
            }
        }

        // add account to db. ----------------------------------------
        //list($account_id) = \DB::insert(static::$_table_name)->set($data); // query builder style.
        $account = static::forge($data);

        // add level to user for current site.
        $account->account_level[0] = new Model_AccountLevel();
        $account->account_level[0]->level_group_id = 3;

        $account->save();
        $account_id = $account->account_id;
        unset($account);
        // end add account to db -------------------------------------

        // add level to user.
        // loop sites to add level of this user to each site.
        $site_id = \Model_Sites::getSiteId(false);
        $list_site_option['list_for'] = 'admin';
        $list_site_option['unlimit'] = true;
        $sites = \Model_Sites::listSites($list_site_option);
        if (isset($sites['items']) && is_array($sites['items']) && !empty($sites['items'])) {
            foreach ($sites['items'] as $site) {
                if ($site->site_id != $site_id) {
                    if ($site->site_id == '1') {
                        $table_name = 'account_level';
                    } else {
                        $table_name = $site->site_id . '_account_level';
                    }
                    
                    \DB::insert($table_name)->set(array(
                        'account_id' => $account_id,
                        'level_group_id' => '3',// for other sites, set user level as member for security reason.
                    ))->execute();
                }
            }
        }
        unset($list_site_option, $site, $sites, $site_id, $table_name);

        // add account fields if there is any value.
        // to add account fields data structure shoud be like this...
        // array(array('field_name' => 'website', 'field_value' => 'http://domain.tld'), array('field_name' => 'fb', 'field_value' => 'http://fb.com/myprofile'));
        // or
        // $af[0]['field_name'] = 'website';
        // $af[0]['field_value'] = 'http://domain.tld';
        // $sf[1]['field_name'] = 'fb';
        // $sf[1]['field_value'] = 'http://fb.com/myprofile';
        if (!empty($data_fields) && is_array($data_fields)) {
            foreach ($data_fields as $field) {
                $account_fields = static::forge($field);
                $account_fields->account_id = $account_id;
                $account_fields->save();
            }
            unset($account_fields, $field);
        }

        // @todo [fuelstart][account][plug] account after register plug.
        // after saved newly user data but not confirm (if require confirm).
        $plugin = new \Library\Plugins();
        if ($plugin->hasAction('AccountAfterRegister') !== false) {
            $plugin->doAction(
                'AccountAfterRegister', 
                [
                    'input_data' => $data,
                    'input_data_fields' => $data_fields,
                    'inputs_post' => \Input::post(),// grab all input
                ]
            );
        }
        unset($plugin);

        return true;
    }// registerAccount.


    /**
     * send email change confirmation for require user to confirm changed action.
     *
     * @param array $data required data array: confirm_code, confirm_code_since, account_username, account_email
     * @return mixed
     */
    public function sendEmailChangeConfirmation(array $data = array())
    {
        if (!isset($data['confirm_code']) || !isset($data['confirm_code_since']) || !isset($data['account_username']) || !isset($data['account_email'])) {
            return false;
        }

        $cfg_member_confirm_wait_time = \Model_Config::getval('member_confirm_wait_time')*60;

        // email content
        $email_content = \Extension\EmailTemplate::readTemplate('email_change1.html');
        $email_content = str_replace("%username%", \Security::htmlentities($data['account_username']), $email_content);
        $email_content = str_replace("%newemail%", \Security::htmlentities($data['account_email']), $email_content);
        $email_content = str_replace("%link_confirm%", \Uri::create('account/confirm-change-email/' . $data['account_id'] . '/' . $data['confirm_code'] . '/confirm'), $email_content);
        $email_content = str_replace("%link_cancel%", \Uri::create('account/confirm-change-email/' . $data['account_id'] . '/' . $data['confirm_code'] . '/cancel'), $email_content);
        $email_content = str_replace("%confirm_until%", date('d F Y H:i:s', (time()+$cfg_member_confirm_wait_time)), $email_content);

        \Package::load('email');
        $config = \Extension\Email::getConfig();
        $email = \Email::forge($config);
        $email->from(\Model_Config::getval('mail_sender_email'));
        $email->to($data['account_old_email']);
        $email->subject(\Lang::get('account_please_confirm_change_email'));
        $email->html_body($email_content);
        $email->alt_body(str_replace("\t", '', strip_tags($email_content)));
        if ($email->send() == false) {
            unset($cfg_member_confirm_wait_time, $config, $email, $email_content);
            return \Lang::get('account_email_could_not_send');
        }

        unset($cfg_member_confirm_wait_time, $config, $email, $email_content);

        return true;
    }// sendEmailChangeConfirmation


    /**
     * send register email
     * @param array $data required data array: account_username, account_email, account_confirm_code
     * @return boolean|string return true when send register email was done and return error text when error occured.
     */
    public function sendRegisterEmail($data = array(), $options = array())
    {
        if (!isset($data['account_username']) || !isset($data['account_email']) || !isset($data['account_confirm_code'])) {return false;}

        $cfg = \Model_Config::getvalues(array('member_verification', 'mail_sender_email', 'member_register_notify_admin', 'member_admin_verify_emails'));

        // email content
        $member_verification = $cfg['member_verification']['value'];
        if ($member_verification == '0') {
            // not verify.
            $not_verify_register = true;
        } elseif ($member_verification == '1') {
            // verify by email. (user verify)
            $email_content = \Extension\EmailTemplate::readTemplate('register_user_verify_account.html');
        } elseif ($member_verification == '2') {
            // verify by admin. (admin allow or not)
            $email_content = \Extension\EmailTemplate::readTemplate('register_admin_verify_account.html');
        }

        // modify email content for ready to send.
        if (isset($email_content) && $email_content != null) {
            $email_content = str_replace("%username%", \Security::htmlentities($data['account_username']), $email_content);
            $email_content = str_replace('%register_confirm_link%', \Uri::create('account/confirm-register/'.urlencode($data['account_username']).'/'.urlencode($data['account_confirm_code'])), $email_content);
        } elseif (isset($email_content) && $email_content == null) {
            return \Lang::get('account_unable_to_load_email_template');
        }

        // if need to send verify register
        if (!isset($not_verify_register) || (isset($not_verify_register) && $not_verify_register == false)) {
            // send email to notify user, admin to verify registration
            \Package::load('email');
            $config = \Extension\Email::getConfig();
            $email = \Email::forge($config);
            $email->from($cfg['mail_sender_email']['value']);
            $email->to($data['account_email']);
            if ($member_verification == '1') {
                $email->subject(\Lang::get('account_please_confirm_your_account'));
            } elseif ($member_verification == '2') {
                $email->subject(\Lang::get('account_please_verify_user_registration'));
            }
            $email->html_body($email_content);
            $email->alt_body(str_replace("\t", '', strip_tags($email_content)));
            if ($email->send() == false) {
                // email could not sent.
                unset($cfg, $config, $email, $email_content, $member_verification, $not_verify_register);
                return \Lang::get('account_email_could_not_send');
            }
            unset($email, $email_content, $not_verify_register);
        }

        // if member verification need admin to verify OR register needs to notify admin.
        if (($member_verification == '2' || $cfg['member_register_notify_admin']['value'] == '1') && (!isset($options['not_notify_admin']) || (isset($options['not_notify_admin']) && $options['not_notify_admin'] == false))) {
            // email content
            $email_content = \Extension\EmailTemplate::readTemplate('register_notify_admin.html');
            $email_content = str_replace("%username%", \Security::htmlentities($data['account_username']), $email_content);

            \Package::load('email');
            $config = \Extension\Email::getConfig();
            $email = \Email::forge($config);
            $email->from($cfg['mail_sender_email']['value']);
            $email->to(\Extension\Email::setEmails($cfg['member_admin_verify_emails']['value']));
            $email->subject(\Lang::get('account_notify_admin_new_register_account', array('username' => $data['account_username'])));
            $email->html_body($email_content);
            $email->alt_body(str_replace("\t", '', strip_tags($email_content)));
            if ($email->send() == false) {
                // email could not sent.
                unset($cfg, $config, $email, $email_content, $member_verification, $not_verify_register);
                return \Lang::get('account_email_could_not_send');
            }
        }

        unset($cfg, $config, $member_verification, $not_verify_register);

        return true;
    }// sendRegisterEmail


    /**
     * send reset password email
     *
     * @param array $data
     * @return mixed
     */
    public static function sendResetPasswordEmail(array $data = array())
    {
        if (!isset($data['account_email'])) {
            return false;
        }

        $query = static::query()->where('account_email', $data['account_email']);

        if ($query->count() > 0) {
            $row = $query->get_one();
            unset($query);

            if ($row->account_status == '0') {
                return \Lang::get('account_was_disabled') . ' : ' . $row->account_status_text;
            }

            $cfg_member_confirm_wait_time = \Model_Config::getval('member_confirm_wait_time')*60;

            // check confirm wait time. you need to wait until 'wait time' passed to send reset password request again.
            if ($row->account_confirm_code != null && time()-$row->account_confirm_code_since <= $cfg_member_confirm_wait_time) {
                return \Lang::get('account_reset_password_please_wait_until', array('wait_til_time' => date('d F Y H:i:s', ($row->account_confirm_code_since+(\Model_Config::getval('member_confirm_wait_time')*60)))));
            }

            $account_new_password = \Str::random('alnum', 10);
            $account_confirm_code = \Str::random('alnum', 5);
            $account_confirm_code_since = time();

            $email_content = \Extension\EmailTemplate::readTemplate('reset_password1.html');
            $email_content = str_replace("%username%", \Security::htmlentities($row->account_username), $email_content);
            $email_content = str_replace("%link_confirm%", \Uri::create('account/resetpw/' . $row->account_id . '/' . $account_confirm_code . '/reset'), $email_content);
            $email_content = str_replace("%link_cancel%", \Uri::create('account/resetpw/' . $row->account_id . '/' . $account_confirm_code . '/cancel'), $email_content);
            $email_content = str_replace("%confirm_until%", date('d F Y H:i:s', (time()+$cfg_member_confirm_wait_time)), $email_content);

            \Package::load('email');
            $config = \Extension\Email::getConfig();
            $email = \Email::forge($config);
            $email->from(\Model_Config::getval('mail_sender_email'));
            $email->to($data['account_email']);
            $email->subject(\Lang::get('account_email_reset_password_request'));
            $email->html_body($email_content);
            $email->alt_body(str_replace("\t", '', strip_tags($email_content)));
            if ($email->send() == false) {
                unset($account_confirm_code, $account_confirm_code_since, $account_new_password, $cfg_member_confirm_wait_time, $config, $email, $email_content, $query, $row);
                return \Lang::get('account_email_could_not_send');
            }

            unset($cfg_member_confirm_wait_time, $config, $email, $email_content);

            // update to db.
            //$row->account_new_password = static::instance()->hashPassword($account_new_password);
            $row->account_confirm_code = $account_confirm_code;
            $row->account_confirm_code_since = $account_confirm_code_since;
            $row->save();

            unset($account_confirm_code, $account_confirm_code_since, $account_new_password, $row);

            return true;
        }

        // account not found.
        return \Lang::get('account_didnot_found_entered_email');
    }// sendResetPasswordEmail


    /**
     * upload avatar
     *
     * @param array $data
     * @return mixed.
     */
    public function uploadAvatar(array $data = array())
    {
        if ((isset($data['account_id']) && !is_numeric($data['account_id']))) {
            return 'Account ID is required.';
        }

        if (!isset($data['input_field'])) {
            $data['input_field'] = 'account_avatar';
        }

        $cfg_values = array('allow_avatar', 'avatar_size', 'avatar_allowed_types', 'avatar_path');
        $config = \Model_Config::getvalues($cfg_values);
        unset($cfg_values);

        if ($config['allow_avatar']['value'] != '1') {
            return \Lang::get('account_didnot_allow_avatar');
        }

        $upload = new \Extension\Upload();
        $upload->allowed_ext = explode('|', $config['avatar_allowed_types']['value']);
        $upload->auto_validate_mime = true;
        $upload->allowed_max_size = $config['avatar_size']['value'] . 'K';
        $upload->directory = $config['avatar_path']['value'];
        $upload->new_name = md5(\Str::random('alnum', 5) . time());
        $upload->overwrite = false;

        if ($upload->upload($data['input_field']) == false) {
            unset($config);

            return '<ul>' . $upload->displayErrors('<li>', '</li>') . '</ul>';
        } else {
            // upload success. delete old avatar but not delete path in db.
            if (isset($data['account_id'])) {
                $this->deleteAccountAvatar($data['account_id'], false);
            }

            // get uploaded data
            $upload_data = $upload->getData();

            // check required memory to resize image to prevent error.
            if (\Extension\Image::checkMemAvailbleForResize($config['avatar_path']['value'] . $upload_data['name'], 400, 1000, false, 3.9) == true) {
                // resize to prevent very large image.
                include_once APPPATH . 'vendor' . DS . 'okvee' . DS . 'vimage' . DS . 'Okvee' . DS . 'Vimage' . DS . 'Vimage.php';
                $vimage = new \Okvee\Vimage\Vimage($config['avatar_path']['value'] . $upload_data['name']);
                $vimage->resize(400, 1000);
                $vimage->save($config['avatar_path']['value'] . $upload_data['name']);
                $vimage->clear();

                unset($vimage);
            } else {
                // delete uploaded file
                \File::delete($config['avatar_path']['value'] . $upload_data['name']);

                // delete old avatar path in db
                if (isset($data['account_id'])) {
                    $this->deleteAccountAvatar($data['account_id']);
                }

                // not enough memory to resize image.
                unset($config, $upload, $upload_data);

                return \Lang::get('account_not_enough_memory_to_resize_image');
            }

            // done.
            return array(
                'result' => true,
                'account_avatar' => $config['avatar_path']['value'] . $upload_data['name'],
            );
        }
    }// uploadAvatar


}
