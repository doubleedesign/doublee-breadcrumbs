<?php
/**
 * Plugin Name:       Breadcrumbs
 * Description:       Allows developers to easily add breadcrumb trails to theme templates.
 * Author:            Leesa Ward
 * Plugin URI:        https://github.com/doubleedesign/breadcrumbs
 * Version:           1.1
 * Text Domain:       breadcrumbs
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 */
define( 'BREADCRUMBS_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-breadcrumbs-activator.php
 */
function activate_breadcrumbs() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-breadcrumbs-activator.php';
	Breadcrumbs_Activator::activate();
}
register_activation_hook( __FILE__, 'activate_breadcrumbs' );

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-breadcrumbs-deactivator.php
 */
function deactivate_breadcrumbs() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-breadcrumbs-deactivator.php';
	Breadcrumbs_Deactivator::deactivate();
}
register_deactivation_hook( __FILE__, 'deactivate_breadcrumbs' );

/**
 * The core plugin class that is used to define admin-specific and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-breadcrumbs.php';

/**
 * Begin execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_breadcrumbs() {
	$plugin = new Breadcrumbs();
	$plugin->run();
}
run_breadcrumbs();
