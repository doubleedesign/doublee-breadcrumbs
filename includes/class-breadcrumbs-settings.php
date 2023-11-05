<?php
/**
 * Plugin settings.
 *
 * @since      1.0.0
 * @package    Breadcrumbs
 * @subpackage Breadcrumbs/admin
 */
class Breadcrumbs_Settings {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Initialize the class
	 * @param $plugin_name
	 * @param $version
	 *
	 * @since    1.0.0
	 */
	public function __construct($plugin_name, $version) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}


	/**
	 * Create the breadcrumbs setting in the wp_options table
     * @wp-hook
	 */
	public function create_settings_in_db() {
		add_option('breadcrumbs_settings', '');
	}


	/**
	 * Filterable list of all the post types to add settings for
	 * @since    1.1.0
     * @wp-hook
	 */
	public function get_breadcrumbable_post_types() {

		// Get all post types in the site
		$post_types = get_post_types();

		// Specify the ones we know we won't be adding breadcrumbs to,
        // like ACF groups, nav menu items, block editor stuff, and some WooCommerce stuff.
		$exclude = array(
            'attachment',
            'revision',
            'wp_navigation',
            'nav_menu_item',
            'wp_global_styles',
            'custom_css',
            'customize_changeset',
            'wp_template',
            'wp_template_part',
            'oembed_cache',
            'user_request',
            'wp_block',
            'acf-field',
            'acf-field-group',
            'acf-post-type',
            'acf-taxonomy',
            'acf-ui-options-page',
            'product_variation',
            'shop_order',
            'shop_order_refund',
            'shop_order_placehold',
            'shop_coupon',
            'event_ticket',
            'event_ticket_email',
            'scheduled-action',
        );

		// Return list with the opportunity for themes to alter it with this filter
		return apply_filters('breadcrumbs_filter_post_types', array_diff($post_types, $exclude));
	}

	/**
	 * Filterable list of all the the taxonomies to add settings for
	 * @since    1.0.0
     * @wp-hook
	 */
	public function get_breadcrumbable_taxonomies() {

		// Get all taxonomies in the site
		$taxonomies = get_taxonomies();

		// Unset the ones we know we won't be adding breadcrumbs to
		unset($taxonomies['nav_menu']);
		unset($taxonomies['link_category']);

		// Return list with the opportunity for themes to alter it with this filter
		return apply_filters('breadcrumbs_filter_taxonomies', $taxonomies);
	}

}
