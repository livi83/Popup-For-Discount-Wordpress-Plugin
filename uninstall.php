<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package Popup_For_Discount
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

$settings = get_option('pfd_settings', []);

$delete_data = !empty($settings['delete_data_on_uninstall']);

if (!$delete_data) {
    return;
}

global $wpdb;

$table_name = $wpdb->prefix . 'pfd_submissions';

$wpdb->query("DROP TABLE IF EXISTS {$table_name}");

delete_option('pfd_settings');