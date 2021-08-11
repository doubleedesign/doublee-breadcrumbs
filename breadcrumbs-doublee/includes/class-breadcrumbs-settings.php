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
	 */
	public function create_settings_in_db() {
		add_option('breadcrumbs_settings', '');
	}


	/**
	 * Filterable list of all the post types to add settings for
	 * @since    1.0.0
	 */
	public function get_breadcrumbable_post_types() {

		// Get all post types in the site
		$post_types = get_post_types();

		// Unset the ones we know we won't be adding breadcrumbs to, like ACF groups and nav menu items.
		unset($post_types['attachment']);
		unset($post_types['revision']);
		unset($post_types['nav_menu_item']);
		unset($post_types['custom_css']);
		unset($post_types['customize_changeset']);
		unset($post_types['oembed_cache']);
		unset($post_types['user_request']);
		unset($post_types['wp_block']);
		unset($post_types['acf-field']);
		unset($post_types['acf-field-group']);

		// Return list with the opportunity for themes to alter it with this filter
		return apply_filters('breadcrumbs_filter_post_types', $post_types);
	}

	/**
	 * Filterable list of all the the taxonomies to add settings for
	 * @since    1.0.0
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