<?php
/**
 * The core plugin class.
 *
 * This is used to define admin-specific and public-facing site hooks.
 * Also maintains the unique identifier of this plugin as well as the current version.
 *
 * @since      1.0.0
 * @package    Breadcrumbs
 * @subpackage Breadcrumbs/includes
 */
class Breadcrumbs {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Breadcrumbs_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

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
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->version     = defined('BREADCRUMBS_VERSION') ? BREADCRUMBS_VERSION : '1.0.0';
		$this->plugin_name = 'breadcrumbs';

		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin,
	 * and create an instance of the loader which will be used to register the hooks with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		// The class responsible for orchestrating the core plugin hooks and filters
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-breadcrumbs-loader.php';

		// The class responsible for plugin-wide settings; parent class for the Admin and Public classes
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-breadcrumbs-settings.php';

		// The class responsible for defining actions that occur in the admin area
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-breadcrumbs-admin.php';

		// The class responsible for defining actions that occur on the front-end
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-breadcrumbs-public.php';

		// Initialise the loader
		$this->loader = new Breadcrumbs_Loader();
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_settings = new Breadcrumbs_Settings($this->plugin_name, $this->version);
		$this->loader->add_action('admin_init', $plugin_settings, 'create_settings_in_db');

		$plugin_admin = new Breadcrumbs_Admin($this->plugin_name, $this->version);
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
		$this->loader->add_action('admin_menu', $plugin_admin, 'add_options_screen');
		$this->loader->add_action('add_meta_boxes', $plugin_admin, 'register_post_meta_boxes');
		$this->loader->add_action('save_post', $plugin_admin, 'save_post_breadcrumbs_metadata');
	}

	/**
	 * Register all of the hooks related to the front-end functionality
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Breadcrumbs_Public($this->plugin_name, $this->version);
		$this->loader->add_action('doublee_breadcrumbs', $plugin_public, 'set_breadcrumbs', 10);
		$this->loader->add_action('doublee_breadcrumbs', $plugin_public, 'display_breadcrumbs', 10);
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Breadcrumbs_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

}
