<?php
/**
 * PS Stats - kostenlose/freie Analyseplattform
 *
 * @link https://n3rds.work
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package psstats
 */

namespace WpPsstats;

use Exception;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

class Logger {
	const LEVEL_NONE = 99;
	const LEVEL_DEBUG = 1;
	const LEVEL_INFO = 3;

	private function get_log_level()
	{
		if ( defined('PSSTATS_DEBUG')) {
			if (PSSTATS_DEBUG) {
				return self::LEVEL_DEBUG;
			}
			return self::LEVEL_NONE;
		}

		return self::LEVEL_INFO;
	}

	public function log( $message , $mode = 3) {
		$log_level = $this->get_log_level();

		if ($log_level > $mode) {
			return;
		}

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
			if ( is_array( $message ) || is_object( $message ) ) {
				error_log( 'Psstats: ' . print_r( $message, true ) );
			} else {
				error_log( 'Psstats: ' . $message );
			}
		}
	}

	public function clear_logged_exceptions() {
		delete_option( $this->make_id() );
	}

	private function persist( $key, $message ) {
		$id     = $this->make_id();
		$logs   = $this->get_last_logged_entries();
		$logs[] = array(
			'name'    => $key,
			'value'   => time(),
			'comment' => $message,
		);
		$logs   = array_slice( $logs, -6 );
		update_option( $id, $logs );
	}

	private function make_id() {
		return Settings::OPTION_PREFIX . 'errorlogs';
	}

	public function get_last_logged_entries() {
		$id   = $this->make_id();
		$logs = get_option( $id );
		if ( empty( $logs ) ) {
			$logs = array();
		}

		// remove any entry older than 1 week
		$logs = array_filter(
			$logs,
			function ( $log ) {
				$one_week_seconds = 604800;
				return ! empty( $log['value'] ) && is_numeric( $log['value'] ) && ( time() - $log['value'] ) <= $one_week_seconds;
			}
		);
		return $logs;
	}

	public function get_readable_trace( Exception $e ) {
		$trace = '';
		if ( $e->getFile() ) {
			$trace = basename( $e->getFile() ) . ':' . $e->getLine() . '; ';
		}
		foreach ( $e->getTrace() as $index => $item ) {
			if ( ! empty( $item['file'] ) ) {
				$trace .= basename( $item['file'] ) . ':' . $item['line'] . '; ';
			}
			if ( $index > 8 ) {
				continue;
			}
		}

		return trim( $trace );
	}

	public function log_exception( $key, Exception $e ) {
		$trace   = $this->get_readable_trace($e);
		$message = $e->getMessage() . ' => ' . $trace;
		$this->log( 'Psstats error: ' . $message );
		$this->persist( $key, $message );
	}

}
