<?php
/**
 * account_level ORM and reusable function
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 *
 */

class Model_AccountLevel extends \Orm\Model
{


    protected static $_table_name = 'account_level';
    protected static $_primary_key = array('level_id');
    protected static $_properties = array('level_id', 'level_group_id', 'account_id');

    // relations
    protected static $_belongs_to = array(
        'account_level_group' => array(
            'model_to' => 'Model_AccountLevelGroup',
            'key_from' => 'level_group_id',
            'key_to' => 'level_group_id',
        ),
        'accounts' => array(
            'model_to' => 'Model_Accounts',
            'key_from' => 'account_id',
            'key_to' => 'account_id',
        )
    );


    /**
     * run before initialize the class
     * use this method to set new table prefix with multisite.
     */
    public static function _init()
    {
        // get current site id
        $site_id = \Model_Sites::getSiteId(false);

        if ($site_id != '1') {
            static::$_table_name = $site_id . '_' . static::$_table_name;
        }
    }// _init


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
     * update account levels
     *
     * @param integer $account_id
     * @param array $data_level
     * @return boolean
     */
    public function updateLevels($account_id = '', $data_level = array())
    {
        // delete not exists level
        $lvls = static::query()->where('account_id', $account_id);
        if ($lvls->count() > 0) {
            foreach ($lvls->get() as $lvl) {
                if (!in_array($lvl->level_group_id, $data_level)) {
                    \DB::delete(static::$_table_name)
                        ->where('account_id', $account_id)
                        ->where('level_id', $lvl->level_id)
                        ->execute();
                }
            }
        }
        unset($lvls, $lvl);

        // update or insert fields
        if (is_array($data_level) && !empty($data_level)) {
            foreach ($data_level as $level_group_id) {
                $result = \DB::select()
                    ->from(static::$_table_name)
                    ->where('account_id', $account_id)
                    ->where('level_group_id', $level_group_id)
                    ->execute();

                if (count($result) <= 0) {
                    // not exists, use insert.
                    \DB::insert(static::$_table_name)
                        ->set([
                            'account_id' => $account_id,
                            'level_group_id' => $level_group_id,
                        ])
                        ->execute();
                }

                unset($result);
            }
        }
        
        // clear cache
        \Extension\Cache::deleteCache('model.accountLevelPermission-checkLevelPermission-' . \Model_Sites::getSiteId(false));

        return true;
    }// updateLevels


}
