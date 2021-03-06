<?php
/**
 * PS Stats - kostenlose/freie Analyseplattform
 *
 * @link https://n3rds.work
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package psstats
 */

namespace WpPsstats\Admin;

use Piwik\Cache;
use Piwik\Option;
use Piwik\Plugins\SitesManager\API;
use WpPsstats\Access;
use WpPsstats\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

class AdminSettings {
	const TAB_TRACKING	= 'tracking';
	const TAB_ACCESS	  = 'access';
	const TAB_EXCLUSIONS  = 'exlusions';
	const TAB_PRIVACY	 = 'privacy';
	const TAB_GEOLOCATION = 'geolocation';
	const TAB_ADVANCED	= 'advanced';

	/**
	 * @var Settings
	 */
	private $settings;

	public function __construct( Settings $settings ) {
		$this->settings = $settings;
	}

	public static function make_url( $tab ) {
		global $_parent_pages;
		$menu_slug = Menu::SLUG_SETTINGS;

		if (is_multisite() && is_network_admin()) {
			if ( isset( $_parent_pages[$menu_slug] ) ) {
				$parent_slug = $_parent_pages[$menu_slug];
				if ( $parent_slug && ! isset( $_parent_pages[$parent_slug] ) ) {
					$url = network_admin_url( add_query_arg( 'page', $menu_slug, $parent_slug ) );
				} else {
					$url = network_admin_url( 'admin.php?page=' . $menu_slug );
				}
			} else {
				$url = '';
			}

			$url = esc_url( $url );
		} else {
			$url = menu_page_url( $menu_slug, false );
		}
		return add_query_arg( array( 'tab' => $tab ), $url );
	}

	public function show() {
		$access		     = new Access( $this->settings );
		$access_settings = new AccessSettings( $access, $this->settings );
		$tracking		 = new TrackingSettings( $this->settings );
		$exclusions	     = new ExclusionSettings( $this->settings );
		$geolocation	 = new GeolocationSettings( $this->settings );
		$privacy		 = new PrivacySettings( $this->settings );
		$advanced		 = new AdvancedSettings( $this->settings );
		$setting_tabs	 = array(
			self::TAB_TRACKING   => $tracking,
			self::TAB_ACCESS	 => $access_settings,
			self::TAB_PRIVACY	=> $privacy,
			self::TAB_EXCLUSIONS => $exclusions,
			self::TAB_GEOLOCATION => $geolocation,
			self::TAB_ADVANCED	=> $advanced,
		);

		$active_tab = self::TAB_TRACKING;

		if ($this->settings->is_network_enabled() && !is_network_admin()){
			$active_tab = self::TAB_EXCLUSIONS;
			$setting_tabs = array(
				self::TAB_EXCLUSIONS => $exclusions,
				self::TAB_PRIVACY	=> $privacy,
			);
		}

		$setting_tabs = apply_filters( 'psstats_setting_tabs', $setting_tabs, $this->settings );

		if ( ! empty( $_GET['tab'] ) && isset( $setting_tabs[ $_GET['tab'] ] ) ) {
			$active_tab = $_GET['tab'];
		}

		$content_tab = $setting_tabs[ $active_tab ];
		$psstats_settings = $this->settings;

		include dirname( __FILE__ ) . '/views/settings.php';
	}

}
