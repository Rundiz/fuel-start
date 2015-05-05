<?php
/**
 * account_level_group ORM and reusable functions
 *
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 *
 */

class Model_AccountLevelGroup extends \Orm\Model
{


    protected static $_table_name = 'account_level_group';
    protected static $_primary_key = array('level_group_id');
    protected static $_properties = array('level_group_id', 'level_name', 'level_description', 'level_priority');

    // relations
    protected static $_has_many = array(
        'account_level' => array(
            'model_to' => 'Model_AccountLevel',
            'key_from' => 'level_group_id',
            'key_to' => 'level_group_id',
            'cascade_delete' => true,
        ),
        'account_level_permission' => array(
            'model_to' => 'Model_AccountLevelPermission',
            'key_from' => 'level_group_id',
            'key_to' => 'level_group_id',
            'cascade_delete' => true,
        )
    );


    /**
     *disallowed edit or delete level_group_id
     *
     * @var array array of disallowed ids
     */
    public $disallowed_edit_delete = array(1, 2, 3, 4);


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
     * add level groupo
     *
     * @param array $data
     * @return boolean
     */
    public static function addLevel($data = array())
    {
        // get new priority
        $result = \DB::select()
            ->as_object()
            ->from(static::$_table_name)
            ->where('level_group_id', 'NOT IN', static::forge()->disallowed_edit_delete)
            ->order_by('level_priority', 'DESC')
            ->execute();

        if (count($result) <= 0) {
            $data['level_priority'] = 3;
        } else {
            $row = $result->current();
            $data['level_priority'] = ($row->level_priority+1);
        }

        unset($result, $row);

        // add to db.
        \DB::insert(static::$_table_name)
            ->set($data)
            ->execute();

        // done
        return true;
    }// addLevel


    /**
     * delete level group.
     *
     * @param integer $level_group_id
     * @return boolean
     */
    public static function deleteLevel($level_group_id = '')
    {
        if (in_array($level_group_id, static::forge()->disallowed_edit_delete)) {
            return false;
        }

        // @todo [fuelstart][api] for delete level group or role here.
        
        // delete related tables.
        \DB::delete(\Model_AccountLevel::getTableName())->where('level_group_id', $level_group_id)->execute();
        \DB::delete(\Model_AccountLevelPermission::getTableName())->where('level_group_id', $level_group_id)->execute();

        // delete level group
        \DB::delete(static::$_table_name)->where('level_group_id', $level_group_id)->execute();

        return true;
    }// deleteLevel


    /**
     * edit level group
     *
     * @param array $data
     * @return boolean
     */
    public static function editLevel($data = array())
    {
        // set level_group_id variable and unset it from $data to prevent update error PK
        $level_group_id = $data['level_group_id'];
        unset($data['level_group_id']);

        \DB::update(static::$_table_name)
            ->where('level_group_id', $level_group_id)
            ->set($data)
            ->execute();

        return true;
    }// editLevel
    
    
    /**
     * get highest priority level of selected user.
     * 
     * @param integer $account_id account id.
     * @return mixed return object when found, return false when not found
     */
    public static function getHighestPriorityAccountLevel($account_id = '')
    {
        // get site id and set table prefix for site
        $site_id = \Model_Sites::getSiteId(false);
        $table_site_prefix = '';
        if ($site_id != '1') {
            $table_site_prefix = $site_id . '_';
        }
        unset($site_id);
        
        $query = \DB::select()
                ->from($table_site_prefix . 'account_level')
                ->as_object('\Model_AccountLevel')
                ->join($table_site_prefix . 'account_level_group', 'LEFT')
                ->on($table_site_prefix . 'account_level_group.level_group_id', '=', $table_site_prefix . 'account_level.level_group_id')
                ->where('account_id', $account_id)
                ->order_by('level_priority', 'ASC')
                ->execute();
        
        if ($query == null || $query->count() == '0') {
            return false;
        }
        
        $entry = $query->current();
        
        unset($query);
        
        return $entry;
    }// getHighestPriorityAccountLevel


    /**
     * list level groups
     *
     * @param array $option
     * @return mixed
     */
    public static function listLevels($option = array())
    {
        $query = static::query();

        if (isset($option['no_guest']) && $option['no_guest'] == true) {
            $query->where_open();
            $query->where('level_group_id', '!=', '4');
            $query->or_where('level_priority', '!=', '1000');
            $query->where_close();
        }

        $output['total'] = $query->count();

        // sort order
        $allowed_orders = array('level_group_id', 'level_name', 'level_description', 'level_priority');
        if (!isset($option['orders']) || (isset($option['orders']) && !in_array($option['orders'], $allowed_orders))) {
            $orders = 'level_priority';
        } else {
            $orders = $option['orders'];
        }
        if (!isset($option['sort']) || (isset($option['sort']) && $option['sort'] != 'DESC')) {
            $sort = 'ASC';
        } else {
            $sort = $option['sort'];
        }

        $output['items'] = $query->order_by($orders, $sort)->get();

        unset($allowed_orders, $orders, $query, $sort);

        return $output;
    }// listLevels


}
