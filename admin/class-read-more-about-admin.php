<?php
/**
 * Holds all of the admin side functions.
 *
 * PHP version 7.3
 *
 * @link       https://jacobmartella.com
 * @since      1.8.0
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
 * @since      1.8.0
 * @package    Read_More_About
 * @subpackage Read_More_About/admin
 */
class Read_More_About_Admin {

	/**
	 * Version of the plugin.
	 *
	 * @since 1.8.0
	 * @var string $version Description.
	 */
	private $version;


	/**
	 * Builds the Read_More_About_Admin object.
	 *
	 * @since 1.8.0
	 *
	 * @param string $version Version of the plugin.
	 */
	public function __construct( $version ) {
		$this->version = $version;
	}

	/**
	 * Enqueues the styles for the admin side of the plugin.
	 *
	 * @since 1.8.0
	 */
	public function enqueue_styles() { }

	/**
	 * Enqueues the scripts for the admin side of the plugin.
	 *
	 * @since 1.8.0
	 */
	public function enqueue_scripts() { }

}
