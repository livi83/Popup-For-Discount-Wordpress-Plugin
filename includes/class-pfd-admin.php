<?php

if (!defined('ABSPATH')) {
    exit;
}

class PFD_Admin
{
    public function init()
    {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_init', [$this, 'handle_delete_submission']);
        add_action('admin_init', [$this, 'handle_bulk_delete_submissions']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
    }

    public function add_admin_menu()
    {
        add_menu_page(
            'Popup for Discount',
            'Popup for Discount',
            'manage_options',
            'popup-for-discount',
            [$this, 'render_settings_page'],
            'dashicons-email-alt2',
            56
        );

        add_submenu_page(
            'popup-for-discount',
            'Settings',
            'Settings',
            'manage_options',
            'popup-for-discount',
            [$this, 'render_settings_page']
        );

        add_submenu_page(
            'popup-for-discount',
            'Collected Emails',
            'Collected Emails',
            'manage_options',
            'popup-for-discount-emails',
            [$this, 'render_emails_page']
        );
    }
    public function render_emails_page()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        global $wpdb;

        $table_name = $wpdb->prefix . 'pfd_submissions';

        $email_search = isset($_GET['email_search'])
            ? sanitize_email(wp_unslash($_GET['email_search']))
            : '';

        $campaign_id = isset($_GET['campaign_id'])
            ? sanitize_title(wp_unslash($_GET['campaign_id']))
            : '';

        $coupon_code = isset($_GET['coupon_code'])
            ? sanitize_text_field(wp_unslash($_GET['coupon_code']))
            : '';

        $date_from = isset($_GET['date_from'])
            ? sanitize_text_field(wp_unslash($_GET['date_from']))
            : '';

        $date_to = isset($_GET['date_to'])
            ? sanitize_text_field(wp_unslash($_GET['date_to']))
            : '';

        $paged = isset($_GET['paged']) ? max(1, absint($_GET['paged'])) : 1;
        $per_page = 20;
        $offset = ($paged - 1) * $per_page;

        $where = ['1=1'];
        $params = [];

        if (!empty($campaign_id)) {
            $where[] = 'campaign_id = %s';
            $params[] = $campaign_id;
        }

        if (!empty($email_search)) {
            $where[] = 'email LIKE %s';
            $params[] = '%' . $wpdb->esc_like($email_search) . '%';
        }

        if (!empty($coupon_code)) {
            $where[] = 'coupon_code = %s';
            $params[] = $coupon_code;
        }

        if (!empty($date_from)) {
            $where[] = 'created_at >= %s';
            $params[] = $date_from . ' 00:00:00';
        }

        if (!empty($date_to)) {
            $where[] = 'created_at <= %s';
            $params[] = $date_to . ' 23:59:59';
        }

        $where_sql = implode(' AND ', $where);

        $count_sql = "SELECT COUNT(*) FROM {$table_name} WHERE {$where_sql}";

        if (!empty($params)) {
            $total_items = (int) $wpdb->get_var($wpdb->prepare($count_sql, $params));
        } else {
            $total_items = (int) $wpdb->get_var($count_sql);
        }

        $query_sql = "SELECT * FROM {$table_name}
            WHERE {$where_sql}
            ORDER BY created_at DESC
            LIMIT %d OFFSET %d";

        $query_params = array_merge($params, [$per_page, $offset]);

        $items = $wpdb->get_results(
            $wpdb->prepare($query_sql, $query_params),
            ARRAY_A
        );

        $total_pages = max(1, (int) ceil($total_items / $per_page));

        require PFD_PLUGIN_DIR . 'templates/admin-emails.php';
    }

    public function register_settings()
    {
        register_setting(
            'pfd_settings_group',
            'pfd_settings',
            [$this, 'sanitize_settings']
        );
    }

    public function handle_delete_submission()
    {
        if (
            !isset($_GET['page'], $_GET['pfd_action'], $_GET['submission_id']) ||
            $_GET['page'] !== 'popup-for-discount-emails' ||
            $_GET['pfd_action'] !== 'delete_submission'
        ) {
            return;
        }

        if (!current_user_can('manage_options')) {
            wp_die('You do not have permission to delete submissions.');
        }

        $submission_id = absint($_GET['submission_id']);

        if (!$submission_id) {
            wp_safe_redirect(admin_url('admin.php?page=popup-for-discount-emails'));
            exit;
        }

        check_admin_referer('pfd_delete_submission_' . $submission_id);

        global $wpdb;

        $table_name = $wpdb->prefix . 'pfd_submissions';

        $wpdb->delete(
            $table_name,
            [
                'id' => $submission_id,
            ],
            [
                '%d',
            ]
        );

        $redirect_url = remove_query_arg(
            [
                'pfd_action',
                'submission_id',
                '_wpnonce',
            ],
            wp_get_referer() ? wp_get_referer() : admin_url('admin.php?page=popup-for-discount-emails')
        );

        $redirect_url = add_query_arg('pfd_deleted', '1', $redirect_url);

        wp_safe_redirect($redirect_url);
        exit;
    }

    public function handle_bulk_delete_submissions()
    {
        if (
            !isset($_POST['pfd_bulk_action'], $_POST['pfd_submission_ids']) ||
            $_POST['pfd_bulk_action'] !== 'delete'
        ) {
            return;
        }

        if (!current_user_can('manage_options')) {
            wp_die('You do not have permission to delete submissions.');
        }

        check_admin_referer('pfd_bulk_delete_submissions');

        $submission_ids = array_map(
            'absint',
            (array) wp_unslash($_POST['pfd_submission_ids'])
        );

        $submission_ids = array_filter($submission_ids);

        if (empty($submission_ids)) {
            wp_safe_redirect(admin_url('admin.php?page=popup-for-discount-emails'));
            exit;
        }

        global $wpdb;

        $table_name = $wpdb->prefix . 'pfd_submissions';

        $placeholders = implode(',', array_fill(0, count($submission_ids), '%d'));

        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$table_name} WHERE id IN ({$placeholders})",
                $submission_ids
            )
        );

        $redirect_url = wp_get_referer() ? wp_get_referer() : admin_url('admin.php?page=popup-for-discount-emails');

        $redirect_url = remove_query_arg(
            [
                'pfd_bulk_action',
                'pfd_submission_ids',
                '_wpnonce',
            ],
            $redirect_url
        );

        $redirect_url = add_query_arg('pfd_bulk_deleted', count($submission_ids), $redirect_url);

        wp_safe_redirect($redirect_url);
        exit;
    }

    public function sanitize_settings($input)
    {
        $output = [];

        $output['enabled'] = !empty($input['enabled']) ? 1 : 0;

        $output['image_url'] = isset($input['image_url']) ? esc_url_raw($input['image_url']) : '';
        $output['logo_url'] = isset($input['logo_url']) ? esc_url_raw($input['logo_url']) : '';

        $output['headline_step_1'] = isset($input['headline_step_1']) ? wp_kses_post($input['headline_step_1']) : '';
        $output['subtext_step_1'] = isset($input['subtext_step_1']) ? wp_kses_post($input['subtext_step_1']) : '';
        $output['email_placeholder'] = isset($input['email_placeholder']) ? sanitize_text_field($input['email_placeholder']) : '';
        $output['button_text'] = isset($input['button_text']) ? sanitize_text_field($input['button_text']) : '';

        $output['headline_step_2'] = isset($input['headline_step_2']) ? sanitize_text_field($input['headline_step_2']) : '';
        $output['instruction_text'] = isset($input['instruction_text']) ? wp_kses_post($input['instruction_text']) : '';
        $output['coupon_code'] = isset($input['coupon_code']) ? sanitize_text_field($input['coupon_code']) : '';
        $output['after_coupon_text'] = isset($input['after_coupon_text']) ? sanitize_text_field($input['after_coupon_text']) : '';
        $output['benefits_title'] = isset($input['benefits_title']) ? sanitize_text_field($input['benefits_title']) : '';
        $output['benefits_list'] = isset($input['benefits_list']) ? sanitize_textarea_field($input['benefits_list']) : '';

        $output['bar_text'] = isset($input['bar_text'])
            ? sanitize_text_field($input['bar_text'])
            : '';

        $output['sticky_button_text'] = isset($input['sticky_button_text'])
            ? sanitize_text_field($input['sticky_button_text'])
            : 'Discount available';

        $output['popup_delay'] = isset($input['popup_delay']) ? absint($input['popup_delay']) : 1200;

        $color_fields = [
            'popup_bg_color',
            'popup_text_color',
            'accent_color',
            'button_bg_color',
            'button_text_color',
            'bar_bg_color',
            'bar_text_color',
            'bar_code_border_color',
            'close_button_color',
        ];

        foreach ($color_fields as $field) {
            $output[$field] = isset($input[$field]) && sanitize_hex_color($input[$field])
                ? sanitize_hex_color($input[$field])
                : '';
        }

        $output['store_ip_address'] = !empty($input['store_ip_address']) ? 1 : 0;
        $output['store_user_agent'] = !empty($input['store_user_agent']) ? 1 : 0;
		
		$output['privacy_text'] = isset($input['privacy_text'])
			? wp_kses_post($input['privacy_text'])
			: '';
		
		$output['discount_value'] = isset($input['discount_value'])
			? sanitize_text_field($input['discount_value'])
			: '20';

		$output['sticky_hide_hours'] = isset($input['sticky_hide_hours'])
			? absint($input['sticky_hide_hours'])
			: 24;
        
        $output['delete_data_on_uninstall'] = !empty($input['delete_data_on_uninstall']) ? 1 : 0;

        $output['campaign_id'] = isset($input['campaign_id'])
            ? sanitize_title($input['campaign_id'])
            : 'default-campaign';
            
        return $output;
    }

    public function enqueue_admin_assets($hook)
    {
        $allowed_hooks = [
            'toplevel_page_popup-for-discount',
            'popup-for-discount_page_popup-for-discount-emails',
        ];

        if (!in_array($hook, $allowed_hooks, true)) {
            return;
        }

        wp_enqueue_style(
            'pfd-admin',
            PFD_PLUGIN_URL . 'assets/css/admin.css',
            [],
            PFD_VERSION
        );

        if ($hook === 'toplevel_page_popup-for-discount') {
            wp_enqueue_media();
        }

        wp_enqueue_script(
            'pfd-admin',
            PFD_PLUGIN_URL . 'assets/js/admin.js',
            ['jquery'],
            PFD_VERSION,
            true
        );
    }

    public function render_settings_page()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        $settings = get_option('pfd_settings', []);

        require PFD_PLUGIN_DIR . 'templates/admin-settings.php';
    }
}