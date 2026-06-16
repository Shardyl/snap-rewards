<?php
/**
 * Ultimate fallback template.
 * @package snap-rewards
 */
get_header();
?>
<main>
	<div class="container" style="padding:80px 0;">
		<?php
		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();
				?>
				<article <?php post_class(); ?>>
					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<div class="entry-summary"><?php the_excerpt(); ?></div>
				</article>
				<?php
			}
			the_posts_pagination();
		} else {
			echo '<p>Nothing found.</p>';
		}
		?>
	</div>
</main>
<?php
get_footer();
