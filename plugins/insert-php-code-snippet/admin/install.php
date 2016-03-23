<?php

function xyz_ips_network_install($networkwide) {
	global $wpdb;

	if (function_exists('is_multisite') && is_multisite()) {
		// check if it is a network activation - if so, run the activation function for each blog id
		if ($networkwide) {
			$old_blog = $wpdb->blogid;
			// Get all blog ids
			$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
			foreach ($blogids as $blog_id) {
				switch_to_blog($blog_id);
				xyz_ips_install();
			}
			switch_to_blog($old_blog);
			return;
		}
	}
	xyz_ips_install();
}


function xyz_ips_install(){
	
	global $wpdb;
	//global $current_user; get_currentuserinfo();
	

	if(get_option('xyz_ips_sort_order')=='')
	{
		add_option('xyz_ips_sort_order','desc');
	}
	if(get_option('xyz_ips_sort_field_name')=='')
	{
		add_option('xyz_ips_sort_field_name','id');
	}
	
	
	
	
	
	if(get_option('xyz_credit_link') == "")
	{
			add_option("xyz_credit_link",0);
	}

	add_option('xyz_ips_limit',20);
	$queryInsertPhp = "CREATE TABLE IF NOT EXISTS  ".$wpdb->prefix."xyz_ips_short_code (
	  `id` int NOT NULL AUTO_INCREMENT,
		  `title` varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
		  `content` longtext COLLATE utf8_unicode_ci NOT NULL,
		  `short_code` varchar(2000) COLLATE utf8_unicode_ci NOT NULL,
		  `status` int NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1";
	$wpdb->query($queryInsertPhp);
}

register_activation_hook( XYZ_INSERT_PHP_PLUGIN_FILE ,'xyz_ips_network_install');





