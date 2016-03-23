<?php
/**
 * WooCommerce Checkout Manager Pro
 *
 *
 * Copyright (C) 2014 Ephrain Marchan, trottyzone
 *
 */
 
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if( !woocmmatl() ) {
remove_action('admin_menu', 'wccs_admin_menu_pro');
add_action('admin_menu', 'wccs_admin_menu_pro2');
}


if ( is_admin() ) {
	add_action('admin_menu', 'wccm_admin_menu'); 
}


function wccs_admin_menu_pro2() {
        add_menu_page( __('WooCCM', 'woocommerce-checkout-manager-pro'), __('WooCCM', 'woocommerce-checkout-manager-pro'), 'manage_options', 'woocommerce-checkout-manager-pro' , 'wooccm_welcome_screen', 'dashicons-businessman', 57);
}



function wccm_admin_menu() { 
        add_submenu_page( 'woocommerce-checkout-manager-pro' , 'License', 'License', 'manage_options', 'License_check_slug', 'pg_eptxml'); 
}


function wooccm_welcome_screen(){ ?>

<div class="wrap about-wrap">

<h1 style="margin-right:0px !important;">Welcome to WooCommerce Checkout Manager Pro&nbsp;<?php $version = get_plugin_data( __FILE__); echo $version['Version']; ?></h1>

<div class="about-text">Thank you for your purchase! WooCommerce Checkout Manager Pro helps you manage the checkout fields using advance options.<br /><br /> Let's Get started! Install your license key provided by your e-mail receipt.</div>

<div class="changelog headline-feature">
	<h2>Email Receipt</h2>

	<div class="feature-section">
		<div class="col">
			<h3>Installing your license key is quite easy, just a simple matter of copy and paste.</h3>
			<p>The license key is also called a License Code and can be installed on the <a href="admin.php?page=License_check_slug">License Page</a>.</p>
			<p>The image at the side shows the Email Receipt that should have been received via email after your purchase.</p>
			 <p>Copy and paste the code next to title of <strong>License Code</strong> without the comma.</p>
		</div>
		<div class="col">
			<img src="<?php echo plugins_url('../../../images/receipt.png', __FILE__); ?>">
		</div>
	</div>

	<div class="clear"></div>
</div>

<hr>

	<div class="return-to-dashboard">
				<a href="admin.php?page=License_check_slug">Go to WooCCM → License</a>
	</div>
<br />
<br />
<br />

</div>                  

<?php }
		
		
   function esip($ip_addr) 
{ 
  //first of all the format of the ip address is matched 
  if(preg_match("/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/",$ip_addr)) 
  { 
    //now all the intger values are separated 
    $parts=explode(".",$ip_addr); 
    //now we need to check each part can range from 0-255 
    foreach($parts as $ip_parts) 
    { 
      if(intval($ip_parts)>255 || intval($ip_parts)<0) 
      return FALSE; //if number is not within range of 0-255 
    } 
    return TRUE; 
  } 
  else 
    return FALSE; //if format of ip address doesn't matches 
} 

    
    
    function domain($domainb) 
    { 
    $bits = explode('/', $domainb); 
    if ($bits[0]=='http:' || $bits[0]=='https:') 
    { 
    $domainb= $bits[2]; 
    } else { 
    $domainb= $bits[0]; 
    } 
    unset($bits); 
    $bits = explode('.', $domainb); 
    $idz=count($bits); 
    $idz-=3; 
    if (strlen($bits[($idz+2)])==2) { 
    $url=$bits[$idz].'.'.$bits[($idz+1)].'.'.$bits[($idz+2)]; 
    } else if (strlen($bits[($idz+2)])==0) { 
    $url=$bits[($idz)].'.'.$bits[($idz+1)]; 
    } else { 
    $url=$bits[($idz+1)].'.'.$bits[($idz+2)]; 
    } 
    return $url; 
    } 

function check_domainIP( $domain ) {
$bits = explode('.', $domain);

if ( count( $bits ) == 4 ) {
if ( is_numeric($bits[0]) && is_numeric($bits[1]) && is_numeric($bits[2]) && is_numeric($bits[3]) ) {
return true;
} 
else {
return false;
}
} else {
return false;
}

}


add_option( 'wccmkelizn32aunique', '0' ); 
function pg_eptxml() {
if( isset($_POST['lw_eptxml']) ) { 
if (substr($_POST['lw_eptxml'],-1,1) == ',') {
    $_POST['lw_eptxml'] = str_replace(',','',$_POST['lw_eptxml']);
}
update_option( 'wccmkelizn32aunique', $_POST['lw_eptxml'] ); 
echo '<div class="updated"><p><strong>'.__('License Code Saved.').'</strong></p></div>';
} 
?>

<div class="wrap"></div>

<style type="text/css">
#lw_eptxml {
width:55.8%;
}
.no {
background:red;
padding:25.5%;
color:#fff;
}
.yes{
background:green;
padding:26%;
color:#fff;
}
h1.heading-blue {
font-size: 1.5em;
margin-right: 0px !important;
background: #222;
border-color: #0074a2;
padding: 20px 20px 20px 26px;
color: #eee;
}
form.form-left, .welcome-panel.left {
margin-left: 20px;
max-width: 1050px;
}
div#welcome-panel {
padding-right: 0px;
max-width: 1040px;
}
.return-to-dashboard {
float: right;
margin-right: 23px;
font-weight: bold;
margin-top: 5px;
}
</style>


<div class="wrap about-wrap">
<h1 class="heading-blue">License Page</h1>
</div>


<?php if ( !function_exists('curl_version') ) { echo '<div class="error" style="padding:10px !important;font-size:14px;margin-left:0px !important">'.__('Please contact your hosting company to enable cURL. Plugin will need this to activate.', 'woocommerce-checkout-manager-pro').'</div>'; } ?>


<form action="admin.php?page=License_check_slug" method="post" class="form-left">
<table style="margin-top:20px;padding-left: 20px;padding-top: 10px;padding-bottom: 10px;" class="wp-list-table widefat tags ui-sortable">
<thead>
    <tr>
		<th><?php _e('Licensing Validator','woocommerce-checkout-manager-pro'); ?></th>
	    <th>
        <span style="font-size: 11px;color:red;">
        <?php
            if ( !woocmmatl() && get_option('errfafvetcgrt6434cwooccminfo15907833') == 'connection_error' ) {
                _e('Unable to connect! ', 'woocommerce-checkout-manager-pro'); echo get_option('hostnamewooccmerrfafvetcgrt6434cwooccminfo15907833').'. '; _e('Please contact your hosting company to add trottyzone.com to the whitelist.','woocommerce-checkout-manager-pro');
            }elseif (woocmmatl() && get_option('errfafvetcgrt6434cwooccminfo15907833') == 'clear') {
                
            }elseif ( !woocmmatl() && get_option('errfafvetcgrt6434cwooccminfo15907833') == 'change_site' ) {
                _e('Current site running the plguin is not registered. Click Change Site button to register this site.', 'woocommerce-checkout-manager-pro');
            }elseif ( !woocmmatl() && get_option('errfafvetcgrt6434cwooccminfo15907833') == 'not_exsit') {
                _e('License Code does not exist! Please check the code.', 'woocommerce-checkout-manager-pro');
            }
    
        ?>
        </span>
        </th>
	</tr>
</thead>

<tbody>
	<tr>
		<td><?php _e('Status','woocommerce-checkout-manager-pro'); ?></td>
		<td>

<?php if( woocmmatl() ) { ?>
<span class="yes"><?php _e('Valid','woocommerce-checkout-manager-pro'); ?></span>
<?php } ?>

<?php if( !woocmmatl() ) { ?>
<span class="no"><?php _e('Invalid','woocommerce-checkout-manager-pro'); ?></span>
<?php } ?>

		</td>
	</tr>
</tbody>


<tbody>
	<tr>
		<td><?php _e('License Code','woocommerce-checkout-manager-pro'); ?></td>
		<td><input id="lw_eptxml" name="lw_eptxml" size="70" type="text" value="<?php echo get_option('wccmkelizn32aunique'); ?>" /></td>
	</tr>

	<tr>
		<td><a href="http://www.trottyzone.com/edit-account/"><input type="button" class="button-secondary" value="<?php _e('Change Site','woocommerce-checkout-manager-pro'); ?>" /></a></td>
		<td><input type="submit" class="button-primary" value="<?php _e('Validate','woocommerce-checkout-manager-pro'); ?>" />

<?php if ( woocmmatl() ) { ?>
<div class="return-to-dashboard">
		<a class="return-dashboard-blue" href="admin.php?page=woocommerce-checkout-manager-pro/woocommerce-checkout-manager-pro.php">← Go to WooCCM</a>
</div>
<?php } ?>


</td>
    </tr>
</tbody>

</table>
</form>


<div id="welcome-panel" class="welcome-panel left">
        <div class="welcome-panel-content">
	            <h3>Welcome to WooCommerce Checkout Manager!</h3>
                	<p class="about-description">Above is the plugin's License Code Validator, it will activate the plugin.</p>
                    <p class="about-description">Just need to enter in the license code that is provided in the plugin <b>e-mail receipt</b> or you can view the license code <b>on trottyzone.com</b></p>
                
                <div class="welcome-panel-column-container">
                	<div class="welcome-panel-column">
                        <ul>
                			<a class="button button-primary button-hero" href="http://www.trottyzone.com/edit-account/">Login to trottyzone.com</a>	
                		</ul>  
                    </div>
	                <div class="welcome-panel-column">
                		<ul>
                			<a class="button button-primary button-hero" href="http://www.trottyzone.com/woocommerce-checkout-manager-pro-documentation/">Proceed to documentation</a>
                    	</ul>
                	</div>
                    <p>Note: The <b>Change Site</b> button allows you to change the website URL for the license code on <a href="http://www.trottyzone.com/">trottyzone.com</a>. Support is also available at the plugin's <a href="http://www.trottyzone.com/forums/forum/wooccm/">forum</a>.
                </div>
	        </div>
</div>    
<?php 
}

 function woocmmatl() { global $wp_version;  $address= $_SERVER['HTTP_HOST']; if ( check_domainIP( $address ) == false ) { $parsed_url = parse_url($address); $check = @esip($parsed_url['host']); $host = @$parsed_url['host']; if ($check == FALSE){ if ($host != ""){ if ( substr(domain($host), 0, 1) == '.' ) { $host = str_replace('www.','',substr(domain($host), 1)); } else { $host = str_replace('www.','',domain($host)); } }else{ if ( substr(domain($address), 0, 1) == '.' ) { $host = str_replace('www.','',substr(domain($address), 1)); } else { $host = str_replace('www.','',domain($address)); } } } } else { $host = $address; } $valuexg = get_option('wccmkelizn32aunique'); if ( strpos($_SERVER['REQUEST_URI'], 'License_check_slug') || strpos($_SERVER['REQUEST_URI'], 'woocommerce-checkout-manager-pro') ) { if ( substr( $_SERVER['REMOTE_ADDR'] , 0, 3) == "127" || $_SERVER['REMOTE_ADDR'] == "1" || $_SERVER['REMOTE_ADDR'] == "::1" ) { return true; } else { if ( !empty( $valuexg ) ) { $api_url = 'http://www.trottyzone.com/wp-content/plugins/wp-licensing/auth/verify.php'; $request_string = array( 'body' => array( 'key' => $valuexg, 'domain' => $host, 'product' => 'woocommerce-checkout-manager-pro' ), 'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url') ); $resultx = wp_remote_post($api_url, $request_string ); $result = wp_remote_retrieve_body( $resultx ); if (is_wp_error($result)) { update_option('errfafvetcgrt6434cwooccminfo15907833', 'connection_error'); update_option('hostnamewooccmerrfafvetcgrt6434cwooccminfo15907833', $host); }else { $result = json_decode($result, true); if ($result['valid'] == 'true') { update_option('errfafvetcgrt6434cwooccminfo15907833', 'clear'); }elseif ($result['info']['domain'] !== 'NA' && $result['valid'] == 'false' ) { update_option('errfafvetcgrt6434cwooccminfo15907833', 'change_site'); }elseif ($result['info']['domain'] == 'NA') { update_option('errfafvetcgrt6434cwooccminfo15907833', 'not_exsit'); } } if($result['valid'] == 'true'){ return true; } } } } return false; }