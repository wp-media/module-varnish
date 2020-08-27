<?php

namespace WP_Rocket\Tests\Unit\Varnish;

use Brain\Monkey\Filters;
use Brain\Monkey\Functions;
use WPMedia\PHPUnit\Unit\TestCase;
use WP_Rocket\Addon\Varnish\Varnish;

/**
 * @covers WP_Rocket\Addon\Varnish\Varnish::purge
 * @group  Varnish
 * @group  Addon
 */
class Test_Purge extends TestCase {
	public function setUp() {
		parent::setUp();

		Functions\when( 'wp_parse_url' )->alias( function( $url, $component = -1 ) {
			return parse_url( $url, $component );
		} );
	}

	public function testShouldSendRequestOnceWithDefaultValues() {
		$varnish = new Varnish();

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

		$varnish->purge( 'http://example.org' );
	}

	public function testShouldSendRequestOnceWithCustomVarnishIP() {
		Filters\expectApplied( 'rocket_varnish_ip' )
			->once()
			->andReturn(
				[
					'localhost',
				]
			);

		$varnish = new Varnish();

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

		$varnish->purge( 'http://example.org/?regex' );
	}

	public function testShouldSendRequestTwiceWhenArrayVarnishIp() {
		Filters\expectApplied( 'rocket_varnish_ip' )
			->once()
			->andReturn(
				[
					'localhost',
					'127.0.0.1',
				]
			);

		$varnish = new Varnish();

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

		$varnish->purge( 'http://example.org/about/' );
	}
}
