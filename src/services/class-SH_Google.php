<?php
/**
 * SH_Social_Service Class
 * @author        Sarah Henderson
 * @date            2013-07-07
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

// widget class
class SH_Google extends SH_Social_Service {

	public function __construct( $type, $settings, $key ) {
		parent::__construct( $type, $settings, $key );
		$this->service  = "Google";
		$this->imageUrl = $this->imagePath . "google.png";
	}


	public function shareButtonUrl( $url, $title ) {

		return "https://plus.google.com/share?url=$url";

	}

	public function linkButtonUrl( $username ) {

		if ( strpos( $username, 'http://' ) === 0 || strpos( $username, 'https://' ) === 0 ) {
			$url = $username;
		} else {
			$url = "http://plus.google.com/$username";
		}
		return $url;
	}

	public function fetchShareCount( $url ) {
		$args = array(
			'method'    => 'POST',
			'headers'   => array(
				// setup content type to JSON
				'Content-Type' => 'application/json'
			),
			// setup POST options to Google API
			'body'      => json_encode( array(
				'method'     => 'pos.plusones.get',
				'id'         => 'p',
				'jsonrpc'    => '2.0',
				'key'        => 'p',
				'apiVersion' => 'v1',
				'params'     => array(
					'nolog'   => true,
					'id'      => $url,
					'source'  => 'widget',
					'userId'  => '@viewer',
					'groupId' => '@self'
				)
			) ),
			// disable checking SSL sertificates
			'sslverify' => false
		);

		// retrieves JSON with HTTP POST method for current URL
		$response = wp_remote_post( "https://clients6.google.com/rpc", $args );

		if ( is_wp_error( $response ) ) {
			// return zero if response is error
			return 0;
		} else {
			$json = json_decode( $response['body'], true );

			// return count of Google +1 for requsted URL
			return intval( $json['result']['metadata']['globalCounts']['count'] );
		}
	}

	public static function hasShareCount() {
		return true;
	}

	public static function description() {
		return __( 'Hint', 'crafty-social-buttons' ) . ": plus.google.com/u/0/<strong>user-id</strong> (" . __( 'a long number', 'crafty-social-buttons' ) . ")";
	}
}