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
	 * Bridging variable for the "Identify the author" Field value.
	 *
	 * @since 1.0.0
	 * @var integer
	 */
	private $author_select;

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

		// Make sure URL is populated when "Someone else wrote this" is selected.
		add_filter( 'acf/validate_value', [ $this, 'validate_url' ], 20, 4 );

		// Render the output of the Markdown Field.
		add_action( 'acf/load_value/type=markdown', [ $this, 'markdown_render' ], 20, 3 );

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
		$this->field_image_add();
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
					'type'              => 'markdown',
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
			'key'           => $this->field_key . 'author_select',
			'parent'        => $this->group_key . 'resource',
			'label'         => __( 'Identify the author', 'transition-resources' ),
			'name'          => 'author_select',
			'type'          => 'select',
			'instructions'  => '',
			'required'      => 0,
			'placeholder'   => '',
			'allow_null'    => 0,
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

		// Add "Authors" Repeater.
		$field = [
			'key'               => $this->field_key . 'authors',
			'parent'            => $this->group_key . 'resource',
			'label'             => __( 'Authors', 'transition-resources' ),
			'name'              => 'authors',
			'type'              => 'repeater',
			'instructions'      => __( 'Add the author or authors of this resource', 'transition-resources' ),
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
			'button_label'      => __( 'Add author', 'transition-resources' ),
			'sub_fields'        => [
				[
					'key'               => $this->field_key . 'author_name',
					'parent'            => $this->group_key . 'resource',
					'label'             => __( 'Author Name', 'transition-resources' ),
					'name'              => 'author_name',
					'type'              => 'text',
					'instructions'      => __( 'Add the full name of the author.', 'transition-resources' ),
					'required'          => 1,
					'placeholder'       => '',
					'conditional_logic' => 0,
				],
				[
					'key'               => $this->field_key . 'author_link',
					'parent'            => $this->group_key . 'resource',
					'label'             => __( 'Author Link', 'transition-resources' ),
					'name'              => 'author_link',
					'type'              => 'url',
					'instructions'      => __( 'Add the website of the author. Required when "Someone else wrote this" is selected.', 'transition-resources' ),
					'required'          => 0,
					'allow_null'        => 1,
					'placeholder'       => '',
					'conditional_logic' => 0,
				],
			],
		];

		// Now add Field.
		acf_add_local_field( $field );

	}

	/**
	 * Validates our ACF "Author" Field.
	 *
	 * @since 1.0.0
	 *
	 * @param bool   $valid The valid status. Return a string to display a custom error message.
	 * @param mixed  $value The value of the Field.
	 * @param array  $field The Field array.
	 * @param string $input_name The input element's name attribute.
	 * @return bool $valid The modified valid status.
	 */
	public function validate_url( $valid, $value, $field, $input_name ) {

		// Bail early if value is already invalid.
		if ( true !== $valid ) {
			return $valid;
		}

		// Bail if not one the Fields we're interested in.
		if ( 'author_select' !== $field['name'] && 'author_link' !== $field['name'] ) {
			return $valid;
		}

		// Store value of "Identify the author" field.
		if ( 'author_select' === $field['name'] ) {
			$this->author_select = (int) $value;
			return $valid;
		}

		// Bail if "Someone else write this" is not selected.
		if ( 2 !== $this->author_select ) {
			return $valid;
		}

		// The URL Field cannot be empty.
		if ( empty( $value ) ) {
			$valid = __( 'You must supply a link when someone else is the author.', 'transition-resources' );
		}

		// --<
		return $valid;

	}

	/**
	 * Adds our ACF "Image" Field.
	 *
	 * @since 1.0.0
	 */
	private function field_image_add() {

		// Add "Image" Field.
		$field = [
			'key'               => $this->field_key . 'picture',
			'parent'            => $this->group_key . 'resource',
			'label'             => __( 'Image', 'transition-resources' ),
			'name'              => 'picture',
			'type'              => 'image',
			'instructions'      => '',
			'required'          => 0,
			'conditional_logic' => 0,
			'wrapper'           => [
				'width' => '',
				'class' => '',
				'id'    => '',
			],
			'uploader'          => '',
			'return_format'     => 'array',
			'library'           => 'all',
			'mime_types'        => '',
			'preview_size'      => 'medium',
			'min_width'         => 450,
			'min_height'        => 300,
			'max_width'         => 2100,
			'max_height'        => 1400,

			/*
			// Possible image min/max properties.
			'min_width'         => 1024,
			'min_height'        => 768,
			'min_size'          => '',
			'max_width'         => 1280,
			'max_height'        => 960,
			'max_size'          => 10, // In MB.
			*/
		];

		// Now add Field.
		acf_add_local_field( $field );

	}

	/**
	 * Renders the value of a Markdown Field.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed          $value The value found in the database.
	 * @param integer|string $post_id The ACF "Post ID" from which the value was loaded.
	 * @param array          $field The Field array holding all the Field options.
	 */
	public function markdown_render( $value, $post_id, $field ) {

		// Add support for Jetpack Markdown.
		if ( ! is_admin() && class_exists( '\WPCom_Markdown' ) ) {
			$markdown = \WPCom_Markdown::get_instance();
			return wpautop( wp_unslash( $markdown->transform( $value ) ) );
		}

		// --<
		return $value;

	}

}
