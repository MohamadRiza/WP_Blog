<?php
/**
 * Class SbyWidget
 *
 * Adds support for a text widget with the [youtube-feed] shortcode inside
 */
namespace SmashBalloon\YouTubeFeed;

use WP_Widget;

class SbyWidget extends WP_Widget
{
    public function __construct() {
        parent::__construct(
            'youtube-feeds-widget',
            __( 'YouTube Feeds', 'feeds-for-youtube' ),
            array( 'description' => __( 'Display your YouTube feeds', 'feeds-for-youtube' ), )
        );
    }

    public function widget( $args, $instance ) {

        $title = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) : '';
        $content = isset( $instance['content'] ) ? strip_tags( $instance['content'] ) : '['.SBY_SLUG.']';

        echo $args['before_widget'];

        if ( ! empty( $title ) ) {
            echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
        }

        echo do_shortcode( $content );

        echo $args['after_widget'];
    }

    public function form( $instance ) {

        $title = isset( $instance['title'] ) ? $instance['title'] : '';
        $content = isset ( $instance['content'] ) ? strip_tags( $instance['content'] ) : '['.SBY_SLUG.']';
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'content' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'content' ) ); ?>" rows="16"><?php echo strip_tags( $content ); ?></textarea>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['content'] = ( ! empty( $new_instance['content'] ) ) ? strip_tags( $new_instance['content'] ) : '';

        return $instance;
    }
}

// register and load the widget
function sby_load_widget() {
    register_widget( 'SbyWidget' );
}
add_action( 'widgets_init', 'sby_load_widget' );

// allow shortcode in widgets
add_filter( 'widget_text', 'do_shortcode' );
