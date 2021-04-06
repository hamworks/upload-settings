<?php

namespace HAMWORKS\WP\Upload_Settings;

/**
 * Class Plugin
 *
 * @package HAMWORKS\WP\Upload_Settings
 */
class Plugin {

	/**
	 * Plugin constructor.
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'build_settings' ) );
		add_filter( 'upload_size_limit', array( $this, 'upload_size_limit' ) );
	}

	public function build_settings() {
		$settings = new Settings( 'media', 'uploads' );
		$settings->add_setting(
			'fileupload_maxk',
			array(
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'default'           => 0,
				'description'       => __( 'Max upload file size' ) . ' ' . __( '(KB)' ),
			)
		);
	}

	public function upload_size_limit( $limit ) {
		$fileupload_maxk = absint( get_option( 'fileupload_maxk' ) );
		if ( $fileupload_maxk ) {
			return min( KB_IN_BYTES * $fileupload_maxk, $limit );
		}
		return $limit;
	}
}
