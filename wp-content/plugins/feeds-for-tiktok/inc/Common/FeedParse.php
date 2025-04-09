<?php

namespace SmashBalloon\TikTokFeeds\Common;

use SmashBalloon\TikTokFeeds\Common\Utils;

class FeedParse
{
	/**
	 * Get open id.
	 *
	 * @param array $header_data Header data.
	 * @return string The open id.
	 */
	public static function get_open_id($header_data)
	{
		return isset($header_data['open_id']) ? esc_attr($header_data['open_id']) : '';
	}

	/**
	 * Get username.
	 *
	 * @param array $header_data Header data.
	 * @return string The username.
	 */
	public static function get_username($header_data)
	{
		return isset($header_data['username']) ? esc_attr($header_data['username']) : '';
	}

	/**
	 * Get display name.
	 *
	 * @param array $header_data Header data.
	 * @return string The display name.
	 */
	public static function get_display_name($header_data)
	{
		return isset($header_data['display_name']) ? esc_attr($header_data['display_name']) : '';
	}

	/**
	 * Get avatar url.
	 *
	 * @param array $header_data Header data.
	 * @return string The avatar url.
	 */
	public static function get_avatar_url($header_data)
	{
		$local_avatar = isset($header_data['local_avatar_url']) ? esc_url($header_data['local_avatar_url']) : false;
		$avatar_url = isset($header_data['avatar_url_100']) ? esc_url($header_data['avatar_url_100']) : false;

		return $local_avatar ? $local_avatar : $avatar_url;
	}

	/**
	 * Get bio description.
	 *
	 * @param array $header_data Header data.
	 * @return string The bio description.
	 */
	public static function get_bio_description($header_data)
	{
		return isset($header_data['bio_description']) ? esc_attr($header_data['bio_description']) : '';
	}

	/**
	 * Get following count.
	 *
	 * @param array $header_data Header data.
	 * @return string The following count.
	 */
	public static function get_following_count($header_data)
	{
		return isset($header_data['following_count']) ? esc_attr($header_data['following_count']) : '';
	}

	/**
	 * Get follower count.
	 *
	 * @param array $header_data Header data.
	 * @return string The follower count.
	 */
	public static function get_follower_count($header_data)
	{
		return isset($header_data['follower_count']) ? esc_attr($header_data['follower_count']) : '';
	}

	/**
	 * Get likes count.
	 *
	 * @param array $header_data Header data.
	 * @return string The likes count.
	 */
	public static function get_likes_count($header_data)
	{
		return isset($header_data['likes_count']) ? esc_attr($header_data['likes_count']) : '';
	}

	/**
	 * Get video count.
	 *
	 * @param array $header_data Header data.
	 * @return string The video count.
	 */
	public static function get_video_count($header_data)
	{
		return isset($header_data['video_count']) ? esc_attr($header_data['video_count']) : '';
	}

	/**
	 * Get is verified.
	 *
	 * @param array $header_data Header data.
	 * @return bool Whether the user is verified or not.
	 */
	public static function get_is_verified($header_data)
	{
		return isset($header_data['is_verified']) ? esc_attr($header_data['is_verified']) : false;
	}

	/**
	 * Get profile url.
	 *
	 * @param array $header_data Header data.
	 * @return string The profile url.
	 */
	public static function get_profile_url($header_data)
	{
		return isset($header_data['profile_deep_link']) ? esc_url($header_data['profile_deep_link']) : '';
	}

	/**
	 * Get post id.
	 *
	 * @param array $post Post data.
	 * @return string The post id.
	 */
	public static function get_post_id($post)
	{
		return isset($post['id']) ? esc_attr($post['id']) : '';
	}

	/**
	 * Get post url.
	 *
	 * @param array $post Post data.
	 * @return string The post url.
	 */
	public static function get_post_url($post)
	{
		return isset($post['share_url']) ? esc_url($post['share_url']) : '';
	}

	/**
	 * Get video description.
	 *
	 * @param array $post Post data.
	 * @return string The video description.
	 */
	public static function get_video_description($post)
	{
		return isset($post['video_description']) ? esc_attr($post['video_description']) : '';
	}

	/**
	 * Get video duration.
	 *
	 * @param array $post Post data.
	 * @return string The video duration.
	 */
	public static function get_video_duration($post)
	{
		return isset($post['video_duration']) ? esc_attr($post['video_duration']) : '';
	}

	/**
	 * Get post cover image url.
	 *
	 * @param array $post Post data.
	 * @return string The post cover image url.
	 */
	public static function get_cover_image_url($post)
	{
		$local_cover_image_url = isset($post['local_cover_image_url']) ? esc_url($post['local_cover_image_url']) : false;
		$cover_image_url = isset($post['cover_image_url']) ? esc_url($post['cover_image_url']) : false;

		return $local_cover_image_url ? $local_cover_image_url : $cover_image_url;
	}

	/**
	 * Get post like count.
	 *
	 * @param array $post Post data.
	 * @return string The post like count.
	 */
	public static function get_post_likes_count($post)
	{
		return isset($post['like_count']) ? esc_attr($post['like_count']) : '';
	}

	/**
	 * Get post view count.
	 *
	 * @param array $post Post data.
	 * @return string The post view count.
	 */
	public static function get_post_view_count($post)
	{
		return isset($post['view_count']) ? esc_attr($post['view_count']) : '';
	}

	/**
	 * Get post comment count.
	 *
	 * @param array $post Post data.
	 * @return string The post comment count.
	 */
	public static function get_post_comment_count($post)
	{
		return isset($post['comment_count']) ? esc_attr($post['comment_count']) : '';
	}

	/**
	 * Get post share count.
	 *
	 * @param array $post Post data.
	 * @return string The post share count.
	 */
	public static function get_post_share_count($post)
	{
		return isset($post['share_count']) ? esc_attr($post['share_count']) : '';
	}

	/**
	 * Get post create time.
	 *
	 * @param array $post Post data.
	 * @return string The post create time.
	 */
	public static function get_post_create_time($post)
	{
		return isset($post['create_time']) ? esc_attr($post['create_time']) : '';
	}

	/**
	 * Get post title.
	 *
	 * @param array $post Post data.
	 * @return string The post title.
	 */
	public static function get_post_title($post)
	{
		return isset($post['title']) ? esc_attr($post['title']) : '';
	}

	/**
	 * Get post embed html.
	 *
	 * @param array $post Post data.
	 * @return string The post embed html.
	 */
	public static function get_post_embed_html($post)
	{
		return isset($post['embed_html']) ? esc_attr($post['embed_html']) : '';
	}

	/**
	 * Get post embed link.
	 *
	 * @param array $post Post data.
	 * @return string The post embed link.
	 */
	public static function get_post_embed_link($post)
	{
		return isset($post['embed_link']) ? esc_url($post['embed_link']) : '';
	}

	/**
	 * Get post iframe URL.
	 *
	 * @param array $post Post data.
	 * @return string The post iframe URL.
	 */
	public static function get_post_iframe_url($post)
	{
		$post_id = self::get_post_id($post);
		$iframe_url = 'https://www.tiktok.com/embed/v2/' . $post_id;

		return $iframe_url;
	}

	/**
	 * Get post mp4 video url.
	 *
	 * @param array $post Post data.
	 * @return string The post video mp4 url
	 */
	public static function get_post_video_url($post)
	{
		return Utils::sbtt_is_pro() && !empty($post['video_url']) ? esc_url($post['video_url']) : '';
	}
}
