<?php
/**
 * Embed Template class.
 *
 * Handles modification of oEmbed templates.
 *
 * @package Transition_Resources
 */

namespace Transition_Resources\Embed;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Embed Resources Template class.
 *
 * A class that modifies oEmbed templates for Resources.
 *
 * @since 1.0.0
 */
class Resources {

	/**
	 * Plugin object.
	 *
	 * @since 1.0.0
	 * @var Plugin
	 */
	public $plugin;

	/**
	 * Embed loader object.
	 *
	 * @since 1.0.0
	 * @var Embed\Loader
	 */
	public $embed;

	/**
	 * Relative path to the template directory.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $path_template = 'assets/templates/embed/';

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Embed\Loader $embed The oEmbed Loader object.
	 */
	public function __construct( $embed ) {

		// Store references to objects.
		$this->plugin = $embed->plugin;
		$this->embed  = $embed;

		// Init when the oEmbed Loader class has loaded all its classes.
		add_action( 'tn_resources/embed/objects/loaded', [ $this, 'initialise' ] );

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
		do_action( 'tn_resources/embed/resources/loaded' );

	}

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {

		// Register oEmbed hooks.
		$this->register_hooks_embed();

	}

	/**
	 * Registers oEmbed hooks.
	 *
	 * @since 1.0.0
	 */
	private function register_hooks_embed() {

		// Filter the Post Title.
		add_filter( 'the_title', [ $this, 'the_title' ], 10, 2 );

		// Inject CSS from this plugin.
		add_action( 'embed_head', [ $this, 'embed_head' ] );

		// Inject template from this plugin.
		add_action( 'embed_content', [ $this, 'embed_content' ] );

		/*
		// Inject template from this plugin.
		add_filter( 'embed_template', [ $this, 'template_path' ], 20, 3 );
		*/

	}

	/**
	 * Modifies the Post Title.
	 *
	 * @since 1.0.0
	 *
	 * @param string  $post_title The title of the Post.
	 * @param integer $post_id The ID of the Post.
	 * @return string $post_title The title of the Post.
	 */
	public function the_title( $post_title, $post_id ) {

		// Bail if not an oEmbed request.
		if ( ! is_embed() ) {
			return $post_title;
		}

		// Bail if not an oEmbed request for a Resource.
		$post = get_post( $post_id );
		if ( $this->plugin->cpt->resources->post_type_name !== $post->post_type ) {
			return $post_title;
		}

		// Prepend "Resource".
		$post_title = sprintf(
			/* translators: %s: The name of the resource. */
			esc_html__( 'Resource: %s', 'transition-resources' ),
			$post_title
		);

		// --<
		return $post_title;

	}

	/**
	 * Appends to the Embed Header.
	 *
	 * @since 1.0.0
	 */
	public function embed_head() {

		// Bail if not an oEmbed request for a Resource.
		if ( get_post_type() !== $this->plugin->cpt->resources->post_type_name ) {
			return;
		}

		// Include template.
		include TRANSITION_RESOURCES_PATH . $this->path_template . 'embed-resource-styles.php';

	}

	/**
	 * Appends to the Embed Content.
	 *
	 * @since 1.0.0
	 */
	public function embed_content() {

		// Bail if not an oEmbed request for a Resource.
		if ( get_post_type() !== $this->plugin->cpt->resources->post_type_name ) {
			return;
		}

		// Include template.
		include TRANSITION_RESOURCES_PATH . $this->path_template . 'embed-resource-fields.php';

	}

	/**
	 * Supplies the Embed Template from this plugin.
	 *
	 * @since 1.0.0
	 *
	 * @param string   $template  Path to the template. See locate_template().
	 * @param string   $type      Sanitized filename without extension.
	 * @param string[] $templates A list of template candidates, in descending order of priority.
	 * @return string $template The modified path to the template.
	 */
	public function template_path( $template, $type, $templates ) {

		// Sanity check.
		if ( empty( $templates ) ) {
			return $template;
		}

		// Bail if the first element is not the Embed Resource template.
		$primary_template = reset( $templates );
		if ( 'embed-resource.php' !== $primary_template ) {
			return $template;
		}

		// Parse template hierarchy including our plugin.
		foreach ( (array) $templates as $template_name ) {
			if ( ! $template_name ) {
				continue;
			}
			if ( file_exists( get_stylesheet_directory() . '/' . $template_name ) ) {
				$template = get_stylesheet_directory() . '/' . $template_name;
				break;
			} elseif ( is_child_theme() && file_exists( get_template_directory() . '/' . $template_name ) ) {
				$template = get_template_directory() . '/' . $template_name;
				break;
			} elseif ( file_exists( TRANSITION_RESOURCES_PATH . $this->path_template . $template_name ) ) {
				$template = TRANSITION_RESOURCES_PATH . $this->path_template . $template_name;
				break;
			} elseif ( file_exists( ABSPATH . WPINC . '/theme-compat/' . $template_name ) ) {
				$template = ABSPATH . WPINC . '/theme-compat/' . $template_name;
				break;
			}
		}

		// --<
		return $template;

	}

}
