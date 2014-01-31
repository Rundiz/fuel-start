<?php
/** 
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

namespace Blog;

class Controller_Blog extends \Controller_BaseController 
{


	public function __construct() 
	{
		parent::__construct();
		
		// load language
		\Lang::load('blog::blog');
	}// __construct
	
	
	public function action_index() 
	{
		if (!\DBUtil::table_exists('blog') && !\DBUtil::table_exists('blog_comment')) {
			\Response::redirect('blog/installrequired');
		}
		
		// list posts -----------------------------------------------------------------------------------------------------
		$option['limit'] = \Model_Config::getval('content_items_perpage');
		$option['offset'] = (trim(\Input::get('page')) != null ? ((int)\Input::get('page')-1)*$option['limit'] : 0);
		
		$list_items = \Blog\Model_Blog::listItems($option);
		
		// pagination config
		$config['pagination_url'] = \Uri::main() . \Uri::getCurrentQuerystrings(true, true, false);
		$config['total_items'] = $list_items['total'];
		$config['per_page'] = $option['limit'];
		$config['uri_segment'] = 'page';
		$config['num_links'] = 3;
		$config['show_first'] = true;
		$config['show_last'] = true;
		$config['first-inactive'] = "\n\t\t<li class=\"disabled\">{link}</li>";
		$config['first-inactive-link'] = '<a href="#">{page}</a>';
		$config['first-marker'] = '&laquo;';
		$config['last-inactive'] = "\n\t\t<li class=\"disabled\">{link}</li>";
		$config['last-inactive-link'] = '<a href="#">{page}</a>';
		$config['last-marker'] = '&raquo;';
		$config['previous-marker'] = '&lsaquo;';
		$config['next-marker'] = '&rsaquo;';
		$pagination = \Pagination::forge('viewlogins_pagination', $config);
		
		$output['list_items'] = $list_items;
		$output['pagination'] = $pagination;
		
		unset($config, $list_accounts, $option, $pagination);
		
		// <head> output ----------------------------------------------------------------------------------------------
		$output['page_title'] = $this->generateTitle(\Lang::get('blog'));
		// <head> output ----------------------------------------------------------------------------------------------
		
		return $this->generatePage('blog_v', $output, false);
	}// action_index
	
	
	public function action_installrequired() 
	{
		echo '<p>Installation is required.</p>';
	}// action_installrequired
	
	
	public function action_post($post_id = '') 
	{
		if (!is_numeric($post_id)) {\Response::redirect('blog');}
		
		// get current blog post data
		$row = \Blog\Model_Blog::find($post_id);
		
		if ($row == null) {
			\Response::redirect('blog', 301);
		}
		
		foreach ($row as $key => $value) {
			$output[$key] = $value;
		}
		
		// <head> output ----------------------------------------------------------------------------------------------
		$output['page_title'] = $this->generateTitle($row->post_name);
		// <head> output ----------------------------------------------------------------------------------------------
		
		return $this->generatePage('blog_post_v', $output, false);
	}//
	
	
}

