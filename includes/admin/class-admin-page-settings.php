<?php
/**
 * Settings Page class.
 *
 * Handles Settings Page functionality.
 *
 * @package Transition_Resources
 */

namespace Transition_Resources\Admin;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Settings Page class.
 *
 * A class that encapsulates Settings Page functionality.
 *
 * @since 1.0.0
 */
class Page_Settings extends Page_Base {

	/**
	 * Plugin object.
	 *
	 * @since 1.0.0
	 * @var Plugin
	 */
	public $plugin;

	/**
	 * Admin object.
	 *
	 * @since 1.0.0
	 * @var Admin\Loader
	 */
	public $admin;

	/**
	 * Form interval ID.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $form_interval_id = 'interval_id';

	/**
	 * Form sync direction ID.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $form_direction_id = 'direction_id';

	/**
	 * Form batch ID.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $form_batch_id = 'batch_id';

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param object $admin The admin object.
	 */
	public function __construct( $admin ) {

		// Store references to objects.
		$this->plugin = $admin->plugin;
		$this->admin  = $admin;

		// Set a unique prefix for all Pages.
		$this->hook_prefix_common = 'tn_resources_admin';

		// Set a unique prefix.
		$this->hook_prefix = 'tn_resources_settings';

		// Assign page slugs.
		$this->page_slug = 'tn_resources_settings';

		/*
		// Assign page layout.
		$this->page_layout = 'dashboard';
		*/

		// Assign path to plugin directory.
		$this->path_plugin = TRANSITION_RESOURCES_PATH;

		// Assign form IDs.
		$this->form_interval_id  = $this->hook_prefix . '_' . $this->form_interval_id;
		$this->form_direction_id = $this->hook_prefix . '_' . $this->form_direction_id;
		$this->form_batch_id     = $this->hook_prefix . '_' . $this->form_batch_id;

		// Bootstrap parent.
		parent::__construct();

	}

	/**
	 * Initialises this object.
	 *
	 * @since 1.0.0
	 */
	public function initialise() {

		// Assign translated strings.
		$this->plugin_name          = __( 'Transition Resources', 'transition-resources' );
		$this->page_title           = __( 'Settings for Transition Resources', 'transition-resources' );
		$this->page_tab_label       = __( 'Settings', 'transition-resources' );
		$this->page_menu_label      = __( 'Transition Resources', 'transition-resources' );
		$this->page_help_label      = __( 'Transition Resources', 'transition-resources' );
		$this->metabox_submit_title = __( 'Settings', 'transition-resources' );

	}

	/**
	 * Adds styles.
	 *
	 * @since 1.0.0
	 */
	public function admin_styles() {

		/*
		// Enqueue our "Settings Page" stylesheet.
		wp_enqueue_style(
			$this->hook_prefix . '-css',
			plugins_url( 'assets/css/page-settings.css', TRANSITION_RESOURCES_FILE ),
			false,
			TRANSITION_RESOURCES_VERSION, // Version.
			'all' // Media.
		);
		*/

	}

	/**
	 * Adds scripts.
	 *
	 * @since 1.0.0
	 */
	public function admin_scripts() {

		/*
		// Enqueue our "Settings Page" script.
		wp_enqueue_script(
			$this->hook_prefix . '-js',
			plugins_url( 'assets/js/page-settings.js', TRANSITION_RESOURCES_FILE ),
			[ 'jquery' ],
			TRANSITION_RESOURCES_VERSION, // Version.
			true
		);
		*/

	}

	/**
	 * Registers meta boxes.
	 *
	 * @since 1.0.0
	 *
	 * @param string $screen_id The Settings Page Screen ID.
	 * @param array  $data The array of metabox data.
	 */
	public function meta_boxes_register( $screen_id, $data ) {

		// Bail if not the Screen ID we want.
		if ( $screen_id !== $this->page_context . $this->page_slug ) {
			return;
		}

		// Check User permissions.
		if ( ! $this->page_capability() ) {
			return;
		}

		/**
		 * Broadcast that the metaboxes have been added.
		 *
		 * @since 1.0.0
		 *
		 * @param string $screen_id The Screen indentifier.
		 * @param array $data The array of metabox data.
		 */
		do_action( $this->hook_prefix . '/settings/page/meta_boxes/added', $screen_id, $data );

	}

	/**
	 * Performs save actions when the form has been submitted.
	 *
	 * @since 1.0.0
	 *
	 * @param string $submit_id The Settings Page form submit ID.
	 */
	public function form_save( $submit_id ) {

		// Check that we trust the source of the data.
		check_admin_referer( $this->form_nonce_action, $this->form_nonce_field );

		// Save settings.
		$this->admin->settings_save();

	}

}
