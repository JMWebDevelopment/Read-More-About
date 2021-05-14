<?php
/**
 * File that creates the widget to display read more about in the sidebar.
 *
 * PHP version 7.3
 *
 * @link       https://jacobmartella.com
 * @since      2.0.0
 *
 * @package    Read_More_About
 * @subpackage Read_More_About/public/partials
 */

/**
 * Read-more-about-widget.php
 *
 * Creates the a widget for the plugin that the user can use
 *
 * @author Jacob Martella
 * @package Read More About
 * @version 2.0
 */
class Read_More_About_Widget extends WP_Widget {

	/**
	 * Read_More_About_Widget constructor.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		parent::__construct(
			'read_more_about_widget',
			__( 'Read More About Widget', 'read-more-about' ),
			[
				'classname'     => 'read_more_about_widget',
				'description'   => 'Displays additional links for a story in the sidebar.'
			]
		);
	}

	/**
	 * Outputs the HTML of the widget
	 *
	 * @param array $args
	 *
	 * @param array $instance
	 *
	 * @since 1.4
	 */
	public function widget( $args, $instance ) {

		extract( $args );

		if ( is_single() ) {

			$fields = get_post_meta( get_the_ID(), 'read_more_links', true );

			if ( $fields ) {

				echo $args['before_widget'];

				echo $args['before_title'] . esc_html( apply_filters( 'widget_title', $instance['title'] ) ) . $args['after_title'];

				echo '<aside class="read-more-about-widget">';
				foreach ( $fields as $field ) {
					echo '<div class="story">';
					if ( 'internal' === $field['read_more_about_in_ex'] ) {
						if ( has_post_thumbnail( $field['read_more_about_internal_link'] ) ) {
							echo '<div class="photo"><a href="' . get_the_permalink( $field['read_more_about_internal_link'] ) . '">' . get_the_post_thumbnail( $field['read_more_about_internal_link'], 'read-more' ) . '</a></div>';
						}
						echo '<h3 class="story-title"><a href="' . get_the_permalink( $field[ 'read_more_about_internal_link' ] ) . '">' . get_the_title( $field['read_more_about_internal_link'] ) . '</a></h3>';
					} else {
						echo '<h3 class="story-title"><a href="' . $field['read_more_about_link'] . '" target="_blank">' . $field['read_more_about_external_title'] . '</a></h3>';
					}
					if ( $field['read_more_about_description'] ) {
						echo apply_filters( 'the_content', $field['read_more_about_description'] );
					}
					echo '</div>';
				}
				echo '</aside>';

				echo $args['after_widget'];

			}
		}
	}

	/**
	 * Creates the form on the back end to accept user inputs
	 *
	 * @param array $instance
	 *
	 * @since 1.4
	 */
	public function form( $instance ) {

		if ( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		}
		else {
			$title = __( '', 'read-more-about' );
		}

		echo '<p>';
		echo '<label for="' . esc_attr( $this->get_field_id( 'title' ) ) . '">' . esc_html__( 'Title:', 'read-more-about' ) . '</label>';
		echo '<input class="widefat" id="' . esc_attr( $this->get_field_id( 'title' ) ) . '" name="' . esc_attr( $this->get_field_name( 'title' ) ) . '" type="text" value="' . esc_attr( $title ) . '" />';
		echo '</p>';

	}

	/**
	 * Updates the widget when the user hits save
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array, an instance of the widget with the updated options
	 *
	 * @since 1.4
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = [];
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
		return $instance;
	}

}
