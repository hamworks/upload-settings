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
		add_filter( 'upload_mimes', array( $this, 'check_upload_mimes' ) );
		add_filter( 'big_image_size_threshold', array( $this, 'big_image_size_threshold' ) );
	}

	public function build_settings() {
		$uploads = new Settings( 'media', 'uploads' );
		$uploads->add_setting(
			'upload_filetypes',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'esc_html',
				'default'           => 'jpg jpeg png gif webp heic mov pdf doc ppt odt pptx docx pps ppsx xls xlsx mp3 ogg wav mp4',
				'description'       => __( 'Upload file types'  ) ,
			)
		);
		$uploads->add_setting(
			'fileupload_maxk',
			array(
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'default'           => 0,
				'description'       => __( 'Max upload file size' ) . ' ' . __( '(KB)' ),
			)
		);
		$default = new Settings( 'media' );
		$default->add_setting(
			'big_image_size_threshold',
			array(
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'default'           => 2560,
				'description'       => __( 'Max width or height' ),
			)
		);
	}

	/**
	 * @param int $limit
	 *
	 * @return int
	 */
	public function upload_size_limit( int $limit ): int {
		$fileupload_maxk = absint( get_option( 'fileupload_maxk' ) );
		if ( $fileupload_maxk ) {
			return min( KB_IN_BYTES * $fileupload_maxk, $limit );
		}
		return $limit;
	}

	/**
	 * Filter function for `big_image_size_threshold`.
	 *
	 * @param int $value
	 *
	 * @return int
	 */
	public function big_image_size_threshold( int $value ): int {
		$big_image_size_threshold = absint( get_option( 'big_image_size_threshold' ) );
		if ( $big_image_size_threshold ) {
			return $big_image_size_threshold;
		}

		return $value;
	}

	public function check_upload_mimes( $mimes ) {
		$site_exts  = explode( ' ', get_option( 'upload_filetypes', 'jpg jpeg png gif webp heic mov pdf doc ppt odt pptx docx pps ppsx xls xlsx mp3 ogg wav mp4' ) );
		$site_mimes = array();
		foreach ( $site_exts as $ext ) {
			foreach ( $mimes as $ext_pattern => $mime ) {
				if ( '' !== $ext && false !== strpos( $ext_pattern, $ext ) ) {
					$site_mimes[ $ext_pattern ] = $mime;
				}
			}
		}
		return $site_mimes;
	}
}
