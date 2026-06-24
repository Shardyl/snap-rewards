<?php
/**
 * Blog index ("Loyalty news"). The captured hero (intro band) is reused as-is, but the post CARDS are rendered
 * DYNAMICALLY from the live posts so each card shows that post's current WordPress featured image, title, date
 * and excerpt (re-composed Cortex posts feed straight through). Card markup mirrors the captured snapshot.
 * @package snap-rewards
 */
get_header();

// Reuse the captured hero (everything before the blog-area grid) so the intro band stays byte-identical.
$blog_html = @file_get_contents( get_template_directory() . '/inc/content/blog.html' );
$cut       = $blog_html ? strpos( $blog_html, '<section class="blog-area' ) : false;
echo ( false !== $cut ) ? substr( $blog_html, 0, $cut ) : '<main id="primary" class="site-main">';
?>
<section class="blog-area pt-0 pb-60">
	<div class="container">
		<div class="row">
			<?php
			$q = new WP_Query( array(
				'post_type'           => 'post',
				'post_status'         => 'publish',
				'posts_per_page'      => 12,
				'ignore_sticky_posts' => true,
			) );
			while ( $q->have_posts() ) :
				$q->the_post();
				$url = esc_url( get_permalink() );
				?>
				<div class="col-lg-4 col-md-6 mb-4">
					<div class="card h-100">
						<div class="o-top-block">
							<div class="o-featured-image"><a href="<?php echo $url; ?>"><?php
								if ( has_post_thumbnail() ) {
									the_post_thumbnail( 'large', array( 'class' => 'img-fluid rounded wp-post-image' ) );
								}
							?></a></div>
							<div class="o-mid-content">
								<header class="entry-header">
									<h2 class="entry-title h4 mb-2"><a href="<?php echo $url; ?>" rel="bookmark" class="text-dark"><?php the_title(); ?></a></h2>
									<p class="post-meta text-muted"><?php echo esc_html( get_the_date() ); ?></p>
								</header>
								<div class="entry-summary mb-3"><p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22, '...' ) ); ?></p></div>
							</div>
						</div>
						<footer class="entry-footer mt-auto"><a href="<?php echo $url; ?>" class="btn btn-sm btn-primary">Read More</a></footer>
					</div>
				</div>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>
	</div>
</section>
</main>
<?php
get_footer();
