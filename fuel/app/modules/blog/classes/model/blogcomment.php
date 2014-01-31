<?php
/** 
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

namespace Blog;

class Model_BlogComment extends \Orm\Model 
{
	
	
	protected static $_table_name = 'blog_comment';
	protected static $_primary_key = array('comment_id');
	
	// relations
	protected static $_belongs_to = array(
		'blog' => array(
			'model_to' => '\Blog\Model_Blog',
			'key_from' => 'post_id',
			'key_to' => 'post_id',
		)
	);
	
	
}

