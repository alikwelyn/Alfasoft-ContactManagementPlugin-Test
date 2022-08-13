<?php

/**
 * Plugin Name: Contact Management
 * Plugin URI: "https://www.alfasoft.pt/"
 * Description: Wordpress plugin to manage contacts
 * Version: 1.0.0
 * Author: Alik Evangelista
 * Author URI: "http://alikevangelista.eu1.alfasoft.pt"
 * Text Domain: contact_management
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) exit;

// Base path
define('CONTACT_MANAGEMENT_FILE', __FILE__);
define('CONTACT_MANAGEMENT_PATH', plugin_dir_path(__FILE__));
define('CONTACT_MANAGEMENT_URI', plugin_dir_url(__FILE__));
define('CONTACT_MANAGEMENT_PLUGIN_NAME', plugin_basename(__FILE__));

// Version
define( 'CONTACT_MANAGEMENT_VERSION', '1.0.0' );

// Custom SQL tables to store values
if (!function_exists("contact_management_settings")) {

	register_activation_hook(CONTACT_MANAGEMENT_FILE, 'contact_management_settings');

	function contact_management_settings() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		// table: contact_management_person
		$table_contact_management_person = $wpdb->prefix . 'contact_management_person';

		$sql_contact_management_person = "CREATE TABLE IF NOT EXISTS $table_contact_management_person (
		`id` INT(11) NOT NULL AUTO_INCREMENT,
		`name` VARCHAR(250) DEFAULT NULL,
		`email` VARCHAR(100) NOT NULL DEFAULT '',
		`created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
		UNIQUE KEY id (id) ) $charset_collate;";

		// table: contact_management_contacts
		$table_contact_management_contacts = $wpdb->prefix . 'contact_management_contacts';

		$sql_contact_management_contacts = "CREATE TABLE IF NOT EXISTS $table_contact_management_contacts (
		`id` INT(11) NOT NULL AUTO_INCREMENT,
		`countryCode` TEXT DEFAULT '' NOT NULL,
		`number` VARCHAR(20) DEFAULT NULL,
		`created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
		UNIQUE KEY id (id) ) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta($sql_contact_management_person);
		dbDelta($sql_contact_management_contacts);
	}

}

if (!class_exists('WP_List_Table')) {
	require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}