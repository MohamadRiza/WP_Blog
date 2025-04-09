<?php
/**
 * Class SinglePostCache
 *
 * @since 1.0
 */
namespace SmashBalloon\Reviews\Common;

use SmashBalloon\Reviews\Common\Helpers\Data_Encryption;

class SinglePostCache {

	public const UPLOAD_FOLDER_NAME = 'sbr-feed-images';

	public const POSTS_TABLE_NAME = SBR_POSTS_TABLE;

	/**
	 * @var array
	 */
	private $post_data;

	private $upload_dir;

	private $storage_data;
	private $provider_id;
	private $lang;
	public $encryption;

	public function __construct( $post_data, $media_finder = null, $provider_id = null ) {
		$this->post_data = $post_data;

        $upload = wp_upload_dir();
		$upload_dir = $upload['basedir'];
		$upload_dir = trailingslashit( $upload_dir ) . self::UPLOAD_FOLDER_NAME;
		$this->upload_dir = $upload_dir;

		$this->lang = '';

		$this->storage_data = array(
			'media_id'     => '',
			'sizes'        => '[]',
			'aspect_ratio' => 1,
			'images_done'  => 0
		);
		$this->encryption = new Data_Encryption();
	}

	public function get_storage_data() {
		return $this->storage_data;
	}

	public function get_post_data() {
		return $this->post_data;
	}

	public function set_provider_id( $provider_id ) {
		$this->provider_id = $provider_id;
	}

	public function set_lang( $lang ) {
		$this->lang = $lang;
	}

	public function get_provider_id(){
		return $this->provider_id;
	}

	public function set_storage_data( $key, $value ){
		return $this->storage_data[ $key ] = $value;
	}


	public function resize_images( $image_sizes ) {
		$new_file_name    = $this->post_data['provider']['name'] . '-' . $this->post_data['review_id'];

		$image_source_set = ! empty( $this->post_data['media'] ) ? $this->post_data['media'] : array();

		// the process is considered a success if one image is successfully resized
		$one_successful_image_resize = false;

		foreach ( $image_sizes as $image_size ) {

			$i = 0;
			foreach ( $image_source_set as $image_file_to_resize ) {
				if ( $i < 10  && $image_file_to_resize['type'] === 'image' ) {
					$this_image_file_name = $new_file_name . '-' . $i . '-' .  $image_size . '.jpg';

					$image_editor = wp_get_image_editor( $image_file_to_resize['url'] );
					// not uncommon for the image editor to not work using it this way
					if ( ! is_wp_error( $image_editor ) ) {
						$sizes = $image_editor->get_size();

						$image_editor->resize( $image_size, null );

						$full_file_name = trailingslashit( $this->upload_dir ) . $this_image_file_name;

						$saved_image = $image_editor->save( $full_file_name );

						if ( $saved_image ) {
							$one_successful_image_resize = true;
						}
					} else {
						// was error
					}
				}

				$i++;
			}

		}

		if ( $one_successful_image_resize ) {
			$aspect_ratio = round( $sizes['width'] / $sizes['height'], 2 );
			$media_id = $new_file_name;
		} else {
			$aspect_ratio = 1;
			if ( empty( $image_source_set ) ) {
				$media_id = '';
				$image_sizes = array();
			} else {
				$media_id = 'error';
			}
		}

		$this->storage_data['media_id'] = $media_id;
		$this->storage_data['sizes'] = wp_json_encode( $image_sizes );
		$this->storage_data['aspect_ratio'] = $aspect_ratio;
		$this->storage_data['images_done'] = 1;
	}

	public function resize_avatar( $image_size ) {
		$new_file_name = $this->post_data['provider']['name'] . '-' . $this->post_data['review_id'] . '-avatar';
		$avatar        = ! empty( $this->post_data['reviewer']['avatar'] ) ? $this->post_data['reviewer']['avatar'] : false;

		if ( empty( $avatar ) ) {
			return;
		}

		$avatar_id            = $new_file_name . '-' .  $image_size;
		$this_image_file_name = $avatar_id . '.png';

		$image_editor = wp_get_image_editor( $avatar );
		// not uncommon for the image editor to not work using it this way
		if ( ! is_wp_error( $image_editor ) ) {
			$sizes = $image_editor->get_size();

			$image_editor->resize( $image_size, null );

			$full_file_name = trailingslashit( $this->upload_dir ) . $this_image_file_name;

			$saved_image = $image_editor->save( $full_file_name );

			if ( ! $saved_image ) {
				$avatar_id = 'error';
			}
		} else {
			$avatar_id = 'error';
		}


		$this->storage_data['avatar_id'] = $avatar_id;
	}

	public function db_record_exists() {
		$feed_id_match = $this->db_record();
		if ( ! empty( $feed_id_match ) ) {
			$this->storage_data['media_id'] = $feed_id_match['media_id'];
			$this->storage_data['sizes'] = $feed_id_match['sizes'];
			$this->storage_data['aspect_ratio'] = $feed_id_match['aspect_ratio'];
			$this->storage_data['images_done'] = $feed_id_match['images_done'];
			$this->storage_data['json_data'] = $feed_id_match['json_data'];

		}
		return null !== $feed_id_match;
	}

	public function db_record() {
		global $wpdb;
		$table_name    = esc_sql( $wpdb->prefix . self::POSTS_TABLE_NAME );
		$provider_id  	= !empty($this->post_data['provider_id'])
			? $this->post_data['provider_id']
			: $this->provider_id;

		if( isset( $this->post_data['review_id'] ) ){
			$feed_id_match = $wpdb->get_results(
				$wpdb->prepare(
				"SELECT * FROM $table_name
                        WHERE post_id = %s AND lang = %s AND provider_id = %s LIMIT 1",
						$this->post_data['review_id'],
						$this->lang,
						$provider_id
					),
					ARRAY_A
				);

			if ( ! empty( $feed_id_match[0] ) ) {
				return $feed_id_match[0];
			}
		}

		return null;
	}

	public function store() {
        $rating = $this->post_data['rating'];
        if (Util::is_facebook_collection_post($this->post_data)) {
            $rating = $this->post_data['rating'] === 'positive' ? 5 : 1;
        }

		$to_store = array(
			array( 'created_on', date( 'Y-m-d H:i:s' ), '%s' ),
			array( 'post_id', $this->post_data['review_id'], '%s' ),
			array( 'time_stamp', date( 'Y-m-d H:i:s', $this->post_data['time'] ), '%s' ),
			array( 'json_data', $this->should_encrypt($this->post_data, wp_json_encode($this->post_data)), '%s' ),
			array( 'post_content', $this->should_encrypt($this->post_data, $this->post_data['text']), '%s' ),
			array( 'rating', $rating, '%d' ),
			array( 'provider', $this->post_data['provider']['name'], '%s' ),
			array( 'provider_id', $this->get_provider_id(), '%s' ),
			array( 'business', $this->post_data['business']['id'] ?? '', '%s' ),
			array( 'media_id', $this->storage_data['media_id'], '%s' ),
			array( 'avatar_id', $this->storage_data['avatar_id'] ?? '', '%s' ),
			array( 'sizes', $this->storage_data['sizes'], '%s' ),
			array( 'aspect_ratio', $this->storage_data['aspect_ratio'], '%s' ),
			array( 'images_done', $this->storage_data['images_done'], '%d' ),
			array( 'last_requested', date( 'Y-m-d H:i:s' ), '%s' ),
			array( 'lang', $this->lang, '%s' ),
		);
		$data = array();
		$format = array();
		foreach ( $to_store as $single_store ) {
			$data[ $single_store[0] ] = $single_store[1];
			$format[] = $single_store[2];
		}

		global $wpdb;
		$table_name = esc_sql( $wpdb->prefix . self::POSTS_TABLE_NAME );
		$error      = $wpdb->insert( $table_name, $data, $format );

		if ( $error !== false ) {
			$insert_id = $wpdb->insert_id;
		} else {
			// log error
		}
	}

    public function update_single($strict_update = false)
    {
        $rating = $this->post_data['rating'];
        if (Util::is_facebook_collection_post($this->post_data)) {
            $rating = $this->post_data['rating'] === 'positive' ? 5 : 1;
        }

        $to_store = array(
            array('post_id', $this->post_data['review_id'], '%s'),
            array('time_stamp', date('Y-m-d H:i:s', $this->post_data['time']), '%s'),
            array('post_content', $this->should_encrypt($this->post_data, $this->post_data['text']), '%s'),
            array('rating', $rating, '%d'),
            array('provider', $this->post_data['provider']['name'], '%s'),
            array('provider_id', $this->get_provider_id(), '%s'),
            array('business', $this->post_data['business']['id'] ?? '', '%s'),
            array('media_id', $this->storage_data['media_id'], '%s'),
            array('sizes', $this->storage_data['sizes'], '%s'),
            array('aspect_ratio', $this->storage_data['aspect_ratio'], '%s'),
            array('images_done', $this->storage_data['images_done'], '%d'),
            array('last_requested', date('Y-m-d H:i:s'), '%s'),
        );
        $data = array();
        $format = array();
        foreach ($to_store as $single_store) {
            $data[$single_store[0]] = $single_store[1];
            $format[] = $single_store[2];
        }

        global $wpdb;
        $table_name = esc_sql($wpdb->prefix . self::POSTS_TABLE_NAME);
        $where = array();
        $where_format = array();

		$where['post_id'] = $this->post_data['review_id'];
        $where_format[] = '%s';

		if ($strict_update) {
			$where['provider_id'] = $this->get_provider_id();
			$where_format[] = '%s';
		}

        $error = $wpdb->update($table_name, $data, $where, $format, $where_format);

        if ($error !== false) {
            $insert_id = $wpdb->insert_id;
        } else {
            // log error
        }
    }

	public function update( $to_update ) {
		$data = array();
		$format = array();
		foreach ( $to_update as $single_update ) {
			$data[ $single_update[0] ] = $single_update[1];
			$format[] = $single_update[2];
		}

		global $wpdb;
		$where = array();
		$where_format = array();

		$where['post_id'] = $this->post_data['review_id'];
		$where_format[] = '%s';
		$table_name = esc_sql( $wpdb->prefix . self::POSTS_TABLE_NAME );
		$error      = $wpdb->update( $table_name, $data, $where, $format, $where_format );

		if ( $error !== false ) {
			$insert_id = $wpdb->insert_id;
		} else {
			// log error
		}
	}

	public static function delete_resizing_table_and_images() {
		$upload = wp_upload_dir();

		global $wpdb;

		$table_name = esc_sql( $wpdb->prefix . self::POSTS_TABLE_NAME );

		$image_files = glob( trailingslashit( $upload['basedir'] ) . trailingslashit( self::UPLOAD_FOLDER_NAME ) . '*'  ); // get all file names
		foreach ( $image_files as $file ) { // iterate files
			if ( is_file( $file ) ) {
				unlink( $file );
			}
		}

		//Delete tables
		$wpdb->query( "DROP TABLE IF EXISTS $table_name" );
	}

	public static function create_resizing_table_and_uploads_folder() {
		$upload     = wp_upload_dir();
		$upload_dir = $upload['basedir'];
		$upload_dir = trailingslashit( $upload_dir ) . self::UPLOAD_FOLDER_NAME;
		if ( ! file_exists( $upload_dir ) ) {
			$created = wp_mkdir_p( $upload_dir );
		}

		global $wpdb;
		$table_name = esc_sql( $wpdb->prefix . self::POSTS_TABLE_NAME );
		$max_index_length = 191;
		$charset_collate  = '';
		if ( method_exists( $wpdb, 'get_charset_collate' ) ) { // get_charset_collate introduced in WP 3.5
			$charset_collate = $wpdb->get_charset_collate();
		}

		if ( $wpdb->get_var( "show tables like '$table_name'" ) !== $table_name ) {
			$sql = 'CREATE TABLE ' . $table_name . " (
            id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            created_on DATETIME,
            post_id VARCHAR(1000) DEFAULT '' NOT NULL,
            time_stamp DATETIME,
            json_data LONGTEXT DEFAULT '' NOT NULL,
            post_content LONGTEXT DEFAULT '' NOT NULL,
            rating INT(1) UNSIGNED NOT NULL,
			provider VARCHAR(1000) DEFAULT '' NOT NULL,
			provider_id VARCHAR(1000) DEFAULT '' NOT NULL,
            business VARCHAR(1000) DEFAULT '' NOT NULL,
			media_id VARCHAR(1000) DEFAULT '' NOT NULL,
            sizes VARCHAR(1000) DEFAULT '' NOT NULL,
            aspect_ratio DECIMAL (4,2) DEFAULT 0 NOT NULL,
            avatar_id VARCHAR(1000) DEFAULT '' NOT NULL,
            images_done TINYINT(1) DEFAULT 0 NOT NULL,
            last_requested DATE,
            lang VARCHAR(1000) DEFAULT '' NOT NULL,
            INDEX provider (provider($max_index_length)),
            INDEX business (business($max_index_length)),
            INDEX provider_business (provider(10), business(15)),
            INDEX provider_lang (provider(140),lang(51))
        ) $charset_collate;";
			$wpdb->query( $sql );
		}
		$error = $wpdb->last_error;
		$query = $wpdb->last_query;
	}

	public static function delete_least_used_image() {
	}

	public function max_total_records_reached() {

	}

	public function media_supplied() {
		return $this->post_data['provider']['name'] !== 'yelp' && $this->post_data['provider']['name'] !== 'tripadvisor';
	}


	public function should_encrypt($post, $element)
	{
		return $post['provider']['name'] === 'facebook' && $element !== null ? $this->encryption->maybe_encrypt($element) : $element;
	}


	 /**
	 * Used to update or insert Single Post Data
	 *
	 *
	 * @return void
	 *
	 * @since 1.6
	 */
	public function update_or_insert($strict_udpate = false)
	{
		if ($this->db_record_exists()) {
			$this->update_single($strict_udpate);
		} else {
			$this->store();
		}
	}

}
