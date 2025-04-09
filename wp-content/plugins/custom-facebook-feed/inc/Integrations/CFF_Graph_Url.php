<?php
/**
 * The Settings Trait
 *
 * @since 4.0
 */

namespace CustomFacebookFeed\Integrations;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}


class CFF_Graph_Url
{
	/**
	 * Return URL String Depeding on the $feed_type and settings
	 *
	 * @param string $type Call Type (Header or Feed Type).
	 * @param array  $settings feed settings.
	 * @since 4.0
	 *
	 * @return mixed|string|boolean
	 */
	public static function get_url($type, $source_id, $settings = [], $misc_args = [])
	{
		if (is_array($source_id) || strpos($source_id, ',') !== false) {
			$ids = explode(',', $source_id);
			$source_id = $ids[0];
		}
		if (!isset($type) || empty($type)) {
			return false;
		}
		$page_type = $settings['pagetype'];
		$is_group = ($page_type === 'group') ? true : false;

		$url_builder = [
			'query' => 'posts',
			'version' => '4.0',
			'source_id' => $source_id
		];

		switch ($type) {
			case 'timeline':
				//Logic to get the Graph query
				if ($settings['showpostsby'] === 'others' || $is_group) {
					$url_builder['query'] = 'feed';
				}
				if ($settings['showpostsby'] === 'onlyothers' && !$is_group) {
					$url_builder['query'] = 'visitor_posts';
				}
				break;
		}
		$misc_args['source_id'] = $source_id;
		$url_builder['fields'] = CFF_Graph_Url::get_call_type_fields_args($type, $settings, $misc_args);
		return 'https://graph.facebook.com/v' . $url_builder['version'] . '/' . $url_builder['source_id'] . '/' . $url_builder['query'] . '?' . $url_builder['fields'];
	}


	/**
	 * A List of Common Fields that can be used in different API calls
	 *
	 * @param string $type Call Type (Comment, Likes....).
	 * @param array  $settings Feed Settings.
	 * @since 4.0
	 *
	 * @return string
	 */
	public static function get_common_fields($type, $args = [])
	{
		$common_fields = [
			'comments' => 'comments.summary(true)' . (isset($args['comments_limit']) ? '.limit(' . $args['comments_limit'] . ')' : '') .
				(isset($args['comments_childs']) ? '{created_time,from{name,id,picture{url},link},id,message,message_tags,attachment,like_count}' : '') .
				(isset($args['short_comments_childs']) ? '{message,created_time}' : ''),
			'likes' => 'likes.summary(true)' . (isset($args['likes_limit']) ? '.limit(' . $args['likes_limit'] . ')' : ''),
			'reactions' => 'reactions.summary(true)' . (isset($args['reactions_limit']) ? '.limit(' . $args['reactions_limit'] . ')' : ''),
			'from' => 'from{picture,id,name,link}',
			'attachments' => 'attachments{title' . (!isset($args['salesposts']) || $args['salesposts'] !== 'true' ? ',description' : '') . ',media_type,unshimmed_url,target{id},multi_share_end_card,media{source,image},subattachments}'
		];
		return $common_fields[$type];
	}

	/**
	 * A list of all API URL args depending on the FeedType
	 *
	 * @param string $type Call Type (Comment, Likes....).
	 * @param array  $settings Feed Settings.
	 * @since 4.0
	 *
	 * @return string
	 */
	public static function get_call_type_fields_args($type, $settings = [], $graph_args = [])
	{
		$feed_type_fields = [
			'timeline' => [
				'fields' => 'id,updated_time,message,message_tags,story,picture,full_picture,status_type,created_time,backdated_time,shares,call_to_action,privacy' . (!isset($settings['storytags']) || $settings['storytags'] !== 'true' ? ',story_tags' : ''),
				'common_fields' => [
					'comments' => [
						'short_comments_childs' => true
					],
					'from',
					'attachments'
				],
				'common_args' => [
					'token', 'limit', 'locale', 'ssl'
				]
			]
		];

		if ($settings['pagetype'] === 'group') {
			array_merge($feed_type_fields['timeline']['common_fields'],
				[
					'reactions' => [
						'reactions_limit' => 0
					]
				]
			);
		} else {
			array_merge($feed_type_fields['timeline']['common_fields'],
				[
					'likes' => [
						'likes_limit' => 0
					]
				]
			);
		}

		if (!isset($feed_type_fields[$type])) {
			return false;
		}
		$fields_string_arr = [];
		$fields_args_arr = [];
		foreach ($feed_type_fields[$type] as $key => $element) {
			if ($key === 'fields') {
				array_push($fields_string_arr, $element);
			}
			if ($key === 'common_fields') {
				foreach ($element as $ckey => $value) {
					$c_type = is_array($value) ? $ckey : $value;
					$c_value = is_array($value) ? $value : [];
					array_push($fields_string_arr, CFF_Graph_Url::get_common_fields($c_type, $c_value));
				}
			}
			if ($key === 'common_args') {
				foreach ($element as $argvalue) {
					$single_arg = CFF_Graph_Url::get_common_args($argvalue, $graph_args);
					if (!empty($single_arg)) {
						array_push($fields_args_arr, $single_arg);
					}
				}
			}
		}
		$url_string = '';
		if (sizeof($fields_string_arr) > 0) {
			$url_string .= 'fields=' . implode(',', $fields_string_arr);
		}
		if (sizeof($fields_args_arr) > 0) {
			$url_string .= '&' . implode('&', $fields_args_arr);
		}
		return $url_string;
	}

	/**
	 * A List of Common Args
	 *
	 * @param string $type Call Type (Comment, Likes....).
	 * @param array  $settings Feed Settings.
	 * @since 4.0
	 *
	 * @return array
	 */
	public static function get_common_args($type, $args = [])
	{
		$token = is_array($args['token']) ? (isset($args['token'][$args['source_id']]) ? $args['token'][$args['source_id']] : '') : $args['token'];
		$api_call_args = [
			'token' => 'access_token=' . $token,
			'limit' => 'limit=' . $args['limit'],
			'locale' => 'locale=' . $args['locale'],
			'photos_type' => 'type=uploaded',
			'ssl' => (is_ssl()) ? 'return_ssl_resources=true' : ''
		];
		return isset($args[$type]) && !empty($args[$type]) ? $api_call_args[$type] : '';
	}

}