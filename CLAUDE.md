# Snap Rewards — WordPress site (migration replica)

Faithful migration of the live **snap-rewards.com** site (Three Digital Software Trading LLC,
Dubai — Shopify receipt-rewards / loyalty product) onto WP Engine. Built per the
`website-management` skill **§9d "Migrating an EXISTING site"** playbook.

## Infrastructure
- **WP Engine install:** `snaprewards` — build URL https://snaprewards.wpenginepowered.com
- **Webroot:** `/sites/snaprewards` · themes at `/sites/snaprewards/wp-content/themes/snap-rewards`
- **SSH/WP-CLI:** `ssh snaprewards@snaprewards.ssh.wpengine.net` (shared Sensa account key ①)
- **Repo:** github.com/Shardyl/snap-rewards · branch `main`
- **Deploy:** push to `main` → GitHub Action rsyncs the theme over the SSH gateway + flushes
  cache. Needs repo secret **`WPE_SSH_KEY_B64`** (base64 of the shared CI deploy key
  `filmspoke-brand/_deploy/ci_deploy_key`; key ② already on the WPE account). Manual fallback:
  `tar czf - snap-rewards | ssh <gateway> "cat > .../_snap.tgz"` then untar (rsync isn't on Windows).

## Theme architecture (`wp-content/themes/snap-rewards`)
- Bespoke replica of the original custom theme. `header.php` / `footer.php` reproduce the masthead
  + footer once, using `wp_nav_menu` (active-state classes reproduce natively). The only per-page
  difference in the original header/footer was the active-menu highlight.
- **Page bodies are captured `<main>` partials**, byte-faithful, in `inc/content/{slug}.html` with
  all URLs rewritten to root-relative. `snap_render($slug)` echoes them. Templates
  (`front-page.php`, `page-{slug}.php`, `single.php`) are thin wrappers around `snap_render`.
- CSS: the original WP-Fastest-Cache bundles, copied to `/css/*.css` (named by stylesheet handle)
  and enqueued in order. JS: the theme's own `/js/*` in original order, jQuery from core.
- **Contact form:** the original CF7 markup is replaced by the `<!--SNAP_CF7-->` token; `snap_render`
  swaps it for the live CF7 shortcode (form id stored in option `snap_cf7_form_id`).
- **Build-time noindex:** `SNAP_BUILD_NOINDEX` constant (functions.php) emits `noindex` meta +
  `X-Robots-Tag` regardless of `blog_public`. **Flip to `false` only at operator-approved go-live.**

## URLs (13 = 9 pages + 4 posts), all at identical slugs to the original
Pages: `/` `/partnerships/` `/contact/` `/blog/` `/privacy-policy/` `/privacy-note/`
`/terms-of-website-use/` `/snap-rewards-affiliate-program/` `/install/` (Shopify-app redirect).
Posts (root-level, `/%postname%/`): the 4 loyalty-news articles.
Menus: header (`primary`) + footer (`footer`), 6 items: Home · Pricing (`/#pricing`) ·
Loyalty news · Partnerships · Contact · Support (→ existing docs KB).

## Status (2026-06-16)
- All 13 URLs HTTP 200, header/footer/assets render, **0 title + 0 description mismatches** vs the
  original (Rank Math meta set verbatim from the scrape). Contact form live. Site **noindexed**.
- **Open / operator-gated:** SMTP app password for the contact form (WP Mail SMTP); optional
  reCAPTCHA keys; `/docs` Support target at go-live (currently → snap-rewards.com/docs); favicon;
  go-live = flip `SNAP_BUILD_NOINDEX`, DNS to WP Engine, submit sitemap.
- Scrape source-of-truth + scripts: `_scrape/` (gitignored).
