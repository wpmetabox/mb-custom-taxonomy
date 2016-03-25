<?php
/**
 * Plugin Name: MB Custom Taxonomy
 * Plugin URI: https://metabox.io/plugins/custom-taxonomy/
 * Description: Create custom taxonomies with easy-to-use UI
 * Version: 1.0.1
 * Author: Rilwis
 * Author URI: http://www.deluxeblogtips.com
 * License: GPL-2.0+
 * Text Domain: mb-custom-taxonomy
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

add_action( 'init', 'mb_custom_taxonomy_load', 0 );

/**
 * Dependent plugin activation/deactivation
 * @link https://gist.github.com/mathetos/7161f6a88108aaede32a
 */
function mb_custom_taxonomy_load()
{
	// If Meta Box is NOT active
	if ( current_user_can( 'activate_plugins' ) && ! class_exists( 'RW_Meta_Box' ) )
	{
		add_action( 'admin_init', 'mb_custom_taxonomy_deactivate' );
		add_action( 'admin_notices', 'mb_custom_taxonomy_admin_notice' );
	}
	else
	{
		mb_custom_taxonomy_load_textdomain();

		if ( is_admin() )
		{
			// Plugin constants
			define( 'MB_CUSTOM_TAXONOMY_URL', plugin_dir_url( __FILE__ ) );

			require_once plugin_dir_path( __FILE__ ) . 'inc/class-mb-custom-taxonomy-register.php';
			require_once plugin_dir_path( __FILE__ ) . 'inc/class-mb-custom-taxonomy-edit.php';

			new MB_Custom_Taxonomy_Register;
			new MB_Custom_Taxonomy_Edit;
		}
	}
}

/**
 * Deactivate plugin
 */
function mb_custom_taxonomy_deactivate()
{
	deactivate_plugins( plugin_basename( __FILE__ ) );
}

/**
 * Show admin notice when Meta Box is not activated
 */
function mb_custom_taxonomy_admin_notice()
{
	$child  = __( 'MB Custom Taxonomy', 'mb-custom-taxonomy' );
	$parent = __( 'Meta Box', 'mb-custom-taxonomy' );
	printf(
		'<div class="error"><p>' . esc_html__( '%1$s requires %2$s to function correctly. Please activate %2$s before activating %1$s. For now, the plug-in has been deactivated.', 'mb-custom-taxonomy' ) . '</p></div>',
		'<strong>' . $child . '</strong>',
		'<strong>' . $parent . '</strong>'
	);

	if ( isset( $_GET['activate'] ) )
	{
		unset( $_GET['activate'] );
	}
}

/**
 * Load plugin textdomain
 */
function mb_custom_taxonomy_load_textdomain()
{
	load_plugin_textdomain( 'mb-custom-taxonomy' );
}
