<?php
/** 
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

namespace Tmd;

class TmdAdmin
{
	
	
	public function __construct()
	{	
		// load language
		\Lang::load('tmd::tmd');
	}
	
	
	public function _define_permission() 
	{
		return array(
			'tmd_perm' => array('tmd_act1_perm', 'tmd_act2_perm'),
			'tmd_page2_perm' => array('tmd_p2_act1_perm', 'tmd_p2_act2_perm'),
		);
	}// _define_permission
	
	
	public function admin_navbar() 
	{
		return '<li><a href="#" onclick="return false;">' . \Lang::get('tmd_test_module') . '</a>
			<ul>
				<li>' . \Extension\Html::anchor('tmd/admin', \Lang::get('tmd_manage')) . '</li>
			</ul>
		</li>';
	}// admin_navbar
	
	
}

