<?php
/**
 * Holds all of the public side functions.
 *
 * PHP version 7.3
 *
 * @link       https://jacobmartella.com
 * @since      1.8.0
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
 * @since      1.8.0
 * @package    Read_More_About
 * @subpackage Read_More_About/public
 */
class Read_More_About_Public {

	/**
	 * Version of the plugin.
	 *
	 * @since 1.8.0
	 * @var string $version Description.
	 */
	private $version;

	/**
	 * Builds the Read_More_About_Public object.
	 *
	 * @since 1.8.0
	 *
	 * @param string $version Version of the plugin.
	 */
	public function __construct( $version ) {
		$this->version = $version;
	}

}
