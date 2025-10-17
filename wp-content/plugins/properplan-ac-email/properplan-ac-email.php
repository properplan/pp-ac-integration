<?php
/**
 * Plugin Name:       ProperPlan AC Email
 * Plugin URI:        https://properplan.com
 * Description:       Provides ProperPlan-specific email functionality for the AC integration.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            ProperPlan
 * Author URI:        https://properplan.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       properplan-ac-email
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

require_once __DIR__ . '/includes/activecampaign-api.php';
require_once __DIR__ . '/includes/admin-page.php';

/**
 * Initialize the ProperPlan AC Email plugin.
 *
 * Currently this plugin only confirms activation and prepares the namespace
 * for future development.
 *
 * @return void
 */
function properplan_ac_email_init() {
        do_action( 'properplan_ac_email_initialized' );
}
add_action( 'plugins_loaded', 'properplan_ac_email_init' );
