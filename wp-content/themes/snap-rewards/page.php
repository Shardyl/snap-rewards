<?php
/**
 * Generic page fallback. Renders a captured partial matching the page slug if
 * present; otherwise the standard WordPress content.
 * @package snap-rewards
 */
get_header();
$slug = get_post_field( 'post_name', get_queried_object_id() );
$partial = get_template_directory() . '/inc/content/' . sanitize_file_name( $slug ) . '.html';
if ( is_readable( $partial ) ) {
	snap_render( $slug );
} else {
	while ( have_posts() ) {
		the_post();
		?>
		<main>
			<div class="container" style="padding:80px 0;">
				<h1><?php the_title(); ?></h1>
				<div class="entry-content"><?php the_content(); ?></div>
			</div>
		</main>
		<?php
	}
}
get_footer();
