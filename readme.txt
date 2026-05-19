=== Popup for Discount ===
Contributors: lkelebercova
Tags: popup, discount, coupon, email collection, leads, marketing
Requires at least: 5.8
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 1.1.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Collect email addresses with a customizable discount popup and export submissions to CSV.

== Description ==

Popup for Discount is a lightweight WordPress plugin that helps website owners collect email addresses through a customizable discount popup.

The plugin displays a popup with an email form, coupon instructions, optional sticky discount button, and bottom coupon bar. Submitted email addresses are stored in a custom WordPress database table and can be viewed, filtered, deleted, and exported from the WordPress admin area.

The plugin does not send emails. It only collects submitted email addresses.

Main features:

* Customizable popup texts
* Custom popup image and logo
* Media Library support for image selection
* Custom coupon code
* Discount value placeholder using {discount}
* Campaign ID for filtering and exports
* Custom colors for popup, button and bottom bar
* Sticky discount button
* Configurable sticky hide duration
* Privacy text below the email form
* Honeypot spam protection
* Basic rate limiting
* Optional anonymized IP hash storage
* Optional user agent storage
* Custom database table for submissions
* Admin table with filters
* CSV export
* Single and bulk delete
* Optional data cleanup on uninstall

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/`.
2. Activate the plugin through the WordPress Plugins screen.
3. Go to `Popup for Discount > Settings`.
4. Configure your popup texts, images, colors, coupon code and campaign ID.
5. Save settings.
6. Visit the frontend of your website to test the popup.

== Frequently Asked Questions ==

= Does this plugin send emails? =

No. Popup for Discount does not send emails. It only collects submitted email addresses and stores them in the WordPress database.

= Where are submitted emails stored? =

Submitted email addresses are stored in a custom database table named `wp_pfd_submissions`. The table prefix may be different depending on your WordPress installation.

= Can I export collected emails? =

Yes. Go to `Popup for Discount > Collected Emails` and click `Export CSV`.

= Can I delete collected emails? =

Yes. You can delete individual submissions or use bulk delete from the admin table.

= Does the plugin use cookies? =

No. The plugin uses browser localStorage to remember popup state, such as whether the visitor closed the popup or submitted an email.

= Does the plugin store IP addresses? =

The plugin can optionally store an anonymized IP hash. It does not need to store the real IP address for normal operation.

= Can I change the discount value globally? =

Yes. Use the `Discount value` field and include `{discount}` in text fields. For example: `Save {discount}% today`.

= Can I track different campaigns? =

Yes. Use the `Campaign ID` field to identify the active campaign. Submissions can be filtered and exported by Campaign ID.

= What happens when I uninstall the plugin? =

By default, plugin data is preserved. If you enable `Delete data on uninstall`, plugin settings and collected submissions will be permanently deleted when the plugin is uninstalled.

== Changelog ==

= 1.1.4 =
* Added Settings link on the Plugins screen.
* Added anonymized IP hash and basic rate limiting.
* Added Campaign ID support.
* Added uninstall cleanup option.
* Added single and bulk delete for submissions.
* Added sticky discount button behavior.
* Added privacy text and honeypot protection.

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.1.4 =
MVP release with campaign tracking, CSV export, delete actions, privacy text, honeypot protection and basic rate limiting.