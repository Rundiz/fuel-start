<?php 
/**
 * The main template file. This file is whole page template with page content placeholder for display controller's view.
 * This main template file is for admin section.
 * 
 * @author Vee Winch.
 * @package Fuel Start
 */

// start Theme class
$theme = \Theme::instance();

// check for mobile, tablet, pc device
// get browser class for use instead of fuelphp agent which is does not work.
include_once APPPATH . 'vendor' . DS . 'browser' . DS . 'lib' . DS . 'Browser.php';
$browser = new Browser();
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
		<link rel="stylesheet" href="<?php echo Uri::createNL($theme->asset_path('css/font-awesome.min.css')); ?>">
		<?php /*<link rel="stylesheet" href="<?php echo Uri::createNL($theme->asset_path('css/bootstrap-theme.min.css')); ?>">*/ ?> 
		<link rel="stylesheet" href="<?php echo Uri::createNL($theme->asset_path('css/admin.css')); ?>">
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
		<div class="whole-page-container">
			<div class="whole-page-inner-container">
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
							<a class="navbar-brand" href="#"><?php echo \Model_Config::getval('site_name'); ?></a>
						</div>

						<!-- Collect the nav links, forms, and other content for toggling -->
						<div class="collapse navbar-collapse" id="fs-admin-navbar-collapse">
							<ul class="nav navbar-nav">
								<li class="active"><a href="#">Link</a></li>
								<li><a href="#">Link</a></li>
								<li class="dropdown<?php if (!$browser->isMobile() && !$browser->isTablet()) { ?> pc_device<?php } ?>">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown <b class="caret"></b></a>
									<ul class="dropdown-menu">
										<li><a href="#">Action</a></li>
										<li><a href="#">Another action</a></li>
										<li><a href="#">Something else here</a></li>
										<li class="divider"></li>
										<li><a href="#">Separated link</a></li>
										<li class="divider"></li>
										<li><a href="#">One more separated link</a></li>
									</ul>
								</li>
							</ul>
							<form class="navbar-form navbar-right" role="search">
								<div class="form-group">
									<input type="text" class="form-control" placeholder="Search">
								</div>
								<button type="submit" class="btn btn-default">Submit</button>
							</form>
						</div><!-- /.navbar-collapse -->
					</div>
				</nav>


				<div class="container-fluid">
					<div class="row">
						<div class="col-sm-12">
							<?php 
							if (isset($page_content)) {
								echo $page_content;
							}
							?> 
						</div>
					</div>
				</div>
			</div><!--.whole-page-inner-container-->
		</div><!--.whole-page-container-->
		<div class="page-footer">
			<?php echo \Lang::get('fslang.fslang_credit'); ?> 
		</div>
		
		
		<script src="<?php echo Uri::createNL($theme->asset_path('js/bootstrap.min.js')); ?>"></script>
		<script src="<?php echo Uri::createNL($theme->asset_path('js/main.js')); ?>"></script>
		<script src="<?php echo Uri::createNL($theme->asset_path('js/admin.js')); ?>"></script>
	</body>
</html>