<?php

namespace SmashBalloon\YouTubeFeed\Services\Admin\Settings;

use Smashballoon\Customizer\Container;
use Smashballoon\Customizer\Feed_Builder;
use SmashBalloon\YouTubeFeed\Services\AdminAjaxService;
use SmashBalloon\YouTubeFeed\Helpers\Util;

class SetupPage extends BaseSettingPage
{

	protected $menu_slug = 'setup';
	protected $menu_title = 'Setup';
	protected $page_title = 'Setup';
	protected $has_menu = true;
	protected $template_file = 'settings.index';
	protected $has_assets = true;
	protected $menu_position = 0;
	protected $menu_position_free_version = 0;
	static $plugin_name = 'youtube';
	static $statues_name = 'sby_statuses';


	/**
	 * Filter settings object
	 * 
	 * @since 2.2.4
	 * 
	 * @return void
	 */
	public function register()
	{
		if(! sby_is_pro() && self::should_init_wizard() ) {
			parent::register();
			add_action( 'wp_ajax_sby_process_wizard', [ $this, 'ajax_process_wizard' ] );
			add_action( 'wp_ajax_sby_dismiss_wizard', [ $this, 'ajax_dismiss_wizard' ] );
			add_filter('sby_localized_settings', [$this, 'filter_settings_object']);
		}
	}

	/**
	 * Filter settings object
	 *
	 * @param array $settings
	 * 
	 * @since 2.2.4
	 * 
	 * @return array 
	 */
	public function filter_settings_object($settings)
	{
		$settings['onboardWizardYoutubeAccountConnectURL'] =  Feed_Builder::oauth_connet_url(admin_url('admin.php?page=youtube-feed-setup&step=2'));

		return $settings;
	}

	/**
	 * Check if we need to Init the Onboarding wizard
	 *
	 * @since 2.2.4
	 * 
	 * @return bool
	 */
	public static function should_init_wizard() {
		$statues = get_option( self::$statues_name, array() );
		if(!isset($statues['wizard_dismissed']) || $statues['wizard_dismissed'] === false ){
			return true;
		}
		return false;

	}

	/**
	 * Install Plugin
	 *
	 * @param string $plugin_url
	 * 
	 * @since 2.2.4
	 * 
	 * @return void
	 */
	public static function install_single_plugin( $plugin_url ){

		if( !$plugin_url || !current_user_can('install_plugins')  ){
			return false;
		}

		set_current_screen( 'youtube-feed-setup' );

		// Prepare variables.
		$url = esc_url_raw(
			add_query_arg(
				array(
					'page' => 'youtube-feed-setup',
				),
				admin_url( 'admin.php' )
			)
		);

	

		$creds = request_filesystem_credentials( $url, '', false, false, null );

		// Check for file system permissions.
		if ( false === $creds ) {
			wp_send_json_error( $error );
		}

		if ( ! WP_Filesystem( $creds ) ) {
			wp_send_json_error( $error );
		}

		/*
		 * We do not need any extra credentials if we have gotten this far, so let's install the plugin.
		 */
		require_once SBY_PLUGIN_DIR . 'inc/class-install-skin.php';

		// Do not allow WordPress to search/download translations, as this will break JS output.
		remove_action( 'upgrader_process_complete', array( 'Language_Pack_Upgrader', 'async_upgrade' ), 20 );

		// Create the plugin upgrader with our custom skin.
		$installer = new \SmashBalloon\YouTubeFeed\PluginSilentUpgrader( new \SmashBalloon\YouTubeFeed\SBY_Install_Skin() );

		// Error check.
		if ( ! method_exists( $installer, 'install' ) || empty( $plugin_url ) ) {
			wp_send_json_error( $error );
		}

		$installer->install( esc_url_raw( wp_unslash( $plugin_url ) ) );

		// Flush the cache and return the newly installed plugin basename.
		wp_cache_flush();

		$plugin_basename = $installer->plugin_info();

		if ( $plugin_basename ) {
			activate_plugin( $plugin_basename );
		}

	}


	/**
	 * Process Wizard Data
	 *	Save Settings, Install Plugins and more
	 *
	 * @since 2.2.4
	 * 
	 * @return void
	 */
	public function ajax_process_wizard(){

		Util::ajaxPreflightChecks();

		if( ! isset( $_POST['data'] ) ){
			wp_send_json_error();
		}

		// If in the future we need to support settings.
		// $sby_settings = get_option( 'sby_settings', array() );

		$onboarding_data = sanitize_text_field( stripslashes( $_POST['data'] ) );
		$onboarding_data  = json_decode( $onboarding_data, true);

		foreach (array_keys($onboarding_data) as $key) {
	
		// If in the future we need to support settings.
		// if( !empty($key) && 'settings' === $key ){
		// }

			if( $key && 'plugins' === $key && !empty($onboarding_data[$key]) && current_user_can( 'install_plugins' ) ){
				foreach ($onboarding_data[$key] as $single) {

					if(!empty($single['url']) && !empty($single['slug'])) {
						@self::install_single_plugin($single['url']);
						$this->disable_installed_plugins_redirect($single['slug']);
					}
				}
			}

		}
		
		// If in the future we need to support settings.
		//update_option( 'sby_settings', $sby_settings );

	}


	/**
	 * Dismiss Onboarding Wizard
	 *
	 * @since 2.2.4
	 * 
	 * @return void
	 */
	public function ajax_dismiss_wizard(){
		
		Util::ajaxPreflightChecks();

		$sby_statuses_option = get_option( 'sby_statuses', array() );
		$sby_statuses_option['wizard_dismissed'] = true;
		update_option( 'sby_statuses', $sby_statuses_option );
		wp_send_json_error();
	}

	/**
	 * Disable Installed Plugins Redirect
	 *
	 * @param string $plugin_slug
	 * 
	 * @since 2.2.4
	 * 
	 * @return void
	 */
	public function disable_installed_plugins_redirect($plugin_slug){
		//Monster Insight
		if( 'monsterinsights' === $plugin_slug ) {
			delete_transient( '_monsterinsights_activation_redirect' );
		}

		//All in one SEO
		if( 'aioseo' === $plugin_slug ) {
			update_option( 'aioseo_activation_redirect', true );
		}

		//WPForms
		if( 'wpforms' === $plugin_slug ) {
			update_option( 'wpforms_activation_redirect', true );
		}
		//Optin Monster
		if( 'optinmonster' === $plugin_slug ) {
			delete_transient( 'optin_monster_api_activation_redirect' );
			update_option( 'optin_monster_api_activation_redirect_disabled', true );
		}

		//Seed PROD
		if( 'seedprod' === $plugin_slug ) {
			update_option( 'seedprod_dismiss_setup_wizard', true );
		}

		//PushEngage
		if( 'pushengage' === $plugin_slug ) {
			delete_transient( 'pushengage_activation_redirect' );
		}

		//WP SMTP
		if( 'wp_mail_smtp' === $plugin_slug ) {
			delete_transient( 'wp_mail_smtp_activation_redirect' );
		}

		//Rafflepress
		if( 'rafflepress' === $plugin_slug ) {
			delete_transient( '_rafflepress_welcome_screen_activation_redirect' );
		}

		//Smash Plugin redirect remove
		$this->disable_smash_installed_plugins_redirect($plugin_slug);

	}

	/**
	 * Disable Smash Balloon Plugins Redirect
	 * 
	 * @param string $plugin_slug
	 * 
	 * @since 2.2.4
	 * 
	 * @return void
	 */
	public function disable_smash_installed_plugins_redirect($plugin_slug)
	{
		$smash_list = [
			'facebook' 		=> 'cff_plugin_do_activation_redirect',
			'instagram' 	=> 'sbi_plugin_do_activation_redirect',
			'youtube' 		=> 'sby_plugin_do_activation_redirect',
			'twitter' 		=> 'ctf_plugin_do_activation_redirect',
			'reviews' 		=> 'sbr_plugin_do_activation_redirect',
		];

		if(!empty($smash_list[self::$plugin_name])){
			unset($smash_list[self::$plugin_name]);
		}

		if (!empty($smash_list[$plugin_slug])) {
			delete_option($smash_list[$plugin_slug]);
		}
	}
}