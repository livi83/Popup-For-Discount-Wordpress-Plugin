<?php
/**
 * Plugin Name: Popup for Discount
 * Plugin URI: https://example.com
 * Description: Custom discount popup for collecting email addresses and displaying coupon instructions.
 * Version: 1.1.3
 * Author: Lívia Kelebercová
 * Author URI: https://example.com
 * Text Domain: popup-for-discount
 */

if (!defined('ABSPATH')) {
    exit;
}

define('PFD_VERSION', '1.1.3');
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

/**
 * Adds a Settings link to the plugin row on the Plugins page.
 *
 * @param array $links Existing plugin action links.
 *
 * @return array
 */
function pfd_add_settings_link($links)
{
    $settings_link = sprintf(
        '<a href="%s">%s</a>',
        esc_url(admin_url('admin.php?page=popup-for-discount')),
        esc_html__('Settings', 'popup-for-discount')
    );

    array_unshift($links, $settings_link);

    return $links;
}
add_filter('plugin_action_links_' . PFD_PLUGIN_BASENAME, 'pfd_add_settings_link');