<?php

if ( ! function_exists( 'rocket_has_constant' ) ) {
	/**
	 * Checks if the constant is defined.
	 *
	 * NOTE: This function allows mocking constants when testing.
	 *
	 * @since 3.5
	 *
	 * @param string $constant_name Name of the constant to check.
	 *
	 * @return bool true when constant is defined; else, false.
	 */
	function rocket_has_constant( $constant_name ) {
		return defined( $constant_name );
	}
}

if ( ! function_exists( 'rocket_get_constant' ) ) {
	/**
	 * Gets the constant is defined.
	 *
	 * NOTE: This function allows mocking constants when testing.
	 *
	 * @since 3.5
	 *
	 * @param string     $constant_name Name of the constant to check.
	 * @param mixed|null $default       Optional. Default value to return if constant is not defined.
	 *
	 * @return bool true when constant is defined; else, false.
	 */
	function rocket_get_constant( $constant_name, $default = null ) {
		if ( ! rocket_has_constant( $constant_name ) ) {
			return $default;
		}

		return constant( $constant_name );
	}
}

if ( ! function_exists( 'get_rocket_parse_url' ) ) {
	/**
	 * Extract and return host, path, query and scheme of an URL
	 *
	 * @since 2.11.5 Supports UTF-8 URLs
	 * @since 2.1 Add $query variable
	 * @since 2.0
	 *
	 * @param string $url The URL to parse.
	 *
	 * @return array Components of an URL
	 */
	function get_rocket_parse_url( $url ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals
		if ( ! is_string( $url ) ) {
			return;
		}

		$encoded_url = preg_replace_callback(
			'%[^:/@?&=#]+%usD',
			function( $matches ) {
				return rawurlencode( $matches[0] );
			},
			$url
		);

		$url      = wp_parse_url( $encoded_url );
		$host     = isset( $url['host'] ) ? strtolower( urldecode( $url['host'] ) ) : '';
		$path     = isset( $url['path'] ) ? urldecode( $url['path'] ) : '';
		$scheme   = isset( $url['scheme'] ) ? urldecode( $url['scheme'] ) : '';
		$query    = isset( $url['query'] ) ? urldecode( $url['query'] ) : '';
		$fragment = isset( $url['fragment'] ) ? urldecode( $url['fragment'] ) : '';

		/**
		 * Filter components of an URL
		 *
		 * @since 2.2
		 *
		 * @param array Components of an URL
		 */
		return (array) apply_filters(
			'rocket_parse_url',
			[
				'host'     => $host,
				'path'     => $path,
				'scheme'   => $scheme,
				'query'    => $query,
				'fragment' => $fragment,
			]
		);
	}
}

if ( ! function_exists( 'get_rocket_i18n_home_url' ) ) {
	/**
	 * Get home URL of a specific lang.
	 *
	 * @since 2.2
	 *
	 * @param  string $lang The language code. Default is an empty string.
	 * @return string $url
	 */
	function get_rocket_i18n_home_url( $lang = '' ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals
		return home_url();
	}
}
