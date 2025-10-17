<?php
/**
 * ActiveCampaign API helpers for ProperPlan AC Email.
 *
 * @package ProperPlan\ACEmail
 */

namespace ProperPlan\ACEmail;

defined( 'ABSPATH' ) || exit;

/**
 * Default API URL for the ProperPlan ActiveCampaign test account.
 */
const DEFAULT_API_URL = 'https://properapptest.api-us1.com';

/**
 * Default API key for the ProperPlan ActiveCampaign test account.
 */
const DEFAULT_API_KEY = '833479e3809a6b0e53dd8e8d64386bec3c13d934e7bc7541b034489e126846a35430911e';

/**
 * ActiveCampaign API client wrapper.
 */
class ActiveCampaign_API {
	/**
	 * Base API URL.
	 *
	 * @var string
	 */
	protected $api_url;

	/**
	 * ActiveCampaign API key.
	 *
	 * @var string
	 */
	protected $api_key;

	/**
	 * Constructor.
	 *
	 * @param string $api_url Base API URL provided by ActiveCampaign.
	 * @param string $api_key API token used for authentication.
	 */
	public function __construct( $api_url, $api_key ) {
		$this->api_url = untrailingslashit( $api_url );
		$this->api_key = $api_key;
	}

	/**
	 * Perform an API request against ActiveCampaign.
	 *
	 * @param string $method HTTP method (GET, POST, etc.).
	 * @param string $path   Request path relative to the API base URL.
	 * @param array  $args   Optional request arguments passed to wp_remote_request().
	 *
	 * @return array|\WP_Error Associative array with response data or WP_Error on failure.
	 */
	public function request( $method, $path, $args = array() ) {
		$url = $this->api_url . '/' . ltrim( $path, '/' );

		$request_args = wp_parse_args(
			array(
				'method'  => strtoupper( $method ),
				'headers' => array(
					'Api-Token'    => $this->api_key,
					'Content-Type' => 'application/json',
				),
			),
			$args
		);

		if ( isset( $request_args['body'] ) && is_array( $request_args['body'] ) ) {
			$request_args['body'] = wp_json_encode( $request_args['body'] );
		}

		$response = wp_remote_request( $url, $request_args );
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code    = (int) wp_remote_retrieve_response_code( $response );
		$body    = json_decode( wp_remote_retrieve_body( $response ), true );
		$headers = wp_remote_retrieve_headers( $response );

		if ( $code >= 400 ) {
			return new \WP_Error(
				'properplan_ac_email_api_error',
				sprintf(
					'ActiveCampaign API request to %1$s failed with status code %2$d.',
					$path,
					$code
				),
				array(
					'code'     => $code,
					'body'     => $body,
					'headers'  => $headers,
					'endpoint' => $url,
				)
			);
		}

		return array(
			'code'    => $code,
			'body'    => $body,
			'headers' => $headers,
		);
	}
}

/**
 * Retrieve ActiveCampaign credentials stored in WordPress options.
 * Falls back to the shared test credentials when options are empty so the
 * plugin can communicate with the sandbox ActiveCampaign account out of the
 * box.
 *
 * @return array{url:string,key:string}|null Array with url and key, or null when incomplete.
 */
function get_api_credentials() {
    $api_url = get_option( 'properplan_ac_email_ac_url', '' );
    $api_key = get_option( 'properplan_ac_email_ac_key', '' );

    if ( empty( $api_url ) ) {
        $api_url = DEFAULT_API_URL;
    }

    if ( empty( $api_key ) ) {
        $api_key = DEFAULT_API_KEY;
    }

    if ( empty( $api_url ) || empty( $api_key ) ) {
        return null;
    }

    return array(
        'url' => $api_url,
        'key' => $api_key,
    );
}

/**
 * Instantiate a new ActiveCampaign API client using the stored credentials.
 *
 * @return ActiveCampaign_API|null
 */
function get_activecampaign_client() {
        $credentials = get_api_credentials();

        if ( null === $credentials ) {
                return null;
        }

        return new ActiveCampaign_API( $credentials['url'], $credentials['key'] );
}

/**
 * Test the connection to ActiveCampaign with the provided credentials.
 *
 * @param string $api_url API base URL.
 * @param string $api_key API token.
 *
 * @return array|\WP_Error Response data on success or WP_Error on failure.
 */
function test_connection( $api_url, $api_key ) {
        if ( empty( $api_url ) || empty( $api_key ) ) {
                return new \WP_Error(
                        'properplan_ac_email_missing_credentials',
                        __( 'Both the API URL and API key are required to test the connection.', 'properplan-ac-email' )
                );
        }

        $client = new ActiveCampaign_API( $api_url, $api_key );

        return $client->request( 'GET', '/api/3/users/me' );
	$credentials = get_api_credentials();

	if ( null === $credentials ) {
		return null;
	}

	return new ActiveCampaign_API( $credentials['url'], $credentials['key'] );
}
