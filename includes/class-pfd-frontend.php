<?php

if (!defined('ABSPATH')) {
    exit;
}

class PFD_Frontend
{
    private array $settings;

    public function init()
    {
        $this->settings = $this->get_settings();

        if (empty($this->settings['enabled'])) {
            return;
        }

        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets']);
        add_action('wp_footer', [$this, 'render_popup']);
    }

    private function get_settings()
    {
        $defaults = [
            'enabled' => 1,

            'image_url' => '',
            'logo_url' => '',

            'headline_step_1' => 'Save up to<br>{discount}% today',
            'subtext_step_1' => 'Enter your email to receive<br>instructions to get discount',
            'email_placeholder' => 'Your email address',
            'button_text' => 'Continue',

            'headline_step_2' => 'Instructions',
            'instruction_text' => 'Use this code at checkout to get -{discount}%',
            'coupon_code' => 'REVOLUTION20',
            'after_coupon_text' => 'or code from influencer',
            'benefits_title' => 'Do not forget to use also:',
            'benefits_list' => "👉 Buy more than 250€ get FREE SHIPPING\n👉 Buy any 8+ products and get -17%\n👉 Pay by bank transfer and get -5%\n👉 Summer Products Now -12%",

            'bar_text' => 'For {discount}% off, use code:',
            'sticky_button_text' => '{discount}% Discount available',
            
            'popup_delay' => 1200,
			'discount_value' => '20',
			'sticky_hide_hours' => 24,

            'popup_bg_color' => '#ffffff',
            'popup_text_color' => '#000000',
            'accent_color' => '#0084ff',
            'button_bg_color' => '#f7b800',
            'button_text_color' => '#000000',
            'bar_bg_color' => '#f7b800',
            'bar_text_color' => '#000000',
            'bar_code_border_color' => '#ffffff',
            'close_button_color' => '#000000',
			
			'privacy_text' => 'By submitting your email, you agree that we may store it for the purpose of providing this discount offer.',

            'campaign_id' => 'default-campaign',
        ];

        $settings = wp_parse_args(get_option('pfd_settings', []), $defaults);
		return $this->replace_placeholders_in_settings($settings);
    }
	
	private function replace_placeholders_in_settings($settings)
	{
		$discount_value = isset($settings['discount_value'])
			? sanitize_text_field($settings['discount_value'])
			: '';

		$replaceable_fields = [
			'headline_step_1',
			'subtext_step_1',
			'email_placeholder',
			'button_text',
			'privacy_text',
			'headline_step_2',
			'instruction_text',
			'after_coupon_text',
			'benefits_title',
			'benefits_list',
			'bar_text',
			'sticky_button_text',
		];

		foreach ($replaceable_fields as $field) {
			if (isset($settings[$field]) && is_string($settings[$field])) {
				$settings[$field] = str_replace(
					'{discount}',
					$discount_value,
					$settings[$field]
				);
			}
		}

		return $settings;
	}
	
    public function enqueue_frontend_assets()
    {
        wp_enqueue_style(
            'pfd-frontend',
            PFD_PLUGIN_URL . 'assets/css/frontend.css',
            [],
            PFD_VERSION
        );

        wp_enqueue_script(
            'pfd-frontend',
            PFD_PLUGIN_URL . 'assets/js/frontend.js',
            [],
            PFD_VERSION,
            true
        );

        wp_localize_script(
            'pfd-frontend',
            'pfdData',
            [
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('pfd_save_email_nonce'),
                'popupDelay' => absint($this->settings['popup_delay']),
                'stickyHideHours' => absint($this->settings['sticky_hide_hours']),
                'campaignId' => sanitize_text_field($this->settings['campaign_id']),
                'couponCode' => sanitize_text_field($this->settings['coupon_code']),
                'discountValue' => sanitize_text_field($this->settings['discount_value']),
                'storageKey' => 'popup_for_discount_state',
            ]
        );
    }

    public function render_popup()
    {
        $settings = $this->settings;

        require PFD_PLUGIN_DIR . 'templates/popup-template.php';
    }
	
	
}