<?php
/**
 * Transition Resources
 *
 * Plugin Name:       Transition Resources
 * Description:       Provides a Resources Directory for the Transition Network.
 * Plugin URI:        https://github.com/transitionnetwork/transition-resources
 * GitHub Plugin URI: https://github.com/transitionnetwork/transition-resources
 * Version:           1.0.0a
 * Author:            Transition Network
 * Author URI:        https://transitionnetwork.org/
 * License:           MIT
 * License URI:       https://opensource.org/license/MIT
 * Requires at least: 6.5
 * Requires PHP:      8.0
 * Text Domain:       transition-resources
 * Domain Path:       /languages
 *
 * @package Transition_Resources
 * @link    https://github.com/transitionnetwork/transition-resources
 * @license MIT
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Version.
define( 'TRANSITION_RESOURCES_VERSION', '1.0.0a' );

// Main plugin file.
if ( ! defined( 'TRANSITION_RESOURCES_FILE' ) ) {
	define( 'TRANSITION_RESOURCES_FILE', __FILE__ );
}

// Plugin basename.
if ( ! defined( 'TRANSITION_RESOURCES_BASE' ) ) {
	define( 'TRANSITION_RESOURCES_BASE', plugin_basename( TRANSITION_RESOURCES_FILE ) );
}

// Plugin path.
if ( ! defined( 'TRANSITION_RESOURCES_PATH' ) ) {
	define( 'TRANSITION_RESOURCES_PATH', plugin_dir_path( TRANSITION_RESOURCES_FILE ) );
}

// Source path.
if ( ! defined( 'TRANSITION_RESOURCES_SRC' ) ) {
	define( 'TRANSITION_RESOURCES_SRC', TRANSITION_RESOURCES_PATH . 'includes' );
}

// Plugin URL.
if ( ! defined( 'TRANSITION_RESOURCES_URL' ) ) {
	define( 'TRANSITION_RESOURCES_URL', plugin_dir_url( TRANSITION_RESOURCES_FILE ) );
}

/**
 * Gets a reference to this plugin.
 *
 * @since 1.0.0
 *
 * @return Transition_Resources\Plugin $plugin The plugin reference.
 */
function transition_resources() {

	// Store plugin object in static variable.
	static $plugin = false;

	// Maybe bootstrap plugin.
	if ( false === $plugin ) {

		// Bootstrap autoloader.
		require_once trailingslashit( TRANSITION_RESOURCES_SRC ) . 'class-autoloader.php';
		$namespace   = 'Transition_Resources';
		$source_path = TRANSITION_RESOURCES_SRC;
		new Transition_Resources\Autoloader( $namespace, $source_path );

		// Bootstrap plugin.
		$plugin = new Transition_Resources\Plugin();

	}

	// --<
	return $plugin;

}

// Initialise plugin immediately.
transition_resources();

/*
 * Uninstall uses the 'uninstall.php' method.
 *
 * @see https://developer.wordpress.org/reference/functions/register_uninstall_hook/
 */
