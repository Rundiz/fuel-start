<?php

namespace Fs;

class Generate
{


    public static $create_folders = array();
    public static $create_files = array();


    /**
     * create folder or write files that store in properties array.
     * 
     * @return boolean
     * @throws Exception
     */
    private static function build()
    {
        foreach (static::$create_folders as $folder) {
            is_dir($folder) or mkdir($folder, 0755, true);
            \Cli::write('  Creating folder: ' . $folder);
        }

        $result = true;

        foreach (static::$create_files as $file) {
            \Cli::write("  Creating {$file['type']}: {$file['path']}", 'green');

            if (!$handle = @fopen($file['path'], 'w+')) {
                throw new Exception('Cannot open file: '. $file['path']);
            }

            $result = @fwrite($handle, $file['contents']);

            // Write $somecontent to our opened file.
            if ($result === false) {
                throw new Exception('Cannot write to file: '. $file['path']);
            }

            @fclose($handle);

            @chmod($file['path'], 0666);
        }

        return $result;
    }// build


    /**
     * get file path and content and then put it into create_files array to make it ready to build.
     * 
     * @param string $filepath
     * @param string $contents
     * @param string $type
     * @throws Exception
     * @return boolean
     */
    public static function create($filepath, $contents, $type = 'file')
    {
        $directory = dirname($filepath);
        is_dir($directory) or static::$create_folders[] = $directory;

        // Check if a file exists then work out how to react
        if (is_file($filepath)) {
            throw new Exception($filepath .' already exists.');
            exit;
        }

        static::$create_files[] = array(
            'path' => $filepath,
            'contents' => $contents,
            'type' => $type
        );
        
        return true;
    }// create


    private static function createAdminFile($module_path, $module_name)
    {
        $lcase_module_name = strtolower($module_name);
        
        // create [module name]admin.php file
        $file_path = $module_path.'classes'.DS.$lcase_module_name.'admin.php';
        $admin_content = <<< ADMIN_CONTENT
<?php

namespace {$module_name};

class {$module_name}Admin
{


    public function __construct() 
    {
        // load language
        \Lang::load('{$lcase_module_name}::{$lcase_module_name}');
    }// __construct


    public function _define_permission()
    {
        return array('{$lcase_module_name}_perm' => array('{$lcase_module_name}_viewall_perm', '{$lcase_module_name}_add_perm', '{$lcase_module_name}_edit_perm', '{$lcase_module_name}_delete_perm'));
    }// _define_permission


    public function admin_navbar()
    {
        if (\Model_AccountLevelPermission::checkAdminPermission('{$lcase_module_name}_perm', '{$lcase_module_name}_viewall_perm')) {
            \$output = '<li>' . \Html::anchor('{$lcase_module_name}/admin', '{$module_name}') . "\\n";
            \$output .= '</li>';
            
            return \$output;
        }
    }// admin_navbar


}
ADMIN_CONTENT;
        // prepare to write file
        static::create($file_path, $admin_content, 'file');
        unset($file_path, $admin_content);
        
        unset($lcase_module_name);
        return true;
    }// createAdminFile


    /**
     * create config
     * 
     * @param string $module_path
     * @param string $module_name
     * @return boolean
     */
    private static function createConfig($module_path, $module_name)
    {
        $lcase_module_name = strtolower($module_name);
        
        // create route config
        $file_path = $module_path.'config'.DS.'routes.php';
        $route_content = <<< ROUTE_CONTENT
<?php
return array(
    '{$lcase_module_name}' => '{$lcase_module_name}/index',
    '{$lcase_module_name}/admin' => '{$lcase_module_name}/admin/index',
);
ROUTE_CONTENT;

        // prepare to write file
        static::create($file_path, $route_content, 'config');
        unset($file_path, $route_content);
        
        unset($lcase_module_name);
        return true;
    }// createConfig


    /**
     * create controllers
     * 
     * @param string $module_path
     * @param string $module_name
     * @return boolean
     */
    private static function createControllers($module_path, $module_name)
    {
        $prefix = \Config::get('controller_prefix', 'Controller_');
        $lcase_module_name = strtolower($module_name);
        
        // frontend controller -----------------------------------------------------------------------------------
        $file_path = $module_path.'classes'.DS.'controller'.DS.'index.php';
        $frontend_controller = <<< FRONTEND_CONTROLLER
<?php

namespace {$module_name};

class {$prefix}Index extends \Controller_BaseController
{


    public function __construct()
    {
        parent::__construct();

        // load languages
        \Lang::load('{$lcase_module_name}::{$lcase_module_name}');
    }// __construct


    public function action_index()
    {
        // <head> output -------------------------------------------
        \$output['page_title'] = \$this->generateTitle('{$module_name}');
        // <head> output -------------------------------------------

        return \$this->generatePage('front/templates/index/index_v', \$output, false);
    }// action_index


}
FRONTEND_CONTROLLER;
        // prepare to write file
        static::create($file_path, $frontend_controller, 'controller');
        unset($file_path, $frontend_controller);
        
        // admin controller ---------------------------------------------------------------------------------------
        $file_path = $module_path.'classes'.DS.'controller'.DS.'admin'.DS.'index.php';
        $admin_controller = <<<ADMIN_CONTROLLER
<?php

namespace {$module_name};

class {$prefix}Admin_Index extends \Controller_AdminController
{


    public function __construct()
    {
        parent::__construct();

        // load languages
        \Lang::load('{$lcase_module_name}::{$lcase_module_name}');
    }// __construct


    public function action_add()
    {
        // set redirect url
        \$redirect = \$this->getAndSetSubmitRedirection();

        // check permission
        if (\Model_AccountLevelPermission::checkAdminPermission('{$lcase_module_name}_perm', '{$lcase_module_name}_add_perm') == false) {
            \Session::set_flash(
                'form_status',
                array(
                    'form_status' => 'error',
                    'form_status_message' => \Lang::get('admin_permission_denied', array('page' => \Uri::string()))
                )
            );
            \Response::redirect(\$redirect);
        }

        // read flash message for display errors.
        \$form_status = \Session::get_flash('form_status');
        if (isset(\$form_status['form_status']) && isset(\$form_status['form_status_message'])) {
            \$output['form_status'] = \$form_status['form_status'];
            \$output['form_status_message'] = \$form_status['form_status_message'];
        }
        unset(\$form_status);

        // your code here

        // if form submitted
        if (\Input::method() == 'POST') {

        }// endif form submitted

        // <head> output -------------------------------------------
        \$output['page_title'] = \$this->generateTitle('{$module_name}');
        // <head> output -------------------------------------------

        // breadcrumb -------------------------------------------------------------------------------------------------
        \$page_breadcrumb = [];
        \$page_breadcrumb[0] = ['name' => \Lang::get('admin_admin_home'), 'url' => \Uri::create('admin')];
        \$page_breadcrumb[1] = ['name' => '{$module_name}', 'url' => \Uri::create('admin/index')];
        \$page_breadcrumb[2] = ['name' => 'Add', 'url' => \Uri::main()];
        \$output['page_breadcrumb'] = \$page_breadcrumb;
        unset(\$page_breadcrumb);
        // breadcrumb -------------------------------------------------------------------------------------------------

        return \$this->generatePage('admin/templates/index/form_v', \$output, false);
    }// action_add


    public function action_edit()
    {
        // set redirect url
        \$redirect = \$this->getAndSetSubmitRedirection();

        // check permission
        if (\Model_AccountLevelPermission::checkAdminPermission('{$lcase_module_name}_perm', '{$lcase_module_name}_edit_perm') == false) {
            \Session::set_flash(
                'form_status',
                array(
                    'form_status' => 'error',
                    'form_status_message' => \Lang::get('admin_permission_denied', array('page' => \Uri::string()))
                )
            );
            \Response::redirect(\$redirect);
        }

        // read flash message for display errors.
        \$form_status = \Session::get_flash('form_status');
        if (isset(\$form_status['form_status']) && isset(\$form_status['form_status_message'])) {
            \$output['form_status'] = \$form_status['form_status'];
            \$output['form_status_message'] = \$form_status['form_status_message'];
        }
        unset(\$form_status);

        // your code here

        // if form submitted
        if (\Input::method() == 'POST') {

        }// endif form submitted

        // <head> output -------------------------------------------
        \$output['page_title'] = \$this->generateTitle('{$module_name}');
        // <head> output -------------------------------------------

        // breadcrumb -------------------------------------------------------------------------------------------------
        \$page_breadcrumb = [];
        \$page_breadcrumb[0] = ['name' => \Lang::get('admin_admin_home'), 'url' => \Uri::create('admin')];
        \$page_breadcrumb[1] = ['name' => '{$module_name}', 'url' => \Uri::create('admin/index')];
        \$page_breadcrumb[2] = ['name' => 'Edit', 'url' => \Uri::main()];
        \$output['page_breadcrumb'] = \$page_breadcrumb;
        unset(\$page_breadcrumb);
        // breadcrumb -------------------------------------------------------------------------------------------------

        return \$this->generatePage('admin/templates/index/form_v', \$output, false);
    }// action_edit


    public function action_index()
    {
        // clear redirect referrer
        \Session::delete('submitted_redirect');

        // check permission
        if (\Model_AccountLevelPermission::checkAdminPermission('{$lcase_module_name}_perm', '{$lcase_module_name}_viewall_perm') == false) {
            \Session::set_flash(
                'form_status',
                array(
                    'form_status' => 'error',
                    'form_status_message' => \Lang::get('admin_permission_denied', array('page' => \Uri::string()))
                )
            );
            \Response::redirect(\Uri::create('admin'));
        }

        // read flash message for display errors.
        \$form_status = \Session::get_flash('form_status');
        if (isset(\$form_status['form_status']) && isset(\$form_status['form_status_message'])) {
            \$output['form_status'] = \$form_status['form_status'];
            \$output['form_status_message'] = \$form_status['form_status_message'];
        }
        unset(\$form_status);

        // your code here

        // search
        \$output['q'] = trim(\Input::get('q'));

        // <head> output -------------------------------------------
        \$output['page_title'] = \$this->generateTitle('{$module_name}');
        // <head> output -------------------------------------------

        // breadcrumb -------------------------------------------------------------------------------------------------
        \$page_breadcrumb = [];
        \$page_breadcrumb[0] = ['name' => \Lang::get('admin_admin_home'), 'url' => \Uri::create('admin')];
        \$page_breadcrumb[1] = ['name' => '{$module_name}', 'url' => \Uri::create('admin/index')];
        \$output['page_breadcrumb'] = \$page_breadcrumb;
        unset(\$page_breadcrumb);
        // breadcrumb -------------------------------------------------------------------------------------------------

        return \$this->generatePage('admin/templates/index/index_v', \$output, false);
    }// action_index


    public function action_multiple()
    {
        \$ids = \Input::post('id');
        \$act = trim(\Input::post('act'));
        \$redirect = \$this->getAndSetSubmitRedirection();

        if (\Extension\NoCsrf::check()) {
            if (\$act == 'delete') {
                // check permission.
                if (\Model_AccountLevelPermission::checkAdminPermission('{$lcase_module_name}_perm', '{$lcase_module_name}_delete_perm') == false) {\Response::redirect(\$redirect);}

                if (is_array(\$ids)) {
                    foreach (\$ids as \$id) {
                        // your code here

                    }
                }
            }
        }

        // go back
        \Response::redirect(\$redirect);
    }// action_multiple


    private function getAndSetSubmitRedirection()
    {
        \$session = \Session::forge();
        
        if (\$session->get('submitted_redirect') == null) {
            if (\Input::referrer() != null && \Input::referrer() != \Uri::main()) {
                \$session->set('submitted_redirect', \Input::referrer());
                return \Input::referrer();
            } else {
                \$redirect_uri = '{$lcase_module_name}/admin';
                \$session->set('submitted_redirect', \$redirect_uri);
                return \$redirect_uri;
            }
        } else {
            return \$session->get('submitted_redirect');
        }
    }// getAndSetRedirection


}
ADMIN_CONTROLLER;
        // prepare to write file
        static::create($file_path, $admin_controller, 'controller');
        unset($file_path, $admin_controller);

        unset($lcase_module_name, $prefix);
        return true;
    }// createControllers


    /**
     * create language files
     * 
     * @param string $module_path
     * @param string $module_name
     * @return boolean
     */
    private static function createLangFiles($module_path, $module_name)
    {
        $lcase_module_name = strtolower($module_name);
        $languages = \Config::get('locales');
        
        if (is_array($languages) && !empty($languages)) {
            foreach ($languages as $key => $item) {
                $file_path = $module_path.'lang'.DS.$key.DS.$lcase_module_name.'.php';
                $lang_content = <<<LANG_CONTENT
<?php
return array(
    // your translations here
    '{$lcase_module_name}_viewall' => 'View all',

    // permissions
    '{$lcase_module_name}_add_perm' => 'Add',
    '{$lcase_module_name}_delete_perm' => 'Delete',
    '{$lcase_module_name}_edit_perm' => 'Edit',
    '{$lcase_module_name}_perm' => '{$module_name}',
    '{$lcase_module_name}_viewall_perm' => 'View all',
);
LANG_CONTENT;
                // prepare to write file
                static::create($file_path, $lang_content, 'lang file');
                unset($file_path, $lang_content);
            }
            unset($item, $key);
        }
        
        unset($lcase_module_name, $languages);
        return true;
    }// createLangFiles


    /**
     * create <module name> with _module.php file
     * 
     * @param string $module_path
     * @param string $module_name
     * @return boolean
     */
    private static function createModuleFile($module_path, $module_name)
    {
        $file_path = $module_path.strtolower($module_name).'_module.php';
        
        $module_file_content = <<< MODULE_FILE
<?php
/**
 * Module Name: {$module_name}
 * Module URL:
 * Version:
 * Description:
 * Author: 
 * Author URL: 
 */
MODULE_FILE;
 
        // prepare to write file
        static::create($file_path, $module_file_content, 'file');
        
        return true;
    }// createModuleFile


    /**
     * create views
     * 
     * @param string $module_path
     * @param string $module_name
     * @return boolean
     */
    private static function createViews($module_path, $module_name)
    {
        $lcase_module_name = strtolower($module_name);
        
        // frontend index views -----------------------------------------------------------------------------------------------------------------------
        $file_path = $module_path.'views'.DS.'front'.DS.'templates'.DS.'index'.DS.'index_v.php';
        $frontend_index = <<< FRONTEND_INDEX
<h1>Hello</h1>
<p>World.</p>
FRONTEND_INDEX;
        // prepare to write file
        static::create($file_path, $frontend_index, 'views');
        unset($file_path, $frontend_index);
        
        // admin index views ---------------------------------------------------------------------------------------------------------------------------
        $file_path = $module_path.'views'.DS.'admin'.DS.'templates'.DS.'index'.DS.'index_v.php';
        $admin_index = <<< ADMIN_INDEX
<h1>{$module_name}</h1>

<div class="row cmds">
    <div class="col-sm-6">
        <ul class="actions-inline">
            <li><?php printf(\Lang::get('admin_total', array('total' => (isset(\$list_item['total']) ? \$list_item['total'] : '0')))); ?></li>
            <?php if (\Model_AccountLevelPermission::checkAdminPermission('{$lcase_module_name}_perm', '{$lcase_module_name}_add_perm')) { ?><li><?php echo \Html::anchor('{$lcase_module_name}/admin/index/add', '<i class="glyphicon glyphicon-plus"></i> ' . __('admin_add'), array('class' => 'btn btn-default')); ?></li><?php } ?> 
        </ul>
    </div>
    <div class="col-sm-6">
        <form method="get" class="form-inline pull-right form-search-items">
            <?php 
            \$querystring = \Input::server('QUERY_STRING');
            \$querystring_exp = explode('&', \$querystring);
            foreach (\$querystring_exp as \$inputs) {
                if (mb_strpos(\$inputs, '=') !== false) {
                    list(\$input_name, \$input_val) = explode('=', \$inputs);
                    if (\$input_name != 'q' && \$input_name != 'page') {
                        echo \Form::hidden(urldecode(\$input_name), urldecode(\$input_val)) . "\\n";
                    }
                }
            }
            unset(\$input_name, \$input_val, \$inputs, \$querystring, \$querystring_exp);
            ?> 
            <div class="form-group">
                <?php echo \Form::input('q', (isset(\$q) ? \$q : ''), array('class' => 'form-control search-input', 'maxlength' => '255')); ?> 
            </div>
            <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search"></span> <?php echo __('admin_search'); ?></button>
            <?php echo \Html::anchor('{$lcase_module_name}/admin', __('{$lcase_module_name}_viewall'), array('class' => 'btn btn-default')); ?> 
        </form>
    </div>
</div>

<?php echo \Form::open(array('action' => '{$lcase_module_name}/admin/index/multiple', 'class' => 'form-horizontal', 'role' => 'form', 'onsubmit' => 'return verifySelectedAction();')); ?> 
    <div class="form-status-placeholder">
        <?php if (isset(\$form_status) && isset(\$form_status_message)) { ?> 
        <div class="alert alert-<?php echo str_replace('error', 'danger', \$form_status); ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo \$form_status_message; ?></div>
        <?php } ?> 
    </div>

    <?php echo \Extension\NoCsrf::generate(); ?> 

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th class="check-column"><input type="checkbox" name="id_all" value="" onclick="checkAll(this.form,'id[]',this.checked)" /></th>
                    <th>column header</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="check-column"><?php echo \Extension\Form::checkbox('id[]', ''); ?></td>
                    <td>write your code here.</td>
                    <td>
                        <?php
                        if (\Model_AccountLevelPermission::checkAdminPermission('{$lcase_module_name}_perm', '{$lcase_module_name}_edit_perm')) {
                            echo \Html::anchor('{$lcase_module_name}/admin/index/edit/id', '<span class="glyphicon glyphicon-pencil"></span> ' . __('admin_edit'), array('class' => 'btn btn-default btn-xs'));
                        }
                        ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div><!--.table-responsive-->

    <div class="row cmds">
        <div class="col-sm-6">
            <select name="act" class="form-control select-inline chosen-select select-action">
                <option value="" selected="selected"></option>
                <?php if (\Model_AccountLevelPermission::checkAdminPermission('{$lcase_module_name}_perm', '{$lcase_module_name}_delete_perm')) { ?><option value="delete"><?php echo __('admin_delete'); ?></option><?php } ?> 
            </select>
            <button type="submit" class="bb-button btn btn-warning"><?php echo __('admin_submit'); ?></button>
            <?php echo \Extension\Html::anchor('admin', __('admin_cancel'), array('class' => 'btn btn-default')); ?> 
        </div>
        <div class="col-sm-6">
            good for pagination to be here.
        </div>
    </div>
<?php echo \Form::close(); ?> 


<script>
    function verifySelectedAction() {
        if ($('.select-action').val() === 'delete') {
            confirm_msg = confirm('<?php echo __('admin_are_you_sure_to_delete_selected_items'); ?>');

            if (confirm_msg === false) {
                // admin cancel confirm dialog. stop form from submitting.
                return false;
            }
        }
    }// verifySelectedAction
</script>
ADMIN_INDEX;
        // prepare to write file
        static::create($file_path, $admin_index, 'views');
        unset($file_path, $admin_index);

        // admin form views ----------------------------------------------------------------------------------------------------------------------------
        $file_path = $module_path.'views'.DS.'admin'.DS.'templates'.DS.'index'.DS.'form_v.php';
        $admin_form = <<< ADMIN_FORM
<h1><?php echo (\Uri::segment(4) == 'add' ? __('admin_add') : __('admin_edit')); ?></h1>
ADMIN_FORM;
        // prepare to write file
        static::create($file_path, $admin_form, 'views');
        unset($file_path, $admin_form);

        unset($lcase_module_name);
        return true;
    }// createViews


    /**
     * generate the module
     * 
     * @param string $module_name the module name is StudlyCaps as PSR-1 specified.
     */
    public static function module($args)
    {
        $module_name = array_shift($args);
        $module_name = str_replace(' ', '', $module_name);
        
        if ($path = \Module::exists(strtolower($module_name))) {
            throw new Exception('A module named '.$module_name.' already exists at '.$path);
            exit;
        }
        
        $module_paths = \Config::get('module_paths');
        $base = reset($module_paths);
        
        if (count($module_paths) > 1) {
            \Cli::write('Your app has multiple module paths defined. Please choose the appropriate path from the list below', 'yellow', 'blue');

            $options = array();
            foreach ($module_paths as $key => $path)
            {
                $idx = $key+1;
                \Cli::write('['.$idx.'] '.$path);
                $options[] = $idx;
            }

            $path_idx = \Cli::prompt('Please choose the desired module path', $options);

            $base = $module_paths[$path_idx - 1];
        }
        
        $module_path = $base.strtolower($module_name).DS;
        
        static::$create_folders[] = $module_path;
        static::$create_folders[] = $module_path.'classes/controller/admin';
        static::$create_folders[] = $module_path.'classes/model';
        static::$create_folders[] = $module_path.'config';
        $languages = \Config::get('locales');
        if (is_array($languages) && !empty($languages)) {
            foreach ($languages as $key => $item) {
                static::$create_folders[] = $module_path.'lang/'.$key;
            }
        } else {
            static::$create_folders[] = $module_path.'lang/en';
        }
        unset($item, $key, $languages);
        static::$create_folders[] = $module_path.'views/admin/templates/index';
        static::$create_folders[] = $module_path.'views/front/templates/index';
        
        // create _module.php file
        static::createModuleFile($module_path, $module_name);
        // create [module name]admin.php file for define permissions
        static::createAdminFile($module_path, $module_name);
        // create config file
        static::createConfig($module_path, $module_name);
        // create language files
        static::createLangFiles($module_path, $module_name);
        // create controllers
        static::createControllers($module_path, $module_name);
        // create views
        static::createViews($module_path, $module_name);

        $result = static::build();
        if (isset($result) && $result === true) {
            \Cli::write('Completed create folders.');
        }
        unset($result);
    }// module


}