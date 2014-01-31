<?php 

// start Theme class
$theme = \Theme::instance();

// check for mobile, tablet, pc device
// get browser class for use instead of fuelphp agent which is does not work.
include_once APPPATH . 'vendor' . DS . 'browser' . DS . 'lib' . DS . 'Browser.php';
$browser = new Browser();
$pc_class = '';
if (!$browser->isMobile() && !$browser->isTablet()) {
	$pc_class = ' pc_device';
}

// get admin cookie.
if (!isset($cookie_admin) || !isset($cookie_admin['account_display_name'])) {
	$model_account = new \Model_Accounts();
	$cookie_admin = $model_account->getAccountCookie('admin');
	
	if ($cookie_admin == null) {
		$cookie_admin = $model_account->getAccountCookie();
	}
	
	unset($model_account);
}

// load functions file to work with theme.
include_once __DIR__ . DS . 'functions.php';
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

		<link rel="stylesheet" href="<?php echo Uri::createNL($theme->asset_path('css/smoothness/jquery-ui-1.10.4.custom.min.css')); ?>">
		<link rel="stylesheet" href="<?php echo Uri::createNL($theme->asset_path('css/bootstrap.min.css')); ?>">
		<?php /*<link rel="stylesheet" href="<?php echo Uri::createNL($theme->asset_path('css/bootstrap-theme.min.css')); ?>">*/ ?> 
		<link rel="stylesheet" href="<?php echo Uri::createNL($theme->asset_path('css/font-awesome.min.css')); ?>">
		<link rel="stylesheet" href="<?php echo Uri::createNL($theme->asset_path('css/sm-core-css.css')); ?>">
		<link rel="stylesheet" href="<?php echo Uri::createNL($theme->asset_path('css/sm-bsblack/sm-bsblack.css')); ?>">
		<link rel="stylesheet" href="<?php echo Uri::createNL($theme->asset_path('js/chosen/chosen.min.css')); ?>">
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
		
		<script type="text/javascript">
			// required js variables for use in .js file.
			var base_url = '<?php echo \Uri::base(); ?>';
			var theme_assets = '<?php echo Uri::createNL(\Theme::instance()->asset_path('')); ?>';
			var csrf_name = '<?php echo \Config::get('security.csrf_token_key'); ?>';
			var nocsrf_val = '<?php echo \Extension\NoCsrf::generate('', true); ?>';
		</script>
	</head>
	<body>
