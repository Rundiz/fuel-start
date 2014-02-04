<?php
/** 
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */

namespace Blog;

class Controller_Admin_Index extends \Controller_AdminController 
{


	public function __construct() 
	{
		parent::__construct();
		
		// load language
		\Lang::load('blog::blog');
	}// __construct
	
	
	public function action_add() 
	{
		// check permission
		if (\Model_AccountLevelPermission::checkAdminPermission('blog_perm', 'blog_write_perm') == false) {
			\Session::set_flash(
				'form_status',
				array(
					'form_status' => 'error',
					'form_status_message' => \Lang::get('admin.admin_permission_denied', array('page' => \Uri::string()))
				)
			);
			\Response::redirect(\Uri::create('blog/admin'));
		}
		
		// read flash message for display errors.
		$form_status = \Session::get_flash('form_status');
		if (isset($form_status['form_status']) && isset($form_status['form_status_message'])) {
			$output['form_status'] = $form_status['form_status'];
			$output['form_status_message'] = $form_status['form_status_message'];
		}
		unset($form_status);
		
		// if form submitted
		if (\Input::method() == 'POST') {
			// store data for save
			$data['post_name'] = \Security::htmlentities(trim(\Input::post('post_name')));
			$data['post_body'] = trim(\Input::post('post_body'));
			
			// validate form.
			$validate = \Validation::forge();
			$validate->add('post_name', \Lang::get('blog_post_name'), array(), array('required'));
			$validate->add('post_body', \Lang::get('blog_post_content'), array(), array('required'));
			
			if (!\Extension\NoCsrf::check()) {
				// validate token failed
				$output['form_status'] = 'error';
				$output['form_status_message'] = \Lang::get('fslang.fslang_invalid_csrf_token');
			} elseif (!$validate->run()) {
				// validate failed
				$output['form_status'] = 'error';
				$output['form_status_message'] = $validate->show_errors();
			} else {
				$result = \Blog\Model_Blog::addPost($data);
				
				if ($result === true) {
					if (\Session::get_flash('form_status', null, false) == null) {
						\Session::set_flash(
							'form_status',
							array(
								'form_status' => 'success',
								'form_status_message' => \Lang::get('admin.admin_saved')
							)
						);
					}
					
					\Response::redirect(\Uri::create('blog/admin'));
				} else {
					$output['form_status'] = 'error';
					$output['form_status_message'] = $result;
				}
			}
			
			// re-populate form
			$output['post_name'] = $data['post_name'];
			$output['post_body'] = $data['post_body'];
		}
		
		// <head> output ----------------------------------------------------------------------------------------------
		$output['page_title'] = $this->generateTitle(\Lang::get('blog'));
		// <head> output ----------------------------------------------------------------------------------------------
		
		return $this->generatePage('admin/blog_form_v', $output, false);
	}// action_add
	
	
	public function action_edit($post_id = '') 
	{
		// check permission
		if (\Model_AccountLevelPermission::checkAdminPermission('blog_perm', 'blog_write_perm') == false) {
			\Session::set_flash(
				'form_status',
				array(
					'form_status' => 'error',
					'form_status_message' => \Lang::get('admin.admin_permission_denied', array('page' => \Uri::string()))
				)
			);
			\Response::redirect(\Uri::create('blog/admin'));
		}
		
		// read flash message for display errors.
		$form_status = \Session::get_flash('form_status');
		if (isset($form_status['form_status']) && isset($form_status['form_status_message'])) {
			$output['form_status'] = $form_status['form_status'];
			$output['form_status_message'] = $form_status['form_status_message'];
		}
		unset($form_status);
		
		// get current post data for form
		$row = \Blog\Model_Blog::find($post_id);
		
		if ($row == null) {
			\Response::redirect(\Uri::create('blog/admin'));
		}
		
		// loop set form field.
		foreach ($row as $key => $value) {
			$output[$key] = $value;
		}
		
		// if form submitted --------------------------------------------------------------------------------------------
		if (\Input::method() == 'POST') {
			// store data for save
			$data['post_id'] = $post_id;
			$data['post_name'] = \Security::htmlentities(trim(\Input::post('post_name')));
			$data['post_body'] = trim(\Input::post('post_body'));
			
			// validate form.
			$validate = \Validation::forge();
			$validate->add('post_name', \Lang::get('blog_post_name'), array(), array('required'));
			$validate->add('post_body', \Lang::get('blog_post_content'), array(), array('required'));
			
			if (!\Extension\NoCsrf::check()) {
				// validate token failed
				$output['form_status'] = 'error';
				$output['form_status_message'] = \Lang::get('fslang.fslang_invalid_csrf_token');
			} elseif (!$validate->run()) {
				// validate failed
				$output['form_status'] = 'error';
				$output['form_status_message'] = $validate->show_errors();
			} else {
				$result = \Blog\Model_Blog::editPost($data);
				
				if ($result === true) {
					if (\Session::get_flash('form_status', null, false) == null) {
						\Session::set_flash(
							'form_status',
							array(
								'form_status' => 'success',
								'form_status_message' => \Lang::get('admin.admin_saved')
							)
						);
					}
					
					\Response::redirect(\Uri::create('blog/admin'));
				} else {
					$output['form_status'] = 'error';
					$output['form_status_message'] = $result;
				}
			}
			
			// re-populate form
			$output['post_name'] = $data['post_name'];
			$output['post_body'] = $data['post_body'];
		}
		
		unset($row);
		
		// <head> output ----------------------------------------------------------------------------------------------
		$output['page_title'] = $this->generateTitle(\Lang::get('blog'));
		// <head> output ----------------------------------------------------------------------------------------------
		
		return $this->generatePage('admin/blog_form_v', $output, false);
	}// action_edit
	
	
	public function action_index() 
	{
		// check permission
		if (\Model_AccountLevelPermission::checkAdminPermission('blog_perm', 'blog_manage_perm') == false) {
			\Session::set_flash(
				'form_status',
				array(
					'form_status' => 'error',
					'form_status_message' => \Lang::get('admin.admin_permission_denied', array('page' => \Uri::string()))
				)
			);
			\Response::redirect(\Uri::create('admin'));
		}
		
		// check table exists and link to install page.
		if (!\DBUtil::table_exists('blog') && !\DBUtil::table_exists('blog_comment')) {
			echo \Extension\Html::anchor('blog/admin/setup', 'Installation required');
			exit;
		}
		
		// read flash message for display errors.
		$form_status = \Session::get_flash('form_status');
		if (isset($form_status['form_status']) && isset($form_status['form_status_message'])) {
			$output['form_status'] = $form_status['form_status'];
			$output['form_status_message'] = $form_status['form_status_message'];
		}
		unset($form_status);
		
		// list posts -----------------------------------------------------------------------------------------------------
		$option['limit'] = \Model_Config::getval('content_admin_items_perpage');
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
		
		return $this->generatePage('admin/blog_v', $output, false);
	}// action_index
	
	
	public function action_multiple() 
	{
		$ids = \Input::post('id');
		$act = trim(\Input::post('act'));
		
		if (\Extension\NoCsrf::check()) {
			if ($act == 'del') {
				// check permission.
				if (\Model_AccountLevelPermission::checkAdminPermission('blog_perm', 'blog_manage_perm') == false) {\Response::redirect(\Uri::create('admin'));}
			
				if (is_array($ids)) {
					foreach ($ids as $id) {
						\Blog\Model_Blog::find($id)->delete();
					}
				}
			}
		}
		
		// go back
		if (\Input::referrer() != null && \Input::referrer() != \Uri::main()) {
			\Response::redirect(\Input::referrer());
		} else {
			\Response::redirect('blog/admin');
		}
	}// action_multiple
	
	
}

