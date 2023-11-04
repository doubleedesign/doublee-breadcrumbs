<?php
/**
 * Fired when the plugin is uninstalled.
 * // TODO: Multisite support
 * // TODO: Batch processing for sites with problematically huge numbers of posts
 *
 * @since      1.0.0
 * @package    Breadcrumbs
 */

// If uninstall not called from WordPress, then exit.
if (!defined( 'WP_UNINSTALL_PLUGIN')) {
	exit;
}

// Delete the breadcrumbs_settings from wp_options table
delete_option('breadcrumbs_settings');

// Delete the breadcrumb_title_override meta field from posts
// Note: Not using a meta query looking for the override here
// because we want to delete it from the postmeta table even if it's empty
$args = array(
	'posts_per_page' => -1,
);
$all_posts = new WP_Query($args);
$all_post_ids = wp_list_pluck($all_posts->posts, 'ID');

foreach($all_post_ids as $this_post_id) {
	delete_post_meta($this_post_id, 'breadcrumb_title_override');
}