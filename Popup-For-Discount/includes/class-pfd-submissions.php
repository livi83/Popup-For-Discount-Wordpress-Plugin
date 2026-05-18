<?php

if (!defined('ABSPATH')) {
    exit;
}

class PFD_Submissions
{
    public function init()
    {
        add_action('wp_ajax_pfd_save_email', [$this, 'save_email']);
        add_action('wp_ajax_nopriv_pfd_save_email', [$this, 'save_email']);
    }

    public function save_email()
    {
        check_ajax_referer('pfd_save_email_nonce', 'nonce');
		$honeypot = isset($_POST['pfd_website'])
			? sanitize_text_field(wp_unslash($_POST['pfd_website']))
			: '';

		if (!empty($honeypot)) {
			wp_send_json_error([
				'message' => 'Submission rejected.',
			], 400);
		}
        $email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
        $coupon_code = isset($_POST['coupon_code']) ? sanitize_text_field(wp_unslash($_POST['coupon_code'])) : '';
        $page_url = isset($_POST['page_url']) ? esc_url_raw(wp_unslash($_POST['page_url'])) : '';

        if (empty($email) || !is_email($email)) {
            wp_send_json_error([
                'message' => 'Please enter a valid email address.',
            ], 400);
        }

        $settings = get_option('pfd_settings', []);

        $user_ip = '';
        $user_agent = '';

        if (!empty($settings['store_ip_address'])) {
            $user_ip = $this->get_user_ip();
        }

        if (!empty($settings['store_user_agent']) && !empty($_SERVER['HTTP_USER_AGENT'])) {
            $user_agent = sanitize_textarea_field(wp_unslash($_SERVER['HTTP_USER_AGENT']));
        }

        global $wpdb;

        $table_name = $wpdb->prefix . 'pfd_submissions';

        $inserted = $wpdb->insert(
            $table_name,
            [
                'email' => $email,
                'coupon_code' => $coupon_code,
                'page_url' => $page_url,
                'user_ip' => $user_ip,
                'user_agent' => $user_agent,
                'created_at' => current_time('mysql'),
            ],
            [
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
            ]
        );

        if (false === $inserted) {
            wp_send_json_error([
                'message' => 'Email could not be saved.',
            ], 500);
        }

        wp_send_json_success([
            'message' => 'Email saved successfully.',
        ]);
    }

    private function get_user_ip()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return sanitize_text_field(wp_unslash($_SERVER['HTTP_CLIENT_IP']));
        }

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', wp_unslash($_SERVER['HTTP_X_FORWARDED_FOR']));
            return sanitize_text_field(trim($ips[0]));
        }

        if (!empty($_SERVER['REMOTE_ADDR'])) {
            return sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR']));
        }

        return '';
    }
}