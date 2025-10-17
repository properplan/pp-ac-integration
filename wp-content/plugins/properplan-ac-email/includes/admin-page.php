<?php
/**
 * Admin settings page for the ProperPlan AC Email plugin.
 *
 * @package ProperPlan\ACEmail
 */

namespace ProperPlan\ACEmail;

defined( 'ABSPATH' ) || exit;

const TEST_API_URL = 'https://properapptest.api-us1.com';
const TEST_API_KEY = '833479e3809a6b0e53dd8e8d64386bec3c13d934e7bc7541b034489e126846a35430911e';

/**
 * Register admin page hooks.
 *
 * @return void
 */
function register_admin_page() {
        add_action( 'admin_menu', __NAMESPACE__ . '\\add_tools_menu_item' );
        add_action( 'admin_post_properplan_ac_email_save_settings', __NAMESPACE__ . '\\handle_save_settings' );
        add_action( 'admin_post_properplan_ac_email_test_connection', __NAMESPACE__ . '\\handle_test_connection' );
}

/**
 * Add the ProperPlan AC Email page under the Tools menu.
 *
 * @return void
 */
function add_tools_menu_item() {
        add_management_page(
                __( 'ProperPlan x ActiveCampaign', 'properplan-ac-email' ),
                __( 'PP x AC API', 'properplan-ac-email' ),
                'manage_options',
                'properplan-ac-email',
                __NAMESPACE__ . '\\render_settings_page'
        );
}

/**
 * Render the plugin settings page.
 *
 * @return void
 */
function render_settings_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
                return;
        }

        $saved_url = get_option( 'properplan_ac_email_ac_url', '' );
        $saved_key = get_option( 'properplan_ac_email_ac_key', '' );

        $test_result  = isset( $_GET['test_result'] ) ? sanitize_text_field( wp_unslash( $_GET['test_result'] ) ) : '';
        $test_message = isset( $_GET['test_message'] ) ? sanitize_text_field( rawurldecode( wp_unslash( $_GET['test_message'] ) ) ) : '';
        $settings_updated = isset( $_GET['settings-updated'] );

        ?>
        <div class="wrap">
                <h1><?php esc_html_e( 'ProperPlan x ActiveCampaign API Settings', 'properplan-ac-email' ); ?></h1>

                <?php if ( $settings_updated ) : ?>
                        <div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Credentials saved.', 'properplan-ac-email' ); ?></p></div>
                <?php endif; ?>

                <?php if ( 'success' === $test_result ) : ?>
                        <div class="notice notice-success is-dismissible"><p><?php echo esc_html( $test_message ); ?></p></div>
                <?php elseif ( 'error' === $test_result ) : ?>
                        <div class="notice notice-error is-dismissible"><p><?php echo esc_html( $test_message ); ?></p></div>
                <?php endif; ?>

                <h2><?php esc_html_e( 'Stored Credentials', 'properplan-ac-email' ); ?></h2>
                <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                        <?php wp_nonce_field( 'properplan_ac_email_save_settings', '_properplan_ac_email_nonce' ); ?>
                        <input type="hidden" name="action" value="properplan_ac_email_save_settings" />

                        <table class="form-table" role="presentation">
                                <tbody>
                                        <tr>
                                                <th scope="row"><label for="properplan_ac_email_ac_url"><?php esc_html_e( 'API URL', 'properplan-ac-email' ); ?></label></th>
                                                <td>
                                                        <input name="properplan_ac_email_ac_url" type="url" id="properplan_ac_email_ac_url" value="<?php echo esc_attr( $saved_url ); ?>" class="regular-text" placeholder="https://example.api-us1.com" required />
                                                        <p class="description"><?php esc_html_e( 'Enter the base URL of your ActiveCampaign instance.', 'properplan-ac-email' ); ?></p>
                                                </td>
                                        </tr>
                                        <tr>
                                                <th scope="row"><label for="properplan_ac_email_ac_key"><?php esc_html_e( 'API Key', 'properplan-ac-email' ); ?></label></th>
                                                <td>
                                                        <input name="properplan_ac_email_ac_key" type="text" id="properplan_ac_email_ac_key" value="<?php echo esc_attr( $saved_key ); ?>" class="regular-text" required />
                                                        <p class="description"><?php esc_html_e( 'Paste the API key generated in ActiveCampaign.', 'properplan-ac-email' ); ?></p>
                                                </td>
                                        </tr>
                                </tbody>
                        </table>

                        <?php submit_button( __( 'Save Credentials', 'properplan-ac-email' ) ); ?>
                </form>

                <h2><?php esc_html_e( 'Test Connection', 'properplan-ac-email' ); ?></h2>
                <p><?php esc_html_e( 'Use the fields below to verify that the credentials can connect to ActiveCampaign without saving them.', 'properplan-ac-email' ); ?></p>
                <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                        <?php wp_nonce_field( 'properplan_ac_email_test_connection', '_properplan_ac_email_test_nonce' ); ?>
                        <input type="hidden" name="action" value="properplan_ac_email_test_connection" />

                        <table class="form-table" role="presentation">
                                <tbody>
                                        <tr>
                                                <th scope="row"><label for="properplan_ac_email_test_url"><?php esc_html_e( 'API URL', 'properplan-ac-email' ); ?></label></th>
                                                <td>
                                                        <input name="properplan_ac_email_test_url" type="url" id="properplan_ac_email_test_url" value="<?php echo esc_attr( $saved_url ? $saved_url : TEST_API_URL ); ?>" class="regular-text" placeholder="https://example.api-us1.com" required />
                                                </td>
                                        </tr>
                                        <tr>
                                                <th scope="row"><label for="properplan_ac_email_test_key"><?php esc_html_e( 'API Key', 'properplan-ac-email' ); ?></label></th>
                                                <td>
                                                        <input name="properplan_ac_email_test_key" type="text" id="properplan_ac_email_test_key" value="<?php echo esc_attr( $saved_key ? $saved_key : TEST_API_KEY ); ?>" class="regular-text" required />
                                                </td>
                                        </tr>
                                </tbody>
                        </table>

                        <?php submit_button( __( 'Test Connection', 'properplan-ac-email' ), 'secondary' ); ?>
                </form>

                <h2><?php esc_html_e( 'Shared Test Credentials', 'properplan-ac-email' ); ?></h2>
                <p><?php esc_html_e( 'Use the following sandbox credentials for quick integration testing.', 'properplan-ac-email' ); ?></p>
                <table class="widefat" style="max-width: 640px;">
                        <tbody>
                                <tr>
                                        <th scope="row"><?php esc_html_e( 'API URL', 'properplan-ac-email' ); ?></th>
                                        <td><code><?php echo esc_html( TEST_API_URL ); ?></code></td>
                                </tr>
                                <tr>
                                        <th scope="row"><?php esc_html_e( 'API Key', 'properplan-ac-email' ); ?></th>
                                        <td><code><?php echo esc_html( TEST_API_KEY ); ?></code></td>
                                </tr>
                        </tbody>
                </table>
        </div>
        <?php
}

/**
 * Handle saving of credentials from the admin page.
 *
 * @return void
 */
function handle_save_settings() {
        if ( ! current_user_can( 'manage_options' ) ) {
                wp_die( esc_html__( 'You do not have permission to manage these settings.', 'properplan-ac-email' ) );
        }

        check_admin_referer( 'properplan_ac_email_save_settings', '_properplan_ac_email_nonce' );

        $api_url = isset( $_POST['properplan_ac_email_ac_url'] ) ? esc_url_raw( wp_unslash( $_POST['properplan_ac_email_ac_url'] ) ) : '';
        $api_key = isset( $_POST['properplan_ac_email_ac_key'] ) ? sanitize_text_field( wp_unslash( $_POST['properplan_ac_email_ac_key'] ) ) : '';

        update_option( 'properplan_ac_email_ac_url', $api_url );
        update_option( 'properplan_ac_email_ac_key', $api_key );

        wp_safe_redirect(
                add_query_arg(
                        array(
                                'page'              => 'properplan-ac-email',
                                'settings-updated'  => 'true',
                        ),
                        admin_url( 'tools.php' )
                )
        );
        exit;
}

/**
 * Handle testing the ActiveCampaign connection.
 *
 * @return void
 */
function handle_test_connection() {
        if ( ! current_user_can( 'manage_options' ) ) {
                wp_die( esc_html__( 'You do not have permission to test the connection.', 'properplan-ac-email' ) );
        }

        check_admin_referer( 'properplan_ac_email_test_connection', '_properplan_ac_email_test_nonce' );

        $api_url = isset( $_POST['properplan_ac_email_test_url'] ) ? esc_url_raw( wp_unslash( $_POST['properplan_ac_email_test_url'] ) ) : '';
        $api_key = isset( $_POST['properplan_ac_email_test_key'] ) ? sanitize_text_field( wp_unslash( $_POST['properplan_ac_email_test_key'] ) ) : '';

        $result       = test_connection( $api_url, $api_key );
        $redirect_url = add_query_arg( array( 'page' => 'properplan-ac-email' ), admin_url( 'tools.php' ) );

        if ( is_wp_error( $result ) ) {
                $redirect_url = add_query_arg(
                        array(
                                'test_result'  => 'error',
                                'test_message' => rawurlencode( $result->get_error_message() ),
                        ),
                        $redirect_url
                );
        } else {
                $success_message = __( 'Connection successful!', 'properplan-ac-email' );

                if ( isset( $result['body']['user'] ) && is_array( $result['body']['user'] ) ) {
                        $user = $result['body']['user'];

                        if ( ! empty( $user['email'] ) ) {
                                $success_message = sprintf(
                                        /* translators: %s: ActiveCampaign user email address. */
                                        __( 'Connection successful! Authenticated as %s.', 'properplan-ac-email' ),
                                        $user['email']
                                );
                        }
                }

                $redirect_url = add_query_arg(
                        array(
                                'test_result'  => 'success',
                                'test_message' => rawurlencode( $success_message ),
                        ),
                        $redirect_url
                );
        }

        wp_safe_redirect( $redirect_url );
        exit;
}

register_admin_page();
