<?php
/**
 * The SEO Framework plugin
 * Copyright (C) 2015 Sybre Waaijer, CyberWire (https://cyberwire.nl/)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 3 as published
 * by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

add_action( 'plugins_loaded', 'the_seo_framework_init', 5 );
/**
 * Load The_SEO_Framework_Load class
 *
 * @action plugins_loaded
 * @priority 5 Use anything above 5, or any action later than plugins_loaded and
 * you can access the class and functions.
 *
 * @staticvar object $the_seo_framework
 *
 * @since 2.2.5
 */
function the_seo_framework_init() {
	//* Cache the class. Do not run everything more than once.
	static $the_seo_framework = null;

	if ( the_seo_framework_active() )
		if ( ! isset( $the_seo_framework ) )
			$the_seo_framework = new The_SEO_Framework_Load();

	return $the_seo_framework;
}

/**
 * Allow this plugin to load through filter
 *
 * Applies the_seo_framework_load filters.
 *
 * @return bool allow loading of plugin
 *
 * @since 2.1.0
 *
 * New function name.
 * @since 2.3.7
 *
 * @action plugins_loaded
 */
function the_seo_framework_load() {
	/**
	 * New filter.
	 * @since 2.3.0
	 *
	 * Removed previous filter.
	 * @since 2.3.5
	 */
	return (bool) apply_filters( 'the_seo_framework_load', true );
}

/**
 * Load plugin files
 * @uses THE_SEO_FRAMEWORK_DIR_PATH_FUNCT
 * @uses THE_SEO_FRAMEWORK_DIR_PATH_CLASS
 *
 * @since 2.1.6
 */
require_once( THE_SEO_FRAMEWORK_DIR_PATH_FUNCT . 'compat.php' );
require_once( THE_SEO_FRAMEWORK_DIR_PATH_FUNCT . 'optionsapi.php' );

require_once( THE_SEO_FRAMEWORK_DIR_PATH_CLASS . 'init.class.php' );
require_once( THE_SEO_FRAMEWORK_DIR_PATH_CLASS . 'admininit.class.php' );
require_once( THE_SEO_FRAMEWORK_DIR_PATH_CLASS . 'render.class.php' );
require_once( THE_SEO_FRAMEWORK_DIR_PATH_CLASS . 'detect.class.php' );
require_once( THE_SEO_FRAMEWORK_DIR_PATH_CLASS . 'postdata.class.php' );
require_once( THE_SEO_FRAMEWORK_DIR_PATH_CLASS . 'generate.class.php' );
require_once( THE_SEO_FRAMEWORK_DIR_PATH_CLASS . 'search.class.php' );
require_once( THE_SEO_FRAMEWORK_DIR_PATH_CLASS . 'doingitright.class.php' );
require_once( THE_SEO_FRAMEWORK_DIR_PATH_CLASS . 'pageoptions.class.php' );
require_once( THE_SEO_FRAMEWORK_DIR_PATH_CLASS . 'inpost.class.php' );
require_once( THE_SEO_FRAMEWORK_DIR_PATH_CLASS . 'adminpages.class.php' );
require_once( THE_SEO_FRAMEWORK_DIR_PATH_CLASS . 'sanitize.class.php' );
require_once( THE_SEO_FRAMEWORK_DIR_PATH_CLASS . 'siteoptions.class.php' );
require_once( THE_SEO_FRAMEWORK_DIR_PATH_CLASS . 'networkoptions.class.php' );
require_once( THE_SEO_FRAMEWORK_DIR_PATH_CLASS . 'metaboxes.class.php' );
require_once( THE_SEO_FRAMEWORK_DIR_PATH_CLASS . 'sitemaps.class.php' );
require_once( THE_SEO_FRAMEWORK_DIR_PATH_CLASS . 'transients.class.php' );

require_once( THE_SEO_FRAMEWORK_DIR_PATH . 'inc/deprecated/deprecated.class.php' );

/**
 * God class.
 *
 * Extending upon parent classes.
 *
 * @since 2.1.6
 */
class The_SEO_Framework_Load extends The_SEO_Framework_Deprecated {

	/**
	 * Constructor, load parent constructor
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Wrapper for function calling through parameters. The golden nugget.
	 * Is this function not working properly? Send me your code through the WordPress support forums.
	 * I'll adjust if possible.
	 *
	 * @param array|string $callback the method array or function string.
	 * @param string $version the version of AutoDescription the function is used.
	 *
	 * @since 2.2.2
	 *
	 * @return mixed $output The function called.
	 *
	 * @param array|string $params The arguments passed to the function.
	 * @since 2.2.4
	 */
	public function call_function( $callback, $version = '', $params = array() ) {

		$output = '';

		/**
		 * Convert string/object to array
		 */
		if ( is_object( $callback ) ) {
			$function = array( $callback, '' );
		} else {
			$function = (array) $callback;
		}

		/**
		 * Convert string/object to array
		 */
		if ( is_object( $params ) ) {
			$args = array( $params, '' );
		} else {
			$args = (array) $params;
		}

		/**
		 * Fetch method/function
		 */
		if ( is_object( $function[0] ) ) {
			$method = (string) $function[1];

			if ( $function[0] == $this ) {
				if ( method_exists( $this, $method ) ) {
					if ( empty( $args ) ) {
						// In-Object calling.
						$output = call_user_func( array( $this, $method ) );
					} else {
						// In-Object calling.
						$output = call_user_func_array( array( $this, $method ), (array) $args );
					}
				} else if ( $version ) {
					$version = $this->the_seo_framework_version( $version );
					 _doing_it_wrong( (string) $this . '::' . (string) $method, __( "Class or Method not found.", 'autodescription' ), $version );
				}
			} else {
				// This doesn't work in Apache configurations.
				$class = get_class( $function[0] );
				$method = (string) $function[1];

				if ( method_exists( $class, $method ) ) {
					if ( empty( $args ) ) {
						// Static calling
						$output = call_user_func( $class . '::'. $method );
					} else {
						// Static calling
						$output = call_user_func_array( $class . '::'. $method, (array) $args );
					}
				} else if ( $version ) {
					$version = $this->the_seo_framework_version( $version );
					 _doing_it_wrong( (string) $class . '::' . (string) $method, __( "Class or Method not found. Needs to be called statically.", 'autodescription' ), $version );
				}
			}
		} else if ( is_string( $function[0] ) && ! empty( $function[1] ) ) {
			if ( empty( $args ) ) {
				// Static calling
				$output = call_user_func( $function[0] . '::' . $function[1] );
			} else {
				// Static calling
				$output = call_user_func_array( $function[0] . '::' . $function[1], (array) $args );
			}
		} else if ( is_string( $function[0] ) ) {
			$func = $function[0];
			if ( empty( $args ) ) {
				$output = call_user_func( $func );
			} else {
				$output = call_user_func_array( $func, (array) $args );
			}
		} else if ( $version ) {
			$version = $this->the_seo_framework_version( $version );
			_doing_it_wrong( (string) $callback, __( "Function needs to be called as string.", 'autodescription' ), $version );
		}

		return $output;
	}

	/**
	 * Helper function for Doing it Wrong
	 *
	 * @since 2.3.0
	 */
	public function the_seo_framework_version( $version = '' ) {

		$output = is_null( $version ) ? '' : sprintf( __( '%s of The SEO Framework', 'autodescription' ), $version );

		return $output;
	}

}

//* Load deprecated functions and classes.
require_once( THE_SEO_FRAMEWORK_DIR_PATH . 'inc/deprecated/deprecated.php' );

/**
 * FLush permalinks on activation/deactivation
 *
 * Calls functions statically.
 *
 * @since 2.2.9
 */
register_activation_hook( THE_SEO_FRAMEWORK_PLUGIN_BASE_FILE, array( 'The_SEO_Framework_Load', 'flush_rewrite_rules_activation' ) );
register_deactivation_hook( THE_SEO_FRAMEWORK_PLUGIN_BASE_FILE, array( 'The_SEO_Framework_Load', 'flush_rewrite_rules_deactivation' ) );