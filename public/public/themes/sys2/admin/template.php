<?php 
/**
 * The main template file. This file is whole page template with page content placeholder for display controller's view.
 * This main template file is for admin section.
 * 
 * @author Vee Winch.
 * @package Fuel Start
 * @subpackage Theme Sys2
 */

include __DIR__ . DS . 'inc_html_head.php'; 
?> 


        <nav class="navbar navbar-inverse navbar-fixed-top navbar-top-page">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle sidebar-toggle">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <?php echo \Html::anchor('admin', \Model_Config::getval('site_name'), array('class' => 'navbar-brand')); ?> 
                </div><!--.navbar-header-->
                
                <ul class="nav navbar-nav navbar-right navbar-top">
                    <li class="dropdown">
                        <?php echo languageSwitchAdminBootstrapNavbar(true); ?> 
                    </li>
                    <li class="dropdown">
                        <a href="#" onclick="return false;" class="dropdown-toggle user-link" data-toggle="dropdown" aria-expanded="false">
                            <?php echo \Html::img($admin_navbar_avatar, ['alt' => 'user avatar', 'class' => 'img-user-avatar img-circle']); ?> 
                            <span class="user-display-name"><?php echo $cookie_admin['account_display_name']; ?></span>
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu" role="menu">
                            <?php if (checkAdminPermission('account_perm', 'account_edit_perm')) { ?><li><?php echo \Html::anchor('admin/account/edit', __('admin_edit_my_account')); ?></li><?php } ?> 
                            <li><?php echo \Html::anchor('admin/logout', __('admin_logout')); ?></li>
                        </ul>
                    </li>
                </ul><!--.navbar-nav-->
            </div><!--.container-fluid-->
        </nav><!--.navbar-->



        <div class="page-wrapper expanded-sidebar-menu">
            <div class="page-sidebar-back"></div>
            <div class="page-sidebar">
                <ul id="sidebar-menu" class="sidebar-menu-list-items sm sm-vertical sm-fssidebar sm-fssidebar-vertical">
                    <li>
                        <?php echo \Html::anchor('admin', '<i class="glyphicon glyphicon-dashboard"></i> <span class="fs-menu-name">'.__('admin_admin_home').'</span>'); ?> 
                        <?php if (isset($fs_list_sites) && $fs_list_sites != null) { ?> 
                        <ul>
                        <?php
                        $site_protocol = \Uri::protocol();
                        $site_path = \Uri::sitePath('admin');
                        ?> 
                        <?php foreach ($fs_list_sites as $fs_site) { ?> 
                            <li><?php echo \Html::anchor($site_protocol . $fs_site->site_domain . $site_path, $fs_site->site_name); ?></li>
                        <?php }// endforeach; ?> 
                        <?php unset($site_path, $site_protocol); ?> 
                        </ul>
                        <?php }// endif; ?> 
                    </li>
                    <li>
                        <?php echo \Html::anchor(\Uri::base(), '<i class="glyphicon glyphicon-home"></i> <span class="fs-menu-name">'.__('admin_visit_site').'</span>'); ?> 
                        <?php if (isset($fs_list_sites) && $fs_list_sites != null) { ?> 
                        <ul>
                        <?php
                        $site_protocol = \Uri::protocol();
                        $site_path = \Uri::sitePath();
                        ?> 
                        <?php foreach ($fs_list_sites as $fs_site) { ?> 
                            <li><?php echo \Html::anchor($site_protocol . $fs_site->site_domain . $site_path, $fs_site->site_name); ?></li>
                        <?php }// endforeach; ?> 
                        <?php unset($site_path, $site_protocol); ?> 
                        </ul>
                        <?php }// endif; ?> 
                    </li>
                    <?php 
                    // permission check for top parent menu.
                    if (checkAdminPermission('account_perm', 'account_viewusers_perm')
                        || checkAdminPermission('account_perm', 'account_add_perm')
                        || checkAdminPermission('account_perm', 'account_edit_perm')
                        || checkAdminPermission('accountlv_perm', 'accountlv_viewlevels_perm')
                        || checkAdminPermission('acperm_perm', 'acperm_manage_level_perm')
                        || checkAdminPermission('acperm_perm', 'acperm_manage_user_perm')
                    ) { ?>
                    <li>
                        <a href="#" onclick="return false;"><i class="fa fa-user"></i> <span class="fs-menu-name"><?php echo __('admin_users'); ?></span></a>
                        <ul>
                            <?php if (checkAdminPermission('account_perm', 'account_viewusers_perm')) { ?><li><?php echo \Html::anchor('admin/account', __('admin_nav_all_users')); ?></li><?php } ?> 
                            <?php if (checkAdminPermission('account_perm', 'account_add_perm')) { ?><li><?php echo \Html::anchor('admin/account/add', __('admin_add_user')); ?></li><?php } ?> 
                            <?php if (checkAdminPermission('account_perm', 'account_edit_perm')) { ?><li><?php echo \Html::anchor('admin/account/edit', __('admin_edit_my_account')); ?></li><?php } ?> 
                            <li class="divider" role="presentation"></li>
                            <?php if (checkAdminPermission('accountlv_perm', 'accountlv_viewlevels_perm')) { ?><li><?php echo \Html::anchor('admin/account-level', __('admin_roles')); ?></li><?php } ?> 
                            <?php if (checkAdminPermission('acperm_perm', 'acperm_manage_level_perm')) { ?><li><?php echo \Html::anchor('admin/account-level-permission', __('admin_permissions_for_roles')); ?></li><?php } ?> 
                            <?php if (checkAdminPermission('acperm_perm', 'acperm_manage_user_perm')) { ?><li><?php echo \Html::anchor('admin/account-permission', __('admin_permissions_for_users')); ?></li><?php } ?> 
                        </ul>
                    </li>
                    <?php }// endif; ?> 
                    <li>
                        <a href="#" onclick="return false;"><i class="fa fa-puzzle-piece"></i> <span class="fs-menu-name"><?php echo __('admin_components'); ?></span></a>
                        <?php echo \Library\Modules::forge()->listAdminNavbar(); ?> 
                    </li>
                    <?php if (checkAdminPermission('siteman_perm', 'siteman_viewsites_perm')) { ?> 
                    <li>
                        <a href="#" onclick="return false;"><i class="fa fa-cubes"></i> <span class="fs-menu-name"><?php echo __('admin_extensions'); ?></span></a>
                        <ul>
                            <?php if (checkAdminPermission('siteman_perm', 'siteman_viewsites_perm')) { ?><li><?php echo \Html::anchor('admin/siteman', __('admin_multisite_manager')); ?></li><?php } ?> 
                        </ul>
                    </li>
                    <?php }// endif; ?> 
                    <?php if (checkAdminPermission('config_global', 'config_global')) { ?><li><?php echo \Html::anchor('admin/config', '<i class="fa fa-sliders"></i> <span class="fs-menu-name">'.__('admin_global_configuration').'</span>'); ?></li><?php } ?> 
                    <?php if (checkAdminPermission('cacheman_perm', 'cacheman_clearcache_perm')) { ?> 
                    <li>
                        <a href="#" onclick="return false;"><i class="glyphicon glyphicon-wrench"></i> <span class="fs-menu-name"><?php echo __('admin_nav_tools'); ?></span></a>
                        <ul>
                            <?php if (checkAdminPermission('cacheman_perm', 'cacheman_clearcache_perm')) { ?><li><?php echo \Html::anchor('admin/cacheman', __('admin_nav_cacheman')); ?></li><?php } ?> 
                        </ul>
                    </li>
                    <?php }// endif; ?> 
                </ul><!--.sidebar-menu-list-items-->
                <div class="expand-collapse-sidebar-menu hidden-xs">
                    <a href="#" onclick="return false;" class="expand-collapse-sidebar-menu-btn">
                        <i class="expand-collapse-sidebar-menu-icon glyphicon glyphicon-chevron-left"></i>
                    </a>
                </div><!--.expand-collapse-sidebar-menu-->
            </div><!--.page-sidebar-->
            
            
            <div class="page-main-column">
                <div class="page-main-column-inner">
                    <?php if (isset($page_breadcrumb)) {echo generateBreadCrumb($page_breadcrumb);} ?> 
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-xs-12">
                                <?php 
                                if (isset($page_content)) {
                                    echo $page_content;
                                }
                                ?> 
                            </div><!--.col*-->
                        </div><!--.row-->
                    </div><!--.container-fluid-->
                </div><!--.page-main-column-inner-->
            </div><!--.page-main-column-->
            
            
            <footer class="page-footer">
                <?php echo __('fslang_credit'); // you can remove credit or change it. ?> 
            </footer>
            <div class="clearfix"></div>
        </div><!--.page-wrapper-->


<?php include __DIR__ . DS . 'inc_html_foot.php'; ?> 