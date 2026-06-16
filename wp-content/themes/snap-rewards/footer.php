<?php
/**
 * Footer — reproduces the original footer exactly.
 *
 * @package snap-rewards
 */
?>
	<footer class="footer-bg footer-p pt-60" style="background-image: url(/wp-content/themes/snap-rewards/img/bg/f-bg.png); background-position: center top; background-size: auto;background-repeat: no-repeat;">
		<div class="footer-top">
			<div class="container">
				<div class="row">
					<div class="col-lg-4">
						<div class="footer-widget mb-30">
							<div class="logo mb-15">
								<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="/wp-content/uploads/2025/02/SnapRewards-footer-logo.png" alt="logo"></a>
							</div>
							<div class="footer-text mb-20">
								<p>Snap Rewards is the ultimate solution for Shopify stores looking to boost customer engagement and build loyalty.</p>
							</div>
							<div class="footer-social">
								<a href="https://www.linkedin.com/company/snap-rewards-company"><i class="fab fa-linkedin"></i></a>
							</div>
						</div>
					</div>

					<div class="col-lg-4">
						<div class="footer-widget o-left-block mb-30">
							<div class="f-widget-title">
								<h5>Main Links</h5>
							</div>
							<div class="footer-link">
								<?php
								if ( has_nav_menu( 'footer' ) ) {
									wp_nav_menu( array(
										'theme_location' => 'footer',
										'container'      => false,
										'menu_id'        => 'menu-menu-2',
										'menu_class'     => 'primary-nav',
										'fallback_cb'    => 'snap_menu_fallback',
									) );
								} else {
									snap_menu_fallback();
								}
								?>
							</div>
						</div>
					</div>

					<div class="col-lg-4">
						<div class="footer-widget mb-30">
							<div class="f-widget-title">
								<h5>Contact Us</h5>
							</div>
							<div class="footer-link">
								<div class="f-contact">
									<ul>
										<li style="display:none;">
											<i class="icon dripicons-phone"></i>
											<span>1800-121-3637</span>
										</li>
										<li style="display:none;">
											<i class="icon dripicons-mail"></i>
											<span><a href="mailto:loyalty@snap-rewards.com">loyalty@snap-rewards.com</a></span>
										</li>
										<li>
											<i class="fal fa-map-marker-alt"></i>
											<p>Snap Rewards<br/>
Three Digital Software Trading LLC<br/>
P-316 The Binary<br/>
Business Bay<br/>
Dubai</p>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="copyright-wrap text-center">
			<div class="container">
				<div class="row">
					<div class="col-12">
						<div class="copyright-text">
							<p>All rights reserved &copy; Snap Rewards 2026  </p>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div style="padding:8px" class="copyright-wrap text-center">
			<a style="color:white" href="/privacy-policy">Privacy Policy</a><span style="color:white"> | </span>
			<a style="color:white" href="/privacy-note">Privacy Note</a><span style="color:white"> | </span>
			<a style="color:white" href="/terms-of-website-use">Terms of Website Use</a>
		</div>
	</footer>

</div><!-- #page -->
<?php wp_footer(); ?>
</body>
</html>
