<?php 
/*
* Plugin Name: Read More About
* Plugin URI: http://www.jacobmartella.com/read-more-about/
* Description: Allows users to add links in a story using a shortcode to provide addition reading material about a subject. Works great for large topics that can't all be explained in one post.
* Version: 1.6.1
* Author: Jacob Martella
* Author URI: http://www.jacobmartella.com
* License: GPLv3
* Text Domain: read-more-about
*/
/**
* Set up the plugin when the user activates the plugin. Adds the breaking news custom post type the text domain for translations.
*/
$read_more_about_plugin_path = plugin_dir_path( __FILE__ );
define( 'READ_MORE_ABOUT_PATH', $read_more_about_plugin_path );

//* Load the custom fields
include_once( READ_MORE_ABOUT_PATH . 'admin/read-more-about-admin.php' );

//* Load the widget
include_once( READ_MORE_ABOUT_PATH . 'read-more-about-widget.php' );

//* Load the text domain
function read_more_about_load_plugin_textdomain() {
	load_plugin_textdomain( 'read-more-about', false, basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'read_more_about_load_plugin_textdomain' );
/**
* Loads the styles for the read more about section on the front end
*/
function read_more_about_styles() {
	wp_enqueue_style( 'read-more-about-style', plugin_dir_url(__FILE__) . 'css/read-more-about.css' );
	wp_enqueue_style( 'lato', '//fonts.googleapis.com/css?family=Lato:100,300,400,700' );
  	wp_enqueue_style( 'oswald', '//fonts.googleapis.com/css?family=Oswald:400,700,300' );
}
add_action( 'wp_enqueue_scripts', 'read_more_about_styles' );

/**
* Loads and prints the styles for the breaking news custom post type
*/
function read_more_about_admin_style() {
	global $typenow;
	if ( $typenow == 'post' ) {
		wp_enqueue_style( 'read_more_about_admin_styles', plugin_dir_url(__FILE__) . 'css/read-more-about-admin.css' );
	}
}
add_action( 'admin_print_styles', 'read_more_about_admin_style' );

/**
* Loads the script for the breaking news custom post type
*/
function read_more_about_admin_scripts() {
	global $typenow;
	if ( $typenow == 'post' ) {
		wp_enqueue_script( 'read_more_about_admin_script', plugin_dir_url( __FILE__ ) . 'js/read-more-about-admin.js' );
	}
}
add_action( 'admin_enqueue_scripts', 'read_more_about_admin_scripts' );

//* Register and create the shortcode to display the section
function read_more_about_register_shortcode() {
	add_shortcode( 'read-more', 'read_more_about_shortcode' );
}
add_action( 'init', 'read_more_about_register_shortcode' );
function read_more_about_shortcode( $atts ) {
	extract( shortcode_atts( array(
		'title' => __( 'Read More', 'read-more-about' ),
		'float' => 'left'
	), $atts ) );
	$the_post_id = get_the_ID();

	$fields = get_post_meta( $the_post_id, 'read_more_links', true );
	$color = get_post_meta( $the_post_id, 'read_more_color_scheme', true );

	$html = '';

	if ( $fields ) {
		$html .= '<aside class="read-more-about ' . $float . ' ' . $color .'">';
		$html .= '<h2 class="title">' . $title . '</h2>';
		foreach ( $fields as $field ) {
			$html .= '<div class="story">';
			if( $field['read_more_about_in_ex'] == 'internal' ) {
				if ( has_post_thumbnail( $field[ 'read_more_about_internal_link' ] ) ){
					$html .= '<div class="photo"><a href="' . get_the_permalink( $field[ 'read_more_about_internal_link' ] ) . '">' . get_the_post_thumbnail( $field[ 'read_more_about_internal_link' ], 'read-more' ) . '</a></div>';
				}
				$html .= '<h3 class="story-title"><a href="' . get_the_permalink( $field[ 'read_more_about_internal_link' ] ) . '">' . get_the_title( $field[ 'read_more_about_internal_link' ] ) . '</a></h3>';
			} else {
				$html .= '<h3 class="story-title"><a href="' . $field[ 'read_more_about_link' ] . '" target="_blank">' . $field[ 'read_more_about_external_title' ] . '</a></h3>';
			}
			if ( $field[ 'read_more_about_description'] ) {
			    $html .= apply_filters( 'the_content', $field[ 'read_more_about_description'] );
            }
			$html .= '</div>';
		}
		$html .= '</aside>';
	}

	return $html;
}

//* Add a button to the TinyMCE Editor to make it easier to add the shortcode
add_action( 'init', 'read_more_about_buttons' );
function read_more_about_buttons() {
    add_filter( 'mce_external_plugins', 'read_more_about_add_buttons' );
    add_filter( 'mce_buttons', 'read_more_about_register_buttons' );
}
function read_more_about_add_buttons( $plugin_array ) {
    $plugin_array[ 'read_more_about' ] = plugin_dir_url( __FILE__ ) . 'js/read-more-about-admin-button.js';
    return $plugin_array;
}
function read_more_about_register_buttons( $buttons ) {
    array_push( $buttons, 'read_more_about' );
    return $buttons;
}


//* Load the Gutenberg block
function read_more_about_blocks_editor_scripts() {
	// Make paths variables so we don't write em twice ;)
	$blockPath = '/js/editor.blocks.js';
	$editorStylePath = '/assets/css/blocks.editor.css';
	// Enqueue the bundled block JS file
	wp_enqueue_script(
		'read-more-about-blocks-js',
		plugins_url( $blockPath, __FILE__ ),
		[ 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-api' ],
		filemtime( plugin_dir_path(__FILE__) . $blockPath )
	);
	// Pass in REST URL
	wp_localize_script(
		'read-more-about-blocks-js',
		'read_more_about_globals',
		[
			'rest_url' => esc_url( rest_url() ),
			'nonce'    => wp_create_nonce( 'wp_rest' ),
		]);
	// Enqueue optional editor only styles
	wp_enqueue_style(
		'read-more-about-editor-css',
		plugins_url( $editorStylePath, __FILE__)
	);
}
// Hook scripts function into block editor hook
add_action( 'enqueue_block_editor_assets', 'read_more_about_blocks_editor_scripts' );

function read_more_about_block_scripts() {
	// Make paths variables so we don't write em twice ;)
	$stylePath = '/assets/css/blocks.style.css';
	// Enqueue optional editor only styles
	wp_enqueue_style(
		'read-more-about-block-css',
		plugins_url( $stylePath, __FILE__)
	);
}
// Hook scripts function into block editor hook
add_action( 'enqueue_block_assets', 'read_more_about_block_scripts' );

add_action( 'rest_api_init', 'read_more_extend_rest_post_response' );
function read_more_extend_rest_post_response() {
	register_rest_field( 'post',
		'read_more_featured_image_src', //NAME OF THE NEW FIELD TO BE ADDED - you can call this anything
		array(
			'get_callback'    => 'read_more_get_image_src',
			'update_callback' => null,
			'schema'          => null,
		)
	);
}

function read_more_get_image_src(  $object, $field_name, $request  ) {
	$feat_img_array['full'] = wp_get_attachment_image_src( $object['featured_media'], 'full', false );
	$feat_img_array['thumbnail'] = wp_get_attachment_image_src( $object['featured_media'], 'thumbnail', false );
	$feat_img_array['srcset'] = wp_get_attachment_image_srcset( $object['featured_media'] );
	$feat_img_array['alt'] = get_post_meta( $object['featured_media'], '_wp_attachment_image_alt', true );
	$image = is_array( $feat_img_array ) ? $feat_img_array : 'false';
	return $image;
}