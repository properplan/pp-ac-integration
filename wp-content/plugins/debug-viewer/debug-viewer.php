<?php
/**
 * Plugin Name: Debug View Log
 * Description: Adds a simple debug log viewer to the WordPress admin.
 * Version: 1.2
 * Author: LP
 */

// Add admin menu for debug log viewer
add_action('admin_menu', function () {
    add_menu_page(
        'Debug Log Viewer',
        'Debug Log Viewer',
        'manage_options',
        'debug-log-viewer',
        'render_debug_log_viewer_page'
    );
});

// Render the debug log viewer page
function render_debug_log_viewer_page() {
    $log_file = WP_CONTENT_DIR . '/debug.log';
    $log_contents = file_exists($log_file) ? file_get_contents($log_file) : 'No log file found.';

    if (isset($_POST['clear_log']) && check_admin_referer('clear_debug_log')) {
        file_put_contents($log_file, '');
        $log_contents = 'The log file has been cleared.';
    }

    echo '<div class="wrap">';
    echo '<h1>Debug Log Viewer</h1>';
    echo '<form method="POST">';
    wp_nonce_field('clear_debug_log');
    echo '<pre style="white-space: pre-wrap; word-wrap: break-word; background: #fff; padding: 1rem; border: 1px solid #ccc;">' . esc_html($log_contents) . '</pre>';
    echo '<input type="submit" name="clear_log" value="Clear Log" class="button button-primary"/>';
    echo '</form>';
    echo '</div>';
}