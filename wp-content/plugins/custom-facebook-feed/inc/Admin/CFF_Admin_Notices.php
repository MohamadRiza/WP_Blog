<?php

/**
 * CFF Admin Notices.
 *
 * @since 4.0
*/

namespace CustomFacebookFeed\Admin;

use CustomFacebookFeed\Builder\CFF_Source;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class CFF_Admin_Notices
{
	public function __construct()
	{
		$this->init();
	}

	/**
	 * Determining if the user is viewing the our page, if so, party on.
	 *
	 * @since 4.0
	 */
	public function init()
	{
		if (!is_admin()) {
			return;
		}
		add_action('cff_admin_notices', [$this, 'cff_group_deprecation_dismiss_notice']);
		add_action('admin_notices', [$this, 'cff_group_deprecation_dismiss_notice']);
	}

	/**
	 * Group Deprecation Notice
	 *
	 * @since 4.0.2/4.0.7
	 */
	public function cff_group_deprecation_dismiss_notice()
	{
		$cff_statuses_option = get_option('cff_statuses', array());
		if (!empty($cff_statuses_option['cff_group_deprecation_dismiss']) && $cff_statuses_option['cff_group_deprecation_dismiss']  !== true) {
			return;
		}

		if (!empty($_GET['cff_dismiss_notice']) && $_GET['cff_dismiss_notice'] === 'group_deprecation') {
			\cff_main()->cff_error_reporter->dismiss_group_deprecation_error();
			$cff_statuses_option['cff_group_deprecation_dismiss'] = true;
			update_option('cff_statuses', $cff_statuses_option, false);
			return;
		}

		if (!CFF_Source::should_show_group_deprecation()) {
			return;
		}
		$close_href = add_query_arg(array('cff_dismiss_notice' => 'group_deprecation'));
		$group_doc_url = 'https://smashballoon.com/doc/facebook-api-changes-affecting-groups-april-2024';
		?>
			<div class="notice notice-error is-dismissible cff-dismissible">
				<p>
				<?php
					echo
					sprintf(
						__('Due to changes with the Facebook API, which we use to create feeds, group feeds will no longer update after April of 2024 %sLearn More %s', 'custom-facebook-feed'),
						'<a href="' . esc_url($group_doc_url) . '">',
						'</a>'
					);
				?>
				&nbsp;<a href="<?php echo esc_attr($close_href); ?>">
						<?php echo __('Dismiss', 'custom-facebook-feed'); ?>
					</a>
				</p>
			</div>
		<?php
	}
}