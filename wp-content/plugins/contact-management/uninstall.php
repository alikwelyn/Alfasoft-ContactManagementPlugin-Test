<?php

// Uninstall database tables

global $wpdb;
$table_contact_management_person = $wpdb->prefix . 'contact_management_person';
$wpdb->query('DROP TABLE IF EXISTS ' . $table_contact_management_person);

$table_contact_management_contacts = $wpdb->prefix . 'contact_management_contacts';
$wpdb->query('DROP TABLE IF EXISTS ' . $table_contact_management_contacts);