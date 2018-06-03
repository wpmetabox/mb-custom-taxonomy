<?php
/**
 * Plugin about page
 *
 * @package Meta Box
 * @subpackage MB Custom Taxonomy
 */

/**
 * Class for about page.
 */
class MB_Custom_Taxonomy_About_Page {
	/**
	 * Init hooks.
	 */
	public function init() {
		add_action( 'rwmb_about_tabs', array( $this, 'add_tabs' ) );
		add_action( 'rwmb_about_tabs_content', array( $this, 'add_tabs_content' ) );
	}

	/**
	 * Add tabs to the About page.
	 */
	public function add_tabs() {
		?>
		<a href="#taxonomies" class="nav-tab"><?php esc_html_e( 'Taxonomies', 'mb-custom-taxonomy' ); ?></a>
		<?php
	}

	/**
	 * Add tabs content to the About page.
	 */
	public function add_tabs_content() {
		include dirname( __FILE__ ) . '/taxonomies.php';
	}
}
