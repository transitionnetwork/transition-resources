<?php
/**
 * The Resource Embed Template.
 *
 * When a Resource is embedded in an iframe, this file is used to create the output
 * if the active theme does not include an embed-resource.php template.
 *
 * @package Transition_Resources
 * @since 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header( 'embed' );

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		tnr_get_template_part( 'embed', 'content' );
	endwhile;
else :
	tnr_get_template_part( 'embed', '404' );
endif;

get_footer( 'embed' );
