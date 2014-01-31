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
									<a href="#" onclick="return false;"><?php echo \Lang::get('admin.admin_website'); ?></a>
									<ul>
										<li><?php echo \Html::anchor('admin', \Lang::get('admin.admin_admin_home')); ?></li>
										<li><?php echo \Html::anchor('', \Lang::get('admin.admin_visit_site')); ?></li>
										<?php if (checkAdminPermission('config.config_global', 'config.config_global')) { ?><li><?php echo \Html::anchor('admin/config', \Lang::get('admin.admin_global_configuration')); ?></li><?php } ?> 
									</ul>
								</li>
								<li>
									<a href="#" onclick="return false;"><?php echo \Lang::get('admin.admin_users_roles_permissions'); ?></a>
									<ul>
										<?php if (checkAdminPermission('account.account_perm', 'account.account_viewusers_perm')) { ?><li><?php echo \Html::anchor('admin/account', \Lang::get('admin.admin_users')); ?></li><?php } ?> 
										<?php if (checkAdminPermission('account.account_perm', 'account.account_add_perm')) { ?><li><?php echo \Html::anchor('admin/account/add', \Lang::get('admin.admin_add_user')); ?></li><?php } ?> 
										<?php if (checkAdminPermission('account.account_perm', 'account.account_edit_perm')) { ?><li><?php echo \Html::anchor('admin/account/edit', \Lang::get('admin.admin_edit_my_account')); ?></li><?php } ?> 
										<?php if (checkAdminPermission('accountlv.accountlv_perm', 'accountlv.accountlv_viewlevels_perm') || checkAdminPermission('acperm.acperm_perm', 'acperm.acperm_manage_perm')) { ?> 
										<li>
											<a href="#" onclick="return false;"><?php echo \Lang::get('admin.admin_roles_permissions'); ?></a>
											<ul>
												<?php if (checkAdminPermission('accountlv.accountlv_perm', 'accountlv.accountlv_viewlevels_perm')) { ?><li><?php echo \Html::anchor('admin/account-level', \Lang::get('admin.admin_roles')); ?></li><?php } ?> 
												<?php if (checkAdminPermission('acperm.acperm_perm', 'acperm.acperm_manage_perm')) { ?><li><?php echo \Html::anchor('admin/account-permission', \Lang::get('admin.admin_permissions')); ?></li><?php } ?> 
											</ul>
										</li>
										<?php } ?> 
									</ul>
								</li>
								<li><a href="#" onclick="return false;"><?php echo \Lang::get('admin.admin_components'); ?></a>
									<?php echo \Library\Modules::forge()->listAdminNavbar(); ?> 
								</li>
							</ul>
							<ul class="nav navbar-nav navbar-right">
								<li><a href="#" onclick="return false;" class="non-link-navbar"><span class="glyphicon glyphicon-user"></span> <?php echo \Lang::get('admin.admin_hello_admin', array('displayname' => $cookie_admin['account_display_name'])); ?></a></li>
								<li class="dropdown<?php echo $pc_class; ?>"><?php echo languageSwitchAdminBootstrapNavbar(); ?></li>
								<li><?php echo \Html::anchor('admin/logout', '<span class="glyphicon glyphicon-log-out"></span> ' . \Lang::get('admin.admin_logout')); ?></li>
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
			<?php echo \Lang::get('fslang.fslang_credit'); ?> 
		</div>
		
		
<?php include __DIR__ . DS . 'inc_html_foot.php'; ?> 