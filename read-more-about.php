<?php
/**
 * Plugin Name:       Read More About
 * Plugin URI:        https://jacobmartella.com/wordpress/wordpress-plugins/read-more-about/
 * Description:       Allows users to add links in a story using a shortcode to provide addition reading material about a subject. Works great for large topics that can't all be explained in one post.
 * Version:           2.1.0
 * Author:            Jacob Martella Web Development
 * Author URI:        https://jacobmartella.com
 * Text Domain:       read-more-about
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 *
 * @package    Read_More_About
 * @subpackage Read_More_About/includes
 */

namespace Read_More_About;

// If this file is called directly, then about execution.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! defined( 'CM_TABLE_PREFIX' ) ) {
	define( 'CM_TABLE_PREFIX', 'wp_' );
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-jm-starter-plugin-activator.php
 *
 * @since 2.0.0
 */
function activate_read_more_about() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-read-more-about-activator.php';
	Read_More_About_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-starter-plugin-deactivator.php
 *
 * @since 2.0.0
 */
function deactivate_read_more_about() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-read-more-about-deactivator.php';
	Read_More_About_Deactivator::deactivate();
}

register_activation_hook( __FILE__, __NAMESPACE__ . '\activate_read_more_about' );
register_deactivation_hook( __FILE__, __NAMESPACE__ . '\deactivate_read_more_about' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-read-more-about.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    2.0.0
 */
function run_read_more_about() {

	$spmm = new Read_More_About();
	$spmm->run();

}

// Call the above function to begin execution of the plugin.
run_read_more_about();
