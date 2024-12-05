<?php
/**
 * Modify Shortcuts Menu settings template.
 *
 * Handles markup for the Modify Shortcuts Menu setting on the Edit User screen.
 *
 * @package Transition_Resources
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>
<!-- <?php echo esc_html( $this->partial_path ); ?>partial-settings-ui-menu.php -->
<tr>
	<th scope="row"><?php esc_html_e( 'Modify Shortcuts Menu', 'transition-resources' ); ?></th>
	<td>
		<select class="settings-select" name="<?php echo esc_attr( $this->key_menu_enabled ); ?>" id="<?php echo esc_attr( $this->key_menu_enabled ); ?>">
			<option value="no" <?php selected( $menu_enabled, 'no' ); ?>><?php esc_html_e( 'No', 'transition-resources' ); ?></option>
			<option value="yes" <?php selected( $menu_enabled, 'yes' ); ?>><?php esc_html_e( 'Yes', 'transition-resources' ); ?></option>
		</select>
		<p class="description"><?php esc_html_e( 'Replace links in the CiviCRM Admin Utilities Shortcuts Menu with ones more useful for developers.', 'transition-resources' ); ?></p>
	</td>
</tr>
