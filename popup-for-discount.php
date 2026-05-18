<?php
/**
 * Plugin Name: Popup for Discount
 * Plugin URI: https://example.com
 * Description: Custom discount popup for collecting email addresses and displaying coupon instructions.
 * Version: 1.1.1
 * Author: Lívia Kelebercová
 * Author URI: https://example.com
 * Text Domain: popup-for-discount
 */

if (!defined('ABSPATH')) {
    exit;
}

define('PFD_VERSION', '1.1.1');
define('PFD_PLUGIN_FILE', __FILE__);
define('PFD_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PFD_PLUGIN_URL', plugin_dir_url(__FILE__));
define('PFD_PLUGIN_BASENAME', plugin_basename(__FILE__));

require_once PFD_PLUGIN_DIR . 'includes/class-pfd-activator.php';
require_once PFD_PLUGIN_DIR . 'includes/class-pfd-admin.php';
require_once PFD_PLUGIN_DIR . 'includes/class-pfd-frontend.php';
require_once PFD_PLUGIN_DIR . 'includes/class-pfd-submissions.php';
require_once PFD_PLUGIN_DIR . 'includes/class-pfd-export.php';

function pfd_activate_plugin()
{
    PFD_Activator::activate();
}
register_activation_hook(__FILE__, 'pfd_activate_plugin');

function pfd_run_plugin()
{
    $submissions = new PFD_Submissions();
    $submissions->init();

    if (is_admin()) {
        $export = new PFD_Export();
        $export->init();

        $admin = new PFD_Admin();
        $admin->init();
    } else {
        $frontend = new PFD_Frontend();
        $frontend->init();
    }
}
add_action('plugins_loaded', 'pfd_run_plugin');