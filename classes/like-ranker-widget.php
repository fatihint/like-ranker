<?php

/**
 * Widget class for creating custom widget
 * Inherits from WP_Widget
 */
class LR_Widget extends WP_Widget {

    // Constructor function
    // Invokes WP_Widget constructor with new arguments for description and title
    function __construct() {
        parent::__construct(
        'lr_widget',
        __('Like Ranker Widget', 'lr_widget_domain'),
        array( 'description' => __( 'Like Ranker widget for listing top 10 posts', 'lr_widget_domain' ) )
        );
    }

    // Creating widget front-end
    public function widget( $args, $instance ) {
        // before and after widget arguments are defined by themes
        $title = apply_filters( 'widget_title', $instance['title'] );
        echo $args['before_widget'];
        if ( ! empty( $title ) ) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        // Display the output
        $content = '';
        // WP_Query arguments
        $query_args = array(
            'post_type' => 'post',
            'meta_key' => '_Like',
            'meta_value' => 0,
            'meta_compare' => '>',
            'orderby' => 'meta_value_num',
            'order' => 'DESC',
            'post_per_page' => 10
        );
        // Custom query to get top 10 posts
        $query = new WP_Query( $query_args );
        $i = 1;
        // Widget's html for the theme
        foreach ( $query->posts as $post ) {
            ?>
            <h4 class="lr-post">
                <span><?php echo $i.') '; ?></span>
                <a class="widget-posts" href="<?php echo $post->guid; ?>"> <?php echo esc_html( $post->post_title ); ?>  - </a>
                <span><?php echo esc_html( $post->_Like ) . ' Likes '?></span>
            </h4>
            <?
            $i++;
        }
        echo $args['after_widget'];
    }

    public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
        } else {
            $title = __( 'Top 10 Posts', 'lr_widget_domain' );
        }
        // Widget admin form
        ?>
        <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <?php
    }

    // Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        return $instance;
    }
}
