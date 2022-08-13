<?php

// Uninstall database tables

global $wpdb;
$table_contact_management_persons = $wpdb->prefix . 'contact_management_persons';
$wpdb->query('DROP TABLE IF EXISTS ' . $table_contact_management_persons);

$table_contact_management_contacts = $wpdb->prefix . 'contact_management_contacts';
$wpdb->query('DROP TABLE IF EXISTS ' . $table_contact_management_contacts);