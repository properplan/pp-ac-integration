<?php
namespace ProperPlan\ACEmail;
defined( 'ABSPATH' ) || exit;

function properplan_send_to_ac() {
    try {
        $post_id = isset($_POST['postId']) ? intval($_POST['postId']) : 0;
        error_log('AC Handler: Received post ID ' . $post_id);
        if ( ! $post_id ) wp_send_json_error(['message' => 'Missing post ID.']);

        $subject = get_post_meta( $post_id, '_ac_email_subject', true );
        $campaign_name = get_post_meta( $post_id, '_ac_campaign_name', true );
        $email_text = get_post_meta( $post_id, '_ac_email_text', true );
        if ( empty($subject) || empty($campaign_name) || empty($email_text) )
            wp_send_json_error(['message' => 'Missing fields.']);

        $client = \ProperPlan\ACEmail\get_activecampaign_client();
        if ( ! $client ) wp_send_json_error(['message' => 'AC client not initialized.']);
        error_log('AC Handler: AC client initialized');

        // Step 1: Create message
        $payload = [
            'message' => [
                'subject' => $subject,
                'fromEmail' => 'team@properplan.com',
                'fromName' => 'Laura P',
                'reply2' => 'team@properplan.com',
                'html' => $email_text,
                'text' => wp_strip_all_tags($email_text),
                'name' => $campaign_name,
            ],
        ];
        error_log('AC Handler: Message payload => ' . print_r($payload, true));

        $response = $client->request('POST', '/api/3/messages', ['body' => $payload]);
        error_log('AC Handler: Message response => ' . print_r($response, true));
        if ( is_wp_error( $response ) ) {
            error_log('AC Handler: Message request WP_Error - ' . $response->get_error_message());
            wp_send_json_error(['message' => 'Message API error: ' . $response->get_error_message()]);
        }
        $code = isset($response['code']) ? (int)$response['code'] : 0;
        error_log('AC Handler: Message creation response code ' . $code);
        if ($code < 200 || $code >= 300)
            wp_send_json_error(['message' => 'Failed to create message.', 'response' => $response]);

        $message_id = $response['body']['message']['id'] ?? '';

        // Step 2: Create campaign draft linked to message
        $campaign_payload = [
            'campaign' => [
                'name' => $campaign_name,
                'type' => 'single',
                'status' => 0, // draft
                'public' => 0,
                'tracklinks' => 'all',
                'trackreads' => 1,
                'htmlunsub' => 1,
                'p' => [ 3 => 3 ], // Updated to use correct list ID 3 (ProperPlan)
                'm' => [ $message_id => (int)$message_id ],
            ],
        ];
        error_log('AC Handler: Campaign payload => ' . print_r($campaign_payload, true));

        $campaign_response = $client->request('POST', '/api/3/campaigns', ['body' => $campaign_payload]);
        error_log('AC Handler: Campaign response => ' . print_r($campaign_response, true));
        if ( is_wp_error( $campaign_response ) ) {
            error_log('AC Handler: Campaign request WP_Error - ' . $campaign_response->get_error_message());
            wp_send_json_error(['message' => 'Campaign API error: ' . $campaign_response->get_error_message()]);
        }
        $camp_code = isset($campaign_response['code']) ? (int)$campaign_response['code'] : 0;
        error_log('AC Handler: Campaign creation response code ' . $camp_code);

        if ($camp_code >= 200 && $camp_code < 300) {
            $campaign_id = $campaign_response['body']['campaign']['id'] ?? '';
            $base_url = method_exists($client, 'get_api_url')
                ? untrailingslashit($client->get_api_url())
                : untrailingslashit(\ProperPlan\ACEmail\DEFAULT_API_URL);
            $campaign_url = $campaign_id ? esc_url($base_url . '/app/campaigns/' . $campaign_id) : '';

            wp_send_json_success([
                'message' => '✅ Email and campaign created successfully!',
                'message_id' => $message_id,
                'campaign_id' => $campaign_id,
                'campaign_url' => $campaign_url,
                'response' => $campaign_response,
            ]);
        }

        wp_send_json_error(['message' => 'Failed to create campaign.', 'response' => $campaign_response]);
    } catch ( \Throwable $e ) {
        error_log( 'AC Handler Exception: ' . $e->getMessage() );
        wp_send_json_error(['message' => 'Internal plugin error: ' . $e->getMessage()]);
    }
}

add_action('wp_ajax_properplan_send_to_ac', __NAMESPACE__ . '\\properplan_send_to_ac');
add_action('wp_ajax_nopriv_properplan_send_to_ac', __NAMESPACE__ . '\\properplan_send_to_ac');