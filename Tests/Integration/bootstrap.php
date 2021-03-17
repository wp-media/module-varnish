<?php

namespace WP_Rocket\Tests\Integration;

use WP_Rocket\Dependencies\League\Container\Container;
use WP_Rocket\Admin\Options;
use WP_Rocket\Event_Management\Event_Manager;

define( 'WPMEDIA_MODULE_ROOT', dirname( dirname( __DIR__ ) ) . DIRECTORY_SEPARATOR );
define( 'WP_ROCKET_PLUGIN_ROOT', WPMEDIA_MODULE_ROOT );
define( 'WPMEDIA_MODULE_TESTS_FIXTURES_DIR', dirname( __DIR__ ) . '/Fixtures' );
define( 'WP_ROCKET_TESTS_FIXTURES_DIR', WPMEDIA_MODULE_TESTS_FIXTURES_DIR );
define( 'WP_ROCKET_TESTS_DIR', __DIR__ );

// Manually load the plugin being tested.
tests_add_filter(
	'muplugins_loaded',
	function() {
		$container     = new Container();
		$event_manager = new Event_Manager();

		add_filter( 'rocket_container', function() use ( $container ) {
			return $container;
		} );

		$container->add(
			'options_api',
			function() {
				return new Options( 'wp_rocket_' );
			}
		);

		$container->add( 'options', 'WP_Rocket\Admin\Options_Data' )
			->addArgument( $container->get( 'options_api' )->get( 'settings', [] ) );

		$container->addServiceProvider( 'WP_Rocket\Addon\Varnish\ServiceProvider' );

		$subscribers = [
			'varnish_subscriber',
		];

		foreach ( $subscribers as $subscriber ) {
			$event_manager->add_subscriber( $container->get( $subscriber ) );
		}
	}
);

/**
 * The original files need to loaded into memory before we mock them with Patchwork. Add files here before the unit
 * tests start.
 */
function load_original_files_before_mocking() {
	$fixtures = [
		'/functions.php',
	];
	foreach ( $fixtures as $file ) {
		require_once WPMEDIA_MODULE_TESTS_FIXTURES_DIR . $file;
	}
}

load_original_files_before_mocking();
