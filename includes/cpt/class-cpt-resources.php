<?php
/**
 * Resources Custom Post Type class.
 *
 * Handles providing a "Resources" Custom Post Type.
 *
 * @package Transition_Resources
 */

namespace Transition_Resources\CPT;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Resources Custom Post Type class.
 *
 * A class that encapsulates a "Resources" Custom Post Type.
 *
 * @since 1.0.0
 */
class Resources extends Base {

	/**
	 * Plugin object.
	 *
	 * @since 1.0.0
	 * @var Plugin
	 */
	public $plugin;

	/**
	 * Custom Post Type object.
	 *
	 * @since 1.0.0
	 * @var Transition_Resources_CPT
	 */
	public $cpt;

	/**
	 * Custom Post Type name.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $post_type_name = 'resource';

	/**
	 * Custom Post Type REST base.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $post_type_rest_base = 'resources';

	/**
	 * Taxonomy name.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $taxonomy_name = 'resource-type';

	/**
	 * Taxonomy REST base.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $taxonomy_rest_base = 'resource-type';

	/**
	 * Second taxonomy name.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $taxonomy_two_name = 'project-type';

	/**
	 * Second taxonomy REST base.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $taxonomy_two_rest_base = 'project-type';

	/**
	 * Third taxonomy name.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $taxonomy_three_name = 'content-type';

	/**
	 * Third taxonomy REST base.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $taxonomy_three_rest_base = 'content-type';

	/**
	 * Fourth taxonomy name.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $taxonomy_four_name = 'country';

	/**
	 * Fourth taxonomy REST base.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $taxonomy_four_rest_base = 'country';

	/**
	 * Free taxonomy name.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $tag_name = 'resource-tag';

	/**
	 * Free taxonomy REST base.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $tag_rest_base = 'resource-tag';

	/**
	 * Creates our Custom Post Type.
	 *
	 * @since 1.0.0
	 */
	public function post_type_create() {

		// Only call this once.
		static $registered;
		if ( $registered ) {
			return;
		}

		// Labels.
		$labels = [
			'name'               => __( 'Resources', 'transition-resources' ),
			'singular_name'      => __( 'Resource', 'transition-resources' ),
			'add_new'            => __( 'Add New', 'transition-resources' ),
			'add_new_item'       => __( 'Add New Resource', 'transition-resources' ),
			'edit_item'          => __( 'Edit Resource', 'transition-resources' ),
			'new_item'           => __( 'New Resource', 'transition-resources' ),
			'all_items'          => __( 'All Resources', 'transition-resources' ),
			'view_item'          => __( 'View Resource', 'transition-resources' ),
			'search_items'       => __( 'Search Resources', 'transition-resources' ),
			'not_found'          => __( 'No matching Resource found', 'transition-resources' ),
			'not_found_in_trash' => __( 'No Resources found in Trash', 'transition-resources' ),
			'menu_name'          => __( 'Resources', 'transition-resources' ),
		];

		// Rewrite.
		$rewrite = [
			'slug'       => 'resources',
			'with_front' => false,
		];

		// Supports.
		$supports = [
			'title',
			'editor',
			'excerpt',
			'thumbnail',
		];

		// Build args.
		$args = [
			'labels'              => $labels,
			'menu_icon'           => 'dashicons-welcome-learn-more',
			'description'         => __( 'A resource post type', 'transition-resources' ),
			'public'              => true,
			'publicly_queryable'  => true,
			'exclude_from_search' => false,
			'show_ui'             => true,
			'show_in_nav_menus'   => true,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => true,
			'has_archive'         => false,
			'query_var'           => true,
			'capability_type'     => 'post',
			'hierarchical'        => false,
			'menu_position'       => 40,
			'map_meta_cap'        => true,
			'rewrite'             => $rewrite,
			'supports'            => $supports,
			'show_in_rest'        => true,
			'rest_base'           => $this->post_type_rest_base,
		];

		// Set up the post type called "Resource".
		register_post_type( $this->post_type_name, $args );

		// Flag done.
		$registered = true;

	}

	/**
	 * Overrides the messages for a Custom Post Type.
	 *
	 * @since 1.0.0
	 *
	 * @param array $messages The existing messages.
	 * @return array $messages The modified messages.
	 */
	public function post_type_messages( $messages ) {

		// Access relevant globals.
		global $post, $post_ID;

		// Define custom messages for our Custom Post Type.
		$messages[ $this->post_type_name ] = [

			// Unused - messages start at index 1.
			0  => '',

			// Item updated.
			1  => sprintf(
				/* translators: %s: The permalink. */
				__( 'Resource updated. <a href="%s">View Resource</a>', 'transition-resources' ),
				esc_url( get_permalink( $post_ID ) )
			),

			// Custom fields.
			2  => __( 'Custom field updated.', 'transition-resources' ),
			3  => __( 'Custom field deleted.', 'transition-resources' ),
			4  => __( 'Resource updated.', 'transition-resources' ),

			// Item restored to a revision.
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			5  => isset( $_GET['revision'] ) ?

				// Revision text.
				sprintf(
					/* translators: %s: The date and time of the revision. */
					__( 'Resource restored to revision from %s', 'transition-resources' ),
					// phpcs:ignore WordPress.Security.NonceVerification.Recommended
					wp_post_revision_title( (int) $_GET['revision'], false )
				) :

				// No revision.
				false,

			// Item published.
			6  => sprintf(
				/* translators: %s: The permalink. */
				__( 'Resource published. <a href="%s">View Resource</a>', 'transition-resources' ),
				esc_url( get_permalink( $post_ID ) )
			),

			// Item saved.
			7  => __( 'Resource saved.', 'transition-resources' ),

			// Item submitted.
			8  => sprintf(
				/* translators: %s: The permalink. */
				__( 'Resource submitted. <a target="_blank" href="%s">Preview Resource</a>', 'transition-resources' ),
				esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) )
			),

			// Item scheduled.
			9  => sprintf(
				/* translators: 1: The date, 2: The permalink. */
				__( 'Resource scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Resource</a>', 'transition-resources' ),
				/* translators: Publish box date format - see https://php.net/date */
				date_i18n( __( 'M j, Y @ G:i', 'transition-resources' ), strtotime( $post->post_date ) ),
				esc_url( get_permalink( $post_ID ) )
			),

			// Draft updated.
			10 => sprintf(
				/* translators: %s: The permalink. */
				__( 'Resource draft updated. <a target="_blank" href="%s">Preview Resource</a>', 'transition-resources' ),
				esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) )
			),

		];

		// --<
		return $messages;

	}

	/**
	 * Overrides the "Add title" label.
	 *
	 * @since 1.0.0
	 *
	 * @param str $title The existing title - usually "Add title".
	 * @return str $title The modified title.
	 */
	public function post_type_title( $title ) {

		// Bail if not our post type.
		if ( get_post_type() !== $this->post_type_name ) {
			return $title;
		}

		// Overwrite with our string.
		$title = __( 'Add the name of the Resource', 'transition-resources' );

		// --<
		return $title;

	}

	/**
	 * Creates a Custom Taxonomy.
	 *
	 * @since 1.0.0
	 */
	public function taxonomy_create() {

		// Only register once.
		static $registered;
		if ( $registered ) {
			return;
		}

		// Labels.
		$labels = [
			'name'              => _x( 'Resource Types', 'taxonomy general name', 'transition-resources' ),
			'singular_name'     => _x( 'Resource Type', 'taxonomy singular name', 'transition-resources' ),
			'search_items'      => __( 'Search Resource Types', 'transition-resources' ),
			'all_items'         => __( 'All Resource Types', 'transition-resources' ),
			'parent_item'       => __( 'Parent Resource Type', 'transition-resources' ),
			'parent_item_colon' => __( 'Parent Resource Type:', 'transition-resources' ),
			'edit_item'         => __( 'Edit Resource Type', 'transition-resources' ),
			'update_item'       => __( 'Update Resource Type', 'transition-resources' ),
			'add_new_item'      => __( 'Add New Resource Type', 'transition-resources' ),
			'new_item_name'     => __( 'New Resource Type Name', 'transition-resources' ),
			'menu_name'         => __( 'Resource Types', 'transition-resources' ),
			'not_found'         => __( 'No Resource Types found', 'transition-resources' ),
		];

		// Rewrite rules.
		$rewrite = [
			'slug' => 'resource-types',
		];

		// Arguments.
		$args = [
			'hierarchical'      => true,
			'labels'            => $labels,
			'rewrite'           => $rewrite,
			// Show column in wp-admin.
			'show_admin_column' => true,
			'show_ui'           => true,
			// REST setup.
			'show_in_rest'      => true,
			'rest_base'         => $this->taxonomy_rest_base,
		];

		// Register a taxonomy for this CPT.
		register_taxonomy( $this->taxonomy_name, $this->post_type_name, $args );

		// Flag done.
		$registered = true;

	}

	/**
	 * Creates a second Custom Taxonomy.
	 *
	 * @since 1.0.0
	 */
	public function taxonomy_two_create() {

		// Only register once.
		static $registered;
		if ( $registered ) {
			return;
		}

		// Labels.
		$labels = [
			'name'              => _x( 'Project Types', 'taxonomy general name', 'transition-resources' ),
			'singular_name'     => _x( 'Project Type', 'taxonomy singular name', 'transition-resources' ),
			'search_items'      => __( 'Search Project Types', 'transition-resources' ),
			'all_items'         => __( 'All Project Types', 'transition-resources' ),
			'parent_item'       => __( 'Parent Project Type', 'transition-resources' ),
			'parent_item_colon' => __( 'Parent Project Type:', 'transition-resources' ),
			'edit_item'         => __( 'Edit Project Type', 'transition-resources' ),
			'update_item'       => __( 'Update Project Type', 'transition-resources' ),
			'add_new_item'      => __( 'Add New Project Type', 'transition-resources' ),
			'new_item_name'     => __( 'New Project Type Name', 'transition-resources' ),
			'menu_name'         => __( 'Project Types', 'transition-resources' ),
			'not_found'         => __( 'No Project Types found', 'transition-resources' ),
		];

		// Rewrite rules.
		$rewrite = [
			'slug' => 'project-types',
		];

		// Arguments.
		$args = [
			'hierarchical'      => true,
			'labels'            => $labels,
			'rewrite'           => $rewrite,
			// Show column in wp-admin.
			'show_admin_column' => true,
			'show_ui'           => true,
			// REST setup.
			'show_in_rest'      => true,
			'rest_base'         => $this->taxonomy_two_rest_base,
		];

		// Register a taxonomy for this CPT.
		register_taxonomy( $this->taxonomy_two_name, $this->post_type_name, $args );

		// Flag done.
		$registered = true;

	}

	/**
	 * Creates a third Custom Taxonomy.
	 *
	 * @since 1.0.0
	 */
	public function taxonomy_three_create() {

		// Only register once.
		static $registered;
		if ( $registered ) {
			return;
		}

		// Labels.
		$labels = [
			'name'              => _x( 'Countries', 'taxonomy general name', 'transition-resources' ),
			'singular_name'     => _x( 'Country', 'taxonomy singular name', 'transition-resources' ),
			'search_items'      => __( 'Search Countries', 'transition-resources' ),
			'all_items'         => __( 'All Countries', 'transition-resources' ),
			'parent_item'       => __( 'Parent Country', 'transition-resources' ),
			'parent_item_colon' => __( 'Parent Country:', 'transition-resources' ),
			'edit_item'         => __( 'Edit Country', 'transition-resources' ),
			'update_item'       => __( 'Update Country', 'transition-resources' ),
			'add_new_item'      => __( 'Add New Country', 'transition-resources' ),
			'new_item_name'     => __( 'New Country Name', 'transition-resources' ),
			'menu_name'         => __( 'Countries', 'transition-resources' ),
			'not_found'         => __( 'No Countries found', 'transition-resources' ),
		];

		// Rewrite rules.
		$rewrite = [
			'slug' => 'countries',
		];

		// Arguments.
		$args = [
			'hierarchical'      => true,
			'labels'            => $labels,
			'rewrite'           => $rewrite,
			// Show column in wp-admin.
			'show_admin_column' => true,
			'show_ui'           => true,
			// REST setup.
			'show_in_rest'      => true,
			'rest_base'         => $this->taxonomy_four_rest_base,
		];

		// Register a taxonomy for this CPT.
		register_taxonomy( $this->taxonomy_four_name, $this->post_type_name, $args );

		// Flag done.
		$registered = true;

	}

	/**
	 * Creates a free Custom Taxonomy.
	 *
	 * @since 1.0.0
	 */
	public function tag_create() {

		// Only register once.
		static $registered;
		if ( $registered ) {
			return;
		}

		// Labels.
		$labels = [
			'name'              => _x( 'Resource Tags', 'taxonomy general name', 'transition-resources' ),
			'singular_name'     => _x( 'Resource Tag', 'taxonomy singular name', 'transition-resources' ),
			'search_items'      => __( 'Search Resource Tags', 'transition-resources' ),
			'all_items'         => __( 'All Resource Tags', 'transition-resources' ),
			'parent_item'       => __( 'Parent Resource Tag', 'transition-resources' ),
			'parent_item_colon' => __( 'Parent Resource Tag:', 'transition-resources' ),
			'edit_item'         => __( 'Edit Resource Tag', 'transition-resources' ),
			'update_item'       => __( 'Update Resource Tag', 'transition-resources' ),
			'add_new_item'      => __( 'Add New Resource Tag', 'transition-resources' ),
			'new_item_name'     => __( 'New Resource Tag Name', 'transition-resources' ),
			'menu_name'         => __( 'Resource Tags', 'transition-resources' ),
			'not_found'         => __( 'No Resource Tags found', 'transition-resources' ),
		];

		// Rewrite rules.
		$rewrite = [
			'slug' => 'resource-tags',
		];

		// Arguments.
		$args = [
			'hierarchical'      => false,
			'labels'            => $labels,
			'rewrite'           => $rewrite,
			// Show column in wp-admin.
			'show_admin_column' => true,
			'show_ui'           => true,
			// REST setup.
			'show_in_rest'      => true,
			'rest_base'         => $this->tag_rest_base,
		];

		// Register a free Taxonomy for this CPT.
		register_taxonomy( $this->tag_name, $this->post_type_name, $args );

		// Flag done.
		$registered = true;

	}

}
