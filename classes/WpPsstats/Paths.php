<?php
/**
 * PS Stats - kostenlose/freie Analyseplattform
 *
 * @link https://n3rds.work
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package psstats
 */

namespace WpPsstats;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

class Paths {

	private function get_file_system() {
		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();
		}

		if ( ! class_exists( '\WP_Filesystem_Direct' ) ) {
			require_once ABSPATH . '/wp-admin/includes/class-wp-filesystem-base.php';
			require_once ABSPATH . '/wp-admin/includes/class-wp-filesystem-direct.php';
		}

		return new \WP_Filesystem_Direct( new \stdClass() );
	}

	public function get_upload_base_url() {
		$upload_dir      = wp_upload_dir();
		$path_upload_url = $upload_dir['baseurl'];

		return rtrim( $path_upload_url, '/' ) . '/' . PSSTATS_UPLOAD_DIR;
	}

	public function get_upload_base_dir() {
		$upload_dir      = wp_upload_dir();
		$path_upload_dir = $upload_dir['basedir'];
		$path_upload_dir = rtrim( $path_upload_dir, '/' ) . '/' . PSSTATS_UPLOAD_DIR;

		return $path_upload_dir;
	}

	public function get_psstats_js_upload_path() {
		return $this->get_upload_base_dir() . '/' . PSSTATS_JS_NAME;
	}

	public function get_config_ini_path() {
		return $this->get_upload_base_dir() . '/' . PSSTATS_CONFIG_PATH;
	}

	public function get_tracker_api_rest_api_endpoint() {
		return path_join( get_rest_url(), API::VERSION . '/' . API::ROUTE_HIT . '/' );
	}

	public function get_tracker_api_url_in_psstats_dir() {
		return plugins_url( 'app/psstats.php', PSSTATS_ANALYTICS_FILE );
	}

	public function get_js_tracker_rest_api_endpoint() {
		return $this->get_tracker_api_rest_api_endpoint();
	}

	public function get_js_tracker_url_in_psstats_dir() {
		$paths = new Paths();

		if ( file_exists( $paths->get_psstats_js_upload_path() ) ) {
			return $this->get_upload_base_url() . '/' . PSSTATS_JS_NAME;
		}

		return plugins_url( 'app/psstats.js', PSSTATS_ANALYTICS_FILE );
	}

	public function get_tmp_dir() {
		$is_multi_site = function_exists( 'is_multisite' ) && is_multisite();

		$cache_dir_alternative = $this->get_upload_base_dir() . '/tmp';
		$base_cache_dir        = WP_CONTENT_DIR . '/cache';
		$default_cache_dir     = $base_cache_dir . '/' . PSSTATS_UPLOAD_DIR;

		if ( ! $is_multi_site &&
			 ( ( is_writable( WP_CONTENT_DIR ) && ! is_dir( $base_cache_dir ) )
			   || is_writable( $base_cache_dir ) ) ) {
			// we prefer wp-content/cache
			$cache_dir = $default_cache_dir;

			if ( ! is_dir( $cache_dir ) ) {
				wp_mkdir_p( $cache_dir );
			}

			if ( ! is_writable( $cache_dir ) ) {
				// wasn't made writable for some reason so we prefer to use the upload dir just to be safe
				$cache_dir = $cache_dir_alternative;
			}
		} else {
			// fallback wp-content/uploads/psstats/tmp if $defaultCacheDir is not writable or if multisite is used
			// with multisite we need to make sure to cache files per site
			$cache_dir = $cache_dir_alternative;

			if ( ! is_dir( $cache_dir ) ) {
				wp_mkdir_p( $cache_dir );
			}
		}

		return $cache_dir;
	}

	public function get_relative_dir_to_psstats( $target_dir ) {
		$psstats_dir         = plugin_dir_path( PSSTATS_ANALYTICS_FILE ) . 'app';
		$psstats_dir_parts   = explode( DIRECTORY_SEPARATOR, $psstats_dir );
		$target_dir_parts   = explode( DIRECTORY_SEPARATOR, $target_dir );
		$relative_directory = '';
		$add_at_the_end     = array();
		$was_previous_same  = false;

		foreach ( $target_dir_parts as $index => $part ) {
			if ( isset( $psstats_dir_parts[ $index ] )
				 && 'psstats' !== $part // not when psstats is same part cause it's the plugin name but eg also the upload folder name and it would generate wrong path
				 && $psstats_dir_parts[ $index ] === $part
				 && ! $was_previous_same ) {
				continue;
			}

			$was_previous_same = true;

			if ( isset( $psstats_dir_parts[ $index ] ) ) {
				$relative_directory .= '../';
			}
			$add_at_the_end[] = $part;
		}

		return $relative_directory . implode( '/', $add_at_the_end );
	}

	public function get_gloal_upload_dir_if_possible( $file_to_look_for = '' ) {
		if ( defined( 'PSSTATS_GLOBAL_UPLOAD_DIR' ) ) {
			return PSSTATS_GLOBAL_UPLOAD_DIR;
		}

		$path_upload_dir = $this->get_upload_base_dir();

		if ( ! is_multisite() || is_network_admin() ) {
			return $path_upload_dir;
		}

		if ( preg_match( '/sites\/(\d)+$/', $path_upload_dir ) ) {
			$path_upload_dir = preg_replace( '/sites\/(\d)+$/', '', $path_upload_dir );
		} else {
			// re-implement _wp_upload_dir to find hopefully the upload_dir for the network site
			$upload_path = trim( get_option( 'upload_path' ) );
			if ( empty( $upload_path ) || 'wp-content/uploads' === $upload_path ) {
				$path_upload_dir = WP_CONTENT_DIR . '/uploads';
			} elseif ( 0 !== strpos( $upload_path, ABSPATH ) ) {
				// $dir is absolute, $upload_path is (maybe) relative to ABSPATH
				$path_upload_dir = path_join( ABSPATH, $upload_path );
			} else {
				$path_upload_dir = $upload_path;
			}
		}

		if ( ! empty( $file_to_look_for ) ) {
			$file_to_look_for = PSSTATS_UPLOAD_DIR . '/' . ltrim( $file_to_look_for, '/' );
		}

		if ( ! empty( $file_to_look_for )
			 && ! file_exists( $path_upload_dir . $file_to_look_for ) ) {
			// seems we haven't auto detected the right one yet... (or it is not yet installed)
			// we go up the site upload dir step by step to try and find the network upload dir
			$parent_dir = $path_upload_dir;
			do {
				$parent_dir = dirname( $parent_dir );
				if ( file_exists( $parent_dir . $file_to_look_for ) ) {
					return $parent_dir;
				}
			} while ( strpos( $parent_dir, ABSPATH ) === 0 ); // we don't go outside WP dir
		}

		$path_upload_dir = rtrim( $path_upload_dir, '/' ) . '/' . PSSTATS_UPLOAD_DIR;

		return $path_upload_dir;
	}

	public function clear_assets_dir() {
		$tmp_dir = $this->get_tmp_dir() . '/assets';
		if ( $tmp_dir && is_dir( $tmp_dir ) ) {
			$file_system_direct = $this->get_file_system();
			$file_system_direct->rmdir( $tmp_dir, true );
		}
	}

	public function clear_cache_dir() {
		$tmp_dir = $this->get_tmp_dir();
		if ( $tmp_dir
			 && is_dir( $tmp_dir )
			 && is_dir( $tmp_dir . '/cache' ) ) {
			// we make sure it's a psstats cache dir to not delete something falsely
			$file_system_direct = $this->get_file_system();
			$file_system_direct->rmdir( $tmp_dir, true );
		}
	}

	public function uninstall() {
		$this->clear_cache_dir();

		$dir = $this->get_upload_base_dir();

		$file_system_direct = $this->get_file_system();
		$file_system_direct->rmdir( $dir, true );

		$global_dir = $this->get_upload_base_dir();
		if ( $global_dir && $global_dir !== $dir ) {
			$file_system_direct->rmdir( $dir );
		}
	}
}
