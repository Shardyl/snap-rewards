<?php
/**
 * Single blog post — renders the captured post body for the matching slug.
 * @package snap-rewards
 */
get_header();
$slug = get_post_field( 'post_name', get_queried_object_id() );
snap_render( $slug );
get_footer();
