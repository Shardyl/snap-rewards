# Sensa CMS

A lightweight, in-place **content editor** for Sensa-family WordPress sites. It lets a non-technical
client edit their site's **text, single images, hero videos/banners and galleries** from wp-admin —
**without ever touching the design** (design stays in the theme's code templates).

One plugin, installed on every site, **self-updating from this GitHub repo**. Each site declares *its own*
editable fields via a small `sensa_cms_config` filter in its theme — so the plugin is identical everywhere,
but every site exposes the right fields for its own pages.

## What it gives the client
- **Page Text** — edit headlines/paragraphs on bespoke (code-template) pages, right on the page's Edit screen.
- **Images** — swap any fixed image via a media picker; saves instantly.
- **Hero videos / banners** — paste a YouTube link, or set an optional banner image.
- **Galleries** — reorder, add/remove YouTube videos, upload images (the **Sensa CMS** admin menu).
- Auto-purges WP Engine cache on save, dark editor canvas, clean classic-editor panels.

## Install on a site
1. Install the plugin (any of):
   - wp-admin → Plugins → Add New → Upload the repo zip, **or**
   - WP-CLI: `wp plugin install https://github.com/sensa-productions/sensa-cms/archive/refs/heads/main.zip --activate`
2. Activate it. It will then **self-update** from this repo (wp-admin → Plugins shows updates).
3. In the site's **theme**, add a `sensa_cms_config` filter declaring the editable fields (see below),
   and call `sc_text('key')` / `sc_img('key')` / `sensa_videos($slug,$default)` / `sensa_photos($slug,$default)`
   in the templates where content should be editable.

> Private repo? Define `SENSA_CMS_GH_TOKEN` (a fine-grained read token) in `wp-config.php` so the updater can
> authenticate. **Public repo = no token needed** (simplest for client sites).

## Helpers (call these in theme templates)
- `sc_text( 'key' )` → returns the override or the coded default (echo it).
- `sc_img( 'key' )` → returns the override image URL or the default.
- `sensa_videos( 'gallery-slug', $default_ids )` / `sensa_photos( 'gallery-slug', $default_urls )`.
- `sensa_yt_id( $url_or_id )` → normalises a YouTube link to its 11-char ID.

## Per-site config (`sensa_cms_config` filter)
Add this in the theme (e.g. `theme/inc/cms-config.php`, required from `functions.php`). See `config-example.php`.

```php
add_filter( 'sensa_cms_config', function () {
    return array(
        // Page slugs whose content lives in code templates (forces the clean classic editor + panels).
        'bespoke_slugs' => array( 'home', 'about', 'contact' ),

        'text' => array(
            'groups' => array(
                'home' => array( 'label' => 'Homepage', 'fields' => array(
                    array( 'k' => 'home_hero_h1', 'l' => 'Hero headline', 'ta' => 1, 'd' => 'Welcome.' ),
                    array( 'k' => 'home_hero_sub', 'l' => 'Hero sub-line', 'ta' => 1, 'd' => 'We do great work.' ),
                ) ),
            ),
            'slug_groups'   => array( 'home' => 'home' ),   // page-slug → group (whole group shows on that page)
            'slug_prefixes' => array( 'contact' => 'contact_' ), // OR: page-slug → key-prefix (for shared groups)
        ),

        'images' => array(
            'groups' => array(
                'home' => array( 'label' => 'Homepage', 'fields' => array(
                    array( 'k' => 'home_hero_img', 'l' => 'Hero image', 'd' => get_stylesheet_directory_uri() . '/img/hero.jpg' ),
                ) ),
            ),
            'slug_groups' => array( 'home' => 'home' ),
        ),

        'galleries' => array(
            'video' => array( 'showreel' => array( 'label' => 'Showreel', 'default' => array( 'dQw4w9WgXcQ' ) ) ),
            'photo' => array( 'gallery'  => array( 'label' => 'Gallery',  'default' => array() ) ),
        ),
    );
} );
```

Field shape: `k` = storage key (unique), `l` = label shown to the editor, `d` = default value,
`ta` = 1 for a multi-line textarea (text fields only).

## Data storage
Overrides live in three site options: `sensa_text`, `sensa_images`, `sensa_content`. Defaults always come from
the config, so clearing a field restores the original. (These option names are kept stable so a site migrating
from the original per-site `sensa-content.php` mu-plugin keeps all its saved content.)

## Updating the plugin everywhere
Bump the `Version:` header in `sensa-cms.php`, commit, push to `main`. Within ~12h (or on a manual
"Check for updates") every site offers the new version in wp-admin → Plugins.
