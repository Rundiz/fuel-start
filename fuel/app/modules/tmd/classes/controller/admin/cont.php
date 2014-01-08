<?php
/** 
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

namespace Tmd;

class Controller_Admin_Cont extends \Controller 
{
	
	
	public function action_index() 
	{
		echo 'uri::string = ' . \Uri::string() . '<br><br>';
		echo "\n\n";
		
		echo __FILE__ . '<br><br>';
		echo "\n\n";
		
		echo '\\' . __NAMESPACE__ . '<br>';
		echo "\n";
		echo __CLASS__ . '::' . __FUNCTION__ . '<br><br>';
		echo "\n\n";
		
		echo \Html::anchor('', 'root') . '<br>';
		echo "\n";
		echo \Html::anchor('admin', 'root admin') . '<br>';
		echo "\n";
		echo \Html::anchor('tmd', 'test module') . '<br>';
		echo "\n";
		echo \Html::anchor('tmd/admin', 'test module admin') . '<br>';
		echo "\n";
		echo \Html::anchor('tmd/admin/cont', 'test module admin controller') . '<br>';
		echo "\n";
	}// action_index
	
	
	public function action_method() 
	{
		echo 'uri::string = ' . \Uri::string() . '<br><br>';
		echo "\n\n";
		
		echo __FILE__ . '<br><br>';
		echo "\n\n";
		
		echo '\\' . __NAMESPACE__ . '<br>';
		echo "\n";
		echo __CLASS__ . '::' . __FUNCTION__ . '<br><br>';
		echo "\n\n";
		
		echo \Html::anchor('', 'root') . '<br>';
		echo "\n";
		echo \Html::anchor('admin', 'root admin') . '<br>';
		echo "\n";
		echo \Html::anchor('tmd', 'test module') . '<br>';
		echo "\n";
		echo \Html::anchor('tmd/admin', 'test module admin') . '<br>';
		echo "\n";
		echo \Html::anchor('tmd/admin/cont', 'test module admin controller') . '<br>';
		echo "\n";
	}// action_method
	
	
}

