<?php
/**
 * Holds all of the public side functions.
 *
 * PHP version 7.3
 *
 * @link       https://jacobmartella.com
 * @since      2.0.0
 *
 * @package    Read_More_About
 * @subpackage Read_More_About/public
 */

namespace Read_More_About;

/**
 * Runs the public side.
 *
 * This class defines all code necessary to run on the public side of the plugin.
 *
 * @since      2.0.0
 * @package    Read_More_About
 * @subpackage Read_More_About/public
 */
class Read_More_About_Public {

	/**
	 * Version of the plugin.
	 *
	 * @since 2.0.0
	 * @var string $version Description.
	 */
	private $version;

	/**
	 * Builds the Read_More_About_Public object.
	 *
	 * @since 2.0.0
	 *
	 * @param string $version Version of the plugin.
	 */
	public function __construct( $version ) {
		$this->version = $version;
	}

	/**
	 * Enqueues the styles for the public side of the plugin.
	 *
	 * @since 2.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'read-more-about-public', plugin_dir_url( __FILE__ ) . 'css/read-more-about-style.min.css', [], $this->version, 'all' );
	}

	/**
	 * Enqueues the scripts for the public side of the plugin.
	 *
	 * @since 2.0.0
	 */
	public function enqueue_scripts() {

	}

	/**
	 * Registers the read more about shortcode.
	 *
	 * @since 2.0.0
	 */
	public function register_shortcode() {
		add_shortcode( 'read-more', [ $this, 'read_more_about_shortcode' ] );
	}

	/**
	 * Renders the Read More About shortcode.
	 *
	 * @since 2.0.0
	 *
	 * @param array $atts      The attributes for the shortcode.
	 * @return string          The HTML for the read more about shortcode.
	 */
	public function read_more_about_shortcode( $atts ) {
		extract(
			shortcode_atts(
				[
					'title' => __( 'Read More', 'read-more-about' ),
					'float' => 'left',
				],
				$atts
			)
		);
		$the_post_id = get_the_ID();

		$fields = get_post_meta( $the_post_id, 'read_more_links', true );
		$color  = get_post_meta( $the_post_id, 'read_more_color_scheme', true );

		$html = '';

		if ( $fields ) {
			$html .= '<aside class="read-more-about ' . $float . ' ' . $color .'">';
			$html .= '<h2 class="title">' . $title . '</h2>';
			foreach ( $fields as $field ) {
				$html .= '<div class="story">';
				if ( 'internal' === $field['read_more_about_in_ex'] ) {
					if ( has_post_thumbnail( $field['read_more_about_internal_link'] ) ) {
						$html .= '<div class="photo"><a href="' . get_the_permalink( $field['read_more_about_internal_link'] ) . '">' . get_the_post_thumbnail( $field['read_more_about_internal_link'], 'read-more' ) . '</a></div>';
					}
					$html .= '<h3 class="story-title"><a href="' . get_the_permalink( $field['read_more_about_internal_link'] ) . '">' . get_the_title( $field['read_more_about_internal_link'] ) . '</a></h3>';
				} else {
					$html .= '<h3 class="story-title"><a href="' . $field['read_more_about_link'] . '" target="_blank">' . $field['read_more_about_external_title'] . '</a></h3>';
				}
				if ( $field['read_more_about_description'] ) {
					$html .= apply_filters( 'the_content', $field['read_more_about_description'] );
				}
				$html .= '</div>';
			}
			$html .= '</aside>';
		}

		return $html;
	}

	/**
	 * Loads and registers the read more about widget.
	 *
	 * @since 2.0.0
	 */
	public function register_widget() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/read-more-about-widget.php';

		register_widget( 'Read_More_About_Widget' );
	}


}
