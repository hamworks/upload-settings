<?php

namespace HAMWORKS\WP\Upload_Settings;

/**
 * Class Settings
 */
class Settings {

	/**
	 * @var array {
	 *     @type string     $type              The type of data associated with this setting.
	 *                                         Valid values are 'string', 'boolean', 'integer', 'number', 'array', and 'object'.
	 *     @type string     $description       A description of the data attached to this setting.
	 *     @type callable   $sanitize_callback A callback function that sanitizes the option's value.
	 *     @type bool|array $show_in_rest      Whether data associated with this setting should be included in the REST API.
	 *                                         When registering complex settings, this argument may optionally be an
	 *                                         array with a 'schema' key.
	 *     @type mixed      $default           Default value when calling `get_option()`.
	 * }
	 */
	private $settings = array();

	/**
	 * @var string
	 */
	private $option_group;

	/**
	 * @var string
	 */
	private $section;

	/**
	 * Settings constructor.
	 *
	 * @param string $option_group
	 * @param string $section
	 */
	public function __construct( string $option_group = 'general', string $section = 'default' ) {
		$this->option_group = $option_group;

		add_action( 'init', array( $this, 'register_settings' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		$this->section = $section;
	}

	/**
	 * @param string   $option_name
	 * @param array    $setting {
	 *     @type string     $type              The type of data associated with this setting.
	 *                                         Valid values are 'string', 'boolean', 'integer', 'number', 'array', and 'object'.
	 *     @type string     $description       A description of the data attached to this setting.
	 *     @type callable   $sanitize_callback A callback function that sanitizes the option's value.
	 *     @type bool|array $show_in_rest      Whether data associated with this setting should be included in the REST API.
	 *                                         When registering complex settings, this argument may optionally be an
	 *                                         array with a 'schema' key.
	 *     @type mixed      $default           Default value when calling `get_option()`.
	 * }
	 */
	public function add_setting( string $option_name, array $setting ) {
		$this->settings[ $option_name ] = $setting;
	}

	public function register_settings() {
		foreach ( $this->settings as $option_name => $setting ) {
			register_setting( 'media', $option_name, $setting );
		}
	}

	public function admin_init() {
		foreach ( $this->settings as $option_name => $setting ) {
			add_settings_field(
				$option_name,
				$setting['description'],
				array( $this, 'field' ),
				$this->option_group,
				$this->section,
				array(
					'label_for'   => $option_name,
					'option_name' => $option_name,
				)
			);
		}
	}

	public function field( $args ) {
		$value = get_option( $args['option_name'] );
		?>
		<input
			type="text"
			id="<?php echo esc_attr( $args['option_name'] ); ?>"
			name="<?php echo esc_attr( $args['option_name'] ); ?>"
			value="<?php echo esc_attr( $value ); ?>"
		/>
		<?php
	}
}
