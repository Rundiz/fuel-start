<?php
/**
 * Blog module admin file
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

namespace Blog;

class BlogAdmin
{


	public function __construct() 
	{
		// load language
		\Lang::load('blog::blog');
	}// __construct
	
	
	/**
	 * define permission for this module.
	 * @return array
	 */
	public function _define_permission() 
	{
		return array(
			'blog_perm' => array('blog_manage_perm', 'blog_write_perm'), 
			'bloc_comment_perm' => array('blog_manage_comment_perm')
		);
	}// _define_permission
	
	
	/**
	 * automatic generate admin navbar menu
	 * 
	 * @return string
	 */
	public function admin_navbar() 
	{
		return '<li><a href="#" onclick="return false;">' . \Lang::get('blog') . '</a>
			<ul>
				<li>' . \Extension\Html::anchor('blog/admin', \Lang::get('blog_manage')) . '</li>
				<li>' . \Extension\Html::anchor('blog/admin/comment', \Lang::get('blog_manage_comment')) . '</li>
			</ul>
		</li>';
	}// admin_navbar


}

