<?php
/**
 * @package Unlimited Elements
 * @author UniteCMS Enhanced
 * @copyright Copyright (c) 2016-2024 UniteCMS
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

if(!defined('ABSPATH')) exit;

class UniteCreatorBreadcrumbs {

    /**
     * Get page items for breadcrumb
     *
     * @param array $params Widget configuration parameters
     * @return array Breadcrumb items
     */
    public function getBreadcrumbItems($params) {
        $items = array();
        $show_current = $this->getParamValueByKey('show_current', $params);
        $home_text = $this->getParamValueByKey('home_text', $params);
        $show_home = $this->getParamValueByKey('show_home', $params);
        $show_category_breadcrumbs = $this->getParamValueByKey('show_category_breadcrumbs', $params, 'true');
        $max_category_depth = intval($this->getParamValueByKey('max_category_depth', $params, 3));
        $show_immediate_category = $this->getParamValueByKey('show_immediate_category', $params, 'true');
        $frontPageID = get_option('page_on_front');
        $currentPageID = get_queried_object_id();

        if($frontPageID && $frontPageID == $currentPageID) {
            if($show_current === 'true') {
                $items[] = array(
                    'text' => $home_text,
                    'url' => ''
                );
            }
            return $items;
        }

        if($show_home === 'true') {
            $items[] = array(
                'text' => $home_text,
                'url' => home_url('/')
            );
        }

        if(is_category() && $show_category_breadcrumbs === 'true') {
            $current_category = get_queried_object();

            if($current_category) {
                $ancestors = get_ancestors($current_category->term_id, 'category');
                $ancestor_count = 0;

                foreach(array_reverse($ancestors) as $ancestor) {
                    if($ancestor_count >= $max_category_depth) break;

                    $ancestor_cat = get_term($ancestor, 'category');
                    if(!is_wp_error($ancestor_cat)) {
                        $items[] = array(
                            'text' => $ancestor_cat->name,
                            'url' => get_category_link($ancestor)
                        );
                        $ancestor_count++;
                    }
                }

                if($show_current === 'true') {
                    $items[] = array(
                        'text' => $current_category->name,
                        'url' => ''
                    );
                }
            }

            return $items;
        }

        if(is_single() && $show_category_breadcrumbs === 'true') {
            $categories = get_the_category();

            if(!empty($categories)) {
                $category = $this->getMostSpecificCategory($categories);

                if($category) {
                    $ancestors = get_ancestors($category->term_id, 'category');
                    $ancestor_count = 0;

                    foreach(array_reverse($ancestors) as $ancestor) {
                        if($ancestor_count >= $max_category_depth) break;

                        $ancestor_cat = get_term($ancestor, 'category');
                        if(!is_wp_error($ancestor_cat)) {
                            $items[] = array(
                                'text' => $ancestor_cat->name,
                                'url' => get_category_link($ancestor)
                            );
                            $ancestor_count++;
                        }
                    }

                    if($show_immediate_category === 'true') {
                        $items[] = array(
                            'text' => $category->name,
                            'url' => get_category_link($category->term_id)
                        );
                    }
                }
            }
        }

        if($show_current === 'true') {
            if(is_single()) {
                $items[] = array(
                    'text' => get_the_title(),
                    'url' => ''
                );
            }
            elseif(is_page()) {
                $items[] = array(
                    'text' => get_the_title(),
                    'url' => ''
                );
            }
        }

        // Custom post type archive handling
        if(is_post_type_archive() && $show_current === 'true') {
            $post_type = get_post_type_object(get_post_type());
            if($post_type) {
                $items[] = array(
                    'text' => $post_type->labels->name,
                    'url' => ''
                );
            }
        }

        // Tag archive handling
        if(is_tag() && $show_current === 'true') {
            $items[] = array(
                'text' => single_tag_title('', false),
                'url' => ''
            );
        }

        // Author archive handling
        if(is_author() && $show_current === 'true') {
            $items[] = array(
                'text' => get_the_author(),
                'url' => ''
            );
        }

        // Search results handling
        if(is_search() && $show_current === 'true') {
            $items[] = array(
                'text' => 'Search Results for: "' . get_search_query() . '"',
                'url' => ''
            );
        }

        // Archive handling
        if(is_year() && $show_current === 'true') {
            $items[] = array(
                'text' => get_the_date('Y'),
                'url' => ''
            );
        }
        elseif(is_month() && $show_current === 'true') {
            $items[] = array(
                'text' => get_the_date('F Y'),
                'url' => ''
            );
        }
        elseif(is_day() && $show_current === 'true') {
            $items[] = array(
                'text' => get_the_date('F j, Y'),
                'url' => ''
            );
        }

        return $items;
    }

    /**
     * Get the most specific (deepest) category for a post
     *
     * @param array $categories List of categories
     * @return WP_Term|null Most specific category
     */
    private function getMostSpecificCategory($categories) {
        if(empty($categories)) return null;

        $most_specific = null;
        $max_depth = 0;

        foreach($categories as $category) {
            $current_ancestors = get_ancestors($category->term_id, 'category');
            $current_depth = count($current_ancestors) + 1;

            if($current_depth > $max_depth) {
                $max_depth = $current_depth;
                $most_specific = $category;
            }

            elseif($current_depth == $max_depth) {
                $current_parent_depth = $current_ancestors ? count(get_ancestors($current_ancestors[0], 'category')) : 0;
                $most_specific_parent_depth = $most_specific ? count(get_ancestors($most_specific->parent, 'category')) : 0;

                if($current_parent_depth > $most_specific_parent_depth) {
                    $most_specific = $category;
                }
            }
        }

        return $most_specific;
    }

    /**
     * Get Widget param value by key with default value support
     *
     * @param string $key Parameter key
     * @param array $data Parameter data
     * @param mixed $default Default value
     * @return mixed Parameter value
     */
    private function getParamValueByKey($key, $data, $default = '') {
        if (array_key_exists($key, $data)) {
            return $data[$key];
        }

        foreach($data as $item) {
            if(is_array($item) && isset($item['name']) && $item['name'] == $key) {
                return $item['value'] ?? $default;
            }
        }

        return $default;
    }
}