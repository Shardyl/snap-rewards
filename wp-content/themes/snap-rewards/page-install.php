<?php
/**
 * Install — redirect page to the Shopify app listing (preserves ?ref attribution).
 * Mirrors the original /install/ behaviour.
 * @package snap-rewards
 */
get_header();
?>
<main>
	<div class="container" style="padding:140px 0;text-align:center;">
		<script>
		  (function() {
		    const params = new URLSearchParams(window.location.search);
		    const ref = params.get('ref') || 'direct';
		    document.cookie = `ref=${ref}; path=/; domain=.snap-rewards.com; max-age=2592000; SameSite=None; Secure`;
		    localStorage.setItem('ref', ref);
		    const redirectUrl = 'https://apps.shopify.com/snap-rewards';
		    setTimeout(() => {
		      window.location.href = redirectUrl;
		    }, 1000);
		  })();
		</script>
		<p style="text-align:center;">Redirecting you to Snap Rewards...<br>
		If not redirected, <a href="https://apps.shopify.com/snap-rewards">click here</a>.</p>
	</div>
</main>
<?php
get_footer();
