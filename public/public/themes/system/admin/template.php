<?php 
/**
 * The main template file. This file is whole page template with page content placeholder for display controller's view.
 * This main template file is for admin section.
 * 
 * @author Vee Winch.
 * @package Fuel Start
 */

include __DIR__ . DS . 'inc_html_head.php'; 
?> 


		<div class="the-page-container">
			<div class="the-page-inner-container">
				<nav class="navbar navbar-static-top navbar-inverse" role="navigation">
					<div class="container-fluid">
						<!-- Brand and toggle get grouped for better mobile display -->
						<div class="navbar-header">
							<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#fs-admin-navbar-collapse">
								<span class="sr-only">Toggle navigation</span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
							</button>
							<a class="navbar-brand" href="<?php echo \Uri::create('admin'); ?>"><?php echo \Model_Config::getval('site_name'); ?></a>
						</div>

						<!-- Collect the nav links, forms, and other content for toggling -->
						<div class="collapse navbar-collapse" id="fs-admin-navbar-collapse">
							<ul id="admin-nav-menu" class="nav navbar-nav sm sm-bsblack">
								<li>
									<a href="#" onclick="return false;"><?php echo \Lang::get('admin_website'); ?></a>
									<ul>
										<li><?php echo \Html::anchor('admin', \Lang::get('admin_admin_home')); ?> 
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
										<li><?php echo \Html::anchor('', \Lang::get('admin_visit_site')); ?> 
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
										<?php if (checkAdminPermission('config_global', 'config_global')) { ?><li><?php echo \Html::anchor('admin/config', \Lang::get('admin_global_configuration')); ?></li><?php } ?> 
										<?php if (checkAdminPermission('cacheman_perm', 'cacheman_clearcache_perm')) { ?> 
										<li>
											<a href="#" onclick="return false;"><?php echo \Lang::get('admin_nav_tools'); ?></a>
											<ul>
												<?php if (checkAdminPermission('cacheman_perm', 'cacheman_clearcache_perm')) { ?><li><?php echo \Html::anchor('admin/cacheman', \Lang::get('admin_nav_cacheman')); ?></li><?php } ?> 
											</ul>
										</li>
										<?php } ?> 
									</ul>
								</li>
								<li>
									<a href="#" onclick="return false;"><?php echo \Lang::get('admin_users_roles_permissions'); ?></a>
									<ul>
										<?php if (checkAdminPermission('account_perm', 'account_viewusers_perm')) { ?><li><?php echo \Html::anchor('admin/account', \Lang::get('admin_users')); ?></li><?php } ?> 
										<?php if (checkAdminPermission('account_perm', 'account_add_perm')) { ?><li><?php echo \Html::anchor('admin/account/add', \Lang::get('admin_add_user')); ?></li><?php } ?> 
										<?php if (checkAdminPermission('account_perm', 'account_edit_perm')) { ?><li><?php echo \Html::anchor('admin/account/edit', \Lang::get('admin_edit_my_account')); ?></li><?php } ?> 
										<?php if (checkAdminPermission('accountlv_perm', 'accountlv_viewlevels_perm') || checkAdminPermission('acperm_perm', 'acperm_manage_perm')) { ?> 
										<li>
											<a href="#" onclick="return false;"><?php echo \Lang::get('admin_roles_permissions'); ?></a>
											<ul>
												<?php if (checkAdminPermission('accountlv_perm', 'accountlv_viewlevels_perm')) { ?><li><?php echo \Html::anchor('admin/account-level', \Lang::get('admin_roles')); ?></li><?php } ?> 
												<?php if (checkAdminPermission('acperm_perm', 'acperm_manage_perm')) { ?><li><?php echo \Html::anchor('admin/account-level-permission', \Lang::get('admin_permissions_for_roles')); ?></li><?php } ?> 
												<?php if (checkAdminPermission('acperm_perm', 'acperm_manage_perm')) { ?><li><?php echo \Html::anchor('admin/account-permission', \Lang::get('admin_permissions_for_users')); ?></li><?php } ?> 
											</ul>
										</li>
										<?php } ?> 
									</ul>
								</li>
								<li><a href="#" onclick="return false;"><?php echo \Lang::get('admin_components'); ?></a>
									<?php echo \Library\Modules::forge()->listAdminNavbar(); ?> 
								</li>
								<li><a href="#" onclick="return false;"><?php echo \Lang::get('admin_extensions'); ?></a>
									<ul>
										<?php if (checkAdminPermission('siteman_perm', 'siteman_viewsites_perm')) { ?><li><?php echo \Html::anchor('admin/siteman', \Lang::get('admin_multisite_manager')); ?></li><?php } ?> 
									</ul>
								</li>
							</ul>
							<ul class="nav navbar-nav navbar-right">
								<li><a href="#" onclick="return false;" class="non-link-navbar"><span class="glyphicon glyphicon-user"></span> <?php echo \Lang::get('admin_hello_admin', array('displayname' => $cookie_admin['account_display_name'])); ?></a></li>
								<li class="dropdown<?php echo $pc_class; ?>"><?php echo languageSwitchAdminBootstrapNavbar(); ?></li>
								<li><?php echo \Html::anchor('admin/logout', '<span class="glyphicon glyphicon-log-out"></span> ' . \Lang::get('admin_logout')); ?></li>
							</ul>
						</div><!-- /.navbar-collapse -->
					</div>
				</nav>

				
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12 page-content-wrapper">
							<?php 
							if (isset($page_content)) {
								echo $page_content;
							}
							?> 
						</div><!--.page-content-wrapper-->
					</div>
				</div>
				
				
			</div><!--.the-page-inner-container-->
		</div><!--.the-page-container-->
		<div class="the-page-footer">
			<?php echo \Lang::get('fslang_credit'); // you can remove credit or change it. ?> 
		</div>
		
		
<?php include __DIR__ . DS . 'inc_html_foot.php'; ?> 