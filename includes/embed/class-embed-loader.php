<?php
/**
 * Embed Loader class.
 *
 * Handles oEmbed modification and enhancement by loading classes that provide that
 * functionality.
 *
 * @package Transition_Resources
 */

namespace Transition_Resources\Embed;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * The oEmbed Loader class.
 *
 * A class that loads oEmbed functionality.
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
	 * Resources template object.
	 *
	 * @since 1.0.0
	 * @var Embed\Resources
	 */
	public $resources;

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Plugin $plugin The plugin object.
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

		// Require settings to have been initialised.
		add_filter( 'tn_resources/admin/settings/initialised', [ $this, 'setup_objects' ], 20 );

		/**
		 * Fires when this class is loaded.
		 *
		 * @since 1.0.0
		 */
		do_action( 'tn_resources/embed/loaded' );

	}

	/**
	 * Instantiates objects.
	 *
	 * @since 1.0.0
	 */
	public function setup_objects() {

		// Get our active Custom Post Types.
		$cpts_enabled = $this->plugin->cpt->setting_cpts_enabled_get();

		// Maybe modify oEmbed for the "Resources" Custom Post Type.
		if ( ! empty( $cpts_enabled['resources'] ) ) {
			$this->resources = new Resources( $this );
		}

		/**
		 * Fires when all oEmbed objects have been loaded.
		 *
		 * @since 1.0.0
		 */
		do_action( 'tn_resources/embed/objects/loaded' );

	}

}
