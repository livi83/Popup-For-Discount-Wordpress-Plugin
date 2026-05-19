<?php

if (!defined('ABSPATH')) {
    exit;
}

class PFD_Submissions
{
    private const RATE_LIMIT_MAX_SUBMISSIONS = 3;
    private const RATE_LIMIT_WINDOW_SECONDS = HOUR_IN_SECONDS;

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
        $campaign_id = isset($_POST['campaign_id']) ? sanitize_title(wp_unslash($_POST['campaign_id'])) : '';
        $coupon_code = isset($_POST['coupon_code']) ? sanitize_text_field(wp_unslash($_POST['coupon_code'])) : '';
        $page_url = isset($_POST['page_url']) ? esc_url_raw(wp_unslash($_POST['page_url'])) : '';

        if (empty($email) || !is_email($email)) {
            wp_send_json_error([
                'message' => 'Please enter a valid email address.',
            ], 400);
        }

        $ip_address = $this->get_user_ip();
        $ip_hash = $this->hash_ip_address($ip_address);

        if ($this->is_rate_limited($ip_hash)) {
            wp_send_json_error([
                'message' => 'Too many submissions. Please try again later.',
            ], 429);
        }

        $this->increment_rate_limit($ip_hash);

        $settings = get_option('pfd_settings', []);

        $user_ip = '';
        $user_agent = '';

        if (!empty($settings['store_ip_address'])) {
            $user_ip = $ip_hash;
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
                'campaign_id' => $campaign_id,
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

    private function hash_ip_address($ip_address)
    {
        if (empty($ip_address)) {
            return '';
        }

        return hash_hmac(
            'sha256',
            $ip_address,
            wp_salt('auth')
        );
    }

    private function get_rate_limit_key($ip_hash)
    {
        if (empty($ip_hash)) {
            return '';
        }

        return 'pfd_rate_limit_' . md5($ip_hash);
    }

    private function is_rate_limited($ip_hash)
    {
        if (empty($ip_hash)) {
            return false;
        }

        $key = $this->get_rate_limit_key($ip_hash);

        if (empty($key)) {
            return false;
        }

        $attempts = (int) get_transient($key);

        return $attempts >= self::RATE_LIMIT_MAX_SUBMISSIONS;
    }

    private function increment_rate_limit($ip_hash)
    {
        if (empty($ip_hash)) {
            return;
        }

        $key = $this->get_rate_limit_key($ip_hash);

        if (empty($key)) {
            return;
        }

        $attempts = (int) get_transient($key);
        $attempts++;

        set_transient(
            $key,
            $attempts,
            self::RATE_LIMIT_WINDOW_SECONDS
        );
    }


    
}