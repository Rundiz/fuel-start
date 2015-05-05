<?php
/**
 * account_sites ORM and reusable function
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 *
 */


class Model_AccountSites extends \Orm\Model
{


    protected static $_table_name = 'account_sites';
    protected static $_primary_key = array('account_site_id');
    protected static $_properties = array('account_site_id', 'account_id', 'site_id', 'account_last_login', 'account_last_login_gmt', 'account_online_code');

    // relations
    protected static $_belongs_to = array(
        'accounts' => array(
            'model_to' => 'Model_Accounts',
            'key_from' => 'account_id',
            'key_to' => 'account_id',
        ),
        'sites' => array(
            'model_to' => 'Model_Sites',
            'key_from' => 'site_id',
            'key_to' => 'site_id',
        ),
    );


    /**
     * add login session
     *
     * @param array $data
     */
    public function addLoginSession($data = array())
    {
        if (!isset($data['site_id'])) {
            $site_id = \Model_Sites::getSiteId(false);
        } else {
            $site_id = $data['site_id'];
        }
        unset($data['site_id']);

        // find exists last login on target site id.
        $result = \DB::select()
            ->as_object()
            ->from(static::$_table_name)
            ->where('account_id', $data['account_id'])
            ->where('site_id', $site_id)
            ->execute();

        if (count($result) <= 0) {
            // use insert
            $insert['account_id'] = $data['account_id'];
            $insert['site_id'] = $site_id;
            $insert['account_last_login'] = time();
            $insert['account_last_login_gmt'] = \Extension\Date::localToGmt();
            if (isset($data['session_id'])) {
                $insert['account_online_code'] = $data['session_id'];
            }
            
            \DB::insert(static::$_table_name)
                ->set($insert)
                ->execute();

            unset($insert);
        } else {
            // use update
            $update['account_last_login'] = time();
            $update['account_last_login_gmt'] = \Extension\Date::localToGmt();
            if (isset($data['session_id'])) {
                $update['account_online_code'] = $data['session_id'];
            }
            
            \DB::update(static::$_table_name)
                ->where('account_id', $data['account_id'])
                ->where('site_id', $site_id)
                ->set($update)
                ->execute();

            unset($update);
        }

        unset($result, $site_id);
    }// addLoginSession


    /**
     * get table name that already matched site id.
     * 
     * @return type
     */
    public static function getTableName()
    {
        return static::$_table_name;
    }// getTableName


}
