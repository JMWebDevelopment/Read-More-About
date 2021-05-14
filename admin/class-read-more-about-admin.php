<?php
/**
 * Holds all of the admin side functions.
 *
 * PHP version 7.3
 *
 * @link       https://jacobmartella.com
 * @since      2.0.0
 *
 * @package    Read_More_About
 * @subpackage Read_More_About/admin
 */

namespace Read_More_About;

/**
 * Runs the admin side.
 *
 * This class defines all code necessary to run on the admin side of the plugin.
 *
 * @since      2.0.0
 * @package    Read_More_About
 * @subpackage Read_More_About/admin
 */
class Read_More_About_Admin {

	/**
	 * Version of the plugin.
	 *
	 * @since 2.0.0
	 * @var string $version Description.
	 */
	private $version;


	/**
	 * Builds the Read_More_About_Admin object.
	 *
	 * @since 2.0.0
	 *
	 * @param string $version Version of the plugin.
	 */
	public function __construct( $version ) {
		$this->version = $version;
	}

	/**
	 * Enqueues the styles for the admin side of the plugin.
	 *
	 * @since 2.0.0
	 */
	public function enqueue_styles() {
		global $typenow;
		if ( 'post' === $typenow ) {
			wp_enqueue_style( 'read-more-about-admin', plugin_dir_url( __FILE__ ) . 'css/admin-style.min.css', [], $this->version, 'all' );
		}
	}

	/**
	 * Enqueues the scripts for the admin side of the plugin.
	 *
	 * @since 2.0.0
	 */
	public function enqueue_scripts() {
		global $typenow;
		if ( 'post' === $typenow ) {
			wp_enqueue_script( 'read-more-about-admin-script', plugin_dir_url( __FILE__ ) . 'js/read-more-about-admin.min.js', [ 'jquery' ], $this->version, 'all' );
		}
	}

	/**
	 * Adds in the meta box for the read more about section.
	 *
	 * @since 2.0.0
	 */
	public function add_meta_box() {
		add_meta_box( 'read-more-about-meta', esc_html__( 'Related Links', 'read-more-about' ) , [ $this, 'meta_box_display' ], [ 'post', 'page' ], 'normal', 'default' );
	}

	/**
	 * Displays the meta box for the read more about section.
	 *
	 * @since 2.0.0
	 */
	public function meta_box_display() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/read-more-about-meta-box.php';
	}

	/**
	 * Saves the data in the meta box for the read more about section.
	 *
	 * @since 2.0.0
	 *
	 * @param int $post_id      The ID of the post.
	 */
	public function save_meta_box( $post_id ) {
		$args        = array( 'numberposts' => -1 );
		$posts_array = [];
		$more_posts  = get_posts( $args );
		foreach ( $more_posts as $the_post ) {
			setup_postdata( $the_post );
			$the_id                 = get_the_ID();
			$name                   = get_the_title();
			$posts_array[ $the_id ] = $name;
		}
		$color_array             = [];
		$color_array['light']    = 'Light';
		$color_array['dark']     = 'Dark';
		$in_ex_array['external'] = __( 'External', 'read-more-about' );
		$in_ex_array['internal'] = __( 'Internal', 'read-more-about' );

		if ( ! isset( $_POST['read_more_about_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['read_more_about_meta_box_nonce'], 'read_more_about_meta_box_nonce' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$old = get_post_meta( $post_id, 'read_more_links', true );
		$new = array();

		$in_ex            = $_POST['read_more_about_in_ex'];
		$ex_link          = $_POST['read_more_about_link'];
		$ex_title         = $_POST['read_more_about_external_title'];
		$in_link          = $_POST['read_more_about_internal_link'];
		$link_description = $_POST['read_more_about_description'];
		$color            = $_POST['read_more_color_scheme'];

		if ( isset( $in_ex ) ) {
			$num = count( $in_ex );
		}

		if ( $color && array_key_exists( $color, $color_array ) ) {
			update_post_meta( $post_id, 'read_more_color_scheme', wp_filter_nohtml_kses( $_POST['read_more_color_scheme'] ) );
		}

		for ( $i = 0; $i < $num; $i++ ) {

			if ( isset( $in_ex ) ) {

				if ( isset( $in_ex[ $i ] ) && array_key_exists( $in_ex[ $i ], $in_ex_array ) && $this->is_filled( $in_ex[ $i ], $ex_link[ $i ], $in_link[ $i ] ) ) {
					$new[ $i ]['read_more_about_in_ex'] = wp_filter_nohtml_kses( $in_ex[ $i ] );
				}

				if ( isset( $ex_link[ $i ] ) && $this->is_filled( $in_ex[ $i ], $ex_link[ $i ], $in_link[ $i ] ) ) {
					$new[ $i ]['read_more_about_link'] = wp_filter_nohtml_kses( $ex_link[ $i ] );
				}

				if ( isset( $ex_title[ $i ] ) && $this->is_filled( $in_ex[ $i ], $ex_link[ $i ], $in_link[ $i ] ) ) {
					$new[ $i ]['read_more_about_external_title'] = stripslashes( wp_strip_all_tags( $ex_title[ $i ] ) );
				}

				if ( isset( $in_link[ $i ] ) && array_key_exists( $in_link[ $i ], $posts_array ) && $this->is_filled( $in_ex[ $i ], $ex_link[ $i ], $in_link[ $i ] ) ) {
					$new[ $i ]['read_more_about_internal_link'] = wp_filter_nohtml_kses( $in_link[ $i ] );
				}

				if ( isset( $link_description[ $i ] ) && $this->is_filled( $in_ex[ $i ], $ex_link[ $i ], $in_link[ $i ] ) ) {
					$new[ $i ]['read_more_about_description'] = wp_filter_nohtml_kses( $link_description[ $i ] );
				}
			}
		}
		if ( ! empty( $new ) && $new !== $old ) {
			update_post_meta( $post_id, 'read_more_links', $new );
		} elseif ( empty( $new ) && $old ) {
			delete_post_meta( $post_id, 'read_more_links', $old );
		}
	}

	/**
	 * Checks to see if the field is filled out.
	 *
	 * @since 2.0.0
	 *
	 * @param int    $in_ex       Whether the link is internal or external.
	 * @param string $ex_link     The external link.
	 * @param int    $in_link     The post ID for the internal link.
	 */
	public function is_filled( $in_ex, $ex_link, $in_link ) {
		if ( 'external' === $in_ex ) {
			if ( '' !== $ex_link ) {
				return true;
			} else {
				return false;
			}
		} else {
			if ( $in_link > 0 ) {
				return true;
			} else {
				return false;
			}
		}
	}

	/**
	 * Adds in the TinyMCE Editor buttons.
	 *
	 * @since 2.0.0
	 */
	public function read_more_about_buttons() {
		add_filter( 'mce_external_plugins', [ $this, 'read_more_about_add_buttons' ] );
		add_filter( 'mce_buttons', [$this, 'read_more_about_register_buttons' ] );
	}

	/**
	 * Adds in the JavaScript for the button.
	 *
	 * @since 2.0.0
	 *
	 * @param array $plugin_array      The incoming array of plugins.
	 */
	public function read_more_about_add_buttons( $plugin_array ) {
		$plugin_array['read_more_about'] = plugin_dir_url( __FILE__ ) . 'js/read-more-about-admin-button.min.js';
		return $plugin_array;
	}

	/**
	 * Adds in the button for the TinyMCY editor.
	 *
	 * @since 2.0.0
	 *
	 * @param array $buttons      The incoming array of buttons.
	 */
	public function read_more_about_register_buttons( $buttons ) {
		array_push( $buttons, 'read_more_about' );
		return $buttons;
	}

	/**
	 * Adds in the rest API data for featured image of a post.
	 *
	 * @since 2.0.0
	 */
	public function extend_rest_post_response() {
		register_rest_field(
			'post',
			'read_more_featured_image_src',
			[
				'get_callback'    => [ $this, 'get_image_src' ],
				'update_callback' => null,
				'schema'          => null,
			]
		);
	}

	/**
	 * Gets the src value for a featured image.
	 *
	 * @since 2.0.0
	 *
	 * @param WP_Post         $object       The current post.
	 * @param string          $field_name   The name of the field.
	 * @param WP_REST_Request $request      The REST request.
	 */
	public function get_image_src( $object, $field_name, $request ) {
		$feat_img_array['full']      = wp_get_attachment_image_src( $object['featured_media'], 'full', false );
		$feat_img_array['thumbnail'] = wp_get_attachment_image_src( $object['featured_media'], 'thumbnail', false );
		$feat_img_array['srcset']    = wp_get_attachment_image_srcset( $object['featured_media'] );
		$feat_img_array['alt']       = get_post_meta( $object['featured_media'], '_wp_attachment_image_alt', true );
		$image                       = is_array( $feat_img_array ) ? $feat_img_array : 'false';
		return $image;
	}

	/**
	 * Loads the block editor scripts and styles.
	 *
	 * @since 2.0.0
	 */
	public function blocks_editor_scripts() {
		$block_path = '../public/js/editor.blocks.js';

		wp_enqueue_style(
			'read-more-about-blocks-editor-css',
			plugin_dir_url( __FILE__ ) . 'css/blocks.editor.css'
		);

		wp_enqueue_script(
			'read-more-about-blocks-js',
			plugins_url( $block_path, __FILE__ ),
			[ 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-api', 'wp-editor' ],
			filemtime( plugin_dir_path(__FILE__) . $block_path )
		);

		wp_localize_script(
			'read-more-about-blocks-js',
			'read_more_about_globals',
			[
				'rest_url'  => esc_url( rest_url() ),
				'nonce'     => wp_create_nonce( 'wp_rest' ),
			]
		);
	}

	/**
	 * Loads the front-end styles for the block.
	 *
	 * @since 2.0.0
	 */
	public function block_scripts() {
		$style_path = '../public/css/block.min.css';
		wp_enqueue_style(
			'read-more-about-block-css',
			plugins_url( $style_path, __FILE__ )
		);
	}

	/**
	 * Checks to make sure Gutenberg is active or the WP version is greater than 5.0.
	 *
	 * @since 2.0.0
	 */
	public function check_gutenberg() {
		if ( ! function_exists( 'register_block_type' ) ) {
			// Block editor is not available.
			return;
		}

		add_action( 'enqueue_block_assets', [ $this, 'block_scripts' ] );
		add_action( 'enqueue_block_editor_assets', [ $this, 'blocks_editor_scripts' ] );
	}

}
