<?php
/**
 * account_fields ORM and reusable function
 *
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 *
 */

class Model_AccountFields extends \Orm\Model
{


    protected static $_table_name = 'account_fields';
    protected static $_primary_key = array();
    protected static $_properties = array('account_id', 'field_name', 'field_value');

    // relations
    protected static $_belongs_to = array(
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
     * get data
     *
     * @param integer $account_id
     * @return object
     */
    public static function getData($account_id = '')
    {
        if (!is_numeric($account_id)) {
            return false;
        }

        $query = \DB::select()->from(static::$_table_name)->where('account_id', $account_id)->as_object(__CLASS__)->execute();
        /**
         * as_object('Model_Name') means you can foreach loop and access $row::method_of_this_class() as you accessing that model object.
         * example:
         * foreach ($query as $row) {
         *     echo $row::testStaticMethod();
         * }
         * the 'testStaticMethod' must be in that model.
         */

        return $query;
    }// getData


    /**
     * update account fields
     *
     * @param integer $account_id
     * @param array $data_fields
     * @return boolean
     */
    public function updateAccountFields($account_id = '', array $data_fields = array())
    {
        if (!is_numeric($account_id)) {
            return false;
        }

        // delete not exists fields.
        $current_af = static::getData($account_id);

        if ($current_af->count() > 0) {
            foreach ($current_af as $af) {
                if (!isset($data_fields[$af->field_name])) {
                    \DB::delete(static::$_table_name)
                        ->where('account_id', $account_id)
                        ->where('field_name', $af->field_name)
                        ->execute();
                }
            }
        }
        unset($af, $current_af);

        // update or insert fields.
        if (is_array($data_fields) && !empty($data_fields)) {
            foreach ($data_fields as $field_name => $field_value) {
                $result = \DB::select()
                    ->from(static::$_table_name)
                    ->where('account_id', $account_id)
                    ->where('field_name', $field_name)
                    ->execute();

                if (count($result) <= 0) {
                    // use insert
                    \DB::insert(static::$_table_name)
                        ->set([
                            'account_id' => $account_id,
                            'field_name' => $field_name,
                            'field_value' => $field_value,
                        ])
                        ->execute();
                } else {
                    // use update
                    \DB::update(static::$_table_name)
                        ->value('field_value', $field_value)
                        ->where('account_id', '=', $account_id)
                        ->where('field_name', $field_name)
                        ->execute();
                }

                unset($result);
            }
            unset($field_name, $field_value);
        }

        return true;
    }// updateAccountFields


}
