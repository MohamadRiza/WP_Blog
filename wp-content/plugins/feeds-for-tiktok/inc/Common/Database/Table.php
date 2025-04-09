<?php
/**
 * Generic database functionality.
 *
 * @package tiktok-feeds
 */

// phpcs:ignoreFile

namespace SmashBalloon\TikTokFeeds\Common\Database;

use Exception;
use LogicException;

/**
 * Generic database class.
 */
abstract class Table
{
	/**
	 * Custom table name.
	 * @var string
	 */
	protected const TABLE_NAME = 'undefined';

	/**
	 * Table version.
	 * @var int
	 */
	protected const VERSION = 0;

	/**
	 * Default query limit.
	 * @var int
	 */
	protected const QUERY_LIMIT = 20;

	/**
	 * Create custom table.
	 *
	 * @return bool True if table exists if created.
	 */
	abstract public function create_table();

	/**
	 * Check if database table exists.
	 *
	 * @return bool True if exists.
	 */
	protected function table_exists()
	{
		global $wpdb;
		$table_name = $this->get_prefixed_table_name();

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) === $table_name );
	}

	/**
	 * Get table name.
	 *
	 * @return string Table name.
	 */
	protected function get_table_name()
	{
		return static::TABLE_NAME;
	}

	/**
	 * Get table name.
	 *
	 * @return string Table name.
	 */
	public function get_prefixed_table_name()
	{
		global $wpdb;

		return $wpdb->prefix . $this->get_table_name();
	}

	/**
	 * Delete table.
	 */
	public function drop_table()
	{
		global $wpdb;

		if ( ! $this->table_exists() ) {
			return;
		}

		$table_name = $this->get_prefixed_table_name();

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$wpdb->query( "DROP TABLE `$table_name`" );

		// Delete version key of table.
		delete_option( $this->get_table_version_option_key() );
	}

	/**
	 * Create database table if needed.
	 *
	 * @return void
	 */
	public function create_or_update_db_table()
	{
		$version_key   = $this->get_table_version_option_key();
		$table_version = get_option( $version_key, 0 );

		if ( version_compare( static::VERSION, $table_version, '>' ) || ! $this->table_exists() ) {
			if ( $this->create_table() ) {
				update_option( $version_key, static::VERSION, true );
			}
		}

		if ( $table_version !== static::VERSION ) {
			$this->run_migrations( $table_version, static::VERSION );
		}
	}

	/**
	 * Get table version option key. It's the key of the version stored in the
	 * WordPress core `wp_options` table.
	 *
	 * @return string
	 */
	public function get_table_version_option_key() {
		return SBTT_SLUG . '_table_version_' . $this->get_prefixed_table_name();
	}

	/**
	 * Get a valid `orderby` value from query args.
	 *
	 * @param array  $args Query args.
	 * @param array  $allowed_orderby Allowed values for orderby.
	 * @param string $default Default order by value.
	 *
	 * @return string Order by value.
	 * @throws LogicException If invalid args.
	 */
	protected function get_args_order_by( array $args, array $allowed_orderby, string $default )
	{
		if ( empty( $args['orderby'] ) ) {
			return $default;
		}

		$orderby = strtolower( $args['orderby'] );
		if ( ! in_array( $orderby, $allowed_orderby, true ) ) {
			throw new LogicException( sprintf( 'Invalid orderby %s', esc_html( $args['orderby'] ) ) );
		}

		return $orderby;
	}

	/**
	 * Get a valid `order` value from query args.
	 *
	 * @param array $args Query args.
	 *
	 * @return string Order value.
	 * @throws LogicException If invalid args.
	 */
	protected function get_args_order( array $args )
	{
		if ( empty( $args['order'] ) ) {
			return 'ASC';
		}

		$order = strtoupper( $args['order'] );
		if ( 'ASC' !== $order && 'DESC' !== $order ) {
			throw new LogicException( sprintf( 'Invalid order %s', esc_html( $args['order'] ) ) );
		}

		return $order;
	}

	/**
	 * Get a valid `limit` value from query args.
	 *
	 * @param array $args Query args.
	 *
	 * @return int Limit.
	 * @throws LogicException If invalid args.
	 */
	protected function get_args_limit( array $args )
	{
		if ( empty( $args['limit'] ) || ! is_numeric( $args['limit'] ) ) {
			return static::QUERY_LIMIT;
		}

		$limit = (int) $args['limit'];
		if ( $limit <= 0 ) {
			throw new LogicException( sprintf( 'Invalid limit %s', esc_html( $args['limit'] ) ) );
		}

		return $limit;
	}

	/**
	 * Get migrations and their versions when should be run.
	 *
	 * @return array
	 */
	protected function get_migrations() {
		return [
			// The key is the version when the migration should be ran.
			// e.g.
			// 1 => DummyMigration::class
		];
	}

	/**
	 * Run migrations. Upgrading will execute all version migrations in ascending order
	 * and downgrading will execute them in descending order.
	 *
	 * @return void
	 */
	public function run_migrations( $current_version, $target_version ) {
		if ( version_compare( $current_version, $target_version, '<' ) ) {
			// Upgrade.
			$sorted_migrations = $this->get_migrations();
			ksort( $sorted_migrations );

			foreach ( $sorted_migrations as $version => $migration ) {
				if ( $version > $current_version && $version <= $target_version ) {
					( new $migration() )->apply();
				}
			}
		}

		if ( version_compare( $current_version, $target_version, '>' ) ) {
			$sorted_migrations = $this->get_migrations();
			krsort( $sorted_migrations );

			// Downgrade.
			foreach ( $sorted_migrations as $version => $migration ) {
				if ( $version > $current_version && $version <= $target_version ) {
					( new $migration() )->rollback();
				}
			}
		}
	}

	/**
	 * Cleanup table.
	 *
	 * @return int
	 */
	public function cleanup_table()
	{
		global $wpdb;

		$days = 1000;
		$max_records_to_delete = 1000;

		$table_name = $this->get_prefixed_table_name();

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return $wpdb->query(
			$wpdb->prepare(
				"DELETE FROM $table_name WHERE added_at <= DATE_SUB( NOW(), INTERVAL %d DAY ) LIMIT %d",
				$days,
				$max_records_to_delete
			)
		);
	}
}