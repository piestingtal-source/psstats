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

class PrivacyBadge {

	public function register_hooks() {
		add_shortcode( 'psstats_privacy_badge', array( $this, 'show_privacy_page' ) );
	}

	public function show_privacy_page( $atts ) {
		$a = shortcode_atts(
			array(
				'size'  => '120',
				'align' => '',
			),
			$atts
		);

		$option = sprintf( ' width="%s" height="%s"', esc_attr( $a['size'] ), esc_attr( $a['size'] ) );

		if ( ! empty( $a['align'] ) ) {
			$option .= sprintf( ' align="%s"', esc_attr( $a['align'] ) );
		}

		$url = plugins_url( 'assets/img/privacybadge.png', PSSTATS_ANALYTICS_FILE );

		$title = __( 'Deine Privatsphäre geschützt! Diese Webseite verwendet Psstats.', 'psstats' );

		return sprintf( '<img alt="%s" src="%s" %s>', $title, esc_attr( $url ), $option );
	}

}
