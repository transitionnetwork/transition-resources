<?php
/**
 * Active Custom Post Types template.
 *
 * Handles markup for the Active Custom Post Types meta box.
 *
 * @package Transition_Resources
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>
<!-- <?php echo esc_html( $this->metabox_path ); ?>metabox-settings-cpts.php -->
<table class="form-table">
	<tr>
		<th scope="row"><?php esc_html_e( 'Active Custom Post Types', 'transition-resources' ); ?></th>
		<td>
			<?php foreach ( $cpts_info as $cpt => $label ) : ?>
				<p><input type="checkbox" class="settings-checkbox" id="tn_resources_post_type_<?php echo esc_attr( $cpt ); ?>" name="<?php echo esc_attr( $this->key_post_types_enabled ); ?>[]" value="<?php echo esc_attr( $cpt ); ?>"<?php checked( 1, $cpts_enabled[ $cpt ] ); ?> /> <label class="settings-label" for="tn_resources_post_type_<?php echo esc_attr( $cpt ); ?>"><?php echo esc_html( $label ); ?></label></p>
			<?php endforeach; ?>
		</td>
	</tr>
</table>
