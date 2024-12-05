<?php
/**
 * ACF Fields class.
 *
 * Handles registration of ACF Field Groups and Custom Fields.
 *
 * @package Transition_Resources
 */

namespace Transition_Resources\ACF;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * ACF Fields class.
 *
 * A class that registers ACF Field Groups and Custom Fields.
 *
 * @since 1.0.0
 */
class Fields {

	/**
	 * Plugin object.
	 *
	 * @since 1.0.0
	 * @var Plugin
	 */
	public $plugin;

	/**
	 * ACF loader object.
	 *
	 * @since 1.0.0
	 * @var ACF\Loader
	 */
	public $acf;

	/**
	 * Metabox template directory path.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $metabox_path = 'assets/templates/wordpress/settings/metaboxes/';

	/**
	 * Partials template directory path.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $partial_path = 'assets/templates/wordpress/settings/partials/';

	/**
	 * ACF Fields setting key in Settings.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $key_fields_enabled = 'fields_enabled';

	/**
	 * Field Group Key Prefix.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $group_key = 'group_';

	/**
	 * Field Key Prefix.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $field_key = 'field_';

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param object $acf The ACF object.
	 */
	public function __construct( $acf ) {

		// Store references to objects.
		$this->plugin = $acf->plugin;
		$this->acf    = $acf;

		// Init when this plugin is loaded.
		add_action( 'tn_resources/acf/loaded', [ $this, 'initialise' ] );

	}

	/**
	 * Initialises this object.
	 *
	 * @since 1.0.0
	 */
	public function initialise() {

		// Bootstrap class.
		$this->register_hooks();

		/**
		 * Fires when this class is loaded.
		 *
		 * @since 1.0.0
		 */
		do_action( 'tn_resources/acf/fields/loaded' );

	}

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {

		// Separate callbacks into descriptive methods.
		$this->register_hooks_settings();
		$this->register_hooks_acf();

	}

	/**
	 * Registers "Settings" hooks.
	 *
	 * @since 1.0.0
	 */
	private function register_hooks_settings() {

		/*
		// Setting up this class requires settings to have been initialised.
		add_filter( 'tn_resources/admin/settings/initialised', [ $this, 'bootstrap_functionality' ] );

		// Add our settings to default settings.
		add_filter( 'tn_resources/admin/settings/defaults', [ $this, 'settings_get_defaults' ] );

		// Add our metaboxes to the Site Settings screen.
		add_filter( 'tn_resources_settings/settings/page/meta_boxes/added', [ $this, 'settings_meta_boxes_append' ], 20, 2 );

		// Save data from Site Settings form submissions.
		add_action( 'tn_resources_settings/settings/form/save/before', [ $this, 'settings_meta_box_save' ] );
		*/

	}

	/**
	 * Registers "ACF" hooks.
	 *
	 * @since 1.0.0
	 */
	private function register_hooks_acf() {

		// Add field groups.
		add_action( 'acf/init', [ $this, 'field_groups_add' ] );

		// Add fields.
		add_action( 'acf/init', [ $this, 'fields_add' ] );

	}

	/**
	 * Bootstraps the functionality in this class.
	 *
	 * @since 1.0.0
	 */
	public function bootstrap_functionality() {

		/**
		 * Fires when all ACF objects have been loaded.
		 *
		 * @since 1.0.0
		 */
		do_action( 'tn_resources/acf/bootstrapped' );

	}

	/**
	 * Appends our settings to the default core settings.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings The existing default settings.
	 * @return array $settings The modified default settings.
	 */
	public function settings_get_defaults( $settings ) {

		// Add our defaults.
		$settings[ $this->key_fields_enabled ] = $this->setting_fields_enabled_default_get();

		// --<
		return $settings;

	}

	/**
	 * Appends our metaboxes to the Settings screen.
	 *
	 * @since 1.0.0
	 *
	 * @param string $screen_id The Settings Screen ID.
	 * @param array  $data The array of metabox data.
	 */
	public function settings_meta_boxes_append( $screen_id, $data ) {

		// Define a handle for the following metabox.
		$handle = 'tn_resources_settings_acf';

		// Add the metabox.
		add_meta_box(
			$handle,
			__( 'ACF', 'transition-resources' ),
			[ $this, 'settings_meta_box_render' ], // Callback.
			$screen_id, // Screen ID.
			'normal', // Column: options are 'normal' and 'side'.
			'core', // Vertical placement: options are 'core', 'high', 'low'.
			$data
		);

	}

	/**
	 * Renders "Custom Post Types" meta box on Settings screen.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $unused Unused param.
	 * @param array $metabox Array containing id, title, callback, and args elements.
	 */
	public function settings_meta_box_render( $unused, $metabox ) {

		// Get our settings.
		$fields_enabled = $this->setting_fields_enabled_get();

		// Include template file.
		include TRANSITION_RESOURCES_PATH . $this->metabox_path . 'metabox-settings-acf-fields.php';

	}

	/**
	 * Saves the data from the "ACF Fields" metabox.
	 *
	 * Adds the data to the settings array. The settings are actually saved later.
	 *
	 * @see Admin\Page_Base::form_submitted()
	 *
	 * @since 1.0.0
	 */
	public function settings_meta_box_save() {

		// Find the data. Nonce has already been checked.
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$fields_enabled = filter_input( INPUT_POST, $this->key_fields_enabled, FILTER_SANITIZE_SPECIAL_CHARS );

		// Sanitise data.
		$fields_enabled = sanitize_text_field( wp_unslash( $fields_enabled ) );

		// Set the setting.
		$this->setting_fields_enabled_set( $fields_enabled );

	}

	/**
	 * Gets the default "Modify Shortcuts Fields" setting.
	 *
	 * @since 1.0.0
	 *
	 * @return string $fields_enabled The default setting value.
	 */
	public function setting_fields_enabled_default_get() {

		// Defaults to not active.
		$fields_enabled = 'no';

		// --<
		return $fields_enabled;

	}

	/**
	 * Gets the "Modify Shortcuts Fields" setting.
	 *
	 * @since 1.0.0
	 *
	 * @return string $fields_enabled The setting if found, default otherwise.
	 */
	public function setting_fields_enabled_get() {

		// Get the setting.
		$fields_enabled = $this->plugin->admin->setting_get( $this->key_fields_enabled );

		// Return setting or default if empty.
		return ! empty( $fields_enabled ) ? $fields_enabled : $this->setting_fields_enabled_default_get();

	}

	/**
	 * Sets the "Modify Shortcuts Fields" setting.
	 *
	 * @since 1.0.0
	 *
	 * @param string $fields_enabled The setting value.
	 */
	public function setting_fields_enabled_set( $fields_enabled ) {

		// Set the setting.
		$this->plugin->admin->setting_set( $this->key_fields_enabled, $fields_enabled );

	}

	/**
	 * Adds our ACF Field Groups.
	 *
	 * @since 1.0.0
	 */
	public function field_groups_add() {

		// Add primary Field Group.
		$this->field_group_add();

	}

	/**
	 * Adds our ACF Field Group.
	 *
	 * @since 1.0.0
	 */
	private function field_group_add() {

		$location = [
			'param'    => 'post_type',
			'operator' => '==',
			'value'    => $this->plugin->cpt->resources->post_type_name,
		];

		// Attach the Field Group to the "Resources" CPT.
		$field_group_location = [
			[ $location ],
		];

		// Hide UI elements on our CPT edit page.
		$field_group_hide_elements = [
			// 'the_content',
			// 'excerpt',
			'discussion',
			'comments',
			// 'revisions',
			'author',
			'format',
			'page_attributes',
			// 'featured_image',
			'tags',
			'send-trackbacks',
		];

		// Define field group.
		$field_group = [
			'key'            => $this->group_key . 'resource',
			'title'          => __( 'Resource Information', 'transition-resources' ),
			'fields'         => [],
			'location'       => $field_group_location,
			'hide_on_screen' => $field_group_hide_elements,
		];

		// Now add the group.
		acf_add_local_field_group( $field_group );

	}

	/**
	 * Adds our ACF Fields.
	 *
	 * @since 1.0.0
	 */
	public function fields_add() {

		// Add Custom Fields.
		$this->field_file_add();
		$this->field_embed_add();
		$this->field_related_add();

	}

	/**
	 * Adds our ACF "File" Field.
	 *
	 * @since 1.0.0
	 */
	private function field_file_add() {

		// Add "Files" Repeater.
		$field = [
			'key'               => $this->field_key . 'files',
			'parent'            => $this->group_key . 'resource',
			'label'             => __( 'Files', 'transition-resources' ),
			'name'              => 'files',
			'type'              => 'repeater',
			'instructions'      => '',
			'required'          => 0,
			'conditional_logic' => 0,
			'wrapper'           => [
				'width' => '',
				'class' => '',
				'id'    => '',
			],
			'collapsed'         => '',
			'min'               => 0,
			'max'               => 0,
			'layout'            => 'block',
			'button_label'      => __( 'Add File', 'transition-resources' ),
			'sub_fields'        => [
				[
					'key'               => $this->field_key . 'file',
					'label'             => __( 'File', 'transition-resources' ),
					'name'              => 'file',
					'type'              => 'file',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => [
						'width' => '',
						'class' => '',
						'id'    => '',
					],
					'return_format'     => 'id',
				],
				[
					'key'               => $this->field_key . 'description',
					'label'             => __( 'File Description', 'transition-resources' ),
					'name'              => 'description',
					'type'              => 'textarea',
					'instructions'      => __( 'Describe the uploaded file. For PDFs, add the text in plaintext format for search purposes.', 'transition-resources' ),
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => [
						'width' => '',
						'class' => '',
						'id'    => '',
					],
					'return_format'     => 'id',
				],
			],
		];

		// Now add Field.
		acf_add_local_field( $field );

	}

	/**
	 * Adds our ACF "Embed" Field.
	 *
	 * @since 1.0.0
	 */
	private function field_embed_add() {

		// Add "Embed" Repeater.
		$field = [
			'key'               => $this->field_key . 'embed',
			'parent'            => $this->group_key . 'resource',
			'label'             => __( 'Embedded Media', 'transition-resources' ),
			'name'              => 'embed',
			'type'              => 'repeater',
			'instructions'      => '',
			'required'          => 0,
			'conditional_logic' => 0,
			'wrapper'           => [
				'width' => '',
				'class' => '',
				'id'    => '',
			],
			'collapsed'         => '',
			'min'               => 0,
			'max'               => 0,
			'layout'            => 'table',
			'button_label'      => __( 'Add Embedded Media', 'transition-resources' ),
			'sub_fields'        => [
				[
					'key'               => $this->field_key . 'file_static',
					'label'             => __( 'Embed', 'transition-resources' ),
					'name'              => 'embed',
					'type'              => 'oembed',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => [
						'width' => '',
						'class' => '',
						'id'    => '',
					],
					'return_format'     => 'id',
				],
			],
		];

		// Now add Field.
		acf_add_local_field( $field );

	}

	/**
	 * Adds our ACF "Related" Field.
	 *
	 * @since 1.0.0
	 */
	private function field_related_add() {

		// Add "Related To" Field.
		$field = [
			'key'                  => $this->field_key . 'related',
			'parent'               => $this->group_key . 'resource',
			'label'                => __( 'Related To', 'transition-resources' ),
			'name'                 => 'related',
			'type'                 => 'post_object',
			'instructions'         => '',
			'required'             => 0,
			'conditional_logic'    => 0,
			'wrapper'              => [
				'width' => '',
				'class' => '',
				'id'    => '',
			],
			'post_type'            => [],
			'post_status'          => [],
			'taxonomy'             => [],
			'return_format'        => 'object',
			'multiple'             => 1,
			'allow_null'           => 1,
			'ui'                   => 1,
			'allow_in_bindings'    => 0,
			'bidirectional'        => 0,
			'bidirectional_target' => [],
		];

		// Now add Field.
		acf_add_local_field( $field );

	}

}
