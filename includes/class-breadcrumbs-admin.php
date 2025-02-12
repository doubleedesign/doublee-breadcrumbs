<?php
namespace Doubleedesign\Breadcrumbs;
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @since      1.0.0
 */
class Breadcrumbs_Admin {

	public function __construct() {
		add_action('admin_enqueue_scripts', array($this, 'enqueue_styles'));
		add_action('admin_menu', array($this, 'add_options_screen'));
		add_action('add_meta_boxes', array($this, 'register_post_meta_boxes'));
		add_action('save_post', array($this, 'save_post_breadcrumbs_metadata'));
	}

	/**
	 * Register the stylesheet for the admin area.
	 * @since    1.0.0
	 */
	public function enqueue_styles(): void {
		wp_enqueue_style(Breadcrumbs::get_plugin_name(), '/wp-content/plugins/doublee-breadcrumbs/assets/breadcrumbs-admin.css', array(), Breadcrumbs::get_version());
	}


	/**
	 * Add the options screen to the CMS
	 * @since    1.0.0
	 */
	public function add_options_screen(): void {
		add_submenu_page(
			'options-general.php',
			'Breadcrumbs',
			'Breadcrumbs',
			'manage_options',
			'breadcrumbs',
			array($this, 'populate_options_screen')
		);
	}


	/**
	 * Callback to populate the options screen in the CMS
	 * @since    1.0.0
	 */
	public function populate_options_screen(): void {
		require_once 'partials/breadcrumbs-admin-display.php';
	}


	/**
	 * Save the settings in the admin options screen
	 * @since    1.0.0
	 */
	protected function save(): void {

		// Get the array of submitted values
		$submitted = $_GET;

		// Remove the 'page' param - that's a hidden field there to make sure we redirect back to ?page=breadcrumbs
		unset($submitted['page']);

		// Save them
		update_option('breadcrumbs_settings', $submitted);
	}


	/**
	 * Register meta boxes for post-level settings
	 * @since    1.0.0
	 */
	public function register_post_meta_boxes(): void {

		// Loop through the enabled post types and add the metabox to each
		$enabled_post_types = Breadcrumbs_Settings::get_breadcrumbable_post_types();
		foreach($enabled_post_types as $post_type) {
			add_meta_box('breadcrumb-settings', __('Breadcrumb settings', 'breadrcumbs'), array($this, 'populate_breadcrumbs_metabox'), $post_type, 'side', 'core');
		}
	}


	/**
	 * Callback to populate the post-level metabox
	 * @since    1.0.0
	 */
	public function populate_breadcrumbs_metabox(): void {

		$default_title = get_the_title();
		$title_override = get_post_meta(get_the_id(), 'breadcrumb_title_override', true);

		// Output field for title override
		echo '<p class="post-attributes-label-wrapper">';
			echo '<label for="breadcrumb-title-override" class="post-attributes-label">';
				_e('Title override', 'breadcrumbs');
			echo '</label>';
		echo '</p>';
		echo '<input type="text" id="breadcrumb-title-override" name="breadcrumb_title_override" value="'.esc_attr($title_override).'" placeholder="'.$default_title.'"/>';

		// Add nonce to check when saving
		wp_nonce_field('save_post_breadcrumbs_metadata', 'breadcrumb_title_override_nonce');
	}


	/**
	 * Save the data in the post-level metabox
	 * @param int $post_id
	 *@since    1.0.0
	 */
	public function save_post_breadcrumbs_metadata(int $post_id): void {

		if(!isset($_POST['breadcrumb_title_override_nonce']) || !wp_verify_nonce($_POST['breadcrumb_title_override_nonce'], 'save_post_breadcrumbs_metadata') || (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)) {
			return;
		}

		if (isset($_POST['breadcrumb_title_override'])) {
			$title_override = sanitize_text_field($_POST['breadcrumb_title_override']);
			update_post_meta($post_id, 'breadcrumb_title_override', $title_override);
		}
	}
}
