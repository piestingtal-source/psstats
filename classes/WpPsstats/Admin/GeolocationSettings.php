<?php
/**
 * PS Stats - kostenlose/freie Analyseplattform
 *
 * @link https://n3rds.work
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package psstats
 */

namespace WpPsstats\Admin;

use WpPsstats\Capabilities;
use WpPsstats\ScheduledTasks;
use WpPsstats\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

class GeolocationSettings implements AdminSettingsInterface {
	const NONCE_NAME = 'psstats_geolocation';
	const FORM_NAME = 'psstats_maxmind_license';

	/**
	 * @var Settings
	 */
	private $settings;

	public function __construct( Settings $settings ) {
		$this->settings = $settings;
	}

	public function get_title() {
		return esc_html__( 'Geolocation', 'psstats' );
	}

	private function update_if_submitted() {
		if ( isset( $_POST )
			 && isset( $_POST[ self::FORM_NAME ] )
			 && is_admin()
			 && check_admin_referer( self::NONCE_NAME )
			 && current_user_can( Capabilities::KEY_SUPERUSER ) ) {

			$maxmind_license = trim(stripslashes($_POST[ self::FORM_NAME ]));

			if (empty($maxmind_license)) {
				$maxmind_license = '';
			} elseif (strlen($maxmind_license) > 20 || strlen($maxmind_license) < 7 || !ctype_graph($maxmind_license)) {
				return false;
			}

			$this->settings->apply_changes(array(
				'maxmind_license_key' => $maxmind_license
			));

			// update geoip in the backgronud
			wp_schedule_single_event( time() + 10, ScheduledTasks::EVENT_GEOIP );

			return true;
		}
	}

	public function show_settings() {
		$invalid_format = $this->update_if_submitted() === false;

		$current_maxmind_license = $this->settings->get_global_option('maxmind_license_key');

		include dirname( __FILE__ ) . '/views/geolocation_settings.php';
	}

}
