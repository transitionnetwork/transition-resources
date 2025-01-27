<?php
/**
 * Abstract Custom Post Type class.
 *
 * Handles common Custom Post Type functionality.
 *
 * @package Transition_Resources
 */

namespace Transition_Resources\CPT;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Abstract Custom Post Type class.
 *
 * A class that encapsulates common Custom Post Type functionality.
 *
 * @since 1.0.0
 */
abstract class Base {

	/**
	 * Custom Post Type name.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $post_type_name;

	/**
	 * Custom Post Type REST base.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $post_type_rest_base;

	/**
	 * Taxonomy name.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $taxonomy_name;

	/**
	 * Taxonomy REST base.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $taxonomy_rest_base;

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Init when this plugin is loaded.
		add_action( 'tn_resources/cpt/objects/loaded', [ $this, 'register_hooks' ] );

	}

	/**
	 * Registers WordPress hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {

		// Activation and deactivation.
		add_action( 'tn_resources/activate', [ $this, 'activate' ] );
		add_action( 'tn_resources/deactivate', [ $this, 'deactivate' ] );

		// Always create post type.
		add_action( 'init', [ $this, 'post_type_create' ] );

		// Make sure our feedback is appropriate.
		add_filter( 'post_updated_messages', [ $this, 'post_type_messages' ] );

		// Make sure our UI text is appropriate.
		add_filter( 'enter_title_here', [ $this, 'post_type_title' ] );

		// Maybe create primary taxonomy.
		if ( ! empty( $this->taxonomy_name ) ) {
			add_action( 'init', [ $this, 'taxonomy_create' ] );
			add_filter( 'wp_terms_checklist_args', [ $this, 'taxonomy_fix_metabox' ], 10, 2 );
			add_action( 'restrict_manage_posts', [ $this, 'taxonomy_filter_post_type' ] );
		}

		// Maybe create second taxonomy.
		if ( ! empty( $this->taxonomy_two_name ) ) {
			add_action( 'init', [ $this, 'taxonomy_two_create' ] );
			add_filter( 'wp_terms_checklist_args', [ $this, 'taxonomy_two_fix_metabox' ], 10, 2 );
			add_action( 'restrict_manage_posts', [ $this, 'taxonomy_two_filter_post_type' ] );
		}

		// Maybe create third taxonomy.
		if ( ! empty( $this->taxonomy_three_name ) ) {
			add_action( 'init', [ $this, 'taxonomy_three_create' ] );
			add_filter( 'wp_terms_checklist_args', [ $this, 'taxonomy_three_fix_metabox' ], 10, 2 );
			add_action( 'restrict_manage_posts', [ $this, 'taxonomy_three_filter_post_type' ] );
		}

		// Maybe create fourth taxonomy.
		if ( ! empty( $this->taxonomy_four_name ) ) {
			add_action( 'init', [ $this, 'taxonomy_four_create' ] );
			add_filter( 'wp_terms_checklist_args', [ $this, 'taxonomy_four_fix_metabox' ], 10, 2 );
			add_action( 'restrict_manage_posts', [ $this, 'taxonomy_four_filter_post_type' ] );
		}

		// Maybe create first free taxonomy.
		if ( ! empty( $this->tag_one_name ) ) {
			add_action( 'init', [ $this, 'tag_one_create' ] );
			add_action( 'restrict_manage_posts', [ $this, 'tag_one_filter_post_type' ] );
		}

		// Maybe create second free taxonomy.
		if ( ! empty( $this->tag_two_name ) ) {
			add_action( 'init', [ $this, 'tag_two_create' ] );
			add_action( 'restrict_manage_posts', [ $this, 'tag_two_filter_post_type' ] );
		}

	}

	/**
	 * Actions to perform on plugin activation.
	 *
	 * @since 1.0.0
	 */
	public function activate() {

		// Pass through.
		$this->post_type_create();
		$this->taxonomy_create();
		$this->taxonomy_alt_create();
		$this->tag_create();

		// Go ahead and flush.
		flush_rewrite_rules();

	}

	/**
	 * Actions to perform on plugin deactivation (NOT deletion).
	 *
	 * @since 1.0.0
	 */
	public function deactivate() {

		// Flush rules to reset.
		flush_rewrite_rules();

	}

	// -----------------------------------------------------------------------------------

	/**
	 * Creates a Custom Post Type.
	 *
	 * @since 1.0.0
	 */
	abstract public function post_type_create();

	/**
	 * Overrides the messages for a Custom Post Type.
	 *
	 * @since 1.0.0
	 *
	 * @param array $messages The existing messages.
	 * @return array $messages The modified messages.
	 */
	abstract public function post_type_messages( $messages );

	/**
	 * Overrides the "Add title" label.
	 *
	 * @since 1.0.0
	 *
	 * @param str $title The existing title - usually "Add title".
	 * @return str $title The modified title.
	 */
	public function post_type_title( $title ) {
		return $title;
	}

	// -----------------------------------------------------------------------------------

	/**
	 * Creates a Custom Taxonomy.
	 *
	 * @since 1.0.0
	 */
	public function taxonomy_create() {}

	/**
	 * Fixes the Custom Taxonomy metabox.
	 *
	 * @see https://core.trac.wordpress.org/ticket/10982
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The existing arguments.
	 * @param int   $post_id The WordPress post ID.
	 */
	public function taxonomy_fix_metabox( $args, $post_id ) {

		// If rendering metabox for our taxonomy.
		if ( isset( $args['taxonomy'] ) && $args['taxonomy'] === $this->taxonomy_name ) {

			// Setting 'checked_ontop' to false seems to fix this.
			$args['checked_ontop'] = false;

		}

		// --<
		return $args;

	}

	/**
	 * Adds a filter for this Custom Taxonomy to the Custom Post Type listing.
	 *
	 * @since 1.0.0
	 */
	public function taxonomy_filter_post_type() {

		// Access current post type.
		global $typenow;

		// Bail if not our post type.
		if ( $typenow !== $this->post_type_name ) {
			return;
		}

		// Get tax object.
		$taxonomy = get_taxonomy( $this->taxonomy_name );

		// Build args.
		$args = [
			/* translators: %s: The plural name of the taxonomy terms. */
			'show_option_all' => sprintf( __( 'Show All %s', 'transition-resources' ), $taxonomy->label ),
			'taxonomy'        => $this->taxonomy_name,
			'name'            => $this->taxonomy_name,
			'orderby'         => 'name',
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended
			'selected'        => isset( $_GET[ $this->taxonomy_name ] ) ? wp_unslash( $_GET[ $this->taxonomy_name ] ) : '',
			'show_count'      => true,
			'hide_empty'      => true,
			'value_field'     => 'slug',
			'hierarchical'    => 1,
		];

		// Show a dropdown.
		wp_dropdown_categories( $args );

	}

	// -----------------------------------------------------------------------------------

	/**
	 * Creates a second Custom Taxonomy.
	 *
	 * @since 1.0.0
	 */
	public function taxonomy_two_create() {}

	/**
	 * Fix the second Custom Taxonomy metabox.
	 *
	 * @see https://core.trac.wordpress.org/ticket/10982
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The existing arguments.
	 * @param int   $post_id The WordPress post ID.
	 */
	public function taxonomy_two_fix_metabox( $args, $post_id ) {

		// If rendering metabox for our taxonomy.
		if ( isset( $args['taxonomy'] ) && $args['taxonomy'] === $this->taxonomy_two_name ) {

			// Setting 'checked_ontop' to false seems to fix this.
			$args['checked_ontop'] = false;

		}

		// --<
		return $args;

	}

	/**
	 * Adds a filter for the second Custom Taxonomy to the Custom Post Type listing.
	 *
	 * @since 1.0.0
	 */
	public function taxonomy_two_filter_post_type() {

		// Access current post type.
		global $typenow;

		// Bail if not our post type.
		if ( $typenow !== $this->post_type_name ) {
			return;
		}

		// Get tax object.
		$taxonomy = get_taxonomy( $this->taxonomy_two_name );

		// Build args.
		$args = [
			/* translators: %s: The plural name of the taxonomy terms. */
			'show_option_all' => sprintf( __( 'Show All %s', 'transition-resources' ), $taxonomy->label ),
			'taxonomy'        => $this->taxonomy_two_name,
			'name'            => $this->taxonomy_two_name,
			'orderby'         => 'name',
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended
			'selected'        => isset( $_GET[ $this->taxonomy_two_name ] ) ? wp_unslash( $_GET[ $this->taxonomy_two_name ] ) : '',
			'show_count'      => true,
			'hide_empty'      => true,
			'value_field'     => 'slug',
			'hierarchical'    => 1,
		];

		// Show a dropdown.
		wp_dropdown_categories( $args );

	}

	// -----------------------------------------------------------------------------------

	/**
	 * Creates a third Custom Taxonomy.
	 *
	 * @since 1.0.0
	 */
	public function taxonomy_three_create() {}

	/**
	 * Fix the third Custom Taxonomy metabox.
	 *
	 * @see https://core.trac.wordpress.org/ticket/10982
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The existing arguments.
	 * @param int   $post_id The WordPress post ID.
	 */
	public function taxonomy_three_fix_metabox( $args, $post_id ) {

		// If rendering metabox for our taxonomy.
		if ( isset( $args['taxonomy'] ) && $args['taxonomy'] === $this->taxonomy_three_name ) {

			// Setting 'checked_ontop' to false seems to fix this.
			$args['checked_ontop'] = false;

		}

		// --<
		return $args;

	}

	/**
	 * Adds a filter for the third Custom Taxonomy to the Custom Post Type listing.
	 *
	 * @since 1.0.0
	 */
	public function taxonomy_three_filter_post_type() {

		// Access current post type.
		global $typenow;

		// Bail if not our post type.
		if ( $typenow !== $this->post_type_name ) {
			return;
		}

		// Get tax object.
		$taxonomy = get_taxonomy( $this->taxonomy_three_name );

		// Build args.
		$args = [
			/* translators: %s: The plural name of the taxonomy terms. */
			'show_option_all' => sprintf( __( 'Show All %s', 'transition-resources' ), $taxonomy->label ),
			'taxonomy'        => $this->taxonomy_three_name,
			'name'            => $this->taxonomy_three_name,
			'orderby'         => 'name',
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended
			'selected'        => isset( $_GET[ $this->taxonomy_three_name ] ) ? wp_unslash( $_GET[ $this->taxonomy_three_name ] ) : '',
			'show_count'      => true,
			'hide_empty'      => true,
			'value_field'     => 'slug',
			'hierarchical'    => 1,
		];

		// Show a dropdown.
		wp_dropdown_categories( $args );

	}

	// -----------------------------------------------------------------------------------

	/**
	 * Creates a fourth Custom Taxonomy.
	 *
	 * @since 1.0.0
	 */
	public function taxonomy_four_create() {}

	/**
	 * Fix the fourth Custom Taxonomy metabox.
	 *
	 * @see https://core.trac.wordpress.org/ticket/10982
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The existing arguments.
	 * @param int   $post_id The WordPress post ID.
	 */
	public function taxonomy_four_fix_metabox( $args, $post_id ) {

		// If rendering metabox for our taxonomy.
		if ( isset( $args['taxonomy'] ) && $args['taxonomy'] === $this->taxonomy_four_name ) {

			// Setting 'checked_ontop' to false seems to fix this.
			$args['checked_ontop'] = false;

		}

		// --<
		return $args;

	}

	/**
	 * Adds a filter for the fourth Custom Taxonomy to the Custom Post Type listing.
	 *
	 * @since 1.0.0
	 */
	public function taxonomy_four_filter_post_type() {

		// Access current post type.
		global $typenow;

		// Bail if not our post type.
		if ( $typenow !== $this->post_type_name ) {
			return;
		}

		// Get tax object.
		$taxonomy = get_taxonomy( $this->taxonomy_four_name );

		// Build args.
		$args = [
			/* translators: %s: The plural name of the taxonomy terms. */
			'show_option_all' => sprintf( __( 'Show All %s', 'transition-resources' ), $taxonomy->label ),
			'taxonomy'        => $this->taxonomy_four_name,
			'name'            => $this->taxonomy_four_name,
			'orderby'         => 'name',
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended
			'selected'        => isset( $_GET[ $this->taxonomy_four_name ] ) ? wp_unslash( $_GET[ $this->taxonomy_four_name ] ) : '',
			'show_count'      => true,
			'hide_empty'      => true,
			'value_field'     => 'slug',
			'hierarchical'    => 1,
		];

		// Show a dropdown.
		wp_dropdown_categories( $args );

	}

	// -----------------------------------------------------------------------------------

	/**
	 * Creates the first free Custom Taxonomy.
	 *
	 * @since 1.0.0
	 */
	public function tag_one_create() {}

	/**
	 * Adds a filter for the first free Custom Taxonomy to the Custom Post Type listing.
	 *
	 * @since 1.0.0
	 */
	public function tag_one_filter_post_type() {

		// Access current post type.
		global $typenow;

		// Bail if not our post type.
		if ( $typenow !== $this->post_type_name ) {
			return;
		}

		// Get tax object.
		$taxonomy = get_taxonomy( $this->tag_one_name );

		// Build args.
		$args = [
			/* translators: %s: The plural name of the taxonomy terms. */
			'show_option_all' => sprintf( __( 'Show All %s', 'transition-resources' ), $taxonomy->label ),
			'taxonomy'        => $this->tag_one_name,
			'name'            => $this->tag_one_name,
			'orderby'         => 'name',
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended
			'selected'        => isset( $_GET[ $this->tag_one_name ] ) ? wp_unslash( $_GET[ $this->tag_one_name ] ) : '',
			'show_count'      => true,
			'hide_empty'      => true,
			'value_field'     => 'slug',
			'hierarchical'    => 1,
		];

		// Show a dropdown.
		wp_dropdown_categories( $args );

	}

	// -----------------------------------------------------------------------------------

	/**
	 * Creates the second free Custom Taxonomy.
	 *
	 * @since 1.0.0
	 */
	public function tag_two_create() {}

	/**
	 * Adds a filter for the second free Custom Taxonomy to the Custom Post Type listing.
	 *
	 * @since 1.0.0
	 */
	public function tag_two_filter_post_type() {

		// Access current post type.
		global $typenow;

		// Bail if not our post type.
		if ( $typenow !== $this->post_type_name ) {
			return;
		}

		// Get tax object.
		$taxonomy = get_taxonomy( $this->tag_two_name );

		// Build args.
		$args = [
			/* translators: %s: The plural name of the taxonomy terms. */
			'show_option_all' => sprintf( __( 'Show All %s', 'transition-resources' ), $taxonomy->label ),
			'taxonomy'        => $this->tag_two_name,
			'name'            => $this->tag_two_name,
			'orderby'         => 'name',
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended
			'selected'        => isset( $_GET[ $this->tag_two_name ] ) ? wp_unslash( $_GET[ $this->tag_two_name ] ) : '',
			'show_count'      => true,
			'hide_empty'      => true,
			'value_field'     => 'slug',
			'hierarchical'    => 1,
		];

		// Show a dropdown.
		wp_dropdown_categories( $args );

	}

}
