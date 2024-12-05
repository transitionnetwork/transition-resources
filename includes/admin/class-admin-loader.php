<?php
/**
 * Admin Loader class.
 *
 * Handles Admin functionality.
 *
 * @package Transition_Resources
 */

namespace Transition_Resources\Admin;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Admin Loader class.
 *
 * A class that loads Admin functionality.
 *
 * @since 1.0.0
 */
class Loader extends Base {

	/**
	 * Plugin object.
	 *
	 * @since 1.0.0
	 * @var Plugin
	 */
	public $plugin;

	/**
	 * Hook prefix.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $hook_prefix = 'tn_resources';

	/**
	 * Settings Page object.
	 *
	 * @since 1.0.0
	 * @var Admin\Page_Settings
	 */
	private $page_settings;

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

		// Assign plugin codebase version.
		$this->plugin_version_code = TRANSITION_RESOURCES_VERSION;

		// Assign option names.
		$this->option_version  = $this->hook_prefix . '_version';
		$this->option_settings = $this->hook_prefix . '_settings';

		// Bootstrap parent.
		parent::__construct();

	}

	/**
	 * Initialises this object.
	 *
	 * @since 1.0.0
	 */
	public function initialise() {

		// Bootstrap class.
		$this->setup_objects();
		$this->register_hooks();

		/**
		 * Fires when this class is loaded.
		 *
		 * @since 1.0.0
		 */
		do_action( 'tn_resources/admin/loaded' );

	}

	/**
	 * Instantiates objects.
	 *
	 * @since 1.0.0
	 */
	public function setup_objects() {

		$this->page_settings = new Page_Settings( $this );

	}

	/**
	 * Register WordPress hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {

	}

}
