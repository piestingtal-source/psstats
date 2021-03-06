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
use WpPsstats\Settings;
use WpPsstats\Site;
use WpPsstats\Site\Sync\SyncConfig as SiteConfigSync;
use WpPsstats\TrackingCode\TrackingCodeGenerator;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

class TrackingSettings implements AdminSettingsInterface {
	const FORM_NAME             = 'psstats';
	const NONCE_NAME            = 'psstats_settings';
	const TRACK_MODE_DEFAULT    = 'default';
	const TRACK_MODE_DISABLED   = 'disabled';
	const TRACK_MODE_MANUALLY   = 'manually';
	const TRACK_MODE_TAGMANAGER = 'tagmanager';

	/**
	 * @var Settings
	 */
	private $settings;

	/**
	 * @param Settings $settings
	 */
	public function __construct( $settings ) {
		$this->settings = $settings;
	}

	public function get_title() {
		return esc_html__( 'Tracking', 'psstats' );
	}

	private function update_if_submitted() {
		if ( isset( $_POST )
			 && ! empty( $_POST[ self::FORM_NAME ] )
			 && is_admin()
			 && check_admin_referer( self::NONCE_NAME )
			 && $this->can_user_manage() ) {
			$this->apply_settings();

			return true;
		}

		return false;
	}

	public function can_user_manage() {
		return current_user_can( Capabilities::KEY_SUPERUSER );
	}

	private function apply_settings() {
		$keys_to_keep = array(
			'track_mode',
			'track_across',
			'track_across_alias',
			'track_crossdomain_linking',
			'track_feed',
			'track_feed_addcampaign',
			'track_feed_campaign',
			'track_heartbeat',
			'track_user_id',
			'track_datacfasync',
			'tagmanger_container_ids',
			'set_download_extensions',
			'set_download_classes',
			'set_link_classes',
			'track_admin',
			'limit_cookies_referral',
			'limit_cookies_session',
			'limit_cookies_visitor',
			'limit_cookies',
			'force_post',
			'disable_cookies',
			'add_download_extensions',
			'track_404',
			'track_search',
			'add_post_annotations',
			'track_content',
			'track_ecommerce',
			'track_noscript',
			'noscript_code',
			'track_codeposition',
			'tracking_code',
			'force_protocol',
			'track_js_endpoint',
			'track_jserrors',
			'track_api_endpoint',
			Settings::SITE_CURRENCY
		);

		if ( psstats_has_tag_manager() ) {
			$keys_to_keep[] = 'tagmanger_container_ids';
		}

		$values = array();

		// default value in case no role/ post type is selected to make sure we unset it if no role /post type is selected
		$values['add_post_annotations']    = array();
		$values['tagmanger_container_ids'] = array();

		$valid_currencies = $this->get_supported_currencies();

		if ( !empty( $_POST[ self::FORM_NAME ]['tracker_debug'] ) ) {
			$site_config_sync = new SiteConfigSync( $this->settings );
			switch ($_POST[ self::FORM_NAME ]['tracker_debug']) {
				case 'always':
					$site_config_sync->set_config_value('Tracker', 'debug', 1);
					$site_config_sync->set_config_value('Tracker', 'debug_on_demand', 0);
					break;
				case 'on_demand':
					$site_config_sync->set_config_value('Tracker', 'debug', 0);
					$site_config_sync->set_config_value('Tracker', 'debug_on_demand', 1);
					break;
				default:
					$site_config_sync->set_config_value('Tracker', 'debug', 0);
					$site_config_sync->set_config_value('Tracker', 'debug_on_demand', 0);
			}
		}

		if ( empty( $_POST[ self::FORM_NAME ][Settings::SITE_CURRENCY] )
		     || !array_key_exists( $_POST[ self::FORM_NAME ][Settings::SITE_CURRENCY], $valid_currencies ) ) {
			$_POST[ self::FORM_NAME ][Settings::SITE_CURRENCY] = 'USD';
		}

		if ( ! empty( $_POST[ self::FORM_NAME ]['track_mode'] ) ) {
			$track_mode         = $_POST[ self::FORM_NAME ]['track_mode'];
			$previus_track_mode = $this->settings->get_global_option( 'track_mode' );

			if ( self::TRACK_MODE_TAGMANAGER === $track_mode ) {
				// no noscript mode in this case
				$_POST[ self::FORM_NAME ]['track_noscript'] = '';
				$_POST[ self::FORM_NAME ]['noscript_code']  = '';
			} else {
				unset( $_POST['tagmanger_container_ids'] );
			}

			if ( self::TRACK_MODE_MANUALLY === $track_mode
				 || ( self::TRACK_MODE_DISABLED === $track_mode &&
					  in_array( $previus_track_mode, array( self::TRACK_MODE_DISABLED, self::TRACK_MODE_MANUALLY ) ) ) ) {
				// We want to keep the tracking code when user switches between disabled and manually or disabled to disabled.
				if ( ! empty( $_POST[ self::FORM_NAME ]['tracking_code'] ) ) {
					$_POST[ self::FORM_NAME ]['tracking_code'] = stripslashes( $_POST[ self::FORM_NAME ]['tracking_code'] );
				} else {
					$_POST[ self::FORM_NAME ]['tracking_code'] = '';
				}
				if ( ! empty( $_POST[ self::FORM_NAME ]['noscript_code'] ) ) {
					$_POST[ self::FORM_NAME ]['noscript_code'] = stripslashes( $_POST[ self::FORM_NAME ]['noscript_code'] );
				} else {
					$_POST[ self::FORM_NAME ]['noscript_code'] = '';
				}
			} else {
				$_POST[ self::FORM_NAME ]['noscript_code'] = '';
				$_POST[ self::FORM_NAME ]['tracking_code'] = '';
			}
		}

		foreach ( $_POST[ self::FORM_NAME ] as $name => $value ) {
			if ( in_array( $name, $keys_to_keep, true ) ) {
				$values[ $name ] = $value;
			}
		}

		$this->settings->apply_tracking_related_changes( $values );

		return true;
	}

	public function show_settings() {
		$was_updated = $this->update_if_submitted();
		$settings    = $this->settings;

		$containers = $this->get_active_containers();

		$track_modes = array(
			self::TRACK_MODE_DISABLED => esc_html__( 'Disabled', 'psstats' ),
			self::TRACK_MODE_DEFAULT  => esc_html__( 'Default tracking', 'psstats' ),
			self::TRACK_MODE_MANUALLY => esc_html__( 'Enter manually', 'psstats' ),
		);

		if ( ! empty( $containers ) ) {
			$track_modes[ self::TRACK_MODE_TAGMANAGER ] = esc_html__( 'Tag Manager', 'psstats' );
		}

		$site   = new Site();
		$idsite = $site->get_current_psstats_site_id();

		$psstats_currencies = $this->get_supported_currencies();

		$tracking_code_generator      = new TrackingCodeGenerator( $this->settings );
		$psstats_default_tracking_code = $tracking_code_generator->prepare_tracking_code( $idsite );

		include dirname( __FILE__ ) . '/views/tracking.php';
	}

	private function get_supported_currencies()
	{
		$all = include dirname( PSSTATS_ANALYTICS_FILE )  . '/app/core/Intl/Data/Resources/currencies.php';
		$currencies = array();
		foreach ($all as $key => $single) {
			$currencies[$key] = $single[0] . ' ' . $single[1];
		}
		return $currencies;
	}

	public function get_active_containers() {
		// we don't use Psstats API here to avoid needing to bootstrap Psstats which is slow and could break things
		$containers = array();
		if ( psstats_has_tag_manager() ) {
			global $wpdb;
			$dbsettings      = new \WpPsstats\Db\Settings();
			$container_table = $dbsettings->prefix_table_name( 'tagmanager_container' );
			try {
				$containers = $wpdb->get_results( sprintf( 'SELECT `idcontainer`, `name` FROM %s where `status` = "active"', $container_table ) );
			} catch ( \Exception $e ) {
				// table may not exist yet etc
				$containers = array();
			}
		}
		$by_id = array();
		foreach ( $containers as $container ) {
			$by_id[ $container->idcontainer ] = $container->name;
		}

		return $by_id;
	}


}
