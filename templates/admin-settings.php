<?php

if (!defined('ABSPATH')) {
    exit;
}

$defaults = [
    'enabled' => 1,

    'image_url' => '',
    'logo_url' => '',

    'headline_step_1' => '',
    'subtext_step_1' => '',
    'email_placeholder' => '',
    'button_text' => '',

    'headline_step_2' => '',
    'instruction_text' => '',
    'coupon_code' => '',
    'after_coupon_text' => '',
    'benefits_title' => '',
    'benefits_list' => '',

    'bar_text' => '',
    'sticky_button_text' => '{discount}% Discount available',

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
    
    'delete_data_on_uninstall' => 0,

    'campaign_id' => 'default-campaign',
];

$settings = wp_parse_args($settings, $defaults);
?>

<div class="wrap pfd-admin-wrap">
    <h1>Popup for Discount</h1>

    <form method="post" action="options.php">
        <div class="pfd-card">
    <h2>General</h2>

    <table class="form-table">
        <tr>
            <th scope="row">Enable popup</th>
            <td>
                <label>
                    <input
                        type="checkbox"
                        name="pfd_settings[enabled]"
                        value="1"
                        <?php checked(!empty($settings['enabled']), true); ?>
                    >
                    Enable popup on frontend
                </label>
            </td>
        </tr>

        <tr>
            <th scope="row">Campaign ID</th>
            <td>
                <input
                    type="text"
                    class="regular-text"
                    name="pfd_settings[campaign_id]"
                    value="<?php echo esc_attr($settings['campaign_id']); ?>"
                    placeholder="christmas-2026"
                >

                <p class="description">
                    Internal identifier for this popup campaign. Example:
                    <code>christmas-2026</code>, <code>black-friday-main</code>.
                    Used for filtering, exports and future analytics.
                </p>
            </td>
        </tr>

		<tr>
			<th scope="row">Discount value</th>
			<td>
				<input
					type="text"
					class="regular-text"
					name="pfd_settings[discount_value]"
					value="<?php echo esc_attr($settings['discount_value']); ?>"
					placeholder="20"
				>

				<p class="description">
					Enter only the discount number or value, for example 20, 50 or 10.
					You can use <code>{discount}</code> in text fields and it will be replaced with this value.
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">Sticky hide duration</th>
			<td>
				<input
					type="number"
					name="pfd_settings[sticky_hide_hours]"
					value="<?php echo esc_attr($settings['sticky_hide_hours']); ?>"
					min="0"
					step="1"
				>

				<p class="description">
					Number of hours the sticky discount button stays hidden after the visitor closes it.
					Example: 24 means it will stay hidden for 24 hours.
				</p>
			</td>
		</tr>
		
        <tr>
            <th scope="row">Popup delay</th>
            <td>
                <input
                    type="number"
                    name="pfd_settings[popup_delay]"
                    value="<?php echo esc_attr($settings['popup_delay']); ?>"
                    min="0"
                    step="100"
                >
                <p class="description">
                    Delay in milliseconds. Example: 1200 = 1.2 seconds.
                </p>
            </td>
        </tr>
    </table>
</div>
        <?php settings_fields('pfd_settings_group'); ?>

        <div class="pfd-card">
            <h2>Images</h2>

            <table class="form-table">
                <tr>
                    <th scope="row">Popup image</th>
                    <td>
                        <div class="pfd-media-field">
                            <input
                                type="url"
                                class="regular-text pfd-media-url"
                                name="pfd_settings[image_url]"
                                value="<?php echo esc_attr($settings['image_url']); ?>"
                                placeholder="https://example.com/image.jpg"
                            >

                            <button
                                type="button"
                                class="button pfd-select-media"
                                data-title="Select popup image"
                                data-button-text="Use this image"
                            >
                                Select image
                            </button>

                            <button
                                type="button"
                                class="button pfd-remove-media"
                            >
                                Remove
                            </button>
                        </div>

                        <div class="pfd-media-preview">
                            <?php if (!empty($settings['image_url'])) : ?>
                                <img src="<?php echo esc_url($settings['image_url']); ?>" alt="">
                            <?php endif; ?>
                        </div>

                        <p class="description">
                            This image will be shown on the left side of the popup.
                        </p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">Logo</th>
                    <td>
                        <div class="pfd-media-field">
                            <input
                                type="url"
                                class="regular-text pfd-media-url"
                                name="pfd_settings[logo_url]"
                                value="<?php echo esc_attr($settings['logo_url']); ?>"
                                placeholder="https://example.com/logo.svg"
                            >

                            <button
                                type="button"
                                class="button pfd-select-media"
                                data-title="Select logo"
                                data-button-text="Use this logo"
                            >
                                Select logo
                            </button>

                            <button
                                type="button"
                                class="button pfd-remove-media"
                            >
                                Remove
                            </button>
                        </div>

                        <div class="pfd-media-preview pfd-logo-preview">
                            <?php if (!empty($settings['logo_url'])) : ?>
                                <img src="<?php echo esc_url($settings['logo_url']); ?>" alt="">
                            <?php endif; ?>
                        </div>

                        <p class="description">
                            This logo will be shown above the popup headline.
                        </p>
                    </td>
                </tr>
            </table>
        </div>

        <div class="pfd-card">
            <h2>Step 1 - Email form</h2>
			<p class="description">
				HTML is allowed, for example &lt;br&gt;. You can use <code>{discount}</code>.
			</p>
            <table class="form-table">
                <tr>
                    <th scope="row">Headline</th>
                    <td>
                        <textarea name="pfd_settings[headline_step_1]" rows="3" class="large-text"><?php echo esc_textarea($settings['headline_step_1']); ?></textarea>
                        <p class="description">HTML is allowed, for example &lt;br&gt;.</p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">Subtext</th>
                    <td>
                        <textarea name="pfd_settings[subtext_step_1]" rows="3" class="large-text"><?php echo esc_textarea($settings['subtext_step_1']); ?></textarea>
                    </td>
                </tr>

                <tr>
                    <th scope="row">Email placeholder</th>
                    <td>
                        <input type="text" class="regular-text" name="pfd_settings[email_placeholder]" value="<?php echo esc_attr($settings['email_placeholder']); ?>">
                    </td>
                </tr>

                <tr>
                    <th scope="row">Button text</th>
                    <td>
                        <input type="text" class="regular-text" name="pfd_settings[button_text]" value="<?php echo esc_attr($settings['button_text']); ?>">
                    </td>
                </tr>
				
				<tr>
					<th scope="row">Privacy text</th>
					<td>
						<textarea
							name="pfd_settings[privacy_text]"
							rows="3"
							class="large-text"
						><?php echo esc_textarea($settings['privacy_text']); ?></textarea>

						<p class="description">
							This text appears below the email form. Basic HTML is allowed, for example a link to your Privacy Policy.
						</p>
					</td>
				</tr>
				
            </table>
        </div>

        <div class="pfd-card">
            <h2>Step 2 - Coupon instructions</h2>

            <table class="form-table">
                <tr>
                    <th scope="row">Headline</th>
                    <td>
                        <input type="text" class="regular-text" name="pfd_settings[headline_step_2]" value="<?php echo esc_attr($settings['headline_step_2']); ?>">
                    </td>
                </tr>

                <tr>
                    <th scope="row">Instruction text</th>
                    <td>
                        <textarea name="pfd_settings[instruction_text]" rows="3" class="large-text"><?php echo esc_textarea($settings['instruction_text']); ?></textarea>
                    </td>
                </tr>

                <tr>
                    <th scope="row">Coupon code</th>
                    <td>
                        <input type="text" class="regular-text" name="pfd_settings[coupon_code]" value="<?php echo esc_attr($settings['coupon_code']); ?>">
                    </td>
                </tr>

                <tr>
                    <th scope="row">Text after coupon</th>
                    <td>
                        <input type="text" class="regular-text" name="pfd_settings[after_coupon_text]" value="<?php echo esc_attr($settings['after_coupon_text']); ?>">
                    </td>
                </tr>

                <tr>
                    <th scope="row">Benefits title</th>
                    <td>
                        <input type="text" class="regular-text" name="pfd_settings[benefits_title]" value="<?php echo esc_attr($settings['benefits_title']); ?>">
                    </td>
                </tr>

                <tr>
                    <th scope="row">Benefits list</th>
                    <td>
                        <textarea name="pfd_settings[benefits_list]" rows="6" class="large-text"><?php echo esc_textarea($settings['benefits_list']); ?></textarea>
                        <p class="description">One benefit per line.</p>
                    </td>
                </tr>
            </table>
        </div>

        <div class="pfd-card">
            <h2>Bottom bar & sticky button</h2>

            <table class="form-table">
                <tr>
                    <th scope="row">Bar text</th>
                    <td>
                        <input type="text" class="regular-text" name="pfd_settings[bar_text]" value="<?php echo esc_attr($settings['bar_text']); ?>">
                    </td>
                </tr>
				<tr>
					<th scope="row">Sticky button text</th>
					<td>
						<input
							type="text"
							class="regular-text"
							name="pfd_settings[sticky_button_text]"
							value="<?php echo esc_attr($settings['sticky_button_text']); ?>"
						>
						<p class="description">
							This button appears when the visitor closes the popup without submitting an email.
							You can use <code>{discount}</code>, for example <code>{discount}% Discount available</code>.
						</p>
					</td>
				</tr>
            </table>
        </div>

        <div class="pfd-card">
            <h2>Colors</h2>

            <table class="form-table pfd-color-table">
                <?php
                $colors = [
                    'popup_bg_color' => 'Popup background color',
                    'popup_text_color' => 'Popup text color',
                    'accent_color' => 'Accent color',
                    'button_bg_color' => 'Button background color',
                    'button_text_color' => 'Button text color',
                    'bar_bg_color' => 'Bottom bar background color',
                    'bar_text_color' => 'Bottom bar text color',
                    'bar_code_border_color' => 'Bottom bar code border color',
                    'close_button_color' => 'Close button color',
                ];

                foreach ($colors as $key => $label) :
                    ?>
                    <tr>
                        <th scope="row"><?php echo esc_html($label); ?></th>
                        <td>
                            <input type="color" name="pfd_settings[<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr($settings[$key]); ?>">
                            <code><?php echo esc_html($settings[$key]); ?></code>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <div class="pfd-card">
            <h2>Data collection</h2>

            <table class="form-table">
                <tr>
                    <th scope="row">Store IP address</th>
                    <td>
                        <label>
                            <input type="checkbox" name="pfd_settings[store_ip_address]" value="1" <?php checked($settings['store_ip_address'], 1); ?>>
                            Store visitor IP address with the submitted email
                        </label>
                    </td>
                </tr>

                <tr>
                    <th scope="row">Store user agent</th>
                    <td>
                        <label>
                            <input type="checkbox" name="pfd_settings[store_user_agent]" value="1" <?php checked($settings['store_user_agent'], 1); ?>>
                            Store browser user agent with the submitted email
                        </label>
                    </td>
                </tr>

                <tr>
                    <th scope="row">Delete data on uninstall</th>
                    <td>
                        <label>
                            <input
                                type="checkbox"
                                name="pfd_settings[delete_data_on_uninstall]"
                                value="1"
                                <?php checked($settings['delete_data_on_uninstall'], 1); ?>
                            >
                            Delete plugin settings and collected email submissions when the plugin is uninstalled
                        </label>

                        <p class="description">
                            Warning: If enabled, uninstalling the plugin will permanently delete all collected emails and plugin settings.
                            Deactivating the plugin will not delete anything.
                        </p>
                    </td>
                </tr>
            </table>
        </div>

        <?php submit_button('Save Settings'); ?>
    </form>
</div>