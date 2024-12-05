<?php
/**
 * ACF Loader class.
 *
 * Handles ACF modification and enhancement by loading classes that provide that
 * functionality for aspects of the ACF.
 *
 * @package Transition_Resources
 */

namespace Transition_Resources\ACF;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * ACF Loader class.
 *
 * A class that loads ACF functionality.
 *
 * @since 1.0.0
 */
class Loader {

	/**
	 * Plugin object.
	 *
	 * @since 1.0.0
	 * @var Plugin
	 */
	public $plugin;

	/**
	 * Fields object.
	 *
	 * @since 1.0.0
	 * @var ACF\Fields
	 */
	public $fields;

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param object $plugin The plugin object.
	 */
	public function __construct( $plugin ) {

		// Store reference to plugin.
		$this->plugin = $plugin;

		// Init when this plugin is loaded.
		add_action( 'tn_resources/loaded', [ $this, 'initialise' ] );

	}

	/**
	 * Initialises this object.
	 *
	 * @since 1.0.0
	 */
	public function initialise() {

		// Bootstrap class.
		$this->setup_objects();

		/**
		 * Fires when this class is loaded.
		 *
		 * @since 1.0.0
		 */
		do_action( 'tn_resources/acf/loaded' );

	}

	/**
	 * Instantiates objects.
	 *
	 * @since 1.0.0
	 */
	public function setup_objects() {

		// Instantiate objects.
		$this->fields = new Fields( $this );

		/**
		 * Fires when all ACF objects have been loaded.
		 *
		 * @since 1.0.0
		 */
		do_action( 'tn_resources/acf/objects/loaded' );

	}

}
