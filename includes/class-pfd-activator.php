<?php

if (!defined('ABSPATH')) {
    exit;
}

class PFD_Activator
{
    public static function activate()
    {
        self::create_submissions_table();
        self::add_default_settings();
    }

    private static function create_submissions_table()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'pfd_submissions';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$table_name} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            email VARCHAR(190) NOT NULL,
            coupon_code VARCHAR(100) NOT NULL DEFAULT '',
            page_url TEXT NULL,
            user_ip VARCHAR(100) NULL,
            user_agent TEXT NULL,
            created_at DATETIME NOT NULL,
            PRIMARY KEY  (id),
            KEY email (email),
            KEY coupon_code (coupon_code),
            KEY created_at (created_at)
        ) {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        dbDelta($sql);
    }

    private static function add_default_settings()
    {
        $default_settings = [
            'enabled' => 1,

            'image_url' => 'https://www.peptiderevolution.eu/wp-content/uploads/2026/02/Rectangle_34.png.png',
            'logo_url'  => 'https://www.peptiderevolution.eu/wp-content/uploads/2026/02/logo.svg',

            'headline_step_1' => 'Save up to<br>54% today',
            'subtext_step_1'  => 'Enter your email to receive<br>instructions to get discount',
            'email_placeholder' => 'Your email address',
            'button_text' => 'Continue',

            'headline_step_2' => 'Instructions',
            'instruction_text' => 'Use this code at checkout to get -20%',
            'coupon_code' => 'REVOLUTION20',
            'after_coupon_text' => 'or code from influencer',
            'benefits_title' => 'Do not forget to use also:',
            'benefits_list' => "👉 Buy more than 250€ get FREE SHIPPING\n👉 Buy any 8+ products and get -17%\n👉 Pay by bank transfer and get -5%\n👉 Summer Products Now -12%",

            'bar_text' => 'For 20% off, use code:',
            'sticky_button_text' => 'Discount available',
            
            'popup_delay' => 1200,

            'popup_bg_color' => '#ffffff',
            'popup_text_color' => '#000000',
            'accent_color' => '#0084ff',
            'button_bg_color' => '#f7b800',
            'button_text_color' => '#000000',
            'bar_bg_color' => '#f7b800',
            'bar_text_color' => '#000000',
            'bar_code_border_color' => '#ffffff',
            'close_button_color' => '#000000',

            'store_ip_address' => 0,
            'store_user_agent' => 0,
			
			'privacy_text' => 'By submitting your email, you agree that we may store it for the purpose of providing this discount offer.',
			
			'discount_value' => '20',
			'sticky_hide_hours' => 24,
			'sticky_button_text' => '{discount}% Discount available',

            'delete_data_on_uninstall' => 0,
            
        ];

        if (false === get_option('pfd_settings')) {
            add_option('pfd_settings', $default_settings);
        }
    }
}