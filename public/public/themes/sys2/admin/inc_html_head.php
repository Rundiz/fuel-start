<?php 

// start Theme class
$theme = \Theme::instance();

// check for mobile, tablet, pc device
// get browser class for use instead of fuelphp agent which is does not work.
include_once APPPATH . 'vendor' . DS . 'browser' . DS . 'lib' . DS . 'Browser.php';
$browser = new Browser();
$pc_class = '';
if (!$browser->isMobile() && !$browser->isTablet()) {
    $pc_class .= ' pc_device';
} elseif ($browser->isMobile()) {
    $pc_class .= ' mobile_device';
} elseif ($browser->isTablet()) {
    $pc_class .= ' tablet_device';
}
unset($browser);

// get admin cookie.
if (!isset($cookie_admin) || !isset($cookie_admin['account_display_name'])) {
    $model_account = new \Model_Accounts();
    $cookie_admin = $model_account->getAccountCookie('admin');
    
    if ($cookie_admin == null) {
        $cookie_admin = $model_account->getAccountCookie();
    }
    
    unset($model_account);
    if (array_key_exists('account_id', $cookie_admin)) {
        $account_id = $cookie_admin['account_id'];
    }
}
if (!isset($account_id)) {
    $account_id = 0;
}

// load functions file to work with theme.
include_once __DIR__ . DS . 'inc_functions.php';


// get admin avatar at navbar
$admin_navbar_avatar = getAdminAvatar($account_id);
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
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php 
        // render meta
        if (isset($page_meta) && is_array($page_meta)) {
            foreach ($page_meta as $a_page_meta) {
                echo $a_page_meta . "\n";
            }
            unset($a_page_meta);
        }
        ?> 

        <?php 
        echo \Asset::css('jquery-ui/jquery-ui.min.css'); 
        echo \Asset::css('bootstrap.min.css'); 
        echo \Asset::css('font-awesome.min.css'); 
        echo $theme->asset->css('bootstrap-theme-sys2.min.css');
        echo $theme->asset->css('smartmenus/sm-core-css.css'); 
        echo $theme->asset->css('smartmenus/sm-fssidebar/sm-fssidebar.min.css'); 
        echo $theme->asset->css('chosen/chosen.min.css'); 
        echo $theme->asset->css('icheck/skins/minimal/_all.css'); 
        echo $theme->asset->css('fs-sys2-admin.css'); 
        
        // render <link>
        if (isset($page_link) && is_array($page_link)) {
            foreach ($page_link as $a_page_link) {
                echo $a_page_link . "\n";
            }
            unset($a_page_link);
        }
        ?> 

        <?php 
        echo \Asset::js('modernizr.min.js'); 
        echo \Asset::js('respond/respond.min.js'); // for ie 6-8 media query min,max width
        echo \Asset::js('jquery.min.js'); 
        
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
            var base_url = '<?php echo \Uri::base(false); ?>';
            var site_url = '<?php echo getRootSiteURL(); ?>';
            var theme_assets = '<?php echo Uri::createNL(\Theme::instance()->asset_path('')); ?>';
            var csrf_name = '<?php echo \Config::get('security.csrf_token_key'); ?>';
            var nocsrf_val = '<?php echo \Extension\NoCsrf::generate('', true); ?>';
        </script>
    </head>
    <body>
