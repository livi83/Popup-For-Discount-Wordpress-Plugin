<?php

if (!defined('ABSPATH')) {
    exit;
}

class PFD_Export
{
    public function init()
    {
        add_action('admin_post_pfd_export_csv', [$this, 'export_csv']);
    }

    public function export_csv()
    {
        if (!current_user_can('manage_options')) {
            wp_die('You do not have permission to export emails.');
        }

        check_admin_referer('pfd_export_csv');

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

        $where = ['1=1'];
        $params = [];

        if (!empty($email_search)) {
            $where[] = 'email LIKE %s';
            $params[] = '%' . $wpdb->esc_like($email_search) . '%';
        }

        if (!empty($campaign_id)) {
            $where[] = 'campaign_id = %s';
            $params[] = $campaign_id;
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

        $sql = "SELECT id, email, campaign_id, coupon_code, page_url, user_ip, user_agent, created_at
                FROM {$table_name}
                WHERE {$where_sql}
                ORDER BY created_at DESC";

        if (!empty($params)) {
            $items = $wpdb->get_results($wpdb->prepare($sql, $params), ARRAY_A);
        } else {
            $items = $wpdb->get_results($sql, ARRAY_A);
        }

        $filename = 'popup-for-discount-emails-' . gmdate('Y-m-d-H-i-s') . '.csv';

        nocache_headers();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        if (!$output) {
            wp_die('Could not open output stream.');
        }

        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($output, [
            'ID',
            'Email',
            'Campaign ID',
            'Coupon code',
            'Page URL',
            'IP hash',
            'User agent',
            'Created at',
        ]);

        foreach ($items as $item) {
            fputcsv($output, [
                $item['id'],
                $item['email'],
                $item['campaign_id'],
                $item['coupon_code'],
                $item['page_url'],
                $item['user_ip'],
                $item['user_agent'],
                $item['created_at'],
            ]);
        }

        fclose($output);
        exit;
    }
}