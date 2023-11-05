<?php
/**
 * Plugin Name:       Breadcrumbs
 * Description:       Allows developers to easily add breadcrumb trails to theme templates.
 * Author:            Leesa Ward
 * Plugin URI:        https://github.com/doubleedesign/breadcrumbs
 * Version:           1.2.0
 * Text Domain:       breadcrumbs
 */

// If this file is called directly, abort.
if(!defined('WPINC')) {
	die;
}

/**
 * Current plugin version.
 */
const BREADCRUMBS_VERSION = '1.2.0';

/**
 * Path of plugin root folder
 */
define('BREADCRUMBS_PLUGIN_PATH', plugin_dir_path(__FILE__));

/**
 * Load and initialise the core plugin class
 */
require plugin_dir_path(__FILE__) . 'includes/class-breadcrumbs.php';
new Breadcrumbs();


/**
 * Functions to run on plugin activation
 * @return void
 */
function activate_breadcrumbs(): void {
	Breadcrumbs::activate();
}
register_activation_hook(__FILE__, 'activate_breadcrumbs');


/**
 * Functions to run on plugin deactivation
 * @return void
 */
function deactivate_breadcrumbs(): void {
	Breadcrumbs::deactivate();
}
register_deactivation_hook(__FILE__, 'deactivate_breadcrumbs');


/**
 * Functions to run on plugin uninstallation
 * @return void
 */
function uninstall_breadcrumbs(): void {
	Breadcrumbs::uninstall();
}
register_uninstall_hook(__FILE__, 'uninstall_breadcrumbs');
