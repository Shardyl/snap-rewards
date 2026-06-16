<?php
/**
 * Single doc — render the doc's own title + body (BetterDocs free mis-renders
 * single docs with the archive layout). The sidebar + breadcrumb are injected
 * by snap_docs_footer_sidebar() into .betterdocs-content-wrapper / -content-area.
 *
 * @package snap-rewards
 */
get_header();
?>
<div class="betterdocs-wrapper betterdocs-single-wrapper betterdocs-single-layout">
	<div class="betterdocs-content-wrapper betterdocs-display-flex">
		<div class="betterdocs-content-area docs-single-main">
			<div class="betterdocs-content-inner-area">
				<?php
				while ( have_posts() ) {
					the_post();
					$updated = get_the_modified_date( 'F j, Y' );
					?>
					<article <?php post_class( 'betterdocs-single-content single-post-area' ); ?>>
						<h1 class="betterdocs-entry-title"><?php the_title(); ?></h1>
						<div class="snap-doc-updated">Last Updated: <?php echo esc_html( $updated ); ?></div>
						<div class="betterdocs-entry-content"><?php the_content(); ?></div>
					</article>
					<?php
				}
				?>
			</div>
		</div>
	</div>
</div>
<?php
get_footer();
