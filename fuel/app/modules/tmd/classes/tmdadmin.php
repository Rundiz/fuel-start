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
		\Lang::load('tmd::tmd', 'tmd');
	}
	
	
	public function _define_permission() 
	{
		return array(
			'tmd.tmd_perm' => array('tmd.tmd_act1_perm', 'tmd.tmd_act2_perm'),
			'tmd.tmd_page2_perm' => array('tmd.tmd_p2_act1_perm', 'tmd.tmd_p2_act2_perm'),
		);
	}// _define_permission
	
	
}

