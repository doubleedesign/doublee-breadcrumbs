<?php
// First we need to load the composer autoloader, so we can use WP Mock and other libraries
require_once __DIR__ . '/../vendor/autoload.php';

beforeAll(function () {
	// Bootstrap WP_Mock once at the start
	WP_Mock::bootstrap();
});

beforeEach(function () {
	error_log(print_r('running beforeeach', true));
	WP_Mock::setUp();

	WP_Mock::userFunction('get_post_types')->andReturn([
		'page',
		'post',
		'nav_menu_item',
		'wp_block',
		'attachment',
		'revision'
	]);
});

afterEach(function () {
	WP_Mock::tearDown();
});
