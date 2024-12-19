<?php
/**
 * The Resource Embed Template Stylesheet.
 *
 * When a Resource is embedded in an iframe, this file provides styles.
 *
 * @package Transition_Resources
 * @since 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>
<style>
	.wp-embed h4 {
		clear: both;
		margin: 1em 0 0.2em 0;
	}

	.wp-embed .file-container {
		display: grid;
		grid-template-columns: repeat(2, 1fr);
	}

	.wp-embed .file-wrapper {
		margin: 0.2em 0;
	}

	.wp-embed .file-icon {
		float: left;
		margin-right: 0.5em;
	}

	.wp-embed ul {
		padding-left: 1em;
		margin: 0.2em;
	}
</style>
