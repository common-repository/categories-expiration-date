<?php

function bv_expired_categories_install() {
	global $wpdb;

	$table_name = $wpdb->prefix . 'categories_expiration';
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		cat_id mediumint(9) NOT NULL,
		expiration mediumint(9) NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";


	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}

register_activation_hook( __FILE__, 'bv_expired_categories_install' );
add_action( 'plugins_loaded', 'bv_expired_categories_install' );