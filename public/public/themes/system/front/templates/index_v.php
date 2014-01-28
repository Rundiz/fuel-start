<?php 
// include functions file to get functions for this theme.
include_once dirname(__DIR__) . DS . 'functions.php';

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

		<link rel="stylesheet" href="<?php echo Uri::createNL($theme->asset_path('css/bootstrap.min.css')); ?>">
		<link rel="stylesheet" href="<?php echo Uri::createNL($theme->asset_path('css/bootstrap-theme.min.css')); ?>">
		<link rel="stylesheet" href="<?php echo Uri::createNL($theme->asset_path('css/front.css')); ?>">
		<?php 
		// render <link>
		if (isset($page_link) && is_array($page_link)) {
			foreach ($page_link as $a_page_link) {
				echo $a_page_link . "\n";
			}
			unset($a_page_link);
		}
		?> 

		<script src="<?php echo Uri::createNL($theme->asset_path('js/modernizr-2.6.2-respond-1.1.0.min.js')); ?>"></script>
		<script src="<?php echo Uri::createNL($theme->asset_path('js/jquery-1.10.2.min.js')); ?>"></script>
		
		<?php 
		// render assets
		echo \Asset::render('fuelstart');
		// render *theme* assets. (required for render theme's assets)
		echo $theme->asset->render('fuelstart');
		?> 
	</head>
	<body>
		<div class="container">
			<div class="row">
				<article class="col-md-12">
					<div class="jumbotron">
						<div class="container">
							<header>
								<h1>Fuel Start</h1>
							</header>
							<p>The backend system for start your php project with <a href="http://fuelphp.com" target="fuelphp">FuelPHP</a>.</p>
						</div>
					</div>
					
					<div class="row row-with-vspace">
						<div class="col-sm-9">
							<p><strong>Fuel Start</strong> is not framework. It is not CMS or CMF. It is just backend or back office system that help you start your project with FuelPHP framework very fast.</p>
							<h2>Features</h2>
							<ul>
								<li>Account management.</li>
								<li>Account's level management.</li>
								<li>Account's level permission - Control what page/action or controller/method that account level can access.</li>
								<li>Administrator base controller - To automatically verify logged in access and send to login page if not login.</li>
								<li>Configuration page - Config site name, account registration and more.</li>
								<li>Multilingual.</li>
							</ul>
						</div>
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
				</article>
			</div>
		</div>     
		
		<script src="<?php echo Uri::createNL($theme->asset_path('js/bootstrap.min.js')); ?>"></script>
		<script src="<?php echo Uri::createNL($theme->asset_path('js/main.js')); ?>"></script>
	</body>
</html>
