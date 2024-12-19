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
	 * @param ACF\Loader $acf The ACF object.
	 */
	public function __construct( $acf ) {

		// Store references to objects.
		$this->plugin = $acf->plugin;
		$this->acf    = $acf;

		// Init when this ACF class has loaded all its classes.
		add_action( 'tn_resources/acf/objects/loaded', [ $this, 'initialise' ] );

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

		// Register ACF hooks.
		$this->register_hooks_acf();

	}

	/**
	 * Registers "ACF" hooks.
	 *
	 * @since 1.0.0
	 */
	private function register_hooks_acf() {

		// Add Field Groups.
		add_action( 'acf/init', [ $this, 'field_groups_add' ] );

		// Add Fields.
		add_action( 'acf/init', [ $this, 'fields_add' ] );

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
		$this->field_license_add();
		$this->field_author_add();

	}

	/**
	 * Adds our ACF "File" Field.
	 *
	 * @since 1.0.0
	 */
	private function field_file_add() {

		// Build allowed extensions.
		$extensions = array_merge(
			wp_get_video_extensions(),
			wp_get_audio_extensions(),
			[ 'pdf' ]
		);

		/**
		 * Filters the list of allowed file extensions.
		 *
		 * @since 1.0.0
		 *
		 * @param string[] $extensions The default list of allowed extensions.
		 */
		$extensions = apply_filters( 'tn_resources/acf/fields/file/extensions', $extensions );

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
					'mime_types'        => implode( ',', $extensions ),
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
			'post_type'            => [
				'resource',
			],
			'post_status'          => [
				'publish',
			],
			'taxonomy'             => [],
			'return_format'        => 'object',
			'multiple'             => 1,
			'allow_null'           => 1,
			'ui'                   => 1,
			'allow_in_bindings'    => 1,
			'bidirectional'        => 0,
			'bidirectional_target' => [],
		];

		// Now add Field.
		acf_add_local_field( $field );

	}

	/**
	 * Adds our ACF "License" Field.
	 *
	 * @since 1.0.0
	 */
	private function field_license_add() {

		// Add "Related To" Field.
		$field = [
			'key'               => $this->field_key . 'license',
			'parent'            => $this->group_key . 'resource',
			'label'             => __( 'License', 'transition-resources' ),
			'name'              => 'license',
			'type'              => 'select',
			'instructions'      => '',
			'required'          => 1,
			'placeholder'       => '',
			'allow_null'        => 0,
			'multiple'          => 0,
			'ui'                => 0,
			'return_format'     => 'value',
			'choices'           => [
				1 => __( 'CC-BY', 'transition-resources' ),
				2 => __( 'CC-BY-NC', 'transition-resources' ),
				3 => __( 'All rights reserved', 'transition-resources' ),
			],
			'default_value'     => 1,
			'conditional_logic' => 0,
		];

		// Now add Field.
		acf_add_local_field( $field );

	}

	/**
	 * Adds our ACF "Author" Field.
	 *
	 * @since 1.0.0
	 */
	private function field_author_add() {

		// Add "Author" Field.
		$field = [
			'key'           => $this->field_key . 'author',
			'parent'        => $this->group_key . 'resource',
			'label'         => __( 'Author', 'transition-resources' ),
			'name'          => 'author',
			'type'          => 'select',
			'instructions'  => '',
			'required'      => 0,
			'placeholder'   => '',
			'allow_null'    => 1,
			'multiple'      => 0,
			'ui'            => 0,
			'return_format' => 'value',
			'choices'       => [
				1 => __( 'I wrote this', 'transition-resources' ),
				2 => __( 'Someone else wrote this', 'transition-resources' ),
			],
			'default_value' => 1,
		];

		// Now add Field.
		acf_add_local_field( $field );

		// Add conditional "Author Link" Field.
		$field = [
			'key'               => $this->field_key . 'author_link',
			'parent'            => $this->group_key . 'resource',
			'label'             => __( 'Author Link', 'transition-resources' ),
			'name'              => 'author_link',
			'type'              => 'url',
			'instructions'      => __( 'Add the website of the author.', 'transition-resources' ),
			'required'          => 0,
			'placeholder'       => '',
			'conditional_logic' => [
				[
					[
						'field'    => $this->field_key . 'author',
						'operator' => '==',
						'value'    => 2,
					],
				],
			],
		];

		// Now add Field.
		acf_add_local_field( $field );

	}

}
