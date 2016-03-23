<?php
add_action('wp_ajax_ajax_backlink', 'xyz_ips_ajax_backlink');
function xyz_ips_ajax_backlink() {

	global $wpdb;
	
	if($_POST){
		update_option('xyz_credit_link','ips');
		
	}
}


?>