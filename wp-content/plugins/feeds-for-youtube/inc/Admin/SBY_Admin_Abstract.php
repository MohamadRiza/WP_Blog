<?php

namespace SmashBalloon\YouTubeFeed\Admin;

use SmashBalloon\YouTubeFeed\Feed_Locator;

abstract class SBY_Admin_Abstract {

	protected $vars;

	protected $base_path;

	protected $slug;

	protected $plugin_name;

	protected $capability;
	
	protected $icon;

	protected $tabs;

	protected $active_tab;

	protected $settings_sections;

	protected $display_your_feed_sections;

	protected $option_name;

	protected $types;

	protected $layouts;

	protected $false_fields;

	protected $textarea_fields;

	protected $position;

	protected $settings;

	public function __construct( $vars, $base_path, $slug, $plugin_name, $capability, $icon, $position, $tabs, $settings, $active_tab = false, $option_name = 'sbspf_settings' ) {
		$this->vars = $vars;
		$this->base_path = $base_path;
		$this->slug = $slug;
		$this->plugin_name = $plugin_name;
		$this->capability = $capability;
		$this->icon = $icon;
		$this->position = $position;

		$this->tabs = $tabs;

		if ( $active_tab ) {
			$this->set_active_tab( $active_tab );
		} else {
			$this->set_active_tab( $tabs[0]['slug'] );
		}
		$this->settings = $settings;
		$this->option_name = $option_name;
		$this->false_fields = array();
		$this->textarea_fields = array();
		$this->display_your_feed_sections = array();
	}

	public function get_vars() {
		return $this->vars;
	}

	public function get_option_name() {
		return $this->option_name;
	}

	public function verify_post( $post ) {
		return wp_verify_nonce( $post[ $this->option_name . '_validate' ], $this->option_name . '_validate' );
	}

	public function hidden_fields_for_tab( $tab ) {
		wp_nonce_field( $this->get_option_name() . '_validate', $this->get_option_name() . '_validate', true, true );
		?>
      <input type="hidden" name="<?php echo $this->get_option_name() . '_tab_marker'; ?>" value="<?php echo esc_attr( $tab ); ?>"/>
		<?php
	}

	public function init() {
//		add_action( 'admin_menu', array( $this, 'create_menus' ) );
		add_action( 'admin_init', array( $this, 'settings_init' ) );
		add_action( 'admin_init', array( $this, 'additional_settings_init' ) );

	}

	public function settings_init() {
		/**
		 * Configure Tab
		 */
		$args = array(
			'id' => 'sbspf_types',
			'tab' => 'configure',
			'save_after' => 'true'
		);
		$this->add_settings_section( $args );

		/* Types */
		$locator_html = '';
		if ( Feed_Locator::count_unique() > -1 ) {
			$locator_html .= '<div class="sby_locations_link">';
			$locator_html .= '<a href="?page=' . $this->slug .'&amp;tab=allfeeds"><svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="search" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-search fa-w-16 fa-2x"><path fill="currentColor" d="M508.5 468.9L387.1 347.5c-2.3-2.3-5.3-3.5-8.5-3.5h-13.2c31.5-36.5 50.6-84 50.6-136C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c52 0 99.5-19.1 136-50.6v13.2c0 3.2 1.3 6.2 3.5 8.5l121.4 121.4c4.7 4.7 12.3 4.7 17 0l22.6-22.6c4.7-4.7 4.7-12.3 0-17zM208 368c-88.4 0-160-71.6-160-160S119.6 48 208 48s160 71.6 160 160-71.6 160-160 160z" class=""></path></svg> ' . __( 'Feed Finder', 'feeds-for-youtube' ) . '</a>';
			$locator_html .= '</div>';
		}
		$args = array(
			'name' => 'type',
			'section' => 'sbspf_types',
			'callback' => 'types',
			'title' => '<label>' . __( 'Select a Feed Type', 'feeds-for-youtube' ) .'</label>',
			'shortcode' => array(
				'key' => 'type',
				'example' => 'channel',
				'description' => __( 'Type of feed to display', 'feeds-for-youtube' ) . ' e.g. channel, playlist, search, favorites, live',
				'after_description' => $locator_html,
				'display_section' => 'configure'
			),
			'types' => $this->types
		);
		$this->add_settings_field( $args );

		/* Cache */
		$args = array(
			'name' => 'cache',
			'section' => 'sbspf_types',
			'callback' => 'cache',
			'title' => __( 'Check for new posts', 'feeds-for-youtube' )
		);
		$this->add_settings_field( $args );

		/**
		 * Customize Tab
		 */
		$args = array(
			'title' => __( 'General', 'feeds-for-youtube' ),
			'id' => 'sbspf_general',
			'tab' => 'customize',
			'save_after' => 'true'
		);
		$this->add_settings_section( $args );

		/* Width and Height */
		$select_options = array(
			array(
				'label' => '%',
				'value' => '%'
			),
			array(
				'label' => 'px',
				'value' => 'px'
			)
		);

		$args = array(
			'name' => 'width',
			'default' => '100',
			'section' => 'sbspf_general',
			'callback' => 'text',
			'min' => 1,
			'size' => 4,
			'title' => __( 'Width of Feed', 'feeds-for-youtube' ),
			'shortcode' => array(
				'key' => 'width',
				'example' => '300px',
				'description' => __( 'The width of your feed. Any number with a unit like "px" or "%".', 'feeds-for-youtube' ),
				'display_section' => 'customize'
			),
			'select_name' => 'widthunit',
			'select_options' => $select_options,
			'hidden' => array(
				'callback' => 'checkbox',
				'name' => 'width_responsive',
				'label' => __( 'Set to be 100% width on mobile?', 'feeds-for-youtube' ),
				'before' => '<div id="sbspf_width_options">',
				'after' => '</div>',
				'tooltip_info' =>  __( 'If you set a width on the feed then this will be used on mobile as well as desktop. Check this setting to set the feed width to be 100% on mobile so that it is responsive.', 'feeds-for-youtube' )
			),
		);
		$this->add_settings_field( $args );

		$select_options = array(
			array(
				'label' => '%',
				'value' => '%'
			),
			array(
				'label' => 'px',
				'value' => 'px'
			)
		);
		$args = array(
			'name' => 'height',
			'default' => '',
			'section' => 'sbspf_general',
			'callback' => 'text',
			'min' => 1,
			'size' => 4,
			'title' => __( 'Height of Feed', 'feeds-for-youtube' ),
			'shortcode' => array(
				'key' => 'height',
				'example' => '500px',
				'description' => __( 'The height of your feed. Any number with a unit like "px" or "em".', 'feeds-for-youtube' ),
				'display_section' => 'customize'
			),
			'select_name' => 'heightunit',
			'select_options' => $select_options,
		);
		$this->add_settings_field( $args );

		$args = array(
			'name' => 'background',
			'default' => '',
			'section' => 'sbspf_general',
			'callback' => 'color',
			'title' => __( 'Background Color', 'feeds-for-youtube' ),
			'shortcode' => array(
				'key' => 'background',
				'example' => '#f00',
				'description' => __( 'Background color for the feed. Any hex color code.', 'feeds-for-youtube' ),
				'display_section' => 'customize'
			),
		);
		$this->add_settings_field( $args );

		$args = array(
			'title' => __( 'Layout', 'feeds-for-youtube' ),
			'id' => 'sbspf_layout',
			'tab' => 'customize',
			'save_after' => 'true'
		);
		$this->add_settings_section( $args );

		$args = array(
			'name' => 'layout',
			'section' => 'sbspf_layout',
			'callback' => 'layout',
			'title' => __( 'Layout Type', 'feeds-for-youtube' ),
			'layouts' => $this->layouts,
			'shortcode' => array(
				'key' => 'layout',
				'example' => 'list',
				'description' => __( 'How your posts are display visually.', 'feeds-for-youtube' ),
				'display_section' => 'layout'
			)
		);
		$this->add_settings_field( $args );
	}

	public function additional_settings_init() {

	}

	public function add_false_field( $name, $tab ) {
		$this->false_fields[ $tab ][] = $name;
	}

	public function get_false_fields( $tab ) {
		if ( isset( $this->false_fields[ $tab ] ) ) {
			return $this->false_fields[ $tab ];
		}

		return array();
	}

	public function add_textarea_field( $name, $tab ) {
		$this->textarea_fields[ $tab ][] = $name;
	}

	public function get_textarea_fields( $tab ) {
		if ( isset( $this->textarea_fields[ $tab ] ) ) {
			return $this->textarea_fields[ $tab ];
		}

		return array();
	}

	public function blank() {

	}

	public function add_settings_section( $args ) {
		$title = isset( $args['title'] ) ? $args['title'] : '';
		$callback = isset( $args['callback'] ) ? $args['callback'] : array( $this, 'blank' );
		add_settings_section(
			$args['id'],
			$title,
			$callback,
			$args['id']
		);

		$save_after = isset( $args['save_after'] ) ? $args['save_after'] : false;
		$this->settings_sections[ $args['tab'] ][] = array(
			'id' => $args['id'],
			'save_after' => $save_after
		);
	}

	public function add_settings_field( $args ) {
		$title_after = '';
		$shortcode = false;
		if ( isset( $args['shortcode'] ) ) {
			$title_after = isset( $args['shortcode']['after'] ) ? $args['shortcode']['after'] : '';
			$shortcode = $args['shortcode'];
		}

		if ( $shortcode ) {
			$this->display_your_feed_sections[ $shortcode['display_section'] ]['settings'][] = $shortcode;
		}

		if ( $args['callback'] === 'types' ) {
			$formatted_label = '<label for="' . $this->option_name . '_' . $args['name'] . '">' . $args['title'] .'</label>';
			$formatted_label .= '<code class="sbspf_shortcode">type'. "\n";
			foreach ( $args['types'] as $type ) {
				$shortcode = array(
					'key' => $type['slug'],
					'example' => $type['example'],
					'description' => $type['description'],
					'display_section' => 'configure'
				);
				$this->display_your_feed_sections[ $shortcode['display_section'] ]['settings'][] = $shortcode;


				$formatted_label .= 'Eg: type=' . $type['slug'] . '<br>';
				$formatted_label .= $type['slug'] . '="' . substr( $type['example'], 0, 14) . '"<br>';

			}
			$formatted_label .= '</code><br>';

			if ( isset( $args['shortcode']['after_description'] ) ) {
				$formatted_label .= $args['shortcode']['after_description'];
			}
			$title = $formatted_label;
		} else {
			$title = $this->format_title( $args['title'], $args['name'], $shortcode, $title_after );
		}

		if ( $args['callback'] === 'checkbox' || (isset( $args['falsefield'] ) && $args['falsefield'] === true) ) {
			$tab = 'none';
			foreach ( $this->settings_sections as $key => $settings_sections ) {
				foreach ( $settings_sections as $this_tab_sections ) {
					if ( $this_tab_sections['id'] === $args['section'] ) {
						$tab = $key;
					}
				}

			}
			$this->add_false_field( $args['name'], $tab );
		}

		if ( $args['callback'] === 'layout' || $args['callback'] === 'sub_option' ) {
			$tab = 'none';
			foreach ( $this->settings_sections as $key => $settings_sections ) {
				foreach ( $settings_sections as $this_tab_sections ) {
					if ( $this_tab_sections['id'] === $args['section'] ) {
						$tab = $key;
					}
				}

			}
			$sub_options = isset( $args['layouts'] ) ? $args['layouts'] : $args['sub_options'];
			foreach ( $sub_options as $sub_option ) {
				if ( isset( $sub_option['options'] ) ) {
					foreach( $sub_option['options'] as $sub_sub_option ) {
						if ( ! empty( $sub_sub_option['shortcode'] ) ) {
							$key = ! empty( $sub_sub_option['shortcode']['key'] ) ? $sub_sub_option['shortcode']['key'] : $sub_option['slug'] . $sub_sub_option['name'];
							$example = ! empty( $sub_sub_option['shortcode']['example'] ) ? $sub_sub_option['shortcode']['example'] : '';
							$description = ! empty( $sub_sub_option['shortcode']['description'] ) ? $sub_sub_option['shortcode']['description'] : '';
							$display_section = ! empty( $sub_sub_option['shortcode']['display_section'] ) ? $sub_sub_option['shortcode']['display_section'] : str_replace( 'sbspf_', '', $args['section'] );
							$sub_shortcode = array(
								'key' => $key,
								'example' => $example,
								'description' => $description,
								'display_section' => $display_section
							);
							if ( isset( $this->display_your_feed_sections[ $display_section ] ) ) {
								$this->display_your_feed_sections[ $display_section ]['settings'][] = $sub_shortcode;
							}
						}
						if ( $sub_sub_option['callback'] === 'checkbox' ) {
							$this->add_false_field( $sub_option['slug'] . $sub_sub_option['name'], $tab );
						}
					}
				}
			}
		}

		if ( $args['callback'] === 'textarea' ) {
			$tab = 'none';
			foreach ( $this->settings_sections as $key => $settings_sections ) {
				foreach ( $settings_sections as $this_tab_sections ) {
					if ( $this_tab_sections['id'] === $args['section'] ) {
						$tab = $key;
					}
				}

			}
			$this->add_textarea_field( $args['name'], $tab );
		}

		add_settings_field(
			$args['name'],
			$title,
			array( $this, $args['callback'] ),
			$args['section'],
			$args['section'],
			$args
		);

		if ( isset( $args['hidden'] ) ) {
			if ( $args['hidden']['callback'] === 'checkbox' ) {
				$tab = 'none';
				foreach ( $this->settings_sections as $key => $settings_sections ) {
					foreach ( $settings_sections as $this_tab_sections ) {
						if ( $this_tab_sections['id'] === $args['section'] ) {
							$tab = $key;
						}
					}

				}
				$this->add_false_field( $args['hidden']['name'], $tab );
			}
		}
	}

	public function set_feed_types( $types ) {
		$this->types = $types;
	}

	public function set_feed_layouts( $layouts ) {
		$this->layouts = $layouts;
	}

	public function set_display_table_sections( $headings ) {
		foreach ( $headings as $heading ) {
			$this->display_your_feed_sections[ $heading['slug'] ] = array(
				'label' => $heading['label'],
				'settings' => array()
			);
		}
	}

	public function checkbox( $args ) {
		$default = isset( $args['default'] ) ? $args['default'] : false;
		$selected = isset( $this->settings[ $args['name'] ] ) ? $this->settings[ $args['name'] ] : $default;
		$label = isset( $args['label'] ) ? $args['label'] : __( 'Yes' );
		$tooltip_text = isset( $args['tooltip_text'] ) ? $args['label'] : $this->default_tooltip_text();
		$has_shortcode = isset( $args['has_shortcode'] ) && $args['has_shortcode'] ? '1' : '';
		?>
      <input name="<?php echo $this->option_name .'['.esc_attr( $args['name'] ).']'; ?>" id="<?php echo $this->option_name . '_' . $args['name']; ?>" class="sbspf_single_checkbox" type="checkbox"<?php if ( $selected ) echo ' checked'; ?>/>
      <label for="<?php echo $this->option_name . '_' . $args['name'] . $has_shortcode; ?>"><?php echo esc_html( $label ); ?></label><?php if ( $has_shortcode === '1' ) : ?><code class="sbspf_shortcode"> <?php echo $args['name'] . "\n"; ?>
        Eg: <?php echo $args['name']; ?>=<?php echo $args['shortcode_example']; ?></code><br><?php endif; ?>
		<?php if ( isset( $args['tooltip_info'] ) ) : ?>
        <a class="sbspf_tooltip_link" href="JavaScript:void(0);"><?php echo $tooltip_text; ?></a>
        <p class="sbspf_tooltip sbspf_more_info"><?php echo $args['tooltip_info']; ?></p>
		<?php
		endif;
	}

	public function multi_checkbox( $args ) {
		$default = isset( $args['default'] ) ? $args['default'] : false;
		$selection_array = isset( $this->settings[ $args['name'] ] ) ? (array)$this->settings[ $args['name'] ] : (array)$default;
		$tooltip_text = isset( $args['tooltip_text'] ) ? $args['label'] : $this->default_tooltip_text();
		$index = 0;
		?>
		<?php foreach ( $args['select_options'] as $select_option ) :
			$selected = in_array( $select_option['value'], $selection_array, true );
			$pro_only = (isset( $select_option['pro'] ) && $select_option['pro']) ? ' sbspf_pro_only' : '';
			$class = ! empty( $select_option['class'] ) ? ' ' . $select_option['class'] : '';
			?>
        <div class="sbspf_multi_checkbox_option<?php echo $pro_only . $class; ?>">
          <input name="<?php echo $this->option_name .'['.esc_attr( $args['name'] ).'][]'; ?>" id="<?php echo $this->option_name . '_' . $args['name']. '_' . $index; ?>" value="<?php echo esc_attr( $select_option['value'] ); ?>" type="checkbox"<?php if ( $selected ) echo ' checked'; ?>/>
          <label for="<?php echo $this->option_name . '_' . $args['name'] . '_' . $index; ?>"><?php echo esc_html( $select_option['label'] ); ?></label>
        </div>
			<?php
			$index++;
		endforeach; ?>

		<?php if ( isset( $args['tooltip_info'] ) ) : ?>
        <a class="sbspf_tooltip_link" href="JavaScript:void(0);"><?php echo $tooltip_text; ?></a>
        <p class="sbspf_tooltip sbspf_more_info"><?php echo $args['tooltip_info']; ?></p>
		<?php
		endif;
	}

	public function text( $args ) {
		$default = isset( $args['default'] ) ? $args['default'] : '';
		$value = isset( $this->settings[ $args['name'] ] ) ? $this->settings[ $args['name'] ] : $default;
		$size = ( isset( $args['size'] ) ) ? ' size="'. $args['size'].'"' : '';
		$class = isset( $args['class'] ) ? ' class="'. esc_attr( $args['class'] ) . '"' : '';

		$tooltip_text = isset( $args['tooltip_text'] ) ? $args['label'] : $this->default_tooltip_text();

		if ( isset( $args['min'] ) ) :
			$min = ( isset( $args['min'] ) ) ? ' min="'. $args['min'].'"' : '';
			$max = ( isset( $args['max'] ) ) ? ' max="'. $args['max'].'"' : '';
			$step = ( isset( $args['step'] ) ) ? ' step="'. $args['step'].'"' : '';
			$class = isset( $args['class'] ) ? ' class="sbspf_number_field sbspf_size_' . $args['size'] . ' '. esc_attr( $args['class'] ) . '"' : ' class="sbspf_number_field sbspf_size_' . $args['size'] . '"';
			?>
          <input name="<?php echo $this->option_name.'['.$args['name'].']'; ?>" id="<?php echo $this->option_name . '_' . $args['name']; ?>"<?php echo $class; ?> type="number"<?php echo $size; ?><?php echo $min; ?><?php echo $max; ?><?php echo $step; ?> value="<?php echo esc_attr( $value ); ?>" />
		<?php elseif ( isset( $args['color'] ) ) : ?>
          <input name="<?php echo $this->option_name.'['.$args['name'].']'; ?>" id="<?php echo $this->option_name . '_' . $args['name']; ?>" class="sbspf_colorpicker" type="text" value="#<?php echo esc_attr( str_replace('#', '', $value ) ); ?>" />
		<?php else: ?>
          <input name="<?php echo $this->option_name.'['.$args['name'].']'; ?>" id="<?php echo $this->option_name . '_' . $args['name']; ?>"<?php echo $class; ?> type="text" value="<?php echo esc_attr( stripslashes( $value ) ); ?>" />
		<?php endif; ?>

		<?php if ( isset( $args['select_options'] ) ) :
			$value = isset( $this->settings[ $args['select_name'] ] ) ? $this->settings[ $args['select_name'] ] : $args['select_options'][0]['value'];
			?>
        <select name="<?php echo $this->option_name.'['.$args['select_name'].']'; ?>" id="<?php echo $this->option_name . '_' . $args['select_name']; ?>">
			<?php foreach ( $args['select_options'] as $select_option ) : ?>
              <option value="<?php echo esc_attr( $select_option['value'] ); ?>"<?php if ( (string)$select_option['value'] === (string)$value ) echo ' selected'; ?>><?php echo esc_html( $select_option['label'] ); ?></option>
			<?php endforeach; ?>
        </select>
		<?php endif; ?>

		<?php if ( isset( $args['hidden'] ) ) : ?>

			<?php
			if ( is_callable( array( $this, $args['hidden']['callback'] ) ) ){
				echo $args['hidden']['before'];
				call_user_func_array(
					array( $this, $args['hidden']['callback'] ),
					array( $args['hidden'] )
				);
				echo $args['hidden']['after'];
			}
			?>
		<?php endif; ?>

		<?php if ( isset( $args['additional'] ) ) : ?>
			<?php echo $args['additional']; ?>
		<?php endif; ?>

		<?php if ( isset( $args['tooltip_info'] ) ) : ?>
        <a class="sbspf_tooltip_link" href="JavaScript:void(0);"><?php echo $tooltip_text; ?></a>
        <p class="sbspf_tooltip sbspf_more_info"><?php echo $args['tooltip_info']; ?></p>
		<?php
		endif;
	}

	public function select( $args ) {
		$default = isset( $args['default'] ) ? $args['default'] : $args['options'][0]['value'];
		$value = isset( $this->settings[ $args['name'] ] ) ? $this->settings[ $args['name'] ] : $default;

		if ( isset( $args['min'] ) && isset( $args['max'] ) && ((int)$args['min'] < (int)$args['max']) && empty( $args['options'] ) ) {
			$args['options'] = array();
			$i = (int)$args['min'];

			while ( $i <= (int)$args['max'] ) {
				$args['options'][] = array(
					'label' => $i,
					'value' => $i
				);
				$i++;
			}
		}

		$tooltip_text = isset( $args['tooltip_text'] ) ? $args['label'] : $this->default_tooltip_text();
		?>
      <select name="<?php echo $this->option_name.'['.$args['name'].']'; ?>" id="<?php echo $this->option_name . '_' . $args['name']; ?>">
		  <?php foreach ( $args['options'] as $select_option ) : ?>
            <option value="<?php echo esc_attr( $select_option['value'] ); ?>"<?php if ( (string)$select_option['value'] === (string)$value ) echo ' selected'; ?>><?php echo esc_html( $select_option['label'] ); ?></option>
		  <?php endforeach; ?>
      </select>

		<?php if ( isset( $args['additional'] ) ) : ?>
			<?php echo $args['additional']; ?>
		<?php endif; ?>

		<?php if ( isset( $args['tooltip_info'] ) ) : ?>
        <a class="sbspf_tooltip_link" href="JavaScript:void(0);"><?php echo $tooltip_text; ?></a>
        <p class="sbspf_tooltip sbspf_more_info"><?php echo $args['tooltip_info']; ?></p>
		<?php endif;
	}

	public function textarea( $args ) {
		$default = isset( $args['default'] ) ? $args['default'] : '';
		$value = isset( $this->settings[ $args['name'] ] ) ? stripslashes( $this->settings[ $args['name'] ] ) : $default;

		if ( isset( $args['tooltip_info'] ) ) : ?>
          <span><?php echo $args['tooltip_info']; ?></span><br>
		<?php endif; ?>

      <textarea name="<?php echo $this->option_name.'['.$args['name'].']'; ?>" id="<?php echo $this->option_name . '_' . $args['name']; ?>"rows="7"><?php echo $value; ?></textarea>

		<?php if ( isset( $args['note'] ) ) : ?>
        <br><span class="sbspf_note"><?php echo $args['note']; ?></span>
		<?php endif;
	}

	public function color( $args ) {
		$args['color'] = true;
		$this->text( $args );
	}

	public function types( $args ) {
		$type_selected = isset( $this->settings[ $args['name'] ] ) ? $this->settings[ $args['name'] ] : $args['types'][0]['slug'];

		foreach ( $args['types'] as $type ) :
			$input_type = isset( $type['input_type'] ) ? $type['input_type'] : 'connected_account';
			$selected = ! empty( $this->settings[ $type['slug'] ] ) ? $this->settings[ $type['slug'] ] : $type['default'];
			if ( $input_type === 'connected_account' ) {
				$selected = isset( $this->settings[ $type['slug'] . '_ids' ] ) ? $this->settings[ $type['slug'] . '_ids' ] : array();
			}
			$on_select = isset( $type['onselect'] ) ? $type['onselect'] : false;
			?>
          <div class="sbspf_row sbspf_type_row" style="min-height: 29px;">
            <div class="sbspf_col sbspf_one">
              <input type="radio" name="<?php echo $this->option_name.'['.esc_attr( $args['name'] ).']'; ?>" class="sbspf_type_input" id="sbspf_type_<?php echo esc_attr( $type['slug'] ); ?>" value="<?php echo esc_attr( $type['slug'] ); ?>"<?php if ( $type_selected === $type['slug'] ) echo 'checked'; ?>>
              <label class="sbspf_radio_label" for="sbspf_type_<?php echo esc_attr( $type['slug'] ); ?>"><?php echo esc_html( $type['label'] ); ?>: <a class="sbspf_type_tooltip_link" href="JavaScript:void(0);"><i class="fa fa-question-circle" aria-hidden="true" style="margin-left: 2px;"></i></a></label>
            </div>
            <div class="sbspf_col sbspf_two">
				<?php if ( $input_type === 'text' ) :
					$placeholder = isset( $type['note'] ) ? ' placeholder="' . esc_attr( $type['note'] ). '"' : '';
					?>
                  <input name="<?php echo $this->option_name.'['.esc_attr( $type['slug'] ).']'; ?>" id="sbspf_<?php echo esc_attr( $type['slug'] ); ?>" type="text" value="<?php echo esc_attr( $selected ); ?>" size="45"<?php echo $placeholder; ?>>
				<?php else :
					$connected_accounts = $this->get_connected_accounts(); ?>
                  <div class="sbspf_<?php echo esc_attr( $type['slug'] ); ?>_feed_ids_wrap">
					  <?php foreach ( $connected_accounts as $connected_account ) : if ( in_array( $connected_account['channel_id'], $selected, true ) ) : ?>
                        <div id="sbspf_<?php echo esc_attr( $type['slug'] ); ?>_feed_id_<?php echo esc_attr( $connected_account['channel_id'] ); ?>" class="sbspf_<?php echo esc_attr( $type['slug'] ); ?>_feed_account_wrap">
                          <strong><?php echo esc_html( $connected_account['username'] ); ?></strong> <span>(<?php echo esc_attr( $connected_account['channel_id'] ); ?>)</span><input type="hidden" name="<?php echo $this->option_name.'['.esc_attr( $type['slug'] ).'_feed_ids][]'; ?>" value="<?php echo esc_attr( $connected_account['channel_id'] ); ?>">
                        </div>
					  <?php endif; endforeach; ?>
                  </div>
					<?php if ( empty( $selected ) ) : ?>
                  <p class="sbspf_no_accounts" style="margin-top: -3px; margin-right: 10px;"><?php _e( 'Connect an account above', 'feeds-for-youtube' ); ?></p>
				<?php endif; ?>

				<?php endif; ?>
				<?php if ( $input_type !== 'text' && isset( $type['note'] ) ) : ?>
                  <br><span class="sbspf_note"><?php echo $type['note']; ?></span>
				<?php endif; ?>
            </div>
			  <?php if ( $on_select ) : ?>
                <div class="sbspf_onselect">
					<?php call_user_func_array( array( $this, $on_select ), array( $type ) ); ?>
                </div>
			  <?php endif;  ?>

            <div class="sbspf_tooltip sbspf_type_tooltip sbspf_more_info">
				<?php if ( ! empty( $type['tooltip'] ) ) : ?>
					<?php echo $type['tooltip']; ?>
				<?php endif; ?>
            </div>



          </div>
		<?php endforeach;
	}

	public function sub_option( $args ) {
		$value = isset( $this->settings[ $args['name'] ] ) ? $this->settings[ $args['name'] ] : 'related';

		$cta_options = $args['sub_options'];
		?>
		<?php if ( ! empty( $args['before'] ) ) {
			echo $args['before'];
		}?>

      <div class="sbspf_sub_options">
		  <?php foreach ( $cta_options as $sub_option ) : ?>
            <div class="sbspf_sub_option_cell">
              <input class="sbspf_sub_option_type" id="sbspf_sub_option_type_<?php echo esc_attr( $sub_option['slug'] ); ?>" name="<?php echo $this->option_name.'['.$args['name'].']'; ?>" type="radio" value="<?php echo esc_attr( $sub_option['slug'] ); ?>"<?php if ( $sub_option['slug'] === $value ) echo ' checked'?>><label for="sbspf_sub_option_type_<?php echo esc_attr( $sub_option['slug'] ); ?>"><span class="sbspf_label"><?php echo $sub_option['label']; ?></span></label>
            </div>
		  <?php endforeach; ?>

        <div class="sbspf_box_setting">
			<?php if ( isset( $cta_options ) ) : foreach ( $cta_options as $sub_option ) : ?>
              <div class="sbspf_sub_option_settings sbspf_sub_option_type_<?php echo esc_attr( $sub_option['slug'] ); ?>">

                <div class="sbspf_sub_option_setting">
					<?php echo sby_admin_icon( 'info-circle', 'sbspf_small_svg' ); ?>&nbsp;&nbsp;&nbsp;<span class="sbspf_note" style="margin-left: 0;"><?php echo $sub_option['note']; ?></span>
                </div>
				  <?php if ( ! empty( $sub_option['options'] ) ) : ?>
					  <?php foreach ( $sub_option['options'] as $option ) :
						  $option['name'] = $sub_option['slug'].$option['name'];
						  ?>
                      <div class="sbspf_sub_option_setting">
						  <?php if ( $option['callback'] !== 'checkbox' ) :
							  if ( isset( $option['shortcode'] ) ) : ?>
                                <label title="<?php echo __( 'Click for shortcode option', 'feeds-for-youtube' ); ?>"><?php echo $option['label']; ?></label><code class="sbspf_shortcode"> <?php echo $option['name'] . "\n"; ?>
                                  Eg: <?php echo $option['name']; ?>=<?php echo $option['shortcode']['example']; ?></code><br>
							  <?php else: ?>
                                <label><?php echo $option['label']; ?></label><br>
							  <?php endif; ?>
						  <?php else:
							  $option['shortcode_example'] = $option['shortcode']['example'];
							  $option['has_shortcode'] = true;
						  endif; ?>
						  <?php call_user_func_array( array( $this, $option['callback'] ), array( $option ) ); ?>

                      </div>

					  <?php endforeach; ?>
				  <?php endif; ?>

              </div>

			<?php endforeach; endif; ?>
        </div>
      </div>
		<?php
	}

	public function cache( $args ) {
		$social_network = $this->vars->social_network();
		$type_selected = isset( $this->settings['caching_type'] ) ? $this->settings['caching_type'] : 'page';
		$caching_time = isset( $this->settings['caching_time'] ) ? $this->settings['caching_time'] : 1;
		$cache_time_unit_selected = isset( $this->settings['cache_time_unit'] ) ? $this->settings['cache_time_unit'] : 'hours';
		$cache_cron_interval_selected = isset( $this->settings['cache_cron_interval'] ) ? $this->settings['cache_cron_interval'] : '';
		$cache_cron_time = isset( $this->settings['cache_cron_time'] ) ? $this->settings['cache_cron_time'] : '';
		$cache_cron_am_pm = isset( $this->settings['cache_cron_am_pm'] ) ? $this->settings['cache_cron_am_pm'] : '';

		?>
      <div class="sbspf_cache_settings_wrap">
        <div class="sbspf_row">
          <input type="radio" name="<?php echo $this->option_name.'[caching_type]'; ?>" class="sbspf_caching_type_input" id="sbspf_caching_type_page" value="page"<?php if ( $type_selected === 'page' ) echo ' checked'?>>
          <label class="sbspf_radio_label" for="sbspf_caching_type_page"><?php _e ( 'When the page loads', 'feeds-for-youtube' ); ?></label>
          <a class="sbspf_tooltip_link" href="JavaScript:void(0);" style="position: relative; top: 2px;"><?php echo $this->default_tooltip_text() ?></a>
          <p class="sbspf_tooltip sbspf_more_info"><?php echo sprintf( __( "Your %s data is temporarily cached by the plugin in your WordPress database. There are two ways that you can set the plugin to check for new data:<br><br>
                <b>1. When the page loads</b><br>Selecting this option means that when the cache expires then the plugin will check %s for new posts the next time that the feed is loaded. You can choose how long this data should be cached for with a minimum time of 15 minutes. If you set the time to 60 minutes then the plugin will clear the cached data after that length of time, and the next time the page is viewed it will check for new data. <b>Tip:</b> If you're experiencing an issue with the plugin not updating automatically then try enabling the setting labeled <b>'Cron Clear Cache'</b> which is located on the 'Customize' tab.<br><br>
                <b>2. In the background</b><br>Selecting this option means that the plugin will check for new data in the background so that the feed is updated behind the scenes. You can select at what time and how often the plugin should check for new data using the settings below. <b>Please note</b> that the plugin will initially check for data from YouTube when the page first loads, but then after that will check in the background on the schedule selected - unless the cache is cleared.", 'feeds-for-youtube' ), $social_network, $social_network ); ?>
          </p>
        </div>
        <div class="sbspf_row sbspf-caching-page-options" style="display: none;">
			<?php _e ( 'Every', 'feeds-for-youtube' ); ?>:
          <input name="<?php echo $this->option_name.'[caching_time]'; ?>" type="text" value="<?php echo esc_attr( $caching_time ); ?>" size="4">
          <select name="<?php echo $this->option_name.'[caching_time_unit]'; ?>">
            <option value="minutes"<?php if ( $cache_time_unit_selected === 'minutes' ) echo ' selected'?>><?php _e ( 'Minutes', 'feeds-for-youtube' ); ?></option>
            <option value="hours"<?php if ( $cache_time_unit_selected === 'hours' ) echo ' selected'?>><?php _e ( 'Hours', 'feeds-for-youtube' ); ?></option>
            <option value="days"<?php if ( $cache_time_unit_selected === 'days' ) echo ' selected'?>><?php _e ( 'Days', 'feeds-for-youtube' ); ?></option>
          </select>
          <a class="sbspf_tooltip_link" href="JavaScript:void(0);"><?php _e ( 'What does this mean?', 'feeds-for-youtube' ); ?></a>
          <p class="sbspf_tooltip sbspf_more_info"><?php echo sprintf( __("Your %s posts are temporarily cached by the plugin in your WordPress database. You can choose how long the posts should be cached for. If you set the time to 1 hour then the plugin will clear the cache after that length of time and check %s for posts again.", 'feeds-for-youtube' ), $social_network, $social_network ); ?></p>
        </div>

        <div class="sbspf_row">
          <input type="radio" name="<?php echo $this->option_name.'[caching_type]'; ?>" id="sbspf_caching_type_cron" class="sbspf_caching_type_input" value="background" <?php if ( $type_selected === 'background' ) echo ' checked'?>>
          <label class="sbspf_radio_label" for="sbspf_caching_type_cron"><?php _e ( 'In the background', 'feeds-for-youtube' ); ?></label>
        </div>
        <div class="sbspf_row sbspf-caching-cron-options" style="display: block;">

          <select name="<?php echo $this->option_name.'[cache_cron_interval]'; ?>" id="sbspf_cache_cron_interval">
            <option value="30mins"<?php if ( $cache_cron_interval_selected === '30mins' ) echo ' selected'?>><?php _e ( 'Every 30 minutes', 'feeds-for-youtube' ); ?></option>
            <option value="1hour"<?php if ( $cache_cron_interval_selected === '1hour' ) echo ' selected'?>><?php _e ( 'Every hour', 'feeds-for-youtube' ); ?></option>
            <option value="12hours"<?php if ( $cache_cron_interval_selected === '12hours' ) echo ' selected'?>><?php _e ( 'Every 12 hours', 'feeds-for-youtube' ); ?></option>
            <option value="24hours"<?php if ( $cache_cron_interval_selected === '24hours' ) echo ' selected'?>><?php _e ( 'Every 24 hours', 'feeds-for-youtube' ); ?></option>
          </select>

          <div id="sbspf-caching-time-settings" style="">
			  <?php _e ( 'at', 'feeds-for-youtube' ); ?>
            <select name="<?php echo $this->option_name.'[cache_cron_time]'; ?>" style="width: 80px">
              <option value="1"<?php if ( (int)$cache_cron_time === 1 ) echo ' selected'?>>1:00</option>
              <option value="2"<?php if ( (int)$cache_cron_time === 2 ) echo ' selected'?>>2:00</option>
              <option value="3"<?php if ( (int)$cache_cron_time === 3 ) echo ' selected'?>>3:00</option>
              <option value="4"<?php if ( (int)$cache_cron_time === 4 ) echo ' selected'?>>4:00</option>
              <option value="5"<?php if ( (int)$cache_cron_time === 5 ) echo ' selected'?>>5:00</option>
              <option value="6"<?php if ( (int)$cache_cron_time === 6 ) echo ' selected'?>>6:00</option>
              <option value="7"<?php if ( (int)$cache_cron_time === 7 ) echo ' selected'?>>7:00</option>
              <option value="8"<?php if ( (int)$cache_cron_time === 8 ) echo ' selected'?>>8:00</option>
              <option value="9"<?php if ( (int)$cache_cron_time === 9 ) echo ' selected'?>>9:00</option>
              <option value="10"<?php if ( (int)$cache_cron_time === 10 ) echo ' selected'?>>10:00</option>
              <option value="11"<?php if ( (int)$cache_cron_time === 11 ) echo ' selected'?>>11:00</option>
              <option value="0"<?php if ( (int)$cache_cron_time === 0 ) echo ' selected'?>>12:00</option>
            </select>

            <select name="<?php echo $this->option_name.'[cache_cron_am_pm]'; ?>" style="width: 50px">
              <option value="am"<?php if ( $cache_cron_am_pm === 'am' ) echo ' selected'?>><?php _e ( 'AM', 'feeds-for-youtube' ); ?></option>
              <option value="pm"<?php if ( $cache_cron_am_pm === 'pm' ) echo ' selected'?>><?php _e ( 'PM', 'feeds-for-youtube' ); ?></option>
            </select>
          </div>

			<?php
			if ( wp_next_scheduled( 'sbspf_feed_update' ) ) {
				$time_format = get_option( 'time_format' );
				if ( ! $time_format ) {
					$time_format = 'g:i a';
				}
				//
				$schedule = wp_get_schedule( 'sbspf_feed_update' );
				if ( $schedule == '30mins' ) $schedule = __( 'every 30 minutes', 'feeds-for-youtube' );
				if ( $schedule == 'twicedaily' ) $schedule = __( 'every 12 hours', 'feeds-for-youtube' );
				$sbspf_next_cron_event = wp_next_scheduled( 'sbspf_feed_update' );
				echo '<p class="sbspf-caching-sched-notice"><span><b>' . __( 'Next check', 'feeds-for-youtube' ) . ': ' . date( $time_format, $sbspf_next_cron_event + sbspf_get_utc_offset() ) . ' (' . $schedule . ')</b> - ' . __( 'Note: Saving the settings on this page will clear the cache and reset this schedule', 'feeds-for-youtube' ) . '</span></p>';
			} else {
				echo '<p style="font-size: 11px; color: #666;">' . __( 'Nothing currently scheduled', 'feeds-for-youtube' ) . '</p>';
			}
			?>
        </div>
      </div>
		<?php
	}

	public function layout( $args ) {
		$default = isset( $args['default'] ) ? $args['default'] : $args['layouts'][0]['slug'];
		$value = isset( $this->settings[ $args['name'] ] ) ? $this->settings[ $args['name'] ] : $default;
		?>
      <div class="sbspf_layouts">
		  <?php foreach ( $args['layouts'] as $layout ) : ?>
            <div class="sbspf_layout_cell">
              <input class="sbspf_layout_type" id="sbspf_layout_type_<?php echo esc_attr( $layout['slug'] ); ?>" name="<?php echo $this->option_name.'['.$args['name'].']'; ?>" type="radio" value="<?php echo esc_attr( $layout['slug'] ); ?>"<?php if ( $layout['slug'] === $value ) echo ' checked'?>><label for="sbspf_layout_type_<?php echo esc_attr( $layout['slug'] ); ?>"><span class="sbspf_label"><?php echo $layout['label']; ?></span><img src="<?php echo esc_url( $this->vars->plugin_url() . $layout['image'] ); ?>" alt=""></label>
            </div>
		  <?php endforeach; ?>

        <div class="sbspf_layout_options_wrap">
			<?php foreach ( $args['layouts'] as $layout ) : ?>
              <div class="sbspf_layout_settings sbspf_layout_type_<?php echo esc_attr( $layout['slug'] ); ?>">

                <div class="sbspf_layout_setting">
					<?php echo sby_admin_icon( 'info-circle' ); ?>&nbsp;&nbsp;&nbsp;<span class="sbspf_note" style="margin-left: 0;"><?php echo $layout['note']; ?></span>
                </div>
				  <?php if ( ! empty( $layout['options'] ) ) : ?>
					  <?php foreach ( $layout['options'] as $option ) :
						  $option['name'] = $layout['slug'].$option['name'];
						  ?>
                      <div class="sbspf_layout_setting">
						  <?php if ( $option['callback'] !== 'checkbox' ) : ?>
                            <label title="<?php echo __( 'Click for shortcode option', 'feeds-for-youtube' ); ?>"><?php echo $option['label']; ?></label><code class="sbspf_shortcode"> <?php echo $option['name'] . "\n"; ?>
                              Eg: <?php echo $option['name']; ?>=<?php echo $option['shortcode']['example']; ?></code><br>
						  <?php else:
							  $option['shortcode_example'] = $option['shortcode']['example'];
							  $option['has_shortcode'] = true;
						  endif; ?>
						  <?php call_user_func_array( array( $this, $option['callback'] ), array( $option ) ); ?>

                      </div>

					  <?php endforeach; ?>
				  <?php endif; ?>

              </div>

			<?php endforeach; ?>
        </div>
      </div>
		<?php
	}

	public function instructions( $args ) {
		?>
      <div class="sbspf_instructions_wrap">
		  <?php echo $args['instructions']?>
      </div>
		<?php
	}

	public function format_title( $label, $name, $shortcode_args = false, $after = '' ) {
		$formatted_label = '<label for="' . $this->option_name . '_' . $name . '">' . $label .'</label>';
		if ( $shortcode_args ) {
			$formatted_label .= '<code class="sbspf_shortcode"> ' . $shortcode_args['key'] . "\n";
			$formatted_label .= 'Eg: ' . $shortcode_args['key'] . '=' . $shortcode_args['example'] . '</code><br>';
		}
		$formatted_label .= $after;

		return $formatted_label;
	}

	public function validate_options( $input, $tab ) {
		$updated_options = get_option( $this->option_name, array() );
		$false_if_empty_keys = $this->get_false_fields( $tab );
		$textarea_keys = $this->get_textarea_fields( $tab );

		foreach ( $false_if_empty_keys as $false_key ) {
			$updated_options[ $false_key ] = false;
		}

		foreach ( $input as $key => $val ) {
			if ( 'custom_js' === $key ) {
				$updated_options[ $key ] = $val;
			} else if ( in_array( $key, $false_if_empty_keys ) ) {
				$updated_options[ $key ] = ($val === 'on');
			} elseif ( in_array( $key, $textarea_keys ) ) {
				$updated_options[ $key ] = sanitize_textarea_field( $val );
			} elseif ( is_array( $val ) ) {
				$updated_options[ $key ] = array();
				foreach ( $val as $key2 => $val2 ) {
					$updated_options[ $key ][ $key2 ] = sanitize_text_field( $val2 );
				}
			} else {
				$updated_options[ $key ] = sanitize_text_field( $val );
			}
		}

		if ( $tab === 'configure' ) {
			do_action( $this->option_name . '_after_configure_save', $updated_options );
		}

		return $updated_options;
	}


	public function update_options( $new_settings ) {
		update_option( $this->get_option_name(), $new_settings );
		$this->settings = $new_settings;
	}

	public function get_sections( $tab ) {
		if ( isset( $this->settings_sections[ $tab ] ) ) {
			return $this->settings_sections[ $tab ];
		}
		return array();
	}

	public function set_active_tab( $active_tab ) {
		foreach ( $this->tabs as $tab ) {
			if ( $tab['slug'] === $active_tab ) {
				$this->active_tab = $tab['slug'];
			}
		}
	}

	public function get_tabs() {
		return $this->tabs;
	}

	public function get_active_tab() {
		return $this->active_tab;
	}

	public function get_slug() {
		return $this->slug;
	}

	public function get_plugin_name() {
		return $this->plugin_name;
	}

	public function get_path( $view ) {
		return trailingslashit( $this->base_path ) . $view . '.php';
	}

	public function create_options_page() {
		require_once trailingslashit( $this->base_path ) . 'main.php';
	}

	public function next_step() {
		$return = array();
		$i = 0;
		foreach ( $this->tabs as $tab ) {
			if ( $this->active_tab === $tab['slug'] && isset( $tab['next_step_instructions'] ) ) {
				$next_tab_slug = isset( $this->tabs[ $i + 1 ]['slug'] ) ? $this->tabs[ $i + 1 ]['slug'] : $tab['slug'];
				$return = array(
					'instructions' => $tab['next_step_instructions'],
					'next_tab' => $next_tab_slug
				);
			}
			$i++;
		}
		return $return;
	}

	public function get_connected_accounts() {
		global $sbspf_settings;

		if ( isset( $sbspf_settings['connected_accounts'] ) ) {
			return $sbspf_settings['connected_accounts'];
		}
		return array();
	}

	public static function connect_account( $args ) {
		global $sbspf_settings;

		// do connection

		// random fake data
		$account_id = time();

		$sbspf_settings['connected_accounts'][ $account_id ] = array(
			'access_token' => 'at_' . str_shuffle( $account_id ),
			'channel_id' => $account_id,
			'username' => 'test' . $account_id,
			'is_valid' => true,
			'last_checked' => time(),
			'profile_picture' => $args['profile_picture']
		);

		update_option( 'sbspf_settings', $sbspf_settings );

		return $sbspf_settings['connected_accounts'][ $account_id ];
	}

	public function default_tooltip_text() {
		return '<span class="screen-reader-text">' . __( 'What does this mean?', 'feeds-for-youtube' ) . '</span>' . sby_admin_icon( 'question-circle' );
	}
}