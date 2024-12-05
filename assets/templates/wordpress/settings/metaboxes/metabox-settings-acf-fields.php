<?php
/**
 * ACF Fields settings template.
 *
 * Handles markup for the "ACF Fields" meta box.
 *
 * @package Transition_Resources
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>
<!-- <?php echo esc_html( $this->metabox_path ); ?>metabox-settings-acf-fields.php -->
<table class="form-table">
	<tr>
		<th scope="row"><?php esc_html_e( 'ACF Fields', 'transition-resources' ); ?></th>
		<td>
			<select class="settings-select" name="<?php echo esc_attr( $this->key_fields_enabled ); ?>" id="<?php echo esc_attr( $this->key_fields_enabled ); ?>">
				<option value="no" <?php selected( $fields_enabled, 'no' ); ?>><?php esc_html_e( 'No', 'transition-resources' ); ?></option>
				<option value="yes" <?php selected( $fields_enabled, 'yes' ); ?>><?php esc_html_e( 'Yes', 'transition-resources' ); ?></option>
			</select>
			<p class="description"><?php esc_html_e( 'Description here.', 'transition-resources' ); ?></p>
		</td>
	</tr>
</table>
