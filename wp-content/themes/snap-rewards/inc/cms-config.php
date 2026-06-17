<?php
/**
 * Sensa CMS — per-site config for Snap Rewards.
 *
 * Declares the editable homepage marketing copy + images. The Sensa CMS plugin reads this via the
 * `sensa_cms_config` filter; templates/partials reference the fields through {{T:key}} / {{I:key}}
 * tokens, which snap_render() swaps for sc_text()/sc_img() output.
 *
 * Defaults below are the exact captured original copy, so clearing a field in wp-admin restores it,
 * and (via the guarded fallbacks at the bottom) the page still renders its original content even if
 * the plugin is ever deactivated.
 *
 * @package snap-rewards
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

function snap_cms_config_array() {
	static $c = null;
	if ( null !== $c ) {
		return $c;
	}
	$home_text = array(
		array( 'k' => 'home_hero_title', 'l' => 'Hero heading (HTML allowed)', 'd' => 'Capture Your Shoppers With <span>Receipt Rewards</span>' ),
		array( 'k' => 'home_hero_sub', 'l' => 'Hero subheading', 'd' => 'Snap Rewards is the ultimate solution for Shopify stores looking to boost customer engagement and build loyalty.', 'ta' => 1 ),
		array( 'k' => 'home_hero_btn', 'l' => 'Hero button label', 'd' => 'Visit App Store' ),
		array( 'k' => 'home_how_title', 'l' => 'How it works — heading', 'd' => 'How it works ' ),
		array( 'k' => 'home_how_sub', 'l' => 'How it works — intro', 'd' => 'Install the Shopify app, configure your receipt upload validation rules, customize your emails and landing page and launch your campaign!', 'ta' => 1 ),
		array( 'k' => 'home_step1_title', 'l' => 'Step 1 — title (HTML allowed)', 'd' => 'Campaign<br>Set up' ),
		array( 'k' => 'home_step1_text', 'l' => 'Step 1 — text', 'd' => 'Set up your product campaigns and voucher rewards simply and easily within the Shopify store.', 'ta' => 1 ),
		array( 'k' => 'home_step2_title', 'l' => 'Step 2 — title', 'd' => 'Configure your Product Matching' ),
		array( 'k' => 'home_step2_text', 'l' => 'Step 2 — text', 'd' => 'Set up your rules for automated product matching using the inbuilt receipt OCR and our customised matching engine.', 'ta' => 1 ),
		array( 'k' => 'home_step3_title', 'l' => 'Step 3 — title (HTML allowed)', 'd' => 'Launch Your<br />Campaign' ),
		array( 'k' => 'home_step3_text', 'l' => 'Step 3 — text', 'd' => 'Launch and promote your campaign online and in-store with QR codes printed straight on your product labels.', 'ta' => 1 ),
		array( 'k' => 'home_choose_title', 'l' => 'Add-to-store — heading', 'd' => 'Add Snap Rewards to your Shopify store with just a few clicks', 'ta' => 1 ),
		array( 'k' => 'home_choose_p1', 'l' => 'Add-to-store — paragraph 1', 'd' => 'Setting up and testing your rewards campaign has never been easier. Simply define your reward criteria, and our AI scans uploaded receipts to match purchases against specific products or categories. Configure your product validation rules to ensure that only eligible purchases qualify for rewards.', 'ta' => 1 ),
		array( 'k' => 'home_choose_p2', 'l' => 'Add-to-store — paragraph 2', 'd' => 'Customise your campaign in minutes with branded emails and an eye-catching landing page. Our intuitive editor lets you craft a seamless experience that captures customer emails, drives engagement, and boosts repeat purchases. Launch in a few clicks and watch your customers effortlessly turn receipts into rewards!', 'ta' => 1 ),
		array( 'k' => 'home_features_title', 'l' => 'App features — heading', 'd' => 'Our App Features' ),
		array( 'k' => 'home_feat1_title', 'l' => 'Feature 1 — title', 'd' => 'AI Receipt OCR' ),
		array( 'k' => 'home_feat1_text', 'l' => 'Feature 1 — text', 'd' => 'Snap Rewards uses the latest AI technology to capture and intelligently understand any POS receipt worldwide!', 'ta' => 1 ),
		array( 'k' => 'home_feat2_title', 'l' => 'Feature 2 — title', 'd' => 'Validation Engine' ),
		array( 'k' => 'home_feat2_text', 'l' => 'Feature 2 — text', 'd' => 'Our validation engine gives you full control to match products and define the qualification rules for your campaign.', 'ta' => 1 ),
		array( 'k' => 'home_feat3_title', 'l' => 'Feature 3 — title', 'd' => 'Email Templates' ),
		array( 'k' => 'home_feat3_text', 'l' => 'Feature 3 — text', 'd' => 'Customise your branding with our template builder and even set your mail server to send out vouchers directly!', 'ta' => 1 ),
		array( 'k' => 'home_video_title', 'l' => 'Video section — heading', 'd' => 'Takes customer engagement to the next level!', 'ta' => 1 ),
		array( 'k' => 'home_video_p', 'l' => 'Video section — intro', 'd' => 'Snap Rewards lets you run fun and exciting receipt campaigns, letting you capture and engage with REAL in-store and offline customers! ', 'ta' => 1 ),
		array( 'k' => 'home_news_title', 'l' => 'Newsletter — heading', 'd' => 'Subscribe to our Newsletter' ),
		array( 'k' => 'home_news_sub', 'l' => 'Newsletter — intro', 'd' => 'Keep up to date with our monthly newsletter and learn more about how our customers are using Snap Rewards to stay creative with their loyalty campaigns.', 'ta' => 1 ),
		array( 'k' => 'home_pricing_title', 'l' => 'Pricing — heading', 'd' => 'Our Pricing Plans' ),
		array( 'k' => 'home_pricing_sub', 'l' => 'Pricing — intro', 'd' => 'Take a look at our flexible pricing plans to see how you can test, configure and launch your campaigns at your own pace.', 'ta' => 1 ),
		array( 'k' => 'home_testi_title', 'l' => 'Testimonials — heading (HTML allowed)', 'd' => 'What Our<br/>Customers Say', 'ta' => 1 ),
		array( 'k' => 'home_testi_sub', 'l' => 'Testimonials — intro', 'd' => 'Take a look at what our existing Shopify store owners have to say about Snap Rewards. ', 'ta' => 1 ),
		array( 'k' => 'home_contact_title', 'l' => 'Get in touch — heading', 'd' => 'Get In Touch' ),
		array( 'k' => 'home_contact_sub', 'l' => 'Get in touch — intro', 'd' => 'Get in touch to learn more about Snap Rewards and see how we can help you on your customer loyalty journey. The ultimate Shopify app that takes customer engagement to the next level! 🚀', 'ta' => 1 ),
	);
	$home_img = array(
		array( 'k' => 'home_hero_banner', 'l' => 'Hero banner image', 'd' => '/wp-content/uploads/2025/02/home-banner.png' ),
		array( 'k' => 'home_choose_mobile', 'l' => 'Add-to-store phone image', 'd' => '/wp-content/uploads/2025/02/Snap-Rewards.png' ),
		array( 'k' => 'home_features_img', 'l' => 'App features image', 'd' => '/wp-content/uploads/2025/02/App-Features-copy-01.png' ),
		array( 'k' => 'home_contact_illustration', 'l' => 'Get in touch illustration', 'd' => '/wp-content/uploads/2025/02/illustration.png' ),
	);
	$c = array(
		'bespoke_slugs' => array( 'home' ),
		'text'          => array(
			'groups'        => array(
				'home' => array( 'label' => 'Homepage', 'fields' => $home_text ),
			),
			'slug_groups'   => array( 'home' => 'home' ),
			'slug_prefixes' => array(),
		),
		'images'        => array(
			'groups'      => array(
				'home' => array( 'label' => 'Homepage', 'fields' => $home_img ),
			),
			'slug_groups' => array( 'home' => 'home' ),
		),
		'galleries'     => array( 'video' => array(), 'photo' => array() ),
	);
	return $c;
}
add_filter( 'sensa_cms_config', 'snap_cms_config_array' );

/* ---------------------------------------------------------------------------
 * Defaults map (key => default) so the theme can render tokens even when the
 * Sensa CMS plugin isn't active. The plugin loads BEFORE theme functions, so when
 * it IS active it defines sc_text()/sc_img() first and these fallbacks are skipped.
 * ------------------------------------------------------------------------- */
function snap_cms_defaults( $kind ) {
	static $cache = array();
	if ( isset( $cache[ $kind ] ) ) {
		return $cache[ $kind ];
	}
	$out = array();
	$cfg = snap_cms_config_array();
	$groups = ( 'images' === $kind ) ? $cfg['images']['groups'] : $cfg['text']['groups'];
	foreach ( $groups as $g ) {
		foreach ( $g['fields'] as $fl ) {
			$out[ $fl['k'] ] = $fl['d'];
		}
	}
	$cache[ $kind ] = $out;
	return $out;
}

if ( ! function_exists( 'sc_text' ) ) {
	function sc_text( $key ) {
		$d = snap_cms_defaults( 'text' );
		return isset( $d[ $key ] ) ? $d[ $key ] : '';
	}
}
if ( ! function_exists( 'sc_img' ) ) {
	function sc_img( $key ) {
		$d = snap_cms_defaults( 'images' );
		return isset( $d[ $key ] ) ? $d[ $key ] : '';
	}
}
