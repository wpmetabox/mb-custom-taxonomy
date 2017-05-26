<?php
/**
 * Plugin Name: MB Custom Taxonomy
 * Plugin URI: https://metabox.io/plugins/custom-taxonomy/
 * Description: Create custom taxonomies with easy-to-use UI
 * Version: 1.2
 * Author: MetaBox.io
 * Author URI: https://metabox.io
 * License: GPL-2.0+
 * Text Domain: mb-custom-taxonomy
 *
 * @package    Meta Box
 * @subpackage MB Custom Taxonomy
 */

// Prevent loading this file directly.
defined( 'ABSPATH' ) || exit;

add_action( 'init', 'mb_custom_taxonomy_load', 0 );

/**
 * Dependent plugin activation/deactivation.
 *
 * @link https://gist.github.com/mathetos/7161f6a88108aaede32a
 */
function mb_custom_taxonomy_load() {
	// If Meta Box is NOT active.
	if ( current_user_can( 'activate_plugins' ) && ! class_exists( 'RW_Meta_Box' ) ) {
		add_action( 'admin_init', 'mb_custom_taxonomy_deactivate' );
		add_action( 'admin_notices', 'mb_custom_taxonomy_admin_notice' );

		return;
	}

	// Plugin constants.
	define( 'MB_CUSTOM_TAXONOMY_URL', plugin_dir_url( __FILE__ ) );

	load_plugin_textdomain( 'mb-custom-taxonomy' );

	require_once plugin_dir_path( __FILE__ ) . 'inc/class-mb-custom-taxonomy-register.php';
	$register = new MB_Custom_Taxonomy_Register;

	if ( is_admin() ) {
		require_once plugin_dir_path( __FILE__ ) . 'inc/class-mb-custom-taxonomy-edit.php';
		require_once plugin_dir_path( __FILE__ ) . 'inc/interfaces/encoder.php';
		require_once plugin_dir_path( __FILE__ ) . 'inc/encoders/taxonomy-encoder.php';

		$tax_encoder = new MB_CPT_Taxonomy_Encoder();
		new MB_Custom_Taxonomy_Edit( $register, $tax_encoder );
	}
}

/**
 * Deactivate plugin.
 */
function mb_custom_taxonomy_deactivate() {
	deactivate_plugins( plugin_basename( __FILE__ ) );
}

/**
 * Show admin notice when Meta Box is not activated.
 */
function mb_custom_taxonomy_admin_notice() {
	$child  = __( 'MB Custom Taxonomy', 'mb-custom-taxonomy' );
	$parent = __( 'Meta Box', 'mb-custom-taxonomy' );
	printf(
		// translators: %1$s is the plugin name, %2$s is the Meta Box plugin name.
		'<div class="error"><p>' . esc_html__( '%1$s requires %2$s to function correctly. Please activate %2$s before activating %1$s. For now, the plug-in has been deactivated.', 'mb-custom-taxonomy' ) . '</p></div>',
		'<strong>' . esc_html( $child ) . '</strong>',
		'<strong>' . esc_html( $parent ) . '</strong>'
	);

	if ( isset( $_GET['activate'] ) ) {
		unset( $_GET['activate'] );
	}
}
