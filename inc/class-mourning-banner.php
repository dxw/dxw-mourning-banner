<?php
if ( ! class_exists( 'Mourning_Banner' ) ) :
	/**
	 * Class Mourning_Banner
	 */
	class Mourning_Banner {

		/**
		 * Init the object.
		 */
		public static function init() {
			$class = __CLASS__;
			new $class;
		}

		/**
		 * Mourning_Banner constructor.
		 */
		public function __construct() {
			add_action( 'plugin_action_links_' . basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ),
				[ $this, 'mourning_banner_plugin_settings' ], 10, 1 );
			add_filter( 'plugin_row_meta', [ $this, 'mourning_banner_plugin_links' ], 10, 2 );
			add_action( 'init', [ $this, 'mourning_banner_maybe_load_js' ] );
		}

		/**
		 * Add settings links near Deactivate link into plugins admin.
		 *
		 * @param string $links
		 *
		 * @return mixed $links
		 */
		public function mourning_banner_plugin_settings( $links ) {
			if ( current_user_can( 'manage_options' ) ) {
				$links[] = sprintf( '<a href="%s">%s</a>', admin_url( 'options-general.php?page=mourning-banner' ),
					'Settings' );
			}

			return $links;
		}

		/**
		 * Add a link at the end of the Description into plugins admin.
		 *
		 * @param $links
		 * @param $file
		 *
		 * @return array
		 */
		public function mourning_banner_plugin_links( $links, $file ) {
			if ( $file == plugin_basename( __FILE__ ) ) {
				$links[] = 'For more info visit ' . ' <a href="https://www.dxw.com/">dxw</a>';
			}

			return $links;
		}


		/**
		 * Loads the script for the frontend.
		 */
		public static function mourning_banner_load_script() {
			wp_enqueue_script( 'mourning-banner', plugins_url( 'mourning-banner.js', __FILE__ ), [ 'jquery' ], null,
				true );
			wp_localize_script( 'mourning-banner', 'mourning_banner_vars', get_option( 'mourning_banner_options' ) );
		}


		/**
		 * Loads the scripts for the backend.
		 */
		public static function mourning_banner_load_script_admin() {
			wp_enqueue_script( 'mourning-banner-admin', plugins_url( 'mourning-banner-admin.js', __FILE__ ),
				[ 'jquery' ],
				null,
				true );
			wp_localize_script( 'mourning-banner-admin', 'mourning_banner_vars',
				get_option( 'mourning_banner_options' ) );
		}


		/**
		 * Loads the banner on the front/back-end of the site.
		 */
		public function mourning_banner_maybe_load_js() {
			$mourning_banner_options = get_option( 'mourning_banner_options' );

			if ( ( empty( $mourning_banner_options ) ) || ( false === $mourning_banner_options ) ) {
				return;
			}

			$current_user_roles = [];
			$current_user       = wp_get_current_user();
			if ( $current_user->exists() ) {
				$current_user_roles = ( array ) $current_user->roles;
			}

			// Front end.
			if ( 'always' === $mourning_banner_options['when_to_display'] ) { // Always.
				add_action( 'wp_enqueue_scripts', [ __CLASS__, 'mourning_banner_load_script' ] );
			}
			if ( 'loggedin' === $mourning_banner_options['when_to_display']
			     || in_array( $mourning_banner_options['when_to_display'],
					$current_user_roles ) ) { // Logged in variations.
				add_action( 'wp_enqueue_scripts', [ __CLASS__, 'mourning_banner_load_script' ] );
			}
			if ( 'loggedout' === $mourning_banner_options['when_to_display'] && ! $current_user->exists() ) { // Logged out.
				add_action( 'wp_enqueue_scripts', [ __CLASS__, 'mourning_banner_load_script' ] );
			}

			// Back end.
			if ( ! empty( $mourning_banner_options['show_in_admin'] ) ) {
				if ( 'loggedin' === $mourning_banner_options['when_to_display'] || 'always' === $mourning_banner_options['when_to_display']
				     || in_array( $mourning_banner_options['when_to_display'], $current_user_roles ) ) {
					add_action( 'admin_enqueue_scripts', [ __CLASS__, 'mourning_banner_load_script_admin' ] );
				}
			}

		}

	}
endif;
