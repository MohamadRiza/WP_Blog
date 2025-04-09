<?php
/**
 * Onbaording Wizard
 *
 *
 * @since 6.3
 */
namespace CustomFacebookFeed\Admin;
use CustomFacebookFeed\Admin\Traits\CFF_Settings;
use CustomFacebookFeed\Builder\CFF_Feed_Builder;
use CustomFacebookFeed\Builder\CFF_Feed_Saver_Manager;
use CustomFacebookFeed\Builder\CFF_Source;

if(!defined('ABSPATH'))	exit;

class CFF_Onboarding_Wizard extends CFF_Feed_Builder
{

	use CFF_Settings;

	static $plugin_name = 'facebook';
	static $current_version = CFF_DBVERSION;
	static $target_version = '2.4';
	static $statues_name = 'cff_statuses';

	public function __construct(){
		$this->init();
	}

	/**
	 * Init Setup Dashboard.
	 *
	 * @since 6.0
	 */
	public function init() {
		if (is_admin()) {
			add_action( 'admin_menu', array( $this, 'register_menu' ) );
			// add ajax listeners
			CFF_Feed_Saver_Manager::hooks();
			CFF_Source::hooks();
			self::hooks();
			$this->ajax_hooks();
		}
	}

	public function ajax_hooks() {
		add_action( 'wp_ajax_cff_feed_saver_manager_process_wizard', array( $this , 'process_wizard_data' ) );
		add_action( 'wp_ajax_cff_feed_saver_manager_dismiss_wizard', array( $this , 'dismiss_wizard' ) );

	}

	/**
	 * Check if we need to Init the Onboarding wizard
	 *
	 * @since 6.0
	 */
	public static function should_init_wizard() {
		$statues = get_option( self::$statues_name, array() );
		if(!isset($statues['wizard_dismissed']) || $statues['wizard_dismissed'] === false ){
			return true;
		}
		return false;

	}



	/**
	 * Wizard Wrapper.
	 *
	 * @since 6.0
	 */
	public function feed_builder() {
		include_once CFF_BUILDER_DIR . 'templates/wizard.php';
	}

	/**
	 * Register Menu.
	 *
	 * @since 6.0
	 */
	public function register_menu() {
		$cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
		$cap = apply_filters( 'cff_settings_pages_capability', $cap );

		$submenu = self::should_init_wizard() ? 'cff-top' : 'sb';

		$feed_builder = add_submenu_page(
			$submenu,
			__( 'Setup', 'custom-facebook-feed' ),
			__( 'Setup', 'custom-facebook-feed' ),
			$cap,
			'cff-setup',
			array( $this, 'feed_builder' ),
			0
		);
		add_action( 'load-' . $feed_builder, array( $this, 'builder_enqueue_admin_scripts' ) );
	}


	/**
	 * Onboarding Wizard Content & Steps
	 *
	 * @return array
	 *
	 * @since 6.X
	 */
	public static function get_onboarding_wizard_content()
	{

		$data =  [
			'heading' => __( 'Smash Balloon', 'custom-facebook-feed' ),
			'subheading' => __( 'Facebook Feed by', 'custom-facebook-feed' ),
			'logo' => CFF_BUILDER_URL . 'assets/img/facebook.png',
			'balloon' => CFF_BUILDER_URL . 'assets/img/balloon.png',
			'balloon1' => CFF_BUILDER_URL . 'assets/img/balloon-1.png',
			'userIcon' => CFF_BUILDER_URL . 'assets/img/user.png',
			'saveSettings' => [ 'featuresList', 'pluginsList' ],
			'successMessages' => [
				'connectAccount' => __( 'Connect a Facebook Account!', 'custom-facebook-feed' ),
				'setupFeatures' => __( 'Setup Features!', 'custom-facebook-feed' ),
				'feedPlugins' => __( 'Feed plugins for # installed', 'custom-facebook-feed' )
			],
			'steps' => [
				[
					'id' 		=> 'welcome',
					'template'	=> CFF_BUILDER_DIR . 'templates/onboarding/welcome.php',
					'heading' => __( 'Let\'s set up your plugin!', 'custom-facebook-feed' ),
					'description' => __( 'Ready to add a dash of Facebook to your website? Setting up your first feed is quick and easy. We\'ll get you up and running in no time.', 'custom-facebook-feed' ),
					'button' => __( 'Launch the Setup Wizard', 'custom-facebook-feed' ),
					'img' => CFF_BUILDER_URL . 'assets/img/waving-hand.png',
					'banner' => CFF_BUILDER_URL . 'assets/img/onboarding-banner.jpg',

				],
				[
					'id' 		=> 'add-source',
					'template'	=> CFF_BUILDER_DIR . 'templates/onboarding/add-source.php',
					'heading' => __( 'Connect your Facebook Account', 'custom-facebook-feed' ),
					'smallHeading' => __( 'STEP 1', 'custom-facebook-feed' ),
				],
				[
					'id' 		=> 'configure-features',
					'template'	=> CFF_BUILDER_DIR . 'templates/onboarding/configure-features.php',
					'heading' => __( 'Configure features', 'custom-facebook-feed' ),
					'smallHeading' => __( 'STEP 2', 'custom-facebook-feed' ),
					'featuresList' => [
						[
							'heading' => __( 'Facebook User Feed', 'custom-facebook-feed' ),
							'description' => __( 'Create and display Facebook feeds from connected accounts', 'custom-facebook-feed' ),
							'color'	=> 'green',
							'active'	=> true,
							'uncheck'	=> true,
							'icon' 		=> '<svg fill="#696D80" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"><path d="M256,0C114.9,0,0,114.9,0,256s114.9,256,256,256s256-114.9,256-256S397.1,0,256,0z M256,42.7 c118.1,0,213.3,95.3,213.3,213.3c0,107.3-78.7,195.4-181.6,210.8V318.2h60.8l9.5-61.7h-70.3v-33.7c0-25.6,8.4-48.4,32.4-48.4h38.5 v-53.8c-6.8-0.9-21.1-2.9-48.1-2.9c-56.5,0-89.6,29.8-89.6,97.8v41h-58.1v61.7h58.1v148C119.7,449.5,42.7,362.1,42.7,256 C42.7,137.9,137.9,42.7,256,42.7z"/></svg>'
						],
						[
							'data' => [
								'id' => 'enable_email_report',
								'type' => 'settings'
							],
							'heading' => __( 'Downtime Prevention', 'custom-facebook-feed' ),
							'description' => __( 'Prevent downtime in the event your feed is unable to update', 'custom-facebook-feed' ),
							'color'	=> 'green',
							'active'	=> true,
							'uncheck'	=> true,
							'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_4085_38049)"><path d="M18.9999 16.9003C20.2151 16.6536 21.2952 15.9641 22.0306 14.9658C22.766 13.9674 23.1043 12.7315 22.9796 11.4978C22.855 10.2641 22.2765 9.12077 21.3563 8.28967C20.4361 7.45858 19.2399 6.99905 17.9999 7.0003H16.7399C16.4086 5.71762 15.764 4.53729 14.8638 3.56529C13.9637 2.59328 12.8363 1.86003 11.5828 1.43136C10.3292 1.0027 8.98891 0.892032 7.68207 1.10931C6.37523 1.32658 5.1428 1.865 4.09544 2.67621C3.04808 3.48742 2.21856 4.54604 1.68137 5.75701C1.14418 6.96799 0.916124 8.29341 1.01769 9.61429C1.11925 10.9352 1.54725 12.2102 2.26326 13.3248C2.97926 14.4394 3.96087 15.3587 5.11993 16.0003" stroke="#696D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M13 11L9 17H15L11 23" stroke="#696D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></g><defs><clipPath id="clip0_4085_38049"><rect width="24" height="24" fill="white"/></clipPath></defs></svg>'
						],
						[
							'data' => [
								'id' => 'cff_locale',
								'type' => 'settings',
								'value'	=> get_option('cff_locale', 'en_US')
							],
							'options' => CFF_Onboarding_Wizard::locales(),
							'type' => 'select',
							'heading' => __( 'Localization', 'custom-facebook-feed' ),
							'description' => __( 'This controls the language of any predefined text strings provided by Facebook.', 'custom-facebook-feed' ),
							'color'	=> 'green',
							'active'	=> true,
							'icon' => '<svg fill="none" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="m3 5h12m-6-2v2m1.0482 9.5c-1.52737-1.5822-2.76747-3.4435-3.63633-5.5m6.08813 9h7m-8.5 3 5-10 5 10m-8.2489-16c-.968 5.7702-4.68141 10.6095-9.7511 13.129" stroke="#696D80" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>'
						]

					],
					'proFeaturesList' => [
						[
							'heading' => __( 'Images and Videos', 'custom-facebook-feed' ),
							'description' => __( 'Display images and play videos from your Facebook posts.', 'custom-facebook-feed' ),
							'uncheck'	=> true,
							'active'	=> false,
							'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 9H20" stroke="#8C8F9A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 15H20" stroke="#8C8F9A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M10 3L8 21" stroke="#8C8F9A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M16 3L14 21" stroke="#8C8F9A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>'
						],
						[
							'heading' => __( 'Albums, Events, and More', 'custom-facebook-feed' ),
							'description' => __( 'Create feeds from your albums page. Show upcoming and past events.', 'custom-facebook-feed' ),
							'uncheck'	=> true,
							'active'	=> false,
							'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 16C14.2091 16 16 14.2091 16 12C16 9.79086 14.2091 8 12 8C9.79086 8 8 9.79086 8 12C8 14.2091 9.79086 16 12 16Z" stroke="#696D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M16 8.00036V13.0004C16 13.796 16.3161 14.5591 16.8787 15.1217C17.4413 15.6843 18.2044 16.0004 19 16.0004C19.7957 16.0004 20.5587 15.6843 21.1213 15.1217C21.6839 14.5591 22 13.796 22 13.0004V12.0004C21.9999 9.74339 21.2362 7.55283 19.8333 5.78489C18.4303 4.01694 16.4706 2.77558 14.2726 2.26265C12.0747 1.74973 9.76794 1.9954 7.72736 2.95972C5.68677 3.92405 4.03241 5.55031 3.03327 7.57408C2.03413 9.59785 1.74898 11.9001 2.22418 14.1065C2.69938 16.3128 3.90699 18.2936 5.65064 19.7266C7.39429 21.1597 9.57144 21.9607 11.8281 21.9995C14.0847 22.0383 16.2881 21.3126 18.08 19.9404" stroke="#696D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>'
						],
						[
							'heading' => __( 'Load More Posts', 'custom-facebook-feed' ),
							'description' => __( 'Visitors can load more posts to see more of your content.', 'custom-facebook-feed' ),
							'uncheck'	=> true,
							'active'	=> false,
							'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8 3H5C4.46957 3 3.96086 3.21071 3.58579 3.58579C3.21071 3.96086 3 4.46957 3 5V8M21 8V5C21 4.46957 20.7893 3.96086 20.4142 3.58579C20.0391 3.21071 19.5304 3 19 3H16M16 21H19C19.5304 21 20.0391 20.7893 20.4142 20.4142C20.7893 20.0391 21 19.5304 21 19V16M3 16V19C3 19.5304 3.21071 20.0391 3.58579 20.4142C3.96086 20.7893 4.46957 21 5 21H8" stroke="#696D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>'
						],
					]
				],
				[
					'id' 		=> 'install-plugins',
					'template'	=> CFF_BUILDER_DIR . 'templates/onboarding/install-plugins.php',
					'heading' => __( 'You might also be interested in...', 'custom-facebook-feed' ),
					'description' => __( 'Facebook Feed users also install these plugins', 'custom-facebook-feed' ),
					'pluginsList' => self::get_awesomemotive_plugins(),
					'star_icons' => CFF_PLUGIN_URL . 'admin/assets/img/stars.svg'
				],
				[
					'id' 		=> 'success-page',
					'template'	=> CFF_BUILDER_DIR . 'templates/onboarding/success-page.php',
					'heading' => __( 'Awesome. You are all set up!', 'custom-facebook-feed' ),
					'description' => __( 'Here\'s an overview of everything that is setup', 'custom-facebook-feed' ),
					'upgradeContent' => [
						'heading' => __( 'Upgrade to unlock hashtag feeds, tagged feeds, a popup lightbox and more', 'custom-facebook-feed' ),
						'description' => __( 'To unlock these features and much more, upgrade to Pro and enter your license key below.', 'custom-facebook-feed' ),
						'button' => [
							'text' => __( 'Upgrade to Facebook Feed Pro', 'custom-facebook-feed' ),
							'link'	=> 'https://smashballoon.com/pricing/facebook-feed/?license_key&upgrade=true&utm_campaign=facebook-free&utm_source=setup&utm_medium=upgrade-license'
						],
						'upgradeCouppon' => sprintf(
							__( 'Upgrade today and %ssave 50%% on a Pro License!%s%s (auto-applied at checkout)', 'custom-facebook-feed' ),
							'<strong>',
							'</strong>',
							'<br>'
						),
						'banner' => CFF_BUILDER_URL . 'assets/img/success-banner.jpg',

						'upgradeFeaturesList' => [
							[
								'heading' => __( 'Hashtag Feeds', 'custom-facebook-feed' ),
								'icon'	=> '<svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.66797 6.5H13.3346" stroke="#0068A0" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M2.66797 10.5H13.3346" stroke="#0068A0" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M6.66536 2.5L5.33203 14.5" stroke="#0068A0" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M10.6654 2.5L9.33203 14.5" stroke="#0068A0" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>'
							],
							[
								'heading' => __( 'Tagged Feeds', 'custom-facebook-feed' ),
								'icon'	=> '<svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.9987 11.1663C9.47146 11.1663 10.6654 9.97243 10.6654 8.49967C10.6654 7.02692 9.47146 5.83301 7.9987 5.83301C6.52594 5.83301 5.33203 7.02692 5.33203 8.49967C5.33203 9.97243 6.52594 11.1663 7.9987 11.1663Z" stroke="#E34F0E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M10.6654 5.83357V9.1669C10.6654 9.69734 10.8761 10.206 11.2512 10.5811C11.6262 10.9562 12.1349 11.1669 12.6654 11.1669C13.1958 11.1669 13.7045 10.9562 14.0796 10.5811C14.4547 10.206 14.6654 9.69734 14.6654 9.1669V8.50024C14.6653 6.99559 14.1562 5.53522 13.2209 4.35659C12.2856 3.17796 10.9791 2.35039 9.5138 2.00844C8.04852 1.66648 6.51066 1.83027 5.15027 2.47315C3.78988 3.11603 2.68697 4.20021 2.02088 5.54939C1.35478 6.89856 1.16468 8.4334 1.48148 9.90431C1.79828 11.3752 2.60335 12.6957 3.76579 13.6511C4.92823 14.6064 6.37966 15.1405 7.88408 15.1663C9.38851 15.1922 10.8574 14.7084 12.052 13.7936" stroke="#E34F0E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>'
							],
							[
								'heading' => __( 'Lightbox', 'custom-facebook-feed' ),
								'icon'	=> '<svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.33333 2.5H3.33333C2.97971 2.5 2.64057 2.64048 2.39052 2.89052C2.14048 3.14057 2 3.47971 2 3.83333V5.83333M14 5.83333V3.83333C14 3.47971 13.8595 3.14057 13.6095 2.89052C13.3594 2.64048 13.0203 2.5 12.6667 2.5H10.6667M10.6667 14.5H12.6667C13.0203 14.5 13.3594 14.3595 13.6095 14.1095C13.8595 13.8594 14 13.5203 14 13.1667V11.1667M2 11.1667V13.1667C2 13.5203 2.14048 13.8594 2.39052 14.1095C2.64057 14.3595 2.97971 14.5 3.33333 14.5H5.33333" stroke="#CC7A00" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>'
							],
							[
								'heading' => __( 'And many more', 'custom-facebook-feed' ),
								'icon'	=> '<svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.9987 9.16634C8.36689 9.16634 8.66536 8.86786 8.66536 8.49967C8.66536 8.13148 8.36689 7.83301 7.9987 7.83301C7.63051 7.83301 7.33203 8.13148 7.33203 8.49967C7.33203 8.86786 7.63051 9.16634 7.9987 9.16634Z" fill="#434960" stroke="#434960" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M12.6667 9.16634C13.0349 9.16634 13.3333 8.86786 13.3333 8.49967C13.3333 8.13148 13.0349 7.83301 12.6667 7.83301C12.2985 7.83301 12 8.13148 12 8.49967C12 8.86786 12.2985 9.16634 12.6667 9.16634Z" fill="#434960" stroke="#434960" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M3.33464 9.16634C3.70283 9.16634 4.0013 8.86786 4.0013 8.49967C4.0013 8.13148 3.70283 7.83301 3.33464 7.83301C2.96645 7.83301 2.66797 8.13148 2.66797 8.49967C2.66797 8.86786 2.96645 9.16634 3.33464 9.16634Z" fill="#434960" stroke="#434960" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>'
							]
						]
					]
				],

			]
		];

		$dynamic_features_list = self::get_dynamic_features_list();

		if( isset($data['steps']) && sizeof($dynamic_features_list) > 0){
			$key_cf_ft = array_search('configure-features', array_column($data['steps'], 'id'));
			if( $key_cf_ft !== false){
				$new_features_lit = array_merge($data['steps'][$key_cf_ft]['featuresList'] , $dynamic_features_list) ;
				$data['steps'][$key_cf_ft]['featuresList'] = $new_features_lit;
			}
		}

		return $data;
	}


	/**
	 * Return Dynamic Features List depending on multiple criteria
	 *
	 * @return array
	 *
	 * @since 6.X
	 */
	public static function get_dynamic_features_list()
	{
		$features_list = [];
		$smash_plugin_list = self::get_smash_plugins_list();
		if( isset($smash_plugin_list['plugins']) && sizeof($smash_plugin_list['plugins']) > 0 ){
			$description_plugins = implode(', ', $smash_plugin_list['text']);
			$search = ',';
			$description_plugins_text = strrev(preg_replace(strrev("/$search/"),strrev(' and '),strrev($description_plugins),1));

			$plugins_info = [];
			foreach ($smash_plugin_list['plugins'] as $p_item) {
				if ($p_item['is_istalled'] === false) {
					array_push(
						$plugins_info,
						$p_item
					);
				}
			}

			array_push($features_list,
				[
					'data' => [
						'id' => $description_plugins,
						'type' => 'install_plugins',
						'plugins' => 'smash'
					],
					'heading' => __('Social Feed Collection', 'custom-facebook-feed'),
					'description' => __('Install' , 'custom-facebook-feed') . ' ' . $description_plugins_text . ' ' . __('feed plugins for more fresh content', 'custom-facebook-feed' ),
					'color'	=> 'blue',
					'active'	=> true,
					'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M21 15.9999V7.9999C20.9996 7.64918 20.9071 7.30471 20.7315 7.00106C20.556 6.69742 20.3037 6.44526 20 6.2699L13 2.2699C12.696 2.09437 12.3511 2.00195 12 2.00195C11.6489 2.00195 11.304 2.09437 11 2.2699L4 6.2699C3.69626 6.44526 3.44398 6.69742 3.26846 7.00106C3.09294 7.30471 3.00036 7.64918 3 7.9999V15.9999C3.00036 16.3506 3.09294 16.6951 3.26846 16.9987C3.44398 17.3024 3.69626 17.5545 4 17.7299L11 21.7299C11.304 21.9054 11.6489 21.9979 12 21.9979C12.3511 21.9979 12.696 21.9054 13 21.7299L20 17.7299C20.3037 17.5545 20.556 17.3024 20.7315 16.9987C20.9071 16.6951 20.9996 16.3506 21 15.9999Z" stroke="#696D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M3.26953 6.95996L11.9995 12.01L20.7295 6.95996" stroke="#696D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 22.08V12" stroke="#696D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
					'plugins' => $plugins_info,
					'tooltip' => __('Smash Balloon offers the best social media feed solutions for WordPress. Enabling this feature will install additional plugins to power all of the supported platforms', 'custom-facebook-feed' ),
				]
			);
		}


		//Reviews Plugin
		$reviews_plugin = self::get_smash_reviews_plugin();
		if($reviews_plugin !== false) {
			array_push($features_list, $reviews_plugin);
		}

		return $features_list;

	}


	/**
	 * Return Uninstalled SmashBalloon Plugins
	 *
	 * @return array
	 *
	 * @since 6.X
	 */
	public static function get_smash_plugins_list(){
		$installed_plugins = get_plugins();

		// check whether the pro or free plugins are installed
		$is_facebook_installed = false;
		$facebook_plugin = 'custom-facebook-feed/custom-facebook-feed.php';
		if ( isset( $installed_plugins['custom-facebook-feed-pro/custom-facebook-feed.php'] ) ) {
			$is_facebook_installed = true;
			$facebook_plugin = 'custom-facebook-feed-pro/custom-facebook-feed.php';
		} elseif ( isset( $installed_plugins['custom-facebook-feed/custom-facebook-feed.php'] ) ) {
			$is_facebook_installed = true;
		}

		$is_instagram_installed = false;
		$instagram_plugin = 'instagram-feed/instagram-feed.php';
		if ( isset( $installed_plugins['instagram-feed-pro/instagram-feed.php'] ) ) {
			$is_instagram_installed = true;
			$instagram_plugin = 'instagram-feed-pro/instagram-feed.php';
		} elseif ( isset( $installed_plugins['instagram-feed/instagram-feed.php'] ) ) {
			$is_instagram_installed = true;
		}

		$is_twitter_installed = false;
		$twitter_plugin = 'custom-twitter-feeds/custom-twitter-feed.php';
		if ( isset( $installed_plugins['custom-twitter-feeds-pro/custom-twitter-feed.php'] ) ) {
			$is_twitter_installed = true;
			$twitter_plugin = 'custom-twitter-feeds-pro/custom-twitter-feed.php';
		} elseif ( isset( $installed_plugins['custom-twitter-feeds/custom-twitter-feed.php'] ) ) {
			$is_twitter_installed = true;
		}

		$is_youtube_installed = false;
		$youtube_plugin = 'feeds-for-youtube/youtube-feed.php';
		if ( isset( $installed_plugins['youtube-feed-pro/youtube-feed.php'] ) ) {
			$is_youtube_installed = true;
			$youtube_plugin = 'youtube-feed-pro/youtube-feed.php';
		} elseif ( isset( $installed_plugins['feeds-for-youtube/youtube-feed.php'] ) ) {
			$is_youtube_installed = true;
		}

		$is_tiktok_installed = false;
		$tiktok_plugin = 'feeds-for-tiktok/feeds-for-tiktok.php';
		if ( isset( $installed_plugins['tiktok-feeds-pro/tiktok-feeds-pro.php'] ) ) {
			$is_tiktok_installed = true;
			$tiktok_plugin = 'tiktok-feeds-pro/tiktok-feeds-pro.php';
		} elseif ( isset( $installed_plugins['feeds-for-tiktok/feeds-for-tiktok.php'] ) ) {
			$is_tiktok_installed = true;
		}

		$smash_list =  [
			'text' => [],
			'plugins' => [
				[
					'type' => 'instagram',
					'is_istalled' => $is_instagram_installed,
					'download_link' => $instagram_plugin,
					'min_php' => '5.6.0',
					'icon' => CFF_PLUGIN_URL . 'admin/assets/img/insta-icon.svg'
				],
				[
					'type' => 'facebook',
					'is_istalled' => $is_facebook_installed,
					'download_link' => $facebook_plugin,
					'min_php' => '5.6.0',
					'icon' => CFF_PLUGIN_URL . 'admin/assets/img/fb-icon.svg'
				],
				[
					'type' => 'twitter',
					'is_istalled' => $is_twitter_installed,
					'download_link' => $twitter_plugin,
					'min_php' => '5.6.0',
					'icon' => CFF_PLUGIN_URL . 'admin/assets/img/twitter-icon.svg'

				],
				[
					'type' => 'youtube',
					'is_istalled' => $is_youtube_installed,
					'download_link' => $youtube_plugin,
					'min_php' => '5.6.0',
					'icon' => CFF_PLUGIN_URL . 'admin/assets/img/youtube-icon.svg'
				],
				[
					'type' => 'tiktok',
					'is_istalled' => $is_tiktok_installed,
					'download_link' => $tiktok_plugin,
					'min_php' => '7.0',
					'icon' => CFF_PLUGIN_URL . 'admin/assets/img/tiktok-icon.svg'
				]
			]
		];
		foreach ($smash_list['plugins'] as $mash_plugin) {
			if( version_compare( PHP_VERSION , $mash_plugin['min_php'] , '<' ) ){
				$mash_plugin['is_istalled'] = true;
			}
			if( $mash_plugin['type'] === self::$plugin_name || $mash_plugin['is_istalled'] === true){
				unset($mash_plugin);
			}else{
				array_push($smash_list['text'], ucfirst($mash_plugin['type']));
			}
		}

		return $smash_list;
	}


	/**
	 * Return Reviews Plugin if not Installed
	 *
	 * @return array
	 *
	 * @since 6.X
	 */
	public static function get_smash_reviews_plugin(){
		$installed_plugins = get_plugins();
		$min_php = '7.1';

		$is_reviews_installed = false;
		$reviews_plugin = 'reviews-feed/sb-reviews.php';
		if ( isset( $installed_plugins['reviews-feed-pro/sb-reviews-pro.php'] ) ) {
			$is_reviews_installed = true;
			$reviews_plugin = 'reviews-feed-pro/sb-reviews-pro.php';
		} elseif ( isset( $installed_plugins['reviews-feed/sb-reviews.php'] ) ) {
			$is_reviews_installed = true;
		}

		if( version_compare( PHP_VERSION , $min_php , '<' )  ){
			$is_reviews_installed = true;
		}

		if( $is_reviews_installed === false ){
			return [
				'data' => [
					'id' => 'reviews',
					'type' => 'install_plugins'
				],
				'heading' => __('Customer Reviews Plugin', 'custom-facebook-feed'),
				'description' => __('Install Reviews Feed to display customer reviews from Google or Yelp and build trust', 'custom-facebook-feed'),
				'color'	=> 'blue',
				'active'	=> true,
				'icon' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M19 9.50003C19.0034 10.8199 18.6951 12.1219 18.1 13.3C17.3944 14.7118 16.3098 15.8992 14.9674 16.7293C13.6251 17.5594 12.0782 17.9994 10.5 18C9.18013 18.0035 7.87812 17.6951 6.7 17.1L1 19L2.9 13.3C2.30493 12.1219 1.99656 10.8199 2 9.50003C2.00061 7.92179 2.44061 6.37488 3.27072 5.03258C4.10083 3.69028 5.28825 2.6056 6.7 1.90003C7.87812 1.30496 9.18013 0.996587 10.5 1.00003H11C13.0843 1.11502 15.053 1.99479 16.5291 3.47089C18.0052 4.94699 18.885 6.91568 19 9.00003V9.50003Z" stroke="#696D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
				'plugins' => [
					[
						'type' => 'reviews',
						'is_istalled' => $is_reviews_installed,
						'download_link' => $reviews_plugin,
						'min_php' => $min_php,
						'icon' => CFF_PLUGIN_URL . 'admin/assets/img/reviews-icon.svg'
					]
				],
				'tooltip' => __('Enabling this feature will install Reviews Feed plugin. Reviews Feed by Smash Balloon helps users to display reviews from Google, TripAdvisor, TrustPilot and more.', 'custom-facebook-feed'),
			];
		}
		return false;

	}


	/**
	 * Return Awesome Motive Plugins
	 *
	 * @return array
	 *
	 * @since 6.X
	 */
	public static function get_awesomemotive_plugins(){
		$installed_plugins = get_plugins();

		$awesomemotive_plugins_list =  [
			[
				'plugin' => 'allinoneseo',
				'data' => [
					'type' => 'install_plugins',
					'id' => 'allinoneseo',
					'pluginName' => __('All in One SEO', 'custom-facebook-feed' ),
				],
				'heading' => __( 'All in One SEO Toolkit', 'custom-facebook-feed' ),
				'description' => __( 'Out-of-the-box SEO for WordPress. Features like XML Sitemaps, SEO for custom post types, SEO for blogs, business sites, or ecommerce sites, and much more.', 'custom-facebook-feed' ),
				'color'	=> 'blue',
				'active'	=> true,
				'icon' => CFF_BUILDER_URL . 'assets/img/allinoneseo.png',
				'installs_number' => '3 Million+ Installs'
			],
			[
				'plugin' => 'monsterinsight',
				'data' => [
					'type' => 'install_plugins',
					'id' => 'monsterinsight',
					'pluginName' => __('Monster Insights', 'custom-facebook-feed' ),
				],
				'heading' => __( 'Analytics by MonsterInsights', 'custom-facebook-feed' ),
				'description' => __( 'Make it “effortless” to connect your WordPress site with Google Analytics, so you can start making data-driven decisions to grow your business.', 'custom-facebook-feed' ),
				'color'	=> 'blue',
				'active'	=> true,
				'icon' => CFF_BUILDER_URL . 'assets/img/monsterinsight.png',
				'installs_number' => '3 Million+ Installs'
			],
			[
				'plugin' => 'wpforms',
				'data' => [
					'type' => 'install_plugins',
					'id' => 'wpforms',
					'pluginName' => __('WPForms', 'custom-facebook-feed' ),
				],
				'heading' => __( 'Forms by WPForms', 'custom-facebook-feed' ),
				'description' => __( 'Create contact, subscription or payment forms with the most beginner friendly drag & drop WordPress forms plugin', 'custom-facebook-feed' ),
				'color'	=> 'blue',
				'active'	=> true,
				'icon' => CFF_BUILDER_URL . 'assets/img/wpforms.png',
				'installs_number' => '5 Million+ Installs'
			],
			[
				'plugin' => 'seedprod',
				'data' => [
					'type' => 'install_plugins',
					'id' => 'seedprod',
					'pluginName' => __('SeedProd', 'custom-facebook-feed' ),
				],
				'heading' => __( 'SeedProd Website Builder', 'custom-facebook-feed' ),
				'description' => __( 'A simple and powerful theme builder, landing page builder, "coming soon" page builder, and maintenance mode notice builder', 'custom-facebook-feed' ),
				'color'	=> 'blue',
				'active'	=> true,
				'icon' => CFF_BUILDER_URL . 'assets/img/seedprod.png',
				'installs_number' => '900 Thousand+ Installs'
			],
			[
				'plugin' => 'optinmonster',
				'data' => [
					'type' => 'install_plugins',
					'id' => 'optinmonster',
					'pluginName' => __('OptinMonster', 'custom-facebook-feed' ),
				],
				'heading' => __( 'OptinMonster Popup Builder', 'custom-facebook-feed' ),
				'description' => __( 'Make popups & opt-in forms to build your email newsletter subscribers, generate leads, and close sales', 'custom-facebook-feed' ),
				'color'	=> 'blue',
				'active'	=> true,
				'icon' => CFF_BUILDER_URL . 'assets/img/optinmonster.png',
				'installs_number' => '1 Million+ Installs'
			],
			[
				'plugin' => 'pushengage',
				'data' => [
					'type' => 'install_plugins',
					'id' => 'pushengage',
					'pluginName' => __('PushEngage', 'custom-facebook-feed' ),
				],
				'heading' => __( 'PushEngage Notifications', 'custom-facebook-feed' ),
				'description' => __( 'Create and send high-converting web push notifications to your website visitors.', 'custom-facebook-feed' ),
				'color'	=> 'blue',
				'active'	=> true,
				'icon' => CFF_BUILDER_URL . 'assets/img/pushengage.svg',
				'installs_number' => '10 Thousand+ Installs'
			]
		];

		$available_plugins = [];
		foreach ($awesomemotive_plugins_list  as $plugin) {
			if( !self::check_awesome_motive_plugin( $plugin['plugin'], $installed_plugins ) ){
				array_push($available_plugins, $plugin);
			}
		}
		return array_slice($available_plugins , 0, 3);
	}

	/**
	 * Check if AWESOME MOTIVE Plugin
	 *
	 * @return boolean
	 *
	 * @since 6.X
	 */
	public static function check_awesome_motive_plugin( $plugin, $installed_plugins ){

		switch ($plugin) {
			case 'allinoneseo':
				if (
					isset( $installed_plugins['all-in-one-seo-pack/all_in_one_seo_pack.php'] )
					|| isset( $installed_plugins['all-in-one-seo-pack-pro/all_in_one_seo_pack.php'] )
				) {
					return true;
				}
				return false;
			case 'monsterinsight':
				if (
					isset( $installed_plugins['google-analytics-for-wordpress/googleanalytics.php'] )
					|| isset( $installed_plugins['google-analytics-premium/googleanalytics-premium.php'] )
				) {
					return true;
				}
				return false;
			case 'wpforms':
				if (
					isset( $installed_plugins['wpforms-lite/wpforms.php'] )
					|| isset( $installed_plugins['wpforms/wpforms.php'] )
				) {
					return true;
				}
				return false;
			case 'seedprod':
				if (
					isset( $installed_plugins['coming-soon/coming-soon.php'] )
				) {
					return true;
				}
				return false;
			case 'optinmonster':
				if (
					isset( $installed_plugins['optinmonster/optin-monster-wp-api.php'] )
				) {
					return true;
				}
				return false;
			case 'pushengage':
				if (
					isset( $installed_plugins['pushengage/main.php'] )
				) {
					return true;
				}
				return false;
		}

	}
	/**
	 * Get Plugin Download
	 *
	 *
	 * @since 6.X
	 */
	public static function get_plugin_download_link( $plugin_name ){
		$plugin_download = false;
		switch ( strtolower($plugin_name) ) {
			case 'facebook':
				$plugin_download = 'https://downloads.wordpress.org/plugin/custom-facebook-feed.zip';
				break;
			case 'instagram':
				$plugin_download = 'https://downloads.wordpress.org/plugin/instagram-feed.zip';
				break;
			case 'twitter':
				$plugin_download = 'https://downloads.wordpress.org/plugin/custom-twitter-feeds.zip';
				break;
			case 'youtube':
				$plugin_download = 'https://downloads.wordpress.org/plugin/feeds-for-youtube.zip';
				break;
			case 'tiktok':
				$plugin_download = 'https://downloads.wordpress.org/plugin/feeds-for-tiktok.zip';
				break;
			case 'reviews':
				$plugin_download = 'https://downloads.wordpress.org/plugin/reviews-feed.zip';
				break;
			case 'allinoneseo':
				$plugin_download = 'https://downloads.wordpress.org/plugin/all-in-one-seo-pack.zip';
				break;
			case 'monsterinsight':
				$plugin_download = 'https://downloads.wordpress.org/plugin/google-analytics-for-wordpress.zip';
				break;
			case 'wpforms':
				$plugin_download = 'https://downloads.wordpress.org/plugin/wpforms-lite.zip';
				break;
			case 'seedprod':
				$plugin_download = 'https://downloads.wordpress.org/plugin/coming-soon.zip';
				break;
			case 'optinmonster':
				$plugin_download = 'https://downloads.wordpress.org/plugin/optinmonster.zip';
				break;
			case 'pushengage':
				$plugin_download = 'https://downloads.wordpress.org/plugin/pushengage.zip';
				break;
		}
		return $plugin_download;
	}
	/**
	 * Install Plugin
	 *
	 *
	 * @since 6.X
	 */
	public static function install_single_plugin( $plugin_name ){
		$plugin_download = self::get_plugin_download_link( strtolower( str_replace(' ', '', $plugin_name) ) );
		if( $plugin_download === false || !current_user_can ('install_plugins')  ){
			return false;
		}


		if ( strpos( $plugin_download , 'https://downloads.wordpress.org/plugin/' ) !== 0 ) {
			return false;
		}

		set_current_screen( 'cff-feed-builder' );
		// Prepare variables.
		$url = esc_url_raw(
			add_query_arg(
				array(
					'page' => 'cff-feed-builder',
				),
				admin_url( 'admin.php' )
			)
		);

			$creds = request_filesystem_credentials( $url, '', false, false, null );
		// Check for file system permissions.
		if ( false === $creds || ! WP_Filesystem( $creds ) ) {
			return false;
		}
		// Do not allow WordPress to search/download translations, as this will break JS output.
		remove_action( 'upgrader_process_complete', array( 'Language_Pack_Upgrader', 'async_upgrade' ), 20 );

		// Create the plugin upgrader with our custom skin.
		$installer = new \CustomFacebookFeed\Helpers\PluginSilentUpgrader( new \CustomFacebookFeed\Admin\CFF_Install_Skin() );

		// Error check.
		if ( ! method_exists( $installer, 'install' ) || empty( $plugin_download ) ) {
			wp_send_json_error( $error );
		}

		$installer->install( esc_url_raw( wp_unslash( $plugin_download ) ) );

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
	 * @since 6.0.8
	 */
	public function process_wizard_data(){
		if( ! isset( $_POST['data'] ) ){
			wp_send_json_error();
		}

		check_ajax_referer( 'cff-admin' , 'nonce');
		$cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
		$cap = apply_filters( 'cff_settings_pages_capability', $cap );
		if ( ! current_user_can( $cap ) ) {
			wp_send_json_error(); // This auto-dies.
		}

		$cff_settings = get_option( 'cff_style_settings', array() );

		$onboarding_data = sanitize_text_field( stripslashes( $_POST['data'] ) );
		$onboarding_data  = json_decode( $onboarding_data, true);
		foreach ($onboarding_data  as $single_data) {
			if( $single_data['type'] === 'settings'){

				if( isset( $single_data['value'] )  ){
					if( $single_data['id'] === 'cff_locale' ){
						update_option( 'cff_locale', $single_data['value']  );
					}else{
						$cff_settings[$single_data['id']] = $single_data['value'];
					}
				}else{
					$cff_settings[$single_data['id']] = $single_data['id'] === 'cff_disable_resize' ? false : true;
				}
			}
			if( $single_data['type'] === 'install_plugins' && current_user_can( 'install_plugins' ) ){
			$plugins = explode(',' , $single_data['id']);
				foreach ($plugins as $plugin_name) {
					@CFF_Onboarding_wizard::install_single_plugin( $plugin_name );
				}
			}
		}
		update_option( 'cff_style_settings', $cff_settings );

		//Deleting Redirect Data for 3rd plugins
		$this->disable_installed_plugins_redirect();

		wp_die();

	}


	/**
	 * Dismiss Onboarding Wizard
	 *
	 * @since 6.0.8
	 */
	public function dismiss_wizard(){
		check_ajax_referer( 'cff-admin' , 'nonce');

		$cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
		$cap = apply_filters( 'cff_settings_pages_capability', $cap );
		if ( ! current_user_can( $cap ) ) {
			wp_send_json_error(); // This auto-dies.
		}

		$cff_statuses_option = get_option( 'cff_statuses', array() );
		$cff_statuses_option['wizard_dismissed'] = true;
		update_option( 'cff_statuses', $cff_statuses_option );
		wp_send_json_error();
	}

	/**
	 * Disable Installed Plugins Redirect
	 *
	 * @since 6.0.8
	 */
	public function disable_installed_plugins_redirect(){
		//Monster Insight
		delete_transient( '_monsterinsights_activation_redirect' );

		//All in one SEO
		update_option( 'aioseo_activation_redirect', true );

		//WPForms
		update_option( 'wpforms_activation_redirect', true );

		//Optin Monster
		delete_transient( 'optin_monster_api_activation_redirect' );
		update_option( 'optin_monster_api_activation_redirect_disabled', true );

		//Seed PROD
		update_option( 'seedprod_dismiss_setup_wizard', true );

		//PushEngage
		delete_transient( 'pushengage_activation_redirect' );

		//Smash Plugin redirect remove
		$this->disable_smash_installed_plugins_redirect();
	}

	/**
	 * Disable Smash Balloon Plugins Redirect
	 *
	 * @since 6.0.8
	 */
	public function disable_smash_installed_plugins_redirect()
	{
		$smash_list = [
			'facebook' 		=> 'cff_plugin_do_activation_redirect',
			'instagram' 	=> 'sbi_plugin_do_activation_redirect',
			'youtube' 		=> 'sby_plugin_do_activation_redirect',
			'twitter' 		=> 'ctf_plugin_do_activation_redirect',
			'reviews' 		=> 'sbr_plugin_do_activation_redirect',
		];

		if(isset($smash_list[self::$plugin_name])){
			unset($smash_list[self::$plugin_name]);
		}

		foreach ($smash_list as $key => $opt) {
			delete_option( $opt );
		}
	}



}