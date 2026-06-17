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
	$pt_text = array(
		array( 'k' => 'pt_hero_title', 'l' => 'Hero heading (HTML allowed)', 'd' => 'The Only Loyalty App That <span>Rewards Receipts</span>' ),
		array( 'k' => 'pt_hero_sub', 'l' => 'Hero subheading', 'd' => 'Reward customers with points, discounts, and prize draws—all through receipt uploads.', 'ta' => 1 ),
		array( 'k' => 'pt_hero_btn1', 'l' => 'Hero button 1', 'd' => 'JOIN NOW' ),
		array( 'k' => 'pt_hero_btn2', 'l' => 'Hero button 2', 'd' => 'Start Free' ),
		array( 'k' => 'pt_why_title', 'l' => 'Why Agencies — heading', 'd' => 'Why Agencies Love Snap Rewards ' ),
		array( 'k' => 'pt_why1_title', 'l' => 'Why 1 — title', 'd' => 'Unmatched Support' ),
		array( 'k' => 'pt_why1_text', 'l' => 'Why 1 — text', 'd' => 'A dedicated success team that partners with you through onboarding, launch, and ongoing growth.', 'ta' => 1 ),
		array( 'k' => 'pt_why2_title', 'l' => 'Why 2 — title', 'd' => 'Simple Migration' ),
		array( 'k' => 'pt_why2_text', 'l' => 'Why 2 — text', 'd' => 'Move clients from other platforms in days, not weeks. Fast, secure migration with Klaviyo integration built-in.', 'ta' => 1 ),
		array( 'k' => 'pt_why3_title', 'l' => 'Why 3 — title', 'd' => 'Responsive Development' ),
		array( 'k' => 'pt_why3_text', 'l' => 'Why 3 — text', 'd' => 'We don’t just build features—we build what you need. Our roadmap evolves from agency &amp; client feedback.', 'ta' => 1 ),
		array( 'k' => 'pt_why4_title', 'l' => 'Why 4 — title', 'd' => 'Customer-Centric Care' ),
		array( 'k' => 'pt_why4_text', 'l' => 'Why 4 — text', 'd' => 'Clear, proactive communication and rapid resolutions.', 'ta' => 1 ),
		array( 'k' => 'pt_diff_title', 'l' => 'What Makes Different — heading', 'd' => 'What Makes Snap Rewards Different ' ),
		array( 'k' => 'pt_diff1_title', 'l' => 'Diff 1 — title', 'd' => 'Receipt Rewards' ),
		array( 'k' => 'pt_diff1_text', 'l' => 'Diff 1 — text', 'd' => 'Capture offline &amp; in-store customers with receipt uploads.', 'ta' => 1 ),
		array( 'k' => 'pt_diff2_title', 'l' => 'Diff 2 — title', 'd' => 'Multiple Reward Types' ),
		array( 'k' => 'pt_diff2_text', 'l' => 'Diff 2 — text', 'd' => 'Offer points, instant discounts, and prize draws together.', 'ta' => 1 ),
		array( 'k' => 'pt_diff3_title', 'l' => 'Diff 3 — title', 'd' => 'Klaviyo Integration' ),
		array( 'k' => 'pt_diff3_text', 'l' => 'Diff 3 — text', 'd' => 'Build automated flows, segment by loyalty data, and personalize  campaigns.', 'ta' => 1 ),
		array( 'k' => 'pt_diff4_title', 'l' => 'Diff 4 — title', 'd' => 'Agency-Ready Platform' ),
		array( 'k' => 'pt_diff4_text', 'l' => 'Diff 4 — text', 'd' => 'Manage multiple clients with speed and confidence.', 'ta' => 1 ),
		array( 'k' => 'pt_built_title', 'l' => 'Built for Agencies — heading', 'd' => 'Built for Agencies' ),
		array( 'k' => 'pt_built_lead', 'l' => 'Built for Agencies — lead (HTML)', 'd' => 'Snap Rewards is more than an app — <b>it’s a partnership</b>.', 'ta' => 1 ),
		array( 'k' => 'pt_built_li1', 'l' => 'Built — point 1', 'd' => 'Support tailored for agencies running multiple clients.', 'ta' => 1 ),
		array( 'k' => 'pt_built_li2', 'l' => 'Built — point 2', 'd' => 'Migration tools that save hours of manual setup.', 'ta' => 1 ),
		array( 'k' => 'pt_built_li3', 'l' => 'Built — point 3', 'd' => 'A development team that listens and delivers on feature requests.', 'ta' => 1 ),
		array( 'k' => 'pt_built_li4', 'l' => 'Built — point 4', 'd' => 'Flexible, scalable architecture designed for boutique brands or global enterprises.', 'ta' => 1 ),
		array( 'k' => 'pt_migration_title', 'l' => 'Migration — heading', 'd' => 'Seamless Migration in 5 Steps' ),
		array( 'k' => 'pt_step1_title', 'l' => 'Step 1 — title', 'd' => 'Plan' ),
		array( 'k' => 'pt_step1_text', 'l' => 'Step 1 — text', 'd' => 'Map out current loyalty program &amp; integrations.', 'ta' => 1 ),
		array( 'k' => 'pt_step2_title', 'l' => 'Step 2 — title', 'd' => 'Prepare' ),
		array( 'k' => 'pt_step2_text', 'l' => 'Step 2 — text', 'd' => 'Export customer and purchase data.', 'ta' => 1 ),
		array( 'k' => 'pt_step3_title', 'l' => 'Step 3 — title', 'd' => 'Migrate' ),
		array( 'k' => 'pt_step3_text', 'l' => 'Step 3 — text', 'd' => 'Import into Snap Rewards, set up tiers &amp; rules.', 'ta' => 1 ),
		array( 'k' => 'pt_step4_title', 'l' => 'Step 4 — title', 'd' => 'Test &amp; Launch' ),
		array( 'k' => 'pt_step4_text', 'l' => 'Step 4 — text', 'd' => 'Validate flows, launch smoothly with client comms.', 'ta' => 1 ),
		array( 'k' => 'pt_step5_title', 'l' => 'Step 5 — title', 'd' => 'Optimize' ),
		array( 'k' => 'pt_step5_text', 'l' => 'Step 5 — text', 'd' => 'Continuous improvements, feature releases, and performance insights.', 'ta' => 1 ),
		array( 'k' => 'pt_partner_title', 'l' => 'Partner Program — heading', 'd' => 'Partner Program – Growing Together' ),
		array( 'k' => 'pt_partner_lead', 'l' => 'Partner Program — lead', 'd' => 'We believe loyalty should reward not just customers, but also our partners. That’s why we created the Snap Rewards Partner Program:', 'ta' => 1 ),
		array( 'k' => 'pt_partner1_title', 'l' => 'Partner 1 — title', 'd' => 'Exclusive Support' ),
		array( 'k' => 'pt_partner1_text', 'l' => 'Partner 1 — text', 'd' => 'Priority migration assistance and co-marketing opportunities.', 'ta' => 1 ),
		array( 'k' => 'pt_partner2_title', 'l' => 'Partner 2 — title', 'd' => 'Shared Success' ),
		array( 'k' => 'pt_partner2_text', 'l' => 'Partner 2 — text', 'd' => 'Collaborate with us on campaigns, product roadmaps, and joint     client wins.', 'ta' => 1 ),
		array( 'k' => 'pt_partner3_title', 'l' => 'Partner 3 — title', 'd' => 'Scalable Benefits' ),
		array( 'k' => 'pt_partner3_text', 'l' => 'Partner 3 — text', 'd' => 'The more you grow with us, the more resources, perks, and     rewards you unlock.', 'ta' => 1 ),
		array( 'k' => 'pt_partner_btn', 'l' => 'Partner Program — button', 'd' => 'JOIN NOW' ),
		array( 'k' => 'pt_results_title', 'l' => 'Results — heading', 'd' => 'Results That Speak' ),
		array( 'k' => 'pt_result1', 'l' => 'Result 1', 'd' => 'Agencies report 50% faster migration times vs. other loyalty apps.', 'ta' => 1 ),
		array( 'k' => 'pt_result2', 'l' => 'Result 2', 'd' => 'Clients see higher redemption rates with receipt rewards.', 'ta' => 1 ),
		array( 'k' => 'pt_result3', 'l' => 'Result 3', 'd' => 'New features ship monthly based on real user feedback.', 'ta' => 1 ),
		array( 'k' => 'pt_cta_title', 'l' => 'Contact CTA — heading', 'd' => 'Ready to transform loyalty into a growth engine?' ),
		array( 'k' => 'pt_cta_body', 'l' => 'Contact CTA — body', 'd' => 'Request a personalized demo. Launch your first receipt campaign in days. Delight customers, scale faster, and stay ahead of competitors.', 'ta' => 1 ),
	);
	$pt_img = array(
		array( 'k' => 'pt_hero_img1', 'l' => 'Hero image 1', 'd' => '/wp-content/uploads/2025/10/snap-rewards-hero-01-1.svg' ),
		array( 'k' => 'pt_hero_img2', 'l' => 'Hero image 2', 'd' => '/wp-content/uploads/2025/10/snap-rewards-hero-021.svg' ),
		array( 'k' => 'pt_built_img', 'l' => 'Built for Agencies image', 'd' => '/wp-content/uploads/2025/10/built-for-agencies-1.svg' ),
		array( 'k' => 'pt_migration_img', 'l' => 'Migration image', 'd' => '/wp-content/uploads/2025/10/seamless-migration-in-5-steps-1.svg' ),
		array( 'k' => 'pt_partner_img', 'l' => 'Partner Program image', 'd' => '/wp-content/uploads/2025/10/partner-program-–-growing-together.svg' ),
		array( 'k' => 'pt_cta_img', 'l' => 'Contact CTA image', 'd' => '/wp-content/uploads/2025/10/get-started-today-1.svg' ),
	);
	$af_text = array(
		array( 'k' => 'af_intro', 'l' => 'Intro', 'd' => 'Earn recurring revenue by referring Shopify merchants to Snap Rewards, the app that helps convert in-store customers into online buyers through receipt-based discount campaigns.', 'ta' => 1 ),
		array( 'k' => 'af_why_title', 'l' => 'Why Join — heading', 'd' => 'Why Join?' ),
		array( 'k' => 'af_why_li1', 'l' => 'Why Join — point 1', 'd' => '30% recurring commission on every paying merchant you refer', 'ta' => 1 ),
		array( 'k' => 'af_why_li2', 'l' => 'Why Join — point 2', 'd' => 'Earn for up to 12 months per referral', 'ta' => 1 ),
		array( 'k' => 'af_why_li3', 'l' => 'Why Join — point 3', 'd' => 'No cap on earnings', 'ta' => 1 ),
		array( 'k' => 'af_why_li4', 'l' => 'Why Join — point 4', 'd' => 'Payouts via bank transfer or PayPal', 'ta' => 1 ),
		array( 'k' => 'af_who_title', 'l' => 'Who Should Join — heading', 'd' => 'Who Should Join?' ),
		array( 'k' => 'af_who_lead', 'l' => 'Who Should Join — lead', 'd' => 'The affiliate program is ideal for:', 'ta' => 1 ),
		array( 'k' => 'af_who_li1', 'l' => 'Who — point 1', 'd' => 'Shopify developers and freelancers', 'ta' => 1 ),
		array( 'k' => 'af_who_li2', 'l' => 'Who — point 2', 'd' => 'YouTube creators, bloggers, and educators', 'ta' => 1 ),
		array( 'k' => 'af_who_li3', 'l' => 'Who — point 3', 'd' => 'Shopify app reviewers and tutorial creators', 'ta' => 1 ),
		array( 'k' => 'af_who_li4', 'l' => 'Who — point 4', 'd' => 'Agencies and consultants working with retail or omnichannel brands', 'ta' => 1 ),
		array( 'k' => 'af_who_li5', 'l' => 'Who — point 5', 'd' => 'Existing Snap Rewards users', 'ta' => 1 ),
		array( 'k' => 'af_how_title', 'l' => 'How It Works — heading', 'd' => 'How It Works' ),
		array( 'k' => 'af_how_li1', 'l' => 'How — step 1 (HTML)', 'd' => 'Email us at <a href="mailto:ben@snap-rewards.com">ben@snap-rewards.com</a> with a short introduction', 'ta' => 1 ),
		array( 'k' => 'af_how_li2', 'l' => 'How — step 2', 'd' => 'We will issue you a unique referral code or tracking link', 'ta' => 1 ),
		array( 'k' => 'af_how_li3', 'l' => 'How — step 3', 'd' => 'Share the link through your content, direct messages, or client work', 'ta' => 1 ),
		array( 'k' => 'af_how_li4', 'l' => 'How — step 4', 'd' => 'Get paid monthly as referred merchants install and subscribe', 'ta' => 1 ),
		array( 'k' => 'af_cookie_title', 'l' => 'Cookie Duration — heading', 'd' => 'Cookie Duration' ),
		array( 'k' => 'af_cookie_p', 'l' => 'Cookie Duration — text', 'd' => 'The referral cookie is valid for 30 days. Merchants must install the app within that period to count as a referral.', 'ta' => 1 ),
		array( 'k' => 'af_payouts_title', 'l' => 'Payouts — heading', 'd' => 'Payouts' ),
		array( 'k' => 'af_payouts_li1', 'l' => 'Payouts — point 1', 'd' => 'Paid monthly via bank transfer or PayPal', 'ta' => 1 ),
		array( 'k' => 'af_payouts_li2', 'l' => 'Payouts — point 2', 'd' => 'Minimum payout amount is $50', 'ta' => 1 ),
		array( 'k' => 'af_payouts_li3', 'l' => 'Payouts — point 3', 'd' => 'Payouts begin once referred merchants are billed', 'ta' => 1 ),
		array( 'k' => 'af_apply_title', 'l' => 'Apply Now — heading', 'd' => 'Apply Now' ),
		array( 'k' => 'af_apply_lead', 'l' => 'Apply Now — lead (HTML)', 'd' => 'Email <a href="mailto:ben@snap-rewards.com">ben@snap-rewards.com</a> to apply. Please include:', 'ta' => 1 ),
		array( 'k' => 'af_apply_li1', 'l' => 'Apply — point 1', 'd' => 'Your name or company', 'ta' => 1 ),
		array( 'k' => 'af_apply_li2', 'l' => 'Apply — point 2', 'd' => 'A brief summary of how you plan to promote Snap Rewards', 'ta' => 1 ),
		array( 'k' => 'af_apply_li3', 'l' => 'Apply — point 3', 'd' => 'Links to your content, portfolio, or relevant audience', 'ta' => 1 ),
		array( 'k' => 'af_apply_foot', 'l' => 'Apply Now — closing line', 'd' => 'We will respond within 1 to 2 business days with your referral code and onboarding instructions.', 'ta' => 1 ),
	);
	$c = array(
		'bespoke_slugs' => array( 'home', 'partnerships', 'snap-rewards-affiliate-program' ),
		'text'          => array(
			'groups'        => array(
				'home'         => array( 'label' => 'Homepage', 'fields' => $home_text ),
				'partnerships' => array( 'label' => 'Partnerships page', 'fields' => $pt_text ),
				'affiliate'    => array( 'label' => 'Affiliate program page', 'fields' => $af_text ),
			),
			'slug_groups'   => array(
				'home'                           => 'home',
				'partnerships'                   => 'partnerships',
				'snap-rewards-affiliate-program' => 'affiliate',
			),
			'slug_prefixes' => array(),
		),
		'images'        => array(
			'groups'      => array(
				'home'         => array( 'label' => 'Homepage', 'fields' => $home_img ),
				'partnerships' => array( 'label' => 'Partnerships page', 'fields' => $pt_img ),
			),
			'slug_groups' => array(
				'home'         => 'home',
				'partnerships' => 'partnerships',
			),
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
