<?php
/**
 * Database Helper
 *
 * Provides functionality to help the DB classes methods.
 *
 * @package SureMails\Inc\DB
 */

namespace SureMails\Inc\DB;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Settings
 *
 * Handles fetching specific settings from the connections option.
 */
class Db_Helper {

	/**
	 * Where Pattern.
	 *
	 * @var string
	 * @since 0.0.1
	 */
	private static string $where_pattern = '/^(\w+)\s*(=|!=|<|<=|>|>=|LIKE)$/';

	/**
	 * Form the where clause string from given array conditions
	 *
	 * @param array<string,string> $where Where Array.
	 * @param bool                 $having_flag Having or Where flag.
	 * @return array<string,string|array<int,string>>
	 */
	public static function form_where_clause( $where = null, $having_flag = false ) {

		if ( empty( $where ) ) {
			return [
				'clause' => '',
				'values' => [],
			];
		}

		$conditions   = [];
		$values_where = [];

		foreach ( $where as $field => $value ) {
			if ( preg_match( self::$where_pattern, $field, $matches ) ) {
				$field_name     = $matches[1];
				$operator       = $matches[2];
				$conditions[]   = "{$field_name} {$operator} %s";
				$values_where[] = esc_sql( $value );
			} else {
				// Default to '=' operator.
				$conditions[]   = "{$field} = %s";
				$values_where[] = esc_sql( $value );
			}
		}

		$connector = $having_flag ? 'HAVING' : 'WHERE';

		$where_clause = $connector . ' ' . implode( ' AND ', $conditions );
		return [
			'clause' => $where_clause,
			'values' => $values_where,
		];
	}

	/**
	 * Form the GROUP BY clause string from given array conditions
	 *
	 * @param string $group_by Group By Field.
	 * @return string
	 */
	public static function form_group_by_clause( $group_by = null ) {

		if ( ! is_string( $group_by ) || empty( $group_by ) ) {
			return '';
		}

		$group_by_safe = esc_sql( $group_by );
		return "GROUP BY {$group_by_safe}";
	}

	/**
	 * Form the ORDER BY clause string from given array conditions
	 *
	 * @param array<string,string> $order_by Order By Array.
	 * @return string
	 */
	public static function form_order_by_clause( $order_by = null ) {

		if ( empty( $order_by ) ) {
			return '';
		}

		$order_clauses = [];
		foreach ( $order_by as $field => $direction ) {
			$direction       = strtoupper( $direction );
			$direction       = in_array( $direction, [ 'ASC', 'DESC' ], true ) ? $direction : 'ASC';
			$field_safe      = esc_sql( $field );
			$order_clauses[] = "{$field_safe} {$direction}";
		}
		return 'ORDER BY ' . implode( ', ', $order_clauses );
	}

	/**
	 * Form the LIMIY clause string from given array conditions
	 *
	 * @param int $limit Limit value.
	 * @param int $offset Offset value.
	 * @return array<string,string|array<int,int>>
	 */
	public static function form_limit_clause( $limit = 0, $offset = 0 ) {
		$values_limit = [];
		$limit_clause = '';

		if ( $limit ) {
			$limit_clause   = 'LIMIT %d';
			$values_limit[] = $limit;
		}

		if ( $offset ) {
			$limit_clause  .= ' OFFSET %d';
			$values_limit[] = $offset;
		}

		return [
			'clause' => $limit_clause,
			'values' => $values_limit,
		];
	}

}
