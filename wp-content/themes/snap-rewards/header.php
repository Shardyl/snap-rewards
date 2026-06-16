<?php
/**
 * Header — reproduces the original masthead exactly.
 *
 * @package snap-rewards
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<link rel="icon" href="/wp-content/uploads/2025/02/cropped-SnapRewards-logo-1-32x32.png" sizes="32x32">
	<link rel="icon" href="/wp-content/uploads/2025/02/cropped-SnapRewards-logo-1-192x192.png" sizes="192x192">
	<link rel="apple-touch-icon" href="/wp-content/uploads/2025/02/cropped-SnapRewards-logo-1-180x180.png">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?> data-cmplz=1>
<?php if ( function_exists( 'wp_body_open' ) ) { wp_body_open(); } ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary">Skip to content</a>

	<header class="header-area site-header" id="masthead">
		<div id="header-sticky" class="menu-area">
			<div class="container">
				<div class="second-menu">
					<div class="row align-items-center">
						<div class="col-xl-2 col-lg-2">
							<div class="logo">
								<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img class="normal" src="/wp-content/uploads/2025/02/SnapRewards-logo.png" alt="logo"> <img class="sticky" src="/wp-content/uploads/2025/02/SnapRewards-footer-logo.png" alt="logo"></a>
							</div>
						</div>
						<div class="col-xl-8 col-lg-9">
							<div class="responsive"><i class="icon dripicons-align-right"></i></div>
							<div class="main-menu text-right text-xl-right">
								<nav id="mobile-menu">
									<?php
									if ( has_nav_menu( 'primary' ) ) {
										wp_nav_menu( array(
											'theme_location' => 'primary',
											'container'      => false,
											'menu_id'        => 'menu-menu-1',
											'menu_class'     => 'primary-nav',
											'fallback_cb'    => 'snap_menu_fallback',
										) );
									} else {
										snap_menu_fallback();
									}
									?>
								</nav>
							</div>
						</div>
						<div class="col-xl-2 text-right d-none d-xl-block">
							<div class="header-btn second-header-btn">
								<a href="https://apps.shopify.com/snap-rewards" class="btn">Install</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</header>

	<!-- main-area -->
