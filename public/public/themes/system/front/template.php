<?php 
/**
 * The main template file. This file is whole page template with page content placeholder for display controller's view.
 * 
 * @author Vee Winch.
 * @package Fuel Start
 */

// include functions file to get functions for this theme.
include_once __DIR__ . DS . 'functions.php';

// start Theme class
$theme = \Theme::instance();
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<title><?php echo $page_title; ?></title>
		<meta name="viewport" content="width=device-width">
		<?php 
		// render meta
		if (isset($page_meta) && is_array($page_meta)) {
			foreach ($page_meta as $a_page_meta) {
				echo $a_page_meta . "\n";
			}
			unset($a_page_meta);
		}
		?> 

		<?php echo \Asset::css('bootstrap.min.css'); ?>
		<?php echo $theme->asset->css('font-awesome.min.css'); ?>
		<?php echo $theme->asset->css('front.css'); ?>
		<?php 
		// render <link>
		if (isset($page_link) && is_array($page_link)) {
			foreach ($page_link as $a_page_link) {
				echo $a_page_link . "\n";
			}
			unset($a_page_link);
		}
		?> 

		<?php echo \Asset::js('modernizr.min.js'); ?>
		<?php echo \Asset::js('respond/respond.min.js'); ?>
		<?php echo \Asset::js('jquery.min.js'); ?>
		<?php 
		// render <script>
		if (isset($page_script) && is_array($page_script)) {
			foreach ($page_script as $a_page_script) {
				echo $a_page_script . "\n";
			}
			unset($a_page_script);
		}
		?> 
		
		<?php 
		// render assets
		echo \Asset::render('fuelstart');
		// render *theme* assets. (required for render theme's assets)
		echo $theme->asset->render('fuelstart');
		?> 
	</head>
	<body>
		<div class="container">
			<div class="row row-with-vspace">
				<header class="col-md-12 webpage-header">
					<h1 class="brand"><?php echo \Model_Config::getval('site_name'); ?></h1>
				</header>
			</div>
			
			<div class="row row-with-vspace">
				<main class="col-sm-9" role="main">
					<?php 
					if (isset($page_content)) {
						echo $page_content;
					}
					?> 
				</main>
				<aside class="col-sm-3 sidebar">
					<div class="sidebar-block">
						<h3><?php echo \Lang::get('fslang.fslang_languages'); ?></h3>
						<?php echo languageSwitchDropdown(); ?> 
					</div>
					
					<div class="sidebar-block">
						<h3><?php echo \Lang::get('fslang.fslang_navigation'); ?></h3>
						<ul>
							<li><a href="<?php echo Uri::create('admin'); ?>">Go to Admin dashboard</a></li>
							<li><a href="<?php echo Uri::create('account/register'); ?>">Register account</a></li>
							<li><a href="<?php echo Uri::create('account/resend-activate'); ?>">Re-send confirm register code</a></li>
							<li><a href="<?php echo Uri::create('account/login'); ?>">Login</a></li>
							<li><a href="<?php echo Uri::create('account/forgotpw'); ?>">Forgot username or password</a></li>
							<li><a href="<?php echo Uri::create('account/edit'); ?>">Edit account</a></li>
							<li><a href="<?php echo Uri::create('account/view-logins'); ?>">View logins</a></li>
							<li><a href="<?php echo Uri::create('account/logout'); ?>">Logout</a></li>
						</ul>
					</div>
				</aside>
			</div>
			
			<div class="row row-with-vspace page-footer">
				<footer class="col-md-12">
					&copy; Fuel Start 2013 - by <a href="http://okvee.net">Okvee.net</a>.
				</footer>
			</div>
		</div>     
		
		<?php echo \Asset::js('bootstrap.min.js'); ?>
		<?php echo $theme->asset->js('main.js'); ?>
	</body>
</html>
