<?php
/** 
 * Blog ORM
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

namespace Blog;

class Model_Blog extends \Orm\Model 
{
	
	
	protected static $_table_name = 'blog';
	protected static $_primary_key = array('post_id');
	
	// relations
	protected static $_has_many = array(
		'blog_comment' => array(
			'model_to' => '\Blog\Model_BlogComment',
			'key_from' => 'post_id',
			'key_to' => 'post_id',
			'cascade_delete' => true,
		)
	);
	
	
	/**
	 * create new post
	 * 
	 * @param array $data
	 * @return boolean
	 */
	public static function addPost($data = array()) 
	{
		// set date time.
		$data['post_date'] = time();
		
		$entry = self::forge($data);
		$entry->save();
		
		return true;
	}// addPost
	
	
	/**
	 * edit post
	 * 
	 * @param array $data
	 * @return boolean
	 */
	public static function editPost($data = array()) 
	{
		if (!is_numeric($data['post_id'])) {
			return false;
		}
		
		$post_id = $data['post_id'];
		unset($data['post_id']);
		
		$entry = self::find($post_id);
		$entry->set($data);
		$entry->save();
		
		return true;
	}// editPost
	
	
	/**
	 * list items
	 * 
	 * @param array $option
	 * @return mixed
	 */
	public static function listItems($option = array()) 
	{
		$query = self::query();
		
		$output['total'] = $query->count();
		
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
		$output['items'] = $query->order_by('post_id', 'DESC')->offset($option['offset'])->limit($option['limit'])->get();
		
		unset($orders, $query, $sort);
		
		return $output;
	}// listItems
	
	
}

