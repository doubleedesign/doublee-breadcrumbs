<?php
namespace Doubleedesign\Breadcrumbs;

/**
 * Provide an admin area view for the plugin
 *
 * @since      1.0.0
 * @package    Breadcrumbs
 */

$post_types = Settings::get_breadcrumbable_post_types();
$taxonomies = Settings::get_breadcrumbable_taxonomies();
$current_settings = get_option('breadcrumbs_settings');
?>

<form class="breadcrumbs-admin wrap" action="<?php $this->save(); ?>">
	<h1>Breadcrumbs</h1>

	<fieldset class="breadcrumbs-admin__group">
		<legend>Settings</legend>
		<h2>Taxonomy to use in breadcrumb trail for single posts</h2>
		<p>If The SEO Framework or Yoast SEO is active on this site, the post's primary term of the taxonomy selected
			below will be used. Otherwise, the first term will be used.</p>
		<table class="form-table form-table--alt" role="presentation">
			<?php
			foreach($post_types as $post_type) {
				$object = get_post_type_object($post_type);
				$post_taxonomies = get_object_taxonomies($post_type);
				$name = 'taxonomy_' . $post_type;
				$current_value = '';
				if(isset($_GET[$name])) {
					$current_value = $_GET[$name];
				}
				else if(isset($current_settings[$name])) {
					$current_value = $current_settings[$name];
				}
				?>
				<tr>
					<th scope="row">
						<label for="<?php echo $post_type; ?>-select"><?php echo $object->label; ?> taxonomy
					</th>
					<td>
						<select id="<?php echo $post_type; ?>-select" name="<?php echo $name; ?>">
							<option value="">No taxonomy</option>
							<?php
							foreach($post_taxonomies as $post_taxonomy) {
								if($current_value == $post_taxonomy) {
									echo '<option value="'.$post_taxonomy.'" selected>'.$post_taxonomy.'</option>';
								}
								else {
									echo '<option value="'.$post_taxonomy.'">'.$post_taxonomy.'</option>';
								}
							} ?>
						</select>
					</td>
				</tr>
			<?php } ?>
            <?php
            if (class_exists('woocommerce')) { ?>
                <tr>
                    <th scope="row">
                        <label for="woocommerce-pages-select">WooCommerce pages <small style="display: block; font-style: italic; font-weight: normal;">Account, Cart, Checkout etc</small></label>
                    </th>
                    <td>
                        <select id="woocommerce-pages-select" name="woocommerce-pages">
                            <option value="" <?php echo $current_settings['woocommerce-pages'] === '' ? 'selected' : ''?>>No extra predecessor</option>
                            <option value="shop_page" <?php echo $current_settings['woocommerce-pages'] === 'shop_page' ? 'selected' : ''?>>Shop page</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="woocommerce-products-select">Products</label>
                    </th>
                    <td>
                        <select id="woocommerce-products-select" name="woocommerce-products">
                            <option value="" <?php echo $current_settings['woocommerce-products'] === '' ? 'selected' : ''?>>No extra predecessor</option>
                            <option value="shop_page" <?php echo $current_settings['woocommerce-products'] === 'shop_page' ? 'selected' : ''?>>Include link to shop page</option>
                        </select>
                    </td>
                </tr>
            <?php } ?>
		</table>

		<input type="hidden" name="page" value="breadcrumbs"/>

		<input class="button button-primary" type="submit" value="Save settings"/>

	</fieldset>

	<fieldset class="breadcrumbs-admin__group">
		<legend>Information for theme developers</legend>

		<h2>Template usage</h2>
		<p>Add the action <code>do_action('doublee_breadcrumbs');</code> in your theme where you want to show breadcrumbs.</p>

		<h2>Filters</h2>
		<ul>
			<li>
				<h3><code>breadcrumbs_filter_post_types</code></h3>
				<p>Add or remove post types that have breadcrumbs. Takes one parameter, an array of post types as per the result of WordPress&rsquo;s <code>get_post_types()</code>.</p>
			</li>
			<li>
				<h3><code>breadcrumbs_filter_taxonomies</code></h3>
				<p>Add or remove taxonomies that have breadcrumbs. Takes one parameter, an array of taxonomies as per the result of WordPress&rsquo;s <code>get_taxonomies()</code>.</p>
			</li>
			<li>
				<h3><code>breadcrumbs_filter_output</code></h3>
				<p>Filter the HTML output used by the <code>doublee_breadcrumbs()</code> output function.</p>
			</li>
		</ul>

		<h2>Post-level settings</h2>
		<p>Titles shown in the breadcrumbs can be overridden at the post level.</p>
        <p>If you are hiding the last item with CSS or don't want the title editable for another reason, you can remove the metabox using something like this in your theme:<br/> <code>remove_meta_box('breadcrumb-settings', array('page', 'post', 'product'), 'side');</code></p>

	</fieldset>
</form>
