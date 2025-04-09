<?php
/**
 * SureMails Plugin Class
 *
 * This file contains the main admin class for the SureMails plugin.
 *
 * @package SureMails\Admin
 */

namespace SureMails\Inc\Admin;

use SureMails\Inc\Settings;
use SureMails\Inc\Traits\Instance;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Plugin
 *
 * Main class for the SureMails Plugin admin functionalities.
 */
class Plugin {
	use Instance;

	/**
	 * Plugin initialization function.
	 */
	protected function __construct() {
		// Hook into WordPress actions and filters.
		add_action( 'admin_init', [ $this, 'activation_redirect' ] );
		add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_notice_scripts' ] );
		add_action( 'admin_notices', [ $this, 'check_configuration' ] );

		// Add settings link to the plugin action links.
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), [ $this, 'add_settings_link' ] );
	}

	/**
	 * Plugin initialization function.
	 *
	 * @return void
	 */
	public function activation_redirect() {
		// Avoid redirection in case of WP_CLI calls.
		if ( defined( 'WP_CLI' ) && \WP_CLI ) {
			return;
		}

		// Avoid redirection in case of ajax calls.
		if ( wp_doing_ajax() ) {
			return;
		}

		$do_redirect = apply_filters( 'suremails_enable_redirect_activation', get_option( 'suremails_do_redirect' ) );

		if ( $do_redirect ) {

			update_option( 'suremails_do_redirect', false );

			if ( ! is_multisite() ) {
				wp_safe_redirect(
					add_query_arg(
						[
							'page' => SUREMAILS,
						],
						admin_url( 'options-general.php' )
					)
				);
				exit;
			}
		}
	}

	/**
	 * Check if the plugin is configured correctly and display a notice if not.
	 *
	 * @return void
	 */
	public function check_configuration() {
		if ( ! is_admin() ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$current_page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$options      = Settings::instance()->get_settings();

		if ( ! empty( $options['connections'] ) || $current_page === SUREMAILS ) {
			return;
		}

		?>
			<div id="suremails-admin-notice" class="notice notice-warning is-dismissible">
			</div>
		<?php
	}

	/**
	 * Enqueue admin notice scripts.
	 *
	 * @return void
	 */
	public function enqueue_admin_notice_scripts() {
		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$current_page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$options      = Settings::instance()->get_settings();
		// If the user is on the SureMails settings page or there are connections, don't show the notice.
		if ( ! empty( $options['connections'] ) || $current_page === SUREMAILS ) {
			return;
		}

		$assets = require_once SUREMAILS_DIR . 'build/admin-notice.asset.php';

		if ( ! isset( $assets ) ) {
			return;
		}

		wp_register_script(
			'suremails-admin-notice',
			SUREMAILS_PLUGIN_URL . 'build/admin-notice.js',
			[ 'wp-element', 'wp-dom-ready', 'wp-i18n' ],
			$assets['version'],
			true
		);

		wp_enqueue_script(
			'suremails-admin-notice',
			SUREMAILS_PLUGIN_URL . 'build/admin-notice.js',
			[ 'wp-element', 'wp-dom-ready', 'wp-i18n' ],
			$assets['version'],
			true
		);

		wp_enqueue_style(
			'suremails-admin-notice',
			SUREMAILS_PLUGIN_URL . 'build/admin-notice.css',
			[],
			$assets['version'],
		);

		wp_localize_script(
			'suremails-admin-notice',
			'suremailsNotice',
			[
				'dashboardUrl' => esc_url( admin_url( 'options-general.php?page=' . SUREMAILS . '#/dashboard' ) ),
			]
		);

		// Set the script translations.
		wp_set_script_translations( 'suremails-admin-notice', 'suremails', SUREMAILS_DIR . 'languages' );
	}

	/**
	 * Add settings page to the WordPress admin menu.
	 *
	 * @return void
	 */
	public function add_admin_menu() {
		add_options_page(
			__( 'SureMail Settings', 'suremails' ),
			__( 'SureMail', 'suremails' ),
			'manage_options',
			SUREMAILS,
			[ $this, 'render_suremails_frontend' ]
		);
	}

	/**
	 * Enqueue admin scripts and styles for the SureMails settings page.
	 *
	 * @param string $hook The current admin page hook.
	 * @return void
	 */
	public function enqueue_admin_scripts( $hook ) {
		// Ensure scripts are only enqueued on the SureMails settings page.
		if ( $hook !== 'settings_page_' . SUREMAILS ) {
			return;
		}

		$assets = require_once SUREMAILS_DIR . '/build/main.asset.php';

		if ( ! isset( $assets ) ) {
			return;
		}

		wp_register_script(
			'suremails-react-script',
			SUREMAILS_PLUGIN_URL . 'build/main.js',
			[ 'wp-api-fetch', 'wp-components', 'wp-i18n', 'wp-hooks', 'updates' ],
			$assets['version'],
			true
		);

		// Enqueue your custom React script.
		wp_enqueue_script(
			'suremails-react-script',
			SUREMAILS_PLUGIN_URL . 'build/main.js', // Adjust the path if necessary.
			[ 'wp-element', 'wp-api-fetch', 'wp-dom-ready', 'wp-api', 'wp-components', 'wp-i18n', 'wp-hooks' ],
			$assets['version'],
			true // Load in footer.
		);

		// RTL checks.
		$rtl_suffix = is_rtl() ? '-rtl' : '';
		$file_name  = 'main' . $rtl_suffix . '.css';

		// Enqueue your custom styles.
		wp_enqueue_style(
			'suremails-react-styles',
			SUREMAILS_PLUGIN_URL . 'build/' . $file_name,
			[],
			$assets['version'],
		);

		// Localize script to pass data to React.
		wp_localize_script(
			'suremails-react-script',
			'suremails',
			[
				'siteUrl'                  => esc_url( get_site_url() ),
				'userEmail'                => wp_get_current_user()->user_email,
				'version'                  => SUREMAILS_VERSION,
				'nonce'                    => current_user_can( 'manage_options' ) ? wp_create_nonce( 'wp_rest' ) : '',
				'_ajax_nonce'              => current_user_can( 'manage_options' ) ? wp_create_nonce( 'suremails_plugin' ) : '',
				'contentGuardPopupStatus'  => Settings::instance()->show_content_guard_lead_popup(),
				'contentGuardActiveStatus' => get_option( 'suremails_content_guard_activated', 'no' ),
				'termsURL'                 => 'https://suremails.com/terms?utm_campaign=suremails&utm_medium=suremails-dashboard',
				'privacyPolicyURL'         => 'https://suremails.com/privacy-policy?utm_campaign=suremails&utm_medium=suremails-dashboard',
				'docsURL'                  => 'https://suremails.com/docs?utm_campaign=suremails&utm_medium=suremails-dashboard',
				'supportURL'               => 'https://suremails.com/contact/?utm_campaign=suremails&utm_medium=suremails-dashboard',
				'adminURL'                 => admin_url( 'options-general.php?page=' . SUREMAILS ),
			]
		);

		// Set the script translations.
		wp_set_script_translations( 'suremails-react-script', 'suremails', SUREMAILS_DIR . 'languages' );
	}

	/**
	 * Render the React application inside the SureMails settings page.
	 *
	 * @return void
	 */
	public function render_suremails_frontend() {
		echo '<div id="suremails-root-app"></div>';
	}

	/**
	 * Add a "Settings" link on the Plugins page.
	 *
	 * @param array $links Existing plugin action links.
	 * @return array Updated plugin action links with the "Settings" link.
	 */
	public function add_settings_link( array $links ) {
		$url           = admin_url( 'options-general.php?page=' . SUREMAILS );
		$settings_link = '<a href="' . esc_url( $url ) . '">' . __( 'Settings', 'suremails' ) . '</a>';
		$links[]       = $settings_link;

		return $links;
	}
}

// Instantiate the singleton instance of Plugin.
Plugin::instance();
