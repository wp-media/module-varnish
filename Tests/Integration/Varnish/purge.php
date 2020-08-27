<?php

namespace WP_Rocket\Tests\Integration\Varnish;

use Brain\Monkey\Functions;
use WPMedia\PHPUnit\Integration\TestCase;

/**
 * @covers WP_Rocket\Addon\Varnish\Varnish::purge
 * @group  Varnish
 * @group  Addon
 */
class Test_Purge extends TestCase {
	private static $varnish;

	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		$container = apply_filters( 'rocket_container', null );

		self::$varnish = $container->get( 'varnish' );
	}

	public function testShouldSendRequestOnceWithDefaultValues() {
		Functions\expect( 'wp_remote_request' )
			->once()
			->with(
				'http://example.org',
				[
					'method'      => 'PURGE',
					'blocking'    => false,
					'redirection' => 0,
					'headers'     => [
						'host'           => 'example.org',
						'X-Purge-Method' => 'default',
					],
				]
			);

		self::$varnish->purge( 'http://example.org' );
	}

	public function testShouldSendRequestOnceWithCustomVarnishIP() {
        $filter = function() {
            return [
                'localhost',
            ];
        };

		add_filter( 'rocket_varnish_ip', $filter );

		Functions\expect( 'wp_remote_request' )
			->once()
			->with(
				'http://localhost/.*',
				[
					'method'      => 'PURGE',
					'blocking'    => false,
					'redirection' => 0,
					'headers'     => [
						'host'           => 'example.org',
						'X-Purge-Method' => 'regex',
					],
				]
			);

		self::$varnish->purge( 'http://example.org/?regex' );

        remove_filter( 'rocket_varnish_ip', $filter );
	}

	public function testShouldSendRequestTwiceWhenArrayVarnishIp() {
        $filter = function() {
            return [
                'localhost',
                '127.0.0.1',
            ];
        };

        add_filter( 'rocket_varnish_ip', $filter );

		Functions\expect( 'wp_remote_request' )
			->once()
			->with(
				'http://localhost/about/',
				[
					'method'      => 'PURGE',
					'blocking'    => false,
					'redirection' => 0,
					'headers'     => [
						'host'           => 'example.org',
						'X-Purge-Method' => 'default',
					],
				]
			);

		Functions\expect( 'wp_remote_request' )
			->once()
			->with(
				'http://127.0.0.1/about/',
				[
					'method'      => 'PURGE',
					'blocking'    => false,
					'redirection' => 0,
					'headers'     => [
						'host'           => 'example.org',
						'X-Purge-Method' => 'default',
					],
				]
			);

		self::$varnish->purge( 'http://example.org/about/' );

        remove_filter( 'rocket_varnish_ip', $filter );
	}
}
