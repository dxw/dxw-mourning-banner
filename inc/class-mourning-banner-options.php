<?php
/**
 * Options page.
 *
 * @package Mourning_Banner
 */

if ( ! class_exists( 'Mourning_Banner_Options' ) ) :
	/**
	 * Class Mourning_Banner_Options
	 */
	class Mourning_Banner_Options {
		/**
		 * @var array $mourning_banner_options
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
            add_action( 'admin_footer',[ $this, 'media_selector_print_scripts'] );
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
                #wp-banner_message-editor-container {max-width: 80%}

                .wp-core-ui .button {margin-right: 20px}
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
				'mourning_banner_option_group', // option_group
				'mourning_banner_options', // option_name
				[ $this, 'mourning_banner_sanitize' ] // sanitize_callback
			);

			add_settings_section(
				'mourning_banner_setting_section', // id
				'Settings', // title
				'', // callback
				'mourning-banner-admin' // page
			);

            add_settings_field(
                'person_name', //id
                'Person name',
                [ $this, 'person_name_callback'],
                'mourning-banner-admin',
                'mourning_banner_setting_section',
                [ 'label_for' => 'person_name']
            );

            add_settings_field(
                'person_birth_date', //id
                'Person Birth Date',
                [ $this, 'person_birth_date_callback'],
                'mourning-banner-admin',
                'mourning_banner_setting_section',
                [ 'label_for' => 'person_birth_date']
            );

            add_settings_field(
                'person_death_date', //id
                'Person Death Date',
                [ $this, 'person_death_date_callback'],
                'mourning-banner-admin',
                'mourning_banner_setting_section',
                [ 'label_for' => 'person_death_date']
            );

            add_settings_field(
                'banner_image', //id
                'Banner image',
                [ $this, 'banner_image_callback'],
                'mourning-banner-admin',
                'mourning_banner_setting_section',
                [ 'label_for' => 'banner_image']
            );

            add_settings_field(
                'banner_message', // id
                'Banner text', // title
                [ $this, 'banner_message_callback' ], // callback
                'mourning-banner-admin', // page
                'mourning_banner_setting_section', // section
                [ 'label_for' => 'banner_message' ] // label
            );

            add_settings_field(
                'banner_link', // id
                'Banner link', // title
                [ $this, 'banner_link_callback' ], // callback
                'mourning-banner-admin', // page
                'mourning_banner_setting_section', // section
                [ 'label_for' => 'banner_link' ] // label
            );

			add_settings_field(
				'all_hidden', // id
				'', // title
				[ $this, 'all_hidden_callback' ], // callback
				'mourning-banner-admin', // page
				'mourning_banner_setting_section', // section
				'' // label
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
            if ( isset( $input['person_name'] ) ) {
                $sanitary_values['person_name'] = sanitize_text_field( $input['person_name'] );
            }

            if ( isset( $input['person_birth_date'] ) ) {
                $sanitary_values['person_birth_date'] = $input['person_birth_date'];
            }

            if ( isset( $input['person_death_date'] ) ) {
                $sanitary_values['person_death_date'] = $input['person_death_date'];
            }

            if (isset($input['banner_image'])) {
                $sanitary_values['banner_image'] = $input['banner_image'];
            }

			if ( isset( $input['banner_message'] ) ) {
				$sanitary_values['banner_message'] = wp_kses_post( $input['banner_message'] );
			}

			if ( isset( $input['when_to_display'] ) ) {
				$sanitary_values['when_to_display'] = $input['when_to_display'];
			}

			if ( isset( $input['background_colour'] ) ) {
				$sanitary_values['background_colour'] = sanitize_text_field( $input['background_colour'] );
			}

			if ( isset( $input['text_colour'] ) ) {
				$sanitary_values['text_colour'] = sanitize_text_field( $input['text_colour'] );
			}

			if ( isset( $input['link_colour'] ) ) {
				$sanitary_values['link_colour'] = sanitize_text_field( $input['link_colour'] );
			}

			if ( isset( $input['element_to_attach_to'] ) ) {
				$sanitary_values['element_to_attach_to'] = sanitize_text_field( $input['element_to_attach_to'] );
			}

			if ( isset( $input['position'] ) ) {
				$sanitary_values['position'] = $input['position'];
			}

			if ( isset( $input['fixed'] ) ) {
				$sanitary_values['fixed'] = $input['fixed'];
			}

			if ( isset( $input['show_in_admin'] ) ) {
				$sanitary_values['show_in_admin'] = $input['show_in_admin'];
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

        /**
         * Person name setup
         */
        public function person_name_callback() {
            $content = isset( $this->mourning_banner_options['person_name'] ) ? $this->mourning_banner_options['person_name'] : '';
            printf(
                '<input type="text" id="person_name" name="mourning_banner_options[person_name]" value="%s" />',
                isset( $content ) ? esc_attr( $content ) : ''
            );
        }

        /**
         * Person birth date setup
         */
        public function person_birth_date_callback() {
            $content = isset( $this->mourning_banner_options['person_birth_date'] ) ? $this->mourning_banner_options['person_birth_date'] : '';
            printf(
                '<input type="date" id="person_birth_date" name="mourning_banner_options[person_birth_date]" value="%s" />',
                isset( $content ) ? esc_attr( $content ) : ''
            );
        }

        /**
         * Person death date setup
         */
        public function person_death_date_callback() {
            $content = isset( $this->mourning_banner_options['person_death_date'] ) ? $this->mourning_banner_options['person_death_date'] : '';
            printf(
                '<input type="date" id="person_death_date" name="mourning_banner_options[person_death_date]" value="%s" />',
                isset( $content ) ? esc_attr( $content ) : ''
            );
        }

        public function banner_image_callback() {
            wp_enqueue_media();

            ?><div class='banner-image-wrapper'>
                <img id='banner-image-preview' src='' width='100' height='100' style='max-height: 100px; width: 100px;'>
            </div>
            <input id="upload_image_button" type="button" class="button" value="<?php _e( 'Upload image' ); ?>" />
            <input type='hidden' name='banner_image' id='banner_image' name='mourning_banner_options[banner_image]' value=''><?php

        }

		/**
		 * All hidden fields setup.
		 */
		public function all_hidden_callback() {
			printf(
				'<input type="hidden" name="mourning_banner_options[when_to_display]" id="when_to_display" value="%s">',
				isset( $this->mourning_banner_options['when_to_display'] ) ? esc_attr( $this->mourning_banner_options['when_to_display'] ) : ''
			);

			printf(
				'<input type="hidden" name="mourning_banner_options[background_colour]" id="background_colour" value="%s">',
				isset( $this->mourning_banner_options['background_colour'] ) ? esc_attr( $this->mourning_banner_options['background_colour'] ) : ''
			);

			printf(
				'<input type="hidden" name="mourning_banner_options[text_colour]" id="text_colour" value="%s">',
				isset( $this->mourning_banner_options['text_colour'] ) ? esc_attr( $this->mourning_banner_options['text_colour'] ) : ''
			);

			printf(
				'<input type="hidden" name="mourning_banner_options[link_colour]" id="link_colour" value="%s">',
				isset( $this->mourning_banner_options['link_colour'] ) ? esc_attr( $this->mourning_banner_options['link_colour'] ) : ''
			);

			printf(
				'<input type="hidden" name="mourning_banner_options[element_to_attach_to]" id="element_to_attach_to" value="%s">',
				isset( $this->mourning_banner_options['element_to_attach_to'] ) ? esc_attr( $this->mourning_banner_options['element_to_attach_to'] ) : ''
			);

			printf(
				'<input type="hidden" name="mourning_banner_options[position]" id="position" value="%s">',
				isset( $this->mourning_banner_options['position'] ) ? esc_attr( $this->mourning_banner_options['position'] ) : ''
			);

			printf(
				'<input type="hidden" name="mourning_banner_options[fixed]" id="fixed" value="%s">',
				isset( $this->mourning_banner_options['fixed'] ) ? esc_attr( $this->mourning_banner_options['fixed'] ) : ''
			);

			printf(
				'<input type="hidden" name="mourning_banner_options[show_in_admin]" id="show_in_admin" value="%s">',
				isset( $this->mourning_banner_options['show_in_admin'] ) ? esc_attr( $this->mourning_banner_options['show_in_admin'] ) : ''
			);
		}


        public function media_selector_print_scripts() {
            $my_saved_attachment_post_id = isset( $this->mourning_banner_options['banner_image']) ? $this->mourning_banner_options['banner_image'] : 0;

            ?><script type='text/javascript'>

                jQuery( document ).ready( function( $ ) {

                    // Uploading files
                    var file_frame;
                    var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
                    var set_to_post_id = <?php echo $my_saved_attachment_post_id; ?>; // Set this

                    jQuery('#upload_image_button').on('click', function( event ){

                        event.preventDefault();

                        // If the media frame already exists, reopen it.
                        if ( file_frame ) {
                            // Set the post ID to what we want
                            file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
                            // Open frame
                            file_frame.open();
                            return;
                        } else {
                            // Set the wp.media post id so the uploader grabs the ID we want when initialised
                            wp.media.model.settings.post.id = set_to_post_id;
                        }

                        // Create the media frame.
                        file_frame = wp.media.frames.file_frame = wp.media({
                            title: 'Select a image to upload',
                            button: {
                                text: 'Use this image',
                            },
                            multiple: false	// Set to true to allow multiple files to be selected
                        });

                        // When an image is selected, run a callback.
                        file_frame.on( 'select', function() {
                            // We set multiple to false so only get one image from the uploader
                            attachment = file_frame.state().get('selection').first().toJSON();

                            // Do something with attachment.id and/or attachment.url here
                            $( '#banner-image-preview' ).attr( 'src', attachment.url ).css( 'width', 'auto' );
                            $( '#banner_image' ).val( attachment.id );

                            // Restore the main post ID
                            wp.media.model.settings.post.id = wp_media_post_id;
                        });

                        // Finally, open the modal
                        file_frame.open();
                    });

                    // Restore the main ID when the add media button is pressed
                    jQuery( 'a.add_media' ).on( 'click', function() {
                        wp.media.model.settings.post.id = wp_media_post_id;
                    });
                });

            </script><?php

        }
	}
endif;