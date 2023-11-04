<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @since      1.0.0
 * @package    Breadcrumbs
 * @subpackage Breadcrumbs/admin
 */
class Breadcrumbs_Admin extends Breadcrumbs_Settings {

	/**
	 * Register the stylesheet for the admin area.
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/breadcrumbs-admin.css', array(), $this->version, 'all');

	}

	/**
	 * Register the JavaScript for the admin area.
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/breadcrumbs-admin.js', array('jquery'), $this->version, false);

	}


	/**
	 * Add the options screen to the CMS
	 * @since    1.0.0
	 */
	public function add_options_screen() {
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
	 * Populate the options screen in the CMS
	 * @since    1.0.0
	 */
	public function populate_options_screen() {
		require_once 'partials/breadcrumbs-admin-display.php';
	}

	/**
	 * Save the settings in the admin options screen
	 * @since    1.0.0
	 */
	protected function save() {

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
	public function register_post_meta_boxes() {

		// Loop through the enabled post types and add the metabox to each
		$enabled_post_types = $this->get_breadcrumbable_post_types();
		foreach($enabled_post_types as $post_type) {
			add_meta_box('breadcrumb-settings', __('Breadcrumb settings', 'breadrcumbs'), array($this, 'populate_breadcrumbs_metabox'), $post_type, 'side', 'core');
		}
	}

	/**
	 * Populate the post-level metabox
	 * @since    1.0.0
	 */
	public function populate_breadcrumbs_metabox() {

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
	 * @since    1.0.0
	 * @param int $post_id
	 */
	public function save_post_breadcrumbs_metadata($post_id) {

		if(!isset($_POST['breadcrumb_title_override_nonce']) || !wp_verify_nonce($_POST['breadcrumb_title_override_nonce'], 'save_post_breadcrumbs_metadata') || (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)) {
			return;
		}

		if (isset($_POST['breadcrumb_title_override'])) {
			$title_override = sanitize_text_field($_POST['breadcrumb_title_override']);
			update_post_meta($post_id, 'breadcrumb_title_override', $title_override);
		}
	}
}