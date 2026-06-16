<?php
/**
 * Docs archive (/docs/) — reproduces the original BetterDocs landing:
 * a "How can we help you?" search hero, then the category folder-box grid.
 * Uses BetterDocs' own shortcodes so content stays native + editable.
 *
 * @package snap-rewards
 */
get_header();
?>
<main id="primary" class="site-main betterdocs-wrapper betterdocs-docs-archive-wrapper">
	<style>
		.snap-docs-hero{background:#f5f6fb;padding:70px 0 60px}
		.snap-docs-hero .snap-wrap{max-width:1000px;margin:0 auto;padding:0 20px}
		.snap-docs-hero h2{text-align:center;color:#33415c;font-weight:700;font-size:42px;margin:0 0 32px}
		.snap-docs-hero .betterdocs-search-form,.snap-docs-hero form{max-width:820px;margin:0 auto}
		.snap-docs-boxes{padding:50px 0 30px}
		.snap-docs-boxes .snap-wrap{max-width:1200px;margin:0 auto;padding:0 20px}
	</style>

	<section class="snap-docs-hero">
		<div class="snap-wrap">
			<h2>How can we help you?</h2>
			<?php echo do_shortcode( '[betterdocs_search_form]' ); ?>
		</div>
	</section>

	<section class="snap-docs-boxes">
		<div class="snap-wrap">
			<?php echo do_shortcode( '[betterdocs_category_box]' ); ?>
		</div>
	</section>
</main>
<?php
get_footer();
