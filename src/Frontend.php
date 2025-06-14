<?php /** @noinspection t */
namespace Doubleedesign\Breadcrumbs;

use Doubleedesign\Breadcrumbs\WPSEO_Primary_Term;
use function Doubleedesign\Breadcrumbs\is_account_page;
use function Doubleedesign\Breadcrumbs\is_cart;
use function Doubleedesign\Breadcrumbs\is_checkout;
use function Doubleedesign\Breadcrumbs\is_wc_endpoint_url;
use function Doubleedesign\Breadcrumbs\the_seo_framework;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @since      1.0.0
 * @package    Breadcrumbs
 */
class Frontend {

	public function __construct() {
		add_action('doublee_breadcrumbs', array($this, 'set_breadcrumbs'), 10);
		add_action('doublee_breadcrumbs', array($this, 'display_breadcrumbs'), 10);
	}


	/**
	 * Initialise the list of breadcrumb items
	 * @since    1.0.0
	 * @access   protected
	 */
	protected array $breadcrumbs = array();


	/**
	 * Populate the breadcrumb array
	 * @since    1.0.0
     * @wp-hook
	 */
	public function set_breadcrumbs(): void {

		// Add the homepage as the first item,
		// specifying to start the array at 1 so the index can be used in the template output without further processing
		$home_id = get_option('page_on_front');
		$home_title_override = get_post_meta($home_id, 'breadcrumb_title_override', true);
		$this->breadcrumbs[1]['title'] = $home_title_override ? $home_title_override : get_the_title($home_id);
		$this->breadcrumbs[1]['url'] = get_bloginfo('url');

		// Populate the rest
		if(is_page() && in_array('page', Settings::get_breadcrumbable_post_types())) {
			$this->get_page_trail();
		}
		else if(is_singular() && in_array(get_post_type(get_the_id()), Settings::get_breadcrumbable_post_types())) {
			$this->get_post_trail();
		}
		else if(is_archive() || is_home()) {
			$this->get_archive_trail();
		}
	}

	/**
	 * Getter function for the $breadcrumbs array
	 * @since    1.0.0
	 * @return array
	 */
	protected function get_breadcrumbs(): array {
		$breadcrumbs = $this->breadcrumbs;

		// Return array of breadcrumbs with the opportunity for themes to alter it with this filter, before it gets to the output stage
		// Allows devs to alter the breadcrumb trail without overriding the HTML output
		return apply_filters('breadcrumbs_filter_list', $breadcrumbs);
	}

	/**
	 * Option for developers to get the raw array to use in their own output
	 * Get from the main Breadcrumbs object like so:
	 * $yourVariable = Breadcrumbs::$instance->get_raw_breadcrumbs()
	 * @return array
	 */
	public function get_raw_breadcrumbs(): array {
		$this->set_breadcrumbs();

		return $this->get_breadcrumbs();
	}


	/**
	 * Utility function to add an item to the $breadcrumbs array
	 * @param string $title
	 * @param string $url
	 * @param bool $current
	 *@since    1.0.0
	 */
	private function add_breadcrumb(string $title, string $url, bool $current = false): void {
		if($current) {
			array_push($this->breadcrumbs, array(
				'title' => $title,
				'url'   => $url,
				'current' => true,
			));
		}
		else {
			array_push($this->breadcrumbs, array(
				'title' => $title,
				'url'   => $url,
			));
		}
	}


	/**
	 * The breadcrumb trail of a page
	 * @since    1.0.0
	 */
	private function get_page_trail(): void {
        $settings = get_option('breadcrumbs_settings');

		// Check if the page has ancestors, and add those to the trail
		$page_ancestors = array_reverse(get_ancestors(get_the_id(), 'page'));
		foreach($page_ancestors as $ancestor_id) {
			$this->add_breadcrumb(get_the_title($ancestor_id), get_the_permalink($ancestor_id));
		}

        // Add shop page to trail for WooCommerce pages if the setting says so
        if(class_exists('woocommerce') && $settings['woocommerce-pages'] === 'shop_page' && ((is_cart() || is_checkout() || is_account_page() || is_wc_endpoint_url()))) {
            $shop = get_option('woocommerce_shop_page_id');
            $this->add_breadcrumb(get_the_title($shop), get_the_permalink($shop));
        }

		// Add the page title, with no link
		$page_title_override = get_post_meta(get_the_id(), 'breadcrumb_title_override', true);
		$page_title = $page_title_override ?: get_the_title(get_the_id());
		$this->add_breadcrumb($page_title, '#', true);
	}

	/**
	 * The breadcrumb trail of a post/CPT
	 * @since    1.0.0
	 */
	private function get_post_trail(): void {
		$post_type = get_post_type();
		$settings = get_option('breadcrumbs_settings');

		// Fort posts, add the blog page set in Settings > Reading
		if($post_type == 'post') {
			if(get_option('page_for_posts')) {
				$blog_page_id = get_option('page_for_posts');
				$archive_url   = get_the_permalink($blog_page_id);
				$title_override = get_post_meta(get_the_id(), 'breadcrumb_title_override', true);
				$archive_title = $title_override ?: get_the_title($blog_page_id);
				$this->add_breadcrumb($archive_title, $archive_url);
			}
		}
        // Add shop page to trail for WooCommerce products if the setting says so
        else if(class_exists('woocommerce') && $post_type === 'product') {
            if($settings['woocommerce-products'] === 'shop_page') {
                $shop = get_option('woocommerce_shop_page_id');
                $this->add_breadcrumb(get_the_title($shop), get_the_permalink($shop));
            }
        }
		// Otherwise, show CPT archive link if has_archive is set on that post type
		else {
			$object = get_post_type_object($post_type);
			$archive_title = $object->label;
			$archive_url = get_post_type_archive_link($post_type);
			if(!empty($archive_url)) { // if has_archive is false for this post type, we dont want to include it
				$this->add_breadcrumb($archive_title, $archive_url);
			}
		}

		// Add taxonomy terms
		if(isset($settings['taxonomy_'.$post_type]) && !empty($settings['taxonomy_'.$post_type])) {
			$primary_taxonomy = $settings['taxonomy_'.$post_type];
			$terms = get_the_terms(get_the_id(), $primary_taxonomy);

			// If the post has more than one term and The SEO Framework or Yoast is in use, use the primary term
			// Otherwise, get the first/only term
			if(count($terms) > 1) {
				if(defined('THE_SEO_FRAMEWORK_PRESENT')) {
					$primary_term_id = the_seo_framework()->get_primary_term_id(get_the_id(), $primary_taxonomy);
					$primary_term = get_term_by('term_id', $primary_term_id, $primary_taxonomy);
				}
				else if(class_exists('WPSEO_Primary_Term')) {
					$primary_term_class = new WPSEO_Primary_Term($primary_taxonomy, get_the_id());
					$primary_term_id = $primary_term_class->get_primary_term();
					$primary_term = get_term_by('term_id', $primary_term_id, $primary_taxonomy);
				}
				else {
					$primary_term = $terms[0];
					$primary_term_id = $primary_term->term_id;
				}
			}
			else {
				$primary_term = $terms[0];
				$primary_term_id = $primary_term->term_id;
			}

			if(isset($primary_term_id)) {
				// Check if this term has ancestors, and add those to the breadcrumb list
				$term_ancestors = get_ancestors($primary_term_id, $primary_taxonomy, 'taxonomy');
				foreach($term_ancestors as $ancestor_id) {
					$ancestor = get_term_by('term_id', $ancestor_id, $primary_taxonomy);
					$this->add_breadcrumb($ancestor->name, get_term_link($primary_term_id));
				}

				// Add the term itself
				$this->add_breadcrumb($primary_term->name, get_term_link($primary_term_id));
			}
		}

		// Get ancestors
		$post_ancestors = get_ancestors(get_the_id(), $post_type);
		foreach($post_ancestors as $ancestor_id) {
			$this->add_breadcrumb(get_the_title($ancestor_id), get_the_permalink($ancestor_id));
		}

		// Add the post title
		$post_title_override = get_post_meta(get_the_id(), 'breadcrumb_title_override', true);
		$post_title = $post_title_override ?: get_the_title(get_the_id());
		$this->add_breadcrumb($post_title, '#', true);
	}

	/**
	 * The breadcrumb trail of a taxonomy term or post type archive
	 * @since    1.0.0
	 */
	private function get_archive_trail(): void {
		$queried_object = get_queried_object();
		$blog_page_id = get_option('page_for_posts');

		if($queried_object) {
			$class = get_class($queried_object);

			switch($class) {
				// Taxonomy term: Term hierarchy + this term's label
				case 'WP_Term':
					$term_ancestors = get_ancestors($queried_object->term_id, $queried_object->taxonomy, 'taxonomy');
					foreach($term_ancestors as $ancestor_id) {
						$ancestor = get_term_by('term_id', $ancestor_id, $queried_object->taxonomy);
						$this->add_breadcrumb($ancestor->name, get_term_link($ancestor_id));
					}
					$this->add_breadcrumb($queried_object->name, '');
					break;

				// Post type archive: Add the post type label as the last item (no link)
				case 'WP_Post_Type':
					$this->add_breadcrumb($queried_object->label, '');
					break;

				// Author archive: Display Name as the last item (no link)
				case 'WP_User':
					$this->add_breadcrumb('Author: ' . $queried_object->data->display_name, '');
					break;

				// Blog page: Add the blog page label as the last item
				// Use the default if another archive type is an instance of WP_Post
				case 'WP_Post':
					if($queried_object->ID == $blog_page_id) {
						$this->add_breadcrumb(get_the_title($blog_page_id), '');
					} else {
						$this->add_breadcrumb(get_the_archive_title(), '');
					}
					break;

				// Catch anything else with a default
				default:
					$this->add_breadcrumb(get_the_archive_title(), '');
					break;
			}
		}
		// Catch anything else, such as date archives (which are not objects)
		else {
			$this->add_breadcrumb(get_the_title($blog_page_id), get_the_permalink($blog_page_id));
			$this->add_breadcrumb(get_the_archive_title(), '');
		}
	}


	/**
	 * The HTML output
	 * @return mixed|void
	 */
	private function get_output() {

        $output = '<ol class="breadcrumbs-list" vocab="https://schema.org/" typeof="BreadcrumbList">';
        foreach($this->get_breadcrumbs() as $index => $item) {
            if($item['url']) {
                $output .= '
                    <li class="breadcrumbs-list__item" property="itemListElement" typeof="ListItem">
                        <a class="breadcrumbs-list__item__link" property="item" typeof="WebPage" href="' . $item['url'] . '">
                            <span class="breadcrumbs-list__item__link__label" property="name">' . $item['title'] . '</span>
                        </a>
                        <meta property="position" content="' . $index . '">
                    </li>
                ';
            }
            else {
                $output .= '
                    <li class="breadcrumbs-list__item" property="itemListElement" typeof="ListItem">
                        <span class="breadcrumbs-list__item__label" property="name">' . $item['title'] . '</span>
                        <meta property="position" content="' . $index . '">
                    </li>
                ';
            }
        }
		$output .= '</ol>';

		// Return output with the opportunity for themes to alter it with this filter
		return apply_filters('breadcrumbs_filter_output', $output, $this->breadcrumbs);
	}


	/**
	 * The function added to a template action for use in themes
	 * @see class-breadcrumbs.php
	 * @since    1.0.0
	 */
	public function display_breadcrumbs(): void {
		echo $this->get_output();
	}
}
