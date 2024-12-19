<?php
/**
 * Global functions.
 *
 * Globally available functions live in this file.
 *
 * @package Transition_Resources
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Loads a template part into a template.
 *
 * This is an adapted clone of the built-in WordPress function, modified so that
 * templates can be filtered.
 *
 * @see get_template_part()
 *
 * @since 1.0.0
 *
 * @param string      $slug The slug name for the generic template.
 * @param string|null $name Optional. The name of the specialized template.
 * @param array       $args Optional. Additional arguments passed to the template.
 *                          Default empty array.
 * @return void|false Void on success, false if the template does not exist.
 */
function tnr_get_template_part( $slug, $name = null, $args = [] ) {

	/**
	 * Fires before the specified template part file is loaded.
	 *
	 * The dynamic portion of the hook name, `$slug`, refers to the slug name
	 * for the generic template part.
	 *
	 * @since 1.0.0
	 *
	 * @param string      $slug The slug name for the generic template.
	 * @param string|null $name The name of the specialized template or null if
	 *                          there is none.
	 * @param array       $args Additional arguments passed to the template.
	 */
	do_action( "get_template_part_{$slug}", $slug, $name, $args );

	$templates = [];
	$name      = (string) $name;
	if ( '' !== $name ) {
		$templates[] = "{$slug}-{$name}.php";
	}

	$templates[] = "{$slug}.php";

	/**
	 * Fires before an attempt is made to locate and load a template part.
	 *
	 * @since 1.0.0
	 *
	 * @param string   $slug      The slug name for the generic template.
	 * @param string   $name      The name of the specialized template or an empty
	 *                            string if there is none.
	 * @param string[] $templates Array of template files to search for, in order.
	 * @param array    $args      Additional arguments passed to the template.
	 */
	do_action( 'get_template_part', $slug, $name, $templates, $args );

	if ( ! tnr_locate_template( $templates, true, false, $args ) ) {
		return false;
	}
}

/**
 * Retrieves the name of the highest priority template file that exists.
 *
 * Searches in the stylesheet directory, then the template directory before checking
 * this plugin's template directory. Lastly checks `wp-includes/theme-compat`.
 *
 * @see locate_template()
 *
 * @since 1.0.0
 *
 * @param string|array $template_names Template file(s) to search for, in order.
 * @param bool         $load           If true the template file will be loaded if it is found.
 * @param bool         $load_once      Whether to require_once or require. Has no effect if `$load` is false.
 *                                     Default true.
 * @param array        $args           Optional. Additional arguments passed to the template.
 *                                     Default empty array.
 * @return string The template filename if one is located.
 */
function tnr_locate_template( $template_names, $load = false, $load_once = true, $args = [] ) {

	// Init return.
	$located = '';

	// Build template stack.
	$template_stack = [
		get_stylesheet_directory(),
		get_template_directory(),
		TRANSITION_RESOURCES_PATH . 'assets/templates/embed',
		ABSPATH . WPINC . '/theme-compat/',
	];

	/**
	 * Filters the template stack.
	 *
	 * The directories that this plugin checks are in the order in which they appear
	 * in this array.
	 *
	 * * Child Theme directory.
	 * * Parent Theme directory.
	 * * This plugin's Template directory.
	 * * WordPress Theme Compat directory.
	 *
	 * @since 1.0.0
	 *
	 * @param array $template_stack The array of absolute template directory paths.
	 */
	$template_stack = apply_filters( 'tn_resources/locate_template/template_stack', $template_stack );

	// Make sure there are no duplicates.
	$template_stack = array_unique( $template_stack );

	// Try to locate the requested template.
	foreach ( (array) $template_names as $template_name ) {
		if ( ! $template_name ) {
			continue;
		}
		foreach ( $template_stack as $stack_item ) {
			if ( file_exists( trailingslashit( $stack_item ) . $template_name ) ) {
				$located = trailingslashit( $stack_item ) . $template_name;
				break 2;
			}
		}
	}

	// Maybe load located template.
	if ( $load && '' !== $located ) {
		load_template( $located, $load_once, $args );
	}

	// --<
	return $located;

}
