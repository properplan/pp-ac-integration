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

/**
 * Register the custom post type for AC Emails.
 */
function properplan_register_ac_email_post_type() {
        $labels = array(
                'name'               => _x( 'AC Emails', 'post type general name', 'properplan-ac-email' ),
                'singular_name'      => _x( 'AC Email', 'post type singular name', 'properplan-ac-email' ),
                'menu_name'          => _x( 'AC Emails', 'admin menu', 'properplan-ac-email' ),
                'name_admin_bar'     => _x( 'AC Email', 'add new on admin bar', 'properplan-ac-email' ),
                'add_new'            => _x( 'Add New', 'ac_email', 'properplan-ac-email' ),
                'add_new_item'       => __( 'Add New AC Email', 'properplan-ac-email' ),
                'new_item'           => __( 'New AC Email', 'properplan-ac-email' ),
                'edit_item'          => __( 'Edit AC Email', 'properplan-ac-email' ),
                'view_item'          => __( 'View AC Email', 'properplan-ac-email' ),
                'all_items'          => __( 'All AC Emails', 'properplan-ac-email' ),
                'search_items'       => __( 'Search AC Emails', 'properplan-ac-email' ),
                'parent_item_colon'  => __( 'Parent AC Emails:', 'properplan-ac-email' ),
                'not_found'          => __( 'No AC Emails found.', 'properplan-ac-email' ),
                'not_found_in_trash' => __( 'No AC Emails found in Trash.', 'properplan-ac-email' )
        );

        $args = array(
                'labels'             => $labels,
                'public'             => true,
                'show_ui'            => true,
                'show_in_menu'       => true,
                'capability_type'    => 'post',
                'hierarchical'       => false,
                'supports'           => array( 'title', 'editor' ),
                'has_archive'        => true,
                'rewrite'            => array( 'slug' => 'ac-email' ),
                'show_in_rest'       => true,
                'menu_position'      => 25,
                'menu_icon'          => 'dashicons-email-alt',
        );

        register_post_type( 'ac_email', $args );
}
add_action( 'init', 'properplan_register_ac_email_post_type' );

/**
 * Add meta box for AC Email details.
 */
function properplan_ac_email_add_meta_box() {
        add_meta_box(
                'ac_email_details',
                __( 'AC Email Details', 'properplan-ac-email' ),
                'properplan_ac_email_meta_box_callback',
                'ac_email',
                'normal',
                'default'
        );
}
add_action( 'add_meta_boxes', 'properplan_ac_email_add_meta_box' );

/**
 * Meta box callback for AC Email fields.
 */
function properplan_ac_email_meta_box_callback( $post ) {
        wp_nonce_field( 'properplan_ac_email_save_meta_box', 'properplan_ac_email_meta_box_nonce' );
        $subject = get_post_meta( $post->ID, '_ac_email_subject', true );
        $campaign_name = get_post_meta( $post->ID, '_ac_campaign_name', true );
        $email_text = get_post_meta( $post->ID, '_ac_email_text', true );
        ?>
        <p>
                <label for="ac_email_subject"><?php esc_html_e( 'Subject', 'properplan-ac-email' ); ?></label><br>
                <input type="text" id="ac_email_subject" name="ac_email_subject" value="<?php echo esc_attr( $subject ); ?>" class="widefat">
        </p>
        <p>
                <label for="ac_campaign_name"><?php esc_html_e( 'Campaign Name', 'properplan-ac-email' ); ?></label><br>
                <input type="text" id="ac_campaign_name" name="ac_campaign_name" value="<?php echo esc_attr( $campaign_name ); ?>" class="widefat">
        </p>
        <p>
                <label for="ac_email_text"><?php esc_html_e( 'Email Text', 'properplan-ac-email' ); ?></label><br>
                <textarea id="ac_email_text" name="ac_email_text" rows="5" class="widefat"><?php echo esc_textarea( $email_text ); ?></textarea>
        </p>
        <p>
                <button type="button" id="send-to-ac-button" class="button button-primary">
                        <?php esc_html_e( 'Send to AC', 'properplan-ac-email' ); ?>
                </button>
        </p>
        <?php
}

/**
 * Save meta box data for AC Email.
 */
function properplan_ac_email_save_meta_box( $post_id ) {
        if ( ! isset( $_POST['properplan_ac_email_meta_box_nonce'] ) ) {
                return;
        }
        if ( ! wp_verify_nonce( $_POST['properplan_ac_email_meta_box_nonce'], 'properplan_ac_email_save_meta_box' ) ) {
                return;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
                return;
        }
        if ( isset( $_POST['post_type'] ) && 'ac_email' === $_POST['post_type'] ) {
                if ( ! current_user_can( 'edit_post', $post_id ) ) {
                        return;
                }
        }
        if ( isset( $_POST['ac_email_subject'] ) ) {
                update_post_meta( $post_id, '_ac_email_subject', sanitize_text_field( $_POST['ac_email_subject'] ) );
        }
        if ( isset( $_POST['ac_campaign_name'] ) ) {
                update_post_meta( $post_id, '_ac_campaign_name', sanitize_text_field( $_POST['ac_campaign_name'] ) );
        }
        if ( isset( $_POST['ac_email_text'] ) ) {
                update_post_meta( $post_id, '_ac_email_text', wp_kses_post( $_POST['ac_email_text'] ) );
        }
}
add_action( 'save_post', 'properplan_ac_email_save_meta_box' );

/**
 * Helper: Get AC Email subject.
 */
function get_ac_email_subject( $post_id ) {
        return get_post_meta( $post_id, '_ac_email_subject', true );
}

/**
 * Helper: Get AC Email campaign name.
 */
function get_ac_campaign_name( $post_id ) {
        return get_post_meta( $post_id, '_ac_campaign_name', true );
}

/**
 * Helper: Get AC Email text.
 */
function get_ac_email_text( $post_id ) {
        return get_post_meta( $post_id, '_ac_email_text', true );
}

/**
 * Display AC Email custom fields on the frontend for ac_email post type.
 */
function properplan_ac_email_display_fields( $content ) {
        if ( is_singular( 'ac_email' ) && in_the_loop() && is_main_query() ) {
                global $post;
                $subject = get_post_meta( $post->ID, '_ac_email_subject', true );
                $campaign_name = get_post_meta( $post->ID, '_ac_campaign_name', true );
                $email_text = get_post_meta( $post->ID, '_ac_email_text', true );

                $fields_html = '<div class="ac-email-fields">';
                if ( ! empty( $subject ) ) {
                        $fields_html .= '<p><strong>' . esc_html__( 'Subject', 'properplan-ac-email' ) . ':</strong> ' . esc_html( $subject ) . '</p>';
                }
                if ( ! empty( $campaign_name ) ) {
                        $fields_html .= '<p><strong>' . esc_html__( 'Campaign Name', 'properplan-ac-email' ) . ':</strong> ' . esc_html( $campaign_name ) . '</p>';
                }
                if ( ! empty( $email_text ) ) {
                        $fields_html .= '<div class="ac-email-text"><strong>' . esc_html__( 'Email Text', 'properplan-ac-email' ) . ':</strong><br>' . wpautop( esc_html( $email_text ) ) . '</div>';
                }
                // Add frontend Send to AC button after the fields, outside of admin context.
                $fields_html .= '<p><button id="frontend-send-to-ac" class="button button-primary" data-post-id="' . esc_attr( get_the_ID() ) . '">Send to AC</button></p>';
                $fields_html .= '</div>';

                return $content . $fields_html;
        }
        return $content;
}
add_filter( 'the_content', 'properplan_ac_email_display_fields' );

/**
 * Enqueue frontend CSS for AC Email fields.
 */

function properplan_ac_email_enqueue_styles() {
        if ( is_singular( 'ac_email' ) ) {
                wp_add_inline_style(
                        'wp-block-library',
                        '.ac-email-fields { background: #f7f7f7; padding: 1.5em; border-radius: 8px; margin: 2em 0 1em 0; }' .
                        '.ac-email-fields p { margin: 0 0 1em 0; }' .
                        '.ac-email-text { background: #fff; padding: 1em; border-radius: 6px; margin-top: 0.5em; }'
                );
        }
}
add_action( 'wp_enqueue_scripts', 'properplan_ac_email_enqueue_styles' );

/**
 * Enqueue frontend JS for AC Email fields.
 */
function properplan_ac_email_enqueue_scripts() {
        if ( is_singular( 'ac_email' ) ) {
                wp_enqueue_script(
                        'properplan-ac-email-frontend-js',
                        plugin_dir_url( __FILE__ ) . 'js/send-to-ac.js',
                        array(),
                        '1.0.0',
                        true
                );
                wp_localize_script(
                        'properplan-ac-email-frontend-js',
                        'properplan_ac_email',
                        array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) )
                );
        }
}
add_action( 'wp_enqueue_scripts', 'properplan_ac_email_enqueue_scripts' );

require_once __DIR__ . '/includes/activecampaign-api.php';
require_once __DIR__ . '/includes/admin-page.php';
require_once __DIR__ . '/includes/send-to-ac-handler.php';

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
