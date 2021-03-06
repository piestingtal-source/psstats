<?php
/**
 * PS Stats - kostenlose/freie Analyseplattform
 *
 * @link https://n3rds.work
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package psstats
 */

namespace WpPsstats;

use Piwik\Piwik;
use Piwik\Plugins\PrivacyManager\DoNotTrackHeaderChecker;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

class OptOut {

	private $language = null;

	public function register_hooks() {
		add_shortcode( 'psstats_opt_out', array( $this, 'show_opt_out' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' )  );
	}

	public function load_scripts() {
		if (!is_admin()) {
			wp_register_script( 'psstats_opt_out_js', plugins_url( 'assets/js/optout.js', PSSTATS_ANALYTICS_FILE ), array(), null, true );
		}
	}
	
	private function translate($id)
	{
		return esc_html(Piwik::translate($id, array(), $this->language));
	}

	public function show_opt_out( $atts ) {
		$a = shortcode_atts(
			array(
				'language' => null,
			),
			$atts
		);
		if (!empty($a['language']) && strlen($a['language']) < 6) {
			$this->language = $a['language'];
		}

		try {
			Bootstrap::do_bootstrap();
		} catch (\Throwable $e ) {
			$logger = new Logger();
			$logger->log_exception('optout', $e);
			return '<p>Ein Fehler ist aufgetreten. Bitte überprüfe den Psstats-Systembericht in WP-Admin.</p>';
		}

		$dnt_checker = new DoNotTrackHeaderChecker();
		$dnt_enabled = $dnt_checker->isDoNotTrackFound();

		if (!empty($dnt_enabled)) {
			return '<p>'. $this->translate('CoreAdminHome_OptOutDntFound').'</p>';
		}

		wp_enqueue_script( 'psstats_opt_out_js' );

		$track_visits = empty($_COOKIE['mtm_consent_removed']);

		$style_tracking_enabled = '';
		$style_tracking_disabled = '';
		$checkbox_attr = '';
		if ($track_visits) {
			$style_tracking_enabled = 'style="display:none;"';
			$checkbox_attr = 'checked="checked"';
		} else {
			$style_tracking_disabled = 'style="display:none;"';
		}

		$content = '<p id="psstats_opted_out_intro" ' . $style_tracking_enabled . '>' . $this->translate('CoreAdminHome_OptOutComplete') . ' '  . $this->translate('CoreAdminHome_OptOutCompleteBis') . '</p>';
		$content .= '<p id="psstats_opted_in_intro" ' .$style_tracking_disabled . '>' . $this->translate('CoreAdminHome_YouMayOptOut2') . ' ' . $this->translate('CoreAdminHome_YouMayOptOut3') . '</p>';

		$content .= '<form>
        <input type="checkbox" id="psstats_optout_checkbox" '.$checkbox_attr.'/>
        <label for="psstats_optout_checkbox"><strong>
        <span id="psstats_opted_in_label" '.$style_tracking_disabled.'>'.$this->translate('CoreAdminHome_YouAreNotOptedOut') .' ' . $this->translate('CoreAdminHome_UncheckToOptOut') . '</span>
		<span id="psstats_opted_out_label" '.$style_tracking_enabled.'>'.$this->translate('CoreAdminHome_YouAreOptedOut') .' ' . $this->translate('CoreAdminHome_CheckToOptIn') . '</span>
        </strong></label></form>';
		$content .= '<noscript><p><strong style="color: #ff0000;">Diese Opt-Out-Funktion erfordert JavaScript.</strong></p></noscript>';
		$content .= '<p id="psstats_outout_err_cookies" style="display: none;"><strong>' . $this->translate('CoreAdminHome_OptOutErrorNoCookies') . '</strong></p>';
		return $content;
	}

}
