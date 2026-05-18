<?php

if (!defined('ABSPATH')) {
    exit;
}

$benefits = [];

if (!empty($settings['benefits_list'])) {
    $benefits = array_filter(array_map('trim', explode("\n", $settings['benefits_list'])));
}

$style_vars = sprintf(
    '--pfd-popup-bg:%s; --pfd-popup-text:%s; --pfd-accent:%s; --pfd-button-bg:%s; --pfd-button-text:%s; --pfd-bar-bg:%s; --pfd-bar-text:%s; --pfd-bar-code-border:%s; --pfd-close-color:%s;',
    esc_attr($settings['popup_bg_color']),
    esc_attr($settings['popup_text_color']),
    esc_attr($settings['accent_color']),
    esc_attr($settings['button_bg_color']),
    esc_attr($settings['button_text_color']),
    esc_attr($settings['bar_bg_color']),
    esc_attr($settings['bar_text_color']),
    esc_attr($settings['bar_code_border_color']),
    esc_attr($settings['close_button_color'])
);
?>

<div id="pfd-popup" class="pfd-overlay" style="display:none; <?php echo esc_attr($style_vars); ?>">
    <div class="pfd-modal <?php echo empty($settings['image_url']) ? 'pfd-modal-no-image' : ''; ?>">
        <button class="pfd-close" type="button" aria-label="<?php esc_attr_e('Close', 'popup-for-discount'); ?>">×</button>

        <?php if (!empty($settings['image_url'])) : ?>
            <div class="pfd-image" style="background-image:url('<?php echo esc_url($settings['image_url']); ?>');"></div>
        <?php endif; ?>

        <div class="pfd-content">
            <?php if (!empty($settings['logo_url'])) : ?>
                <img class="pfd-logo" src="<?php echo esc_url($settings['logo_url']); ?>" alt="<?php esc_attr_e('Logo', 'popup-for-discount'); ?>">
            <?php endif; ?>

            <div class="pfd-step pfd-step-1">
                <h2><?php echo wp_kses_post($settings['headline_step_1']); ?></h2>

                <?php if (!empty($settings['subtext_step_1'])) : ?>
                    <p class="pfd-accent-text"><?php echo wp_kses_post($settings['subtext_step_1']); ?></p>
                <?php endif; ?>

               <form id="pfd-form">
					<input
						type="email"
						id="pfd-email"
						placeholder="<?php echo esc_attr($settings['email_placeholder']); ?>"
						required
					>

					<input
						type="text"
						id="pfd-website"
						name="pfd_website"
						value=""
						autocomplete="off"
						tabindex="-1"
						aria-hidden="true"
					>

					<button type="submit">
						<?php echo esc_html($settings['button_text']); ?>
					</button>

					<?php if (!empty($settings['privacy_text'])) : ?>
						<div class="pfd-privacy-text">
							<?php echo wp_kses_post($settings['privacy_text']); ?>
						</div>
					<?php endif; ?>
			</form>
            </div>

            <div class="pfd-step pfd-step-2" style="display:none;">
                <h2><?php echo esc_html($settings['headline_step_2']); ?></h2>

                <?php if (!empty($settings['instruction_text'])) : ?>
                    <p><?php echo wp_kses_post($settings['instruction_text']); ?></p>
                <?php endif; ?>

                <div class="pfd-code" id="pfd-coupon-code">
                    <?php echo esc_html($settings['coupon_code']); ?>
                </div>

                <?php if (!empty($settings['after_coupon_text'])) : ?>
                    <p><?php echo esc_html($settings['after_coupon_text']); ?></p>
                <?php endif; ?>

                <?php if (!empty($settings['benefits_title'])) : ?>
                    <strong><?php echo esc_html($settings['benefits_title']); ?></strong>
                <?php endif; ?>

                <?php if (!empty($benefits)) : ?>
                    <ul class="pfd-benefits">
                        <?php foreach ($benefits as $benefit) : ?>
                            <li><?php echo esc_html($benefit); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div id="pfd-bar" class="pfd-bar" style="display:none; <?php echo esc_attr($style_vars); ?>">
    <button class="pfd-bar-close" type="button" aria-label="<?php esc_attr_e('Close', 'popup-for-discount'); ?>">×</button>

    <span><?php echo esc_html($settings['bar_text']); ?></span>

    <strong id="pfd-bar-code">
        <?php echo esc_html($settings['coupon_code']); ?>
    </strong>
</div>
<div
    id="pfd-sticky-box"
    class="pfd-sticky-box"
    style="display:none; <?php echo esc_attr($style_vars); ?>"
>
    <button
        id="pfd-sticky-close"
        class="pfd-sticky-close"
        type="button"
        aria-label="<?php esc_attr_e('Close discount button', 'popup-for-discount'); ?>"
    >
        ×
    </button>

    <button
        id="pfd-sticky-button"
        class="pfd-sticky-button"
        type="button"
    >
        <?php echo esc_html($settings['sticky_button_text']); ?>
    </button>
</div>