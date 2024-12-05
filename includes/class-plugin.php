<?php
/**
 * Main Plugin class.
 *
 * Encapsulates plugin functionality.
 *
 * @package Transition_Resources
 */

namespace Transition_Resources;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Main Plugin class.
 *
 * A class that encapsulates plugin functionality.
 *
 * @since 1.0.0
 */
class Plugin {

	/**
	 * Admin loader object.
	 *
	 * @since 1.0.0
	 * @var Admin\Loader
	 */
	public $admin;

	/**
	 * Custom Post Type loader object.
	 *
	 * @since 1.0.0
	 * @var CPT\Loader
	 */
	public $cpt;

	/**
	 * ACF loader object.
	 *
	 * @since 1.0.0
	 * @var ACF\Loader
	 */
	public $acf;

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Initialise.
		$this->initialise();

	}

	/**
	 * Initialises this plugin.
	 *
	 * @since 1.0.0
	 */
	public function initialise() {

		// Only do this once.
		static $done;
		if ( isset( $done ) && true === $done ) {
			return;
		}

		// Bootstrap plugin.
		$this->setup_objects();
		$this->register_hooks();

		/**
		 * Broadcast that this plugin is now loaded.
		 *
		 * @since 1.0.0
		 */
		do_action( 'tn_resources/loaded' );

		// We're done.
		$done = true;

	}

	/**
	 * Sets up this plugin's objects.
	 *
	 * @since 1.0.0
	 */
	public function setup_objects() {

		// Init objects.
		$this->admin = new Admin\Loader( $this );
		$this->cpt   = new CPT\Loader( $this );
		$this->acf   = new ACF\Loader( $this );

	}

	/**
	 * Registers WordPress hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {

		// Check for activation and deactivation.
		add_action( 'activated_plugin', [ $this, 'plugin_activated' ], 10, 2 );
		add_action( 'deactivated_plugin', [ $this, 'plugin_deactivated' ], 10, 2 );

		// Load translation.
		add_action( 'plugins_loaded', [ $this, 'translation' ] );

	}

	/**
	 * Enables translation.
	 *
	 * @since 1.0.0
	 */
	public function translation() {

		// Load translations.
		// phpcs:ignore WordPress.WP.DeprecatedParameters.Load_plugin_textdomainParam2Found
		load_plugin_textdomain(
			'transition-resources', // Unique name.
			false, // Deprecated argument.
			dirname( plugin_basename( TRANSITION_RESOURCES_FILE ) ) . '/languages/' // Relative path to files.
		);

	}

	/**
	 * This plugin has been activated.
	 *
	 * @since 1.0.0
	 *
	 * @param string $plugin The plugin file.
	 * @param bool   $network_wide True if network-activated, false otherwise.
	 */
	public function plugin_activated( $plugin, $network_wide = false ) {

		// Bail if it's not our plugin.
		if ( plugin_basename( TRANSITION_RESOURCES_FILE ) !== $plugin ) {
			return;
		}

		/**
		 * Fires when this plugin has been activated.
		 *
		 * Used internally by:
		 *
		 * * Transition_Resources\Admin\Base::activate() (Priority: 10)
		 *
		 * @since 1.0.0
		 *
		 * @param bool $network_wide True if network-activated, false otherwise.
		 */
		do_action( 'tn_resources/activated', $network_wide );

	}

	/**
	 * This plugin has been deactivated.
	 *
	 * @since 1.0.0
	 *
	 * @param string $plugin The plugin file.
	 * @param bool   $network_wide True if network-activated, false otherwise.
	 */
	public function plugin_deactivated( $plugin, $network_wide = false ) {

		// Bail if it's not our plugin.
		if ( plugin_basename( TRANSITION_RESOURCES_FILE ) !== $plugin ) {
			return;
		}

		/**
		 * Fires when this plugin has been deactivated.
		 *
		 * Used internally by:
		 *
		 * * Transition_Resources\Admin\Base::deactivate() (Priority: 10)
		 *
		 * @since 1.0.0
		 *
		 * @param bool $network_wide True if network-activated, false otherwise.
		 */
		do_action( 'tn_resources/deactivated', $network_wide );

	}

}
