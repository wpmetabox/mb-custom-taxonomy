<?php
/**
 * Plugin Name: MB Custom Taxonomy
 * Plugin URI:  https://metabox.io/plugins/custom-taxonomy/
 * Description: Create custom taxonomies with easy-to-use UI
 * Version:     1.4.3
 * Author:      MetaBox.io
 * Author URI:  https://metabox.io
 * License:     GPL-2.0+
 * Text Domain: mb-custom-taxonomy
 *
 * @package    Meta Box
 * @subpackage MB Custom Taxonomy
 */

// Prevent loading this file directly.
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'mb_custom_taxonomy_load' ) ) {
	// Plugin constants.
	define( 'MB_CUSTOM_TAXONOMY_FILE', __FILE__ );
	define( 'MB_CUSTOM_TAXONOMY_URL', plugin_dir_url( __FILE__ ) );

	add_action( 'init', 'mb_custom_taxonomy_load', 0 );

	/**
	 * Dependent plugin activation/deactivation.
	 *
	 * @link https://gist.github.com/mathetos/7161f6a88108aaede32a
	 */
	function mb_custom_taxonomy_load() {
		// Do nothing if the MB Custom Post Type plugin is active.
		if ( function_exists( 'mb_cpt_load' ) ) {
			return;
		}

		// If Meta Box is NOT active.
		if ( current_user_can( 'activate_plugins' ) && ! defined( 'RWMB_VER' ) ) {
			add_action( 'admin_notices', 'mb_custom_taxonomy_admin_notice' );

			return;
		}

		load_plugin_textdomain( 'mb-custom-taxonomy' );

		// Show Meta Box admin menu.
		add_filter( 'rwmb_admin_menu', '__return_true' );

		require dirname( __FILE__ ) . '/inc/class-mb-custom-taxonomy-register.php';
		$register = new MB_Custom_Taxonomy_Register();

		if ( ! is_admin() ) {
			return;
		}

		require dirname( __FILE__ ) . '/inc/class-mb-custom-taxonomy-edit.php';
		require dirname( __FILE__ ) . '/inc/interfaces/encoder.php';
		require dirname( __FILE__ ) . '/inc/encoders/taxonomy-encoder.php';
		require dirname( __FILE__ ) . '/inc/about/about.php';

		$tax_encoder = new MB_CPT_Taxonomy_Encoder();
		new MB_Custom_Taxonomy_Edit( $register, $tax_encoder );

		$about_page = new MB_Custom_Taxonomy_About_Page();
		$about_page->init();
	}

	/**
	 * Show admin notice when Meta Box is not activated.
	 */
	function mb_custom_taxonomy_admin_notice() {
		$plugins      = get_plugins();
		$is_installed = isset( $plugins['meta-box/meta-box.php'] );
		$install_url  = wp_nonce_url( admin_url( 'update.php?action=install-plugin&plugin=meta-box' ), 'install-plugin_meta-box' );
		$activate_url = wp_nonce_url( admin_url( 'plugins.php?action=activate&amp;plugin=meta-box/meta-box.php' ), 'activate-plugin_meta-box/meta-box.php' );
		$action_url   = $is_installed ? $activate_url : $install_url;
		$action       = $is_installed ? __( 'activate', 'mb-taxonomy' ) : __( 'install', 'mb-taxonomy' );

		$child  = __( 'MB Custom Taxonomy', 'mb-custom-post-type' );
		$parent = __( 'Meta Box', 'mb-custom-post-type' );
		printf(
			// Translators: %1$s is the plugin name, %2$s is the Meta Box plugin name.
			'<div class="error"><p>' . esc_html__( '%1$s requires %2$s to function correctly. %3$s to %4$s %2$s.', 'mb-custom-post-type' ) . '</p></div>',
			'<strong>' . esc_html( $child ) . '</strong>',
			'<strong>' . esc_html( $parent ) . '</strong>',
			'<a href="' . esc_url( $action_url ) . '">' . esc_html__( 'Click here', 'mb-custom-post-type' ) . '</a>',
			esc_html( $action )
		);

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
	}
}
