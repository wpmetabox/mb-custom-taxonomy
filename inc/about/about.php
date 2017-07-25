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
	 * Plugin data.
	 *
	 * @var array
	 */
	protected $plugin;

	/**
	 * Init hooks.
	 */
	public function init() {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			include ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$this->plugin = get_plugin_data( MB_CUSTOM_TAXONOMY_FILE );

		add_action( 'admin_menu', array( $this, 'register_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * Register admin page.
	 */
	public function register_page() {
		add_submenu_page(
			'edit.php?post_type=mb-taxonomy',
			__( 'MB Custom Taxonomy About', 'mb-custom-taxonomy' ),
			__( 'About', 'mb-custom-taxonomy' ),
			'manage_options',
			'mb-custom-taxonomy-about',
			array( $this, 'render_page' )
		);
	}

	/**
	 * Render admin page.
	 */
	public function render_page() {
		?>
		<div class="wrap about-wrap">
			<?php include dirname( __FILE__ ) . '/sections/welcome.php'; ?>
			<?php include dirname( __FILE__ ) . '/sections/tabs.php'; ?>
			<?php include dirname( __FILE__ ) . '/sections/getting-started.php'; ?>
		</div>
		<?php
	}

	/**
	 * Enqueue CSS and JS.
	 */
	public function enqueue() {
		global $plugin_page;
		if ( 'mb-custom-taxonomy-about' !== $plugin_page ) {
			return;
		}
		wp_enqueue_style( 'mb-custom-taxonomy-about', MB_CUSTOM_TAXONOMY_URL . 'inc/about/css/style.css' );
		wp_enqueue_script( 'mb-custom-taxonomy-about', MB_CUSTOM_TAXONOMY_URL . 'inc/about/js/script.js', array( 'jquery' ), '1.4', true );
	}
}
