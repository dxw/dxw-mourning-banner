<?php
/**
 * Mourning_Banner_Options Class
 *
 * @package Mourning_Banner
 */

if ( ! class_exists( 'Mourning_Banner_Options' ) ) :
	/**
	 * Class Mourning_Banner_Options
	 */
	class MOURNING_Banner_Options {
		/**
		 * @var $mourning_banner_options
		 */
		private $mourning_banner_options;

		/**
		 * Init the object.
		 */
		public static function init() {
			$class = __CLASS__;
			new $class;
		}

		/**
		 * Mourning_Banner_Options constructor.
		 */
		public function __construct() {
			add_action( 'admin_menu', [ $this, 'mourning_banner_add_plugin_page' ] );
			add_action( 'admin_init', [ $this, 'mourning_banner_page_init' ] );
		}

		/**
		 * Add the options plugin page under Settings.
		 */
		public function mourning_banner_add_plugin_page() {
			add_options_page(
				'Mourning Banner',
				'Mourning Banner',
				'manage_options',
				'mourning-banner',
				[ $this, 'mourning_banner_create_admin_page' ]
			);
		}

		/**
		 * Create the options plugin page.
		 */
		public function mourning_banner_create_admin_page() {
			$this->mourning_banner_options = get_option( 'mourning_banner_options' ); ?>

            <style type="text/css">
                #wp-banner_message-editor-container {
                    max-width: 70%
                }

                .wp-core-ui .button {
                    margin-right: 20px
                }
            </style>
            <div class="wrap">
                <h2>Death of a notable person banner</h2>
                <p>This plugin should only be activated to display a banner at a national time of mourning.</p>

                <form method="post" action="options.php">
					<?php
					settings_fields( 'mourning_banner_option_group' );
					do_settings_sections( 'mourning-banner-admin' );
					submit_button( 'Save settings', 'primary', 'submit', false );
					submit_button( 'Reset to defaults settings', 'secondary', 'reset-default', false );
					submit_button( 'Delete all settings', 'secondary', 'reset-all', false );
					?>
                </form>
            </div>
		<?php }

		/**
		 * Register and add settings for the options plugin page.
		 */
		public function mourning_banner_page_init() {
			register_setting(
				'mourning_banner_option_group',
				'mourning_banner_options',
				[ $this, 'mourning_banner_sanitize' ]
			);

			add_settings_section(
				'mourning_banner_setting_section',
				'Settings',
				'',
				'mourning-banner-admin'
			);

			add_settings_field(
				'banner_message', // id
				'Banner text', // title
				[ $this, 'banner_message_callback' ],
				'mourning-banner-admin',
				'mourning_banner_setting_section',
				[ 'label_for' => 'banner_message' ]
			);
		}

		/**
		 * Sanitize the settings for the options plugin page
		 *
		 * @param $input
		 *
		 * @return array|bool
		 */
		public function mourning_banner_sanitize( $input ) {

			$setting = 'mourning_banner_option_group';
			$code    = esc_attr( 'settings_updated' );
			$type    = 'updated';

			if ( isset( $_POST['reset-default'] ) ) {

				$message = 'Settings have been reset to default values.';
				add_settings_error( $setting, $code, $message, $type );

				return maybe_unserialize( MOURNING_BANNER_DEFAULTS ); // Default settings.

			}

			if ( isset( $_POST['reset-all'] ) && ! empty( get_option( 'mourning_banner_options' ) ) ) {

				$message = 'All settings deleted.';
				add_settings_error( $setting, $code, $message, $type );

				delete_option( 'mourning_banner_options' );

				return false;
			}

			if ( isset( $_POST['submit'] ) ) {

				$message = 'Settings saved.';
				add_settings_error( $setting, $code, $message, $type );
			}

			$sanitary_values = [];
			if ( isset( $input['banner_message'] ) ) {
				$sanitary_values['banner_message'] = wp_kses_post( $input['banner_message'] );
			}

			return $sanitary_values;
		}

		/**
		 * Banner message setup.
		 */
		public function banner_message_callback() {
			$content   = isset( $this->mourning_banner_options['banner_message'] ) ? $this->mourning_banner_options['banner_message'] : '';
			$editor_id = 'banner_message';
			$args      = [
				'tinymce'       => [
					'toolbar1' => 'fontsizeselect,bold,italic,underline,alignleft,aligncenter,alignright,link,unlink,removeformat,undo,redo',
					'toolbar2' => '',
				],
				'media_buttons' => false,
				'textarea_name' => 'mourning_banner_options[banner_message]',
				'quicktags'     => false,
				'textarea_rows' => 10,
			];
			wp_editor( $content, $editor_id, $args );

		}

	}
endif;
