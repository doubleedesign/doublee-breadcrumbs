<?php
namespace Doubleedesign\Breadcrumbs;
/**
 * The core plugin class.
 *
 * This is used to define admin-specific and public-facing site hooks.
 * Also maintains the unique identifier of this plugin as well as the current version.
 *
 * @since      1.0.0
 * @package    Breadcrumbs
 */
class Breadcrumbs {
	public static Breadcrumbs_Public $instance;

	/**
	 * Set up the core functionality of the plugin in the constructor
	 * by loading the modular classes of functionality.
	 *
	 * @since    1.2.0
	 */
	public function __construct() {
		$this->load_classes();
	}


	/**
	 * Load the required dependencies for this plugin.
	 * Each time we create a class file, we need to add it and initialise it here.
	 *
	 * @return   void
	 * @since    1.2.0
	 * @access   private
	 */
	private function load_classes(): void {

		// The class responsible for plugin-wide settings; parent class for the Admin and Public classes
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-breadcrumbs-settings.php';
		new Breadcrumbs_Settings();

		// The class responsible for defining actions that occur in the admin area
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-breadcrumbs-admin.php';
		new Breadcrumbs_Admin();

		// The class responsible for defining actions that occur on the front-end
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-breadcrumbs-public.php';
		self::$instance = new Breadcrumbs_Public();
	}


	/**
	 * Run functions on plugin activation.
	 * Things we only want to run once - when the plugin is activated
	 * (as opposed to every time the admin initialises, for example)
	 * @return void
	 */
	public static function activate(): void {
		add_option('breadcrumbs_settings');
	}


	/**
	 * Run functions on plugin deactivation.
	 * NOTE: This can be a destructive operation!
	 * Basically anything done by the plugin should be reversed or adjusted to work with built-in WordPress functionality
	 * if the plugin is deactivated. However, it is important to note that often developers/administrators will
	 * deactivate a plugin temporarily to troubleshoot something and then reactivate it, so we should not do a full cleanup
	 * (such as deleting data) by default.
	 *
	 * Consider carefully whether deactivation or uninstallation is the better place to remove/undo something.
	 *
	 * @return void
	 */
	public static function deactivate(): void {
	}


	/**
	 * Run functions on plugin uninstallation
	 * NOTE: This is for VERY destructive operations!
	 * There are some things that it is best practice to do on uninstallation,
	 * for example custom database tables created by the plugin (if we had any)
	 * should be deleted when the plugin is uninstalled from the site.
	 * Think of this as "not using it anymore" levels of cleanup.
	 *
	 * Consider carefully whether deactivation or uninstallation is the better place to remove/undo something.
	 *
	 * @return void
	 */
	public static function uninstall(): void {
		delete_metadata('post', 0, 'breadcrumb_title_override', null, true); // TODO: This isn't working for Pages/CPTs
		delete_option('breadcrumbs_settings');
	}


	/**
	 * Utility function to get the plugin name.
	 *
	 * @since     1.2.0
	 * @return    string    The name of the plugin.
	 */
	public static function get_plugin_name(): string {
		$plugin_data = get_plugin_data(BREADCRUMBS_PLUGIN_PATH . 'breadcrumbs.php');

		return $plugin_data['Name'];
	}


	/**
	 * Utility function to get the plugin version number.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public static function get_version(): string {
		return BREADCRUMBS_VERSION;
	}

}
