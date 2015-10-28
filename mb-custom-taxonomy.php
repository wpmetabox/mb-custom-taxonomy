<?php
/**
 * Plugin Name: MB Custom Taxonomy
 * Plugin URI: https://www.metabox.io/plugins/custom-taxonomy/
 * Description: Create custom taxonomies with easy-to-use UI
 * Version: 1.0.0
 * Author: Rilwis
 * Author URI: https://metabox.io
 * License: GPL-2.0+
 * Text Domain: mb-custom-taxonomy
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

// Plugin constants
define( 'MB_CUSTOM_TAXONOMY_URL', plugin_dir_url( __FILE__ ) );
define( 'MB_CUSTOM_TAXONOMY_DIR', plugin_dir_path( __FILE__ ) );

if ( is_admin() )
{
	require_once MB_CUSTOM_TAXONOMY_DIR . 'inc/required-plugin.php';
	require_once MB_CUSTOM_TAXONOMY_DIR . 'inc/class-mb-custom-taxonomy-register.php';
	require_once MB_CUSTOM_TAXONOMY_DIR . 'inc/class-mb-custom-taxonomy-edit.php';
	new MB_Custom_Taxonomy_Register;
	new MB_Custom_Taxonomy_Edit;
}

add_action( 'plugins_loaded', 'mb_custom_taxonomy_load_textdomain' );

/**
 * Load plugin textdomain
 * @return void
 */
function mb_custom_taxonomy_load_textdomain()
{
	load_plugin_textdomain( 'mb-custom-taxonomy' );
}
