<?php

if (!defined('ABSPATH')) {
    exit;
}

$current_url = menu_page_url('popup-for-discount-emails', false);
?>

<div class="wrap pfd-admin-wrap">
    <h1>Collected Emails</h1>

    <div class="pfd-card">
        <form method="get" action="">
            <input type="hidden" name="page" value="popup-for-discount-emails">

            <div class="pfd-filters">
                <label>
                    Email
                    <input
                        type="text"
                        name="email_search"
                        value="<?php echo esc_attr($email_search); ?>"
                        placeholder="example@email.com"
                    >
                </label>

                <label>
                    Coupon code
                    <input
                        type="text"
                        name="coupon_code"
                        value="<?php echo esc_attr($coupon_code); ?>"
                        placeholder="REVOLUTION20"
                    >
                </label>

                <label>
                    Date from
                    <input
                        type="date"
                        name="date_from"
                        value="<?php echo esc_attr($date_from); ?>"
                    >
                </label>

                <label>
                    Date to
                    <input
                        type="date"
                        name="date_to"
                        value="<?php echo esc_attr($date_to); ?>"
                    >
                </label>

             <div class="pfd-filter-actions">
                <button type="submit" class="button button-primary">
                    Filter
                </button>

                <a href="<?php echo esc_url($current_url); ?>" class="button">
                    Reset
                </a>

                <?php
                $export_url = wp_nonce_url(
                    add_query_arg(
                        [
                            'action' => 'pfd_export_csv',
                            'email_search' => $email_search,
                            'coupon_code' => $coupon_code,
                            'date_from' => $date_from,
                            'date_to' => $date_to,
                        ],
                        admin_url('admin-post.php')
                    ),
                    'pfd_export_csv'
                );
                ?>

                <a href="<?php echo esc_url($export_url); ?>" class="button button-secondary">
                    Export CSV
                </a>
            </div>
            </div>
        </form>
    </div>

    <div class="pfd-card">
        <h2>Results</h2>

        <?php if (isset($_GET['pfd_deleted'])) : ?>
            <div class="notice notice-success is-dismissible">
                <p>Email submission deleted.</p>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['pfd_bulk_deleted'])) : ?>
            <div class="notice notice-success is-dismissible">
                <p>
                    <?php
                    printf(
                        esc_html__('%d email submissions deleted.', 'popup-for-discount'),
                        absint($_GET['pfd_bulk_deleted'])
                    );
                    ?>
                </p>
            </div>
        <?php endif; ?>

        <p>
            Total results:
            <strong><?php echo esc_html(number_format_i18n($total_items)); ?></strong>
        </p>
        
        <form method="post" action="">
            <?php wp_nonce_field('pfd_bulk_delete_submissions'); ?>

            <div class="pfd-bulk-actions">
                <select name="pfd_bulk_action">
                    <option value="">Bulk actions</option>
                    <option value="delete">Delete</option>
                </select>

                <button
                    type="submit"
                    class="button"
                    onclick="return confirm('Are you sure you want to delete selected submissions?');"
                >
                    Apply
                </button>
            </div>

            <table class="widefat striped pfd-emails-table">

              <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="pfd-select-all-submissions">
                        </th>
                        <th>ID</th>
                        <th>Email</th>
                        <th>Coupon code</th>
                        <th>Page URL</th>
                        <th>Date</th>
                        <th>IP</th>
                        <th>User agent</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($items)) : ?>
                        <?php foreach ($items as $item) : ?>
                            <tr>
                                <td>
                                    <input
                                        type="checkbox"
                                        class="pfd-submission-checkbox"
                                        name="pfd_submission_ids[]"
                                        value="<?php echo esc_attr($item['id']); ?>"
                                    >
                                </td>

                                <td><?php echo esc_html($item['id']); ?></td>

                                <td>
                                    <strong><?php echo esc_html($item['email']); ?></strong>
                                </td>

                                <td><?php echo esc_html($item['coupon_code']); ?></td>

                                <td>
                                    <?php if (!empty($item['page_url'])) : ?>
                                        <a href="<?php echo esc_url($item['page_url']); ?>" target="_blank" rel="noopener noreferrer">
                                            <?php echo esc_html(wp_trim_words($item['page_url'], 8, '...')); ?>
                                        </a>
                                    <?php else : ?>
                                        —
                                    <?php endif; ?>
                                </td>

                                <td><?php echo esc_html($item['created_at']); ?></td>

                                <td>
                                    <?php echo !empty($item['user_ip']) ? esc_html($item['user_ip']) : '—'; ?>
                                </td>

                                <td>
                                    <?php echo !empty($item['user_agent']) ? esc_html(wp_trim_words($item['user_agent'], 10, '...')) : '—'; ?>
                                </td>

                                <td>
                                    <?php
                                    $delete_url = wp_nonce_url(
                                        add_query_arg(
                                            [
                                                'page' => 'popup-for-discount-emails',
                                                'pfd_action' => 'delete_submission',
                                                'submission_id' => absint($item['id']),
                                            ],
                                            admin_url('admin.php')
                                        ),
                                        'pfd_delete_submission_' . absint($item['id'])
                                    );
                                    ?>

                                    <a
                                        href="<?php echo esc_url($delete_url); ?>"
                                        class="button button-small"
                                        onclick="return confirm('Are you sure you want to delete this submission?');"
                                    >
                                        Delete
                                    </a>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="9">
                                No collected emails found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </form>

        <?php if ($total_pages > 1) : ?>
            <div class="tablenav">
                <div class="tablenav-pages">
                    <?php
                    echo wp_kses_post(
                        paginate_links([
                            'base' => add_query_arg('paged', '%#%'),
                            'format' => '',
                            'prev_text' => '&laquo;',
                            'next_text' => '&raquo;',
                            'total' => $total_pages,
                            'current' => $paged,
                        ])
                    );
                    ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>