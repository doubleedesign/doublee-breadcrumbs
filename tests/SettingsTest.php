<?php

use Doubleedesign\Breadcrumbs\Settings;

describe('Settings', function() {

	describe('Which post types can have breadcrumbs', function() {

		it('excludes menu items by default', function() {
			$settings = new Settings();

			$post_types = $settings::get_breadcrumbable_post_types();

			expect($post_types)->not->toContain('nav_menu_item');
		});

		it('includes pages by default', function() {
			$settings = new Settings();

			$post_types = $settings::get_breadcrumbable_post_types();

			expect($post_types)->toContain('page');
		});
	});
});
