<?php

namespace SmashBalloon\TikTokFeeds\Common\Integrations;

// If this file is called directly, abort.
if (! defined('ABSPATH')) {
	die;
}

class WPCode
{
	/**
	 * Load the WPCode snippets from the snippet library, if it exists.
	 *
	 * @return array
	 */
	public static function load_snippets()
	{
		$snippets = array();
		if (function_exists('wpcode_get_library_snippets_by_username')) {
			$snippets = array_values(array_filter(wpcode_get_library_snippets_by_username('smashballoon'), function ($snippet) {
				if (!isset($snippet['tags']) || !is_array($snippet['tags'])) {
					return false;
				}
				return in_array('tiktok-feeds', $snippet['tags'], true);
			}));
		}
		return $snippets;
	}

	/**
	 * Checks if the plugin is installed, either the lite or premium version.
	 *
	 * @return bool True if the plugin is installed.
	 */
	public static function is_plugin_installed()
	{
		return self::is_pro_installed() || self::is_lite_installed();
	}

	/**
	 * Is the pro plugin installed.
	 *
	 * @return bool True if the pro plugin is installed.
	 */
	public static function is_pro_installed()
	{
		$installedPlugins = array_keys(get_plugins());

		return in_array('wpcode-premium/wpcode.php', $installedPlugins, true);
	}

	/**
	 * Is the lite plugin installed.
	 *
	 * @return bool True if the lite plugin is installed.
	 */
	public static function is_lite_installed()
	{
		$installedPlugins = array_keys(get_plugins());

		return in_array('insert-headers-and-footers/ihaf.php', $installedPlugins, true);
	}

	/**
	 * Check if plugin is active.
	 *
	 * @return bool
	 */
	public static function is_plugin_active()
	{
		return function_exists('wpcode');
	}

	/**
	 * Create new snippets and save it to the WPCode library.
	 *
	 * @param array $snippets
	 * @return void
	 */
	public static function create_snippets($snippets)
	{
		if (class_exists('\WPCode_Snippet')) {
			foreach ($snippets as $snippet) {
				$new_snippet = new \WPCode_Snippet($snippet);
				$new_snippet->save();
			}
		}
	}

	/**
	 * Create a sample snippet.
	 *
	 * @return void
	 */
	public static function create_sample_snippet()
	{
		$snippet = array(
			'code_type' => 'php',
			'code' => 'echo "Hello World!";',
			'location' => 'site_wide_footer',
			'auto_insert' => true,
			'title' => 'Sample Snippet',
			'note' => 'This is a sample snippet',
			'tags' => array('sample', 'test'),
			'active' => true,
		);
		self::create_snippets(array($snippet));
	}
}
