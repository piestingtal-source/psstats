<?php
/**
 * PS Stats - kostenlose/freie Analyseplattform
 *
 * @link https://n3rds.work
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package psstats
 */

namespace WpPsstats\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

interface AdminSettingsInterface {
	public function get_title();

	public function show_settings();
}
