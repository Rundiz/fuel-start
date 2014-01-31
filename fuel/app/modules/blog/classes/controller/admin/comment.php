<?php
/** 
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

namespace Blog;

class Controller_Admin_Comment extends \Controller_AdminController 
{
	
	
	public function action_index() 
	{
		echo '<p>This controller is just an example how you check admin permission with different page name or different controller.</p><p>Please view source code.</p>';
		
		echo '<blockquote>';
		// check permission
		if (\Model_AccountLevelPermission::checkAdminPermission('bloc_comment_perm', 'blog_manage_comment_perm') == false) {
			echo '<p>You have no permission to manage this page and action.</p>';
		} else {
			echo '<p>You have permission to manage this page and action.</p>';
		}
		echo '</blockquote>';
	}// action_index
	
	
}

