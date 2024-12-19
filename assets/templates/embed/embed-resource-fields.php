<?php
/**
 * The Resource Embed Template.
 *
 * When a Resource is embedded in an iframe, this file is used to add the ACF Fields
 * to the content.
 *
 * @package Transition_Resources
 * @since 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>

<?php if ( have_rows( 'files' ) ) : ?>

	<h4><?php esc_html_e( 'Attached Files', 'transition-resources' ); ?></h4>
	<div class="file-container">
		<?php while ( have_rows( 'files' ) ) : ?>
			<?php the_row(); ?>

			<div class="file-wrapper">
				<?php $file_id = get_sub_field( 'file' ); ?>

				<?php if ( ! empty( $file_id ) ) : ?>
					<?php $attachment = acf_get_attachment( $file_id ); ?>
					<?php if ( ! empty( $attachment['icon'] ) ) : ?>
						<div class="file-icon">
							<img data-name="icon" src="<?php echo esc_url( $attachment['icon'] ); ?>" alt=""/>
						</div>
					<?php endif; ?>

					<div class="file-info">
						<p>
							<strong data-name="title"><?php echo esc_html( $attachment['title'] ); ?></strong>
						</p>
						<p>
							<strong><?php esc_html_e( 'File', 'transition-resources' ); ?>:</strong>
							<a href="<?php echo esc_url( $attachment['url'] ); ?>" target="_blank"><?php esc_html_e( 'Download here', 'transition-resources' ); ?></a>
						</p>
						<p>
							<strong><?php esc_html_e( 'File size', 'transition-resources' ); ?>:</strong>
							<span data-name="filesize"><?php echo esc_html( size_format( $attachment['filesize'] ) ); ?></span>
						</p>
					</div>
				<?php endif; ?>

			</div>

		<?php endwhile; ?>
	</div>

<?php endif; ?>

<?php if ( have_rows( 'embed' ) ) : ?>

	<?php
	// Count the embeds, which will not show inside an iframe.
	$count = 0;
	while ( have_rows( 'embed' ) ) {
		the_row();
		$count++;
	}
	?>

	<?php if ( 0 < $count ) : ?>

		<h4><?php esc_html_e( 'Embedded Media', 'transition-resources' ); ?></h4>
		<div class="embed-container">
			<p class="embed-explanation">
				<?php

				$message = sprintf(
					/* translators: %d: Comment count. */
					_n( 'Please visit the resource to view the %d media item.', 'Please visit the resource to view the %d media items.', $count, 'transition-resources' ),
					esc_html( number_format_i18n( $count ) ) // Substitution.
				);

				echo esc_html( $message );

				?>
			</p>
		</div>

	<?php endif; ?>

<?php endif; ?>

<?php $related = get_field( 'related' ); ?>
<?php if ( ! empty( $related ) ) : ?>

	<h4><?php esc_html_e( 'Related Resources', 'transition-resources' ); ?></h4>
	<div class="related-container">
		<ul>
		<?php foreach ( $related as $item ) : ?>
			<li>
				<a href="<?php echo esc_url( get_permalink( $item->ID ) ); ?>" target="_blank"><?php echo esc_html( $item->post_title ); ?></a>
			</li>
		<?php endforeach; ?>
		</ul>
	</div>

<?php endif; ?>

<?php $license = get_field( 'license' ); ?>
<?php if ( ! empty( $license ) ) : ?>

	<h4><?php esc_html_e( 'License', 'transition-resources' ); ?></h4>
	<div class="license-container">
		<?php if ( 1 === (int) $license ) : ?>
			<p><?php esc_html_e( 'CC-BY', 'transition-resources' ); ?></p>
		<?php elseif ( 2 === (int) $license ) : ?>
			<p><?php esc_html_e( 'CC-BY-NC', 'transition-resources' ); ?></p>
		<?php elseif ( 3 === (int) $license ) : ?>
			<p><?php esc_html_e( 'All rights reserved', 'transition-resources' ); ?></p>
		<?php endif; ?>
	</div>

<?php endif; ?>

<?php if ( have_rows( 'authors' ) ) : ?>

	<h4><?php esc_html_e( 'Authors', 'transition-resources' ); ?></h4>
	<div class="author-container">
		<ul>
			<?php while ( have_rows( 'authors' ) ) : ?>
				<?php the_row(); ?>

				<?php $author_name = get_sub_field( 'author_name' ); ?>
				<?php $author_link = get_sub_field( 'author_link' ); ?>
				<li>
					<?php if ( ! empty( $author_link ) ) : ?>
						<a href="<?php echo esc_url( $author_link ); ?>" target="_blank"><?php echo esc_html( $author_name ); ?></a>
					<?php else : ?>
						<?php echo esc_html( $author_name ); ?>
					<?php endif; ?>
				</li>
			<?php endwhile; ?>
		</ul>
	</div>

<?php endif; ?>
