<?php
/**
 * Plugin Name:     Death of a notable person banner
 * Plugin URI:      https://www.dxw.com
 * Description:     This plugin should only be activated to display a banner at a national time of mourning.
 * Author:          dxw
 * Author URI:      https://www.dxw.com
 * Text Domain:     dxw-mourning-banner
 * Domain Path:     /languages
 * Version:         0.1
 *
 * @package         Mourning_Banner
 */

/**
 * Set the default options.
 */
$mourning_banner_defaults = [
	'banner_message'       => '<h2>Name of person</h2><p>birth year - death year</p><br /><a href="#">Link to bio</a>',
	'when_to_display'      => 'always',
	'background_colour'    => '#000000',
	'text_colour'          => '#ffffff',
	'link_colour'          => '#ffffff',
	'element_to_attach_to' => 'body',
	'position'             => 'prepend',
	'fixed'                => 'no',
	'show_in_admin'        => '0',
];


/**
 * Defines the default settings for the plugin.
 */
defined( 'MOURNING_BANNER_DEFAULTS' ) or define( 'MOURNING_BANNER_DEFAULTS',
	maybe_serialize( $mourning_banner_defaults ) );

/**
 * Register the activation functionality to set the default settings.
 */
register_activation_hook( __FILE__, 'mourning_banner_plugin_activation' );

/**
 * Init Mourning_Banner class when plugins are loaded.
 */
require_once __DIR__ . '/inc/class-mourning-banner.php';
add_action( 'plugins_loaded', [ 'Mourning_Banner', 'init' ] );

/**
 * Init Mourning_Banner_Options class when plugins are loaded and in the admin.
 */
if ( is_admin() ) {
	require_once __DIR__ . '/inc/class-mourning-banner-options.php';
	add_action( 'plugins_loaded', [ 'Mourning_Banner_Options', 'init' ] );
}

/**
 * Set defaults on activation.
 */
function mourning_banner_plugin_activation() {
	if ( false === get_option( 'mourning_banner_options' ) ) {
		add_option( 'mourning_banner_options', maybe_unserialize( MOURNING_BANNER_DEFAULTS ) );
	}

	if ( empty( get_option( 'mourning_banner_options' ) ) ) {
		update_option( 'mourning_banner_options', maybe_unserialize( MOURNING_BANNER_DEFAULTS ) );
	}
}


