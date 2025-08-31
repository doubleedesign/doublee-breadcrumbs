<?php
use function Brain\Monkey\{setUp, tearDown};
use function Brain\Monkey\Functions\when;
use Doubleedesign\Breadcrumbs\Settings;

describe('Settings', function() {

	beforeEach(function() {
		setUp();

		when('get_post_types')->justReturn([
			'page',
			'post',
			'nav_menu_item',
			'wp_block',
			'attachment',
			'revision'
		]);
	});

	afterEach(function() {
		tearDown();
	});

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
