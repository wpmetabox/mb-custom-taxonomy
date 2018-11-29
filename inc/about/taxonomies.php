<?php
/**
 * Getting started section.
 *
 * @package    Meta Box
 * @subpackage MB Custom Taxonomy
 */

?>
<div id="taxonomies" class="gt-tab-pane">
	<div class="feature-section two-col">
		<div class="col">
			<h3><?php esc_html_e( 'Create Custom Taxonomies', 'mb-custom-taxonomy' ); ?></h3>
			<p><?php esc_html_e( 'Create your first custom taxonomy to organize your content into groups that you can query to show them in the frontend.', 'mb-custom-taxonomy' ); ?>
			<p>
			<p>
				<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=mb-taxonomy' ) ); ?>" class="button"><?php esc_html_e( 'Start Now', 'mb-custom-taxonomy' ); ?></a>
			</p>
			<p>
				<?php
				echo wp_kses_post(
					sprintf(
						// Translators: %s is the link to the documentation.
						__( 'Confused when to use custom taxonomy vs. custom fields? <a href="%s" target="_blank">Read here</a>.', 'mb-custom-taxonomy' ),
						'https://metabox.io/custom-fields-vs-custom-taxonomies/?utm_source=WordPress&utm_medium=link&utm_campaign=plugin'
					)
				);
				?>
			</p>
		</div>

		<div class="col">
			<h3><?php esc_html_e( 'Want To Create Custom Post Types?', 'mb-custom-taxonomy' ); ?></h3>
			<p>
				<?php
				echo wp_kses_post(
					sprintf(
						// Translators: %s is the link to the plugin.
						__( 'Check out the <a href="%s" target="_blank">MB Custom Post Type</a> plugin which allows you to create custom post types and taxonomies in WordPress quickly with the same UI.', 'mb-custom-taxonomy' ),
						'https://metabox.io/custom-post-type/?utm_source=WordPress&utm_medium=link&utm_campaign=plugin'
					)
				);
				?>
			</p>
			<?php if ( ! function_exists( 'mb_cpt_load' ) ) : ?>
				<?php
				$mbct_plugins      = get_plugins();
				$mbct_is_installed = isset( $mbct_plugins['mb-custom-post-type/mb-custom-post-type.php'] );
				$mbct_install_url  = wp_nonce_url( admin_url( 'update.php?action=install-plugin&plugin=mb-custom-post-type' ), 'install-plugin_mb-custom-post-type' );
				$mbct_activate_url = wp_nonce_url( admin_url( 'plugins.php?action=activate&amp;plugin=mb-custom-post-type/mb-custom-post-type.php' ), 'activate-plugin_mb-custom-post-type/mb-custom-post-type.php' );
				$mbct_action_url   = $mbct_is_installed ? $mbct_activate_url : $mbct_install_url;
				$mbct_action       = $mbct_is_installed ? __( 'Activate Now', 'mb-taxonomy' ) : __( 'Install Now', 'mb-taxonomy' );
				?>
				<p><a class="button" href="<?php echo esc_url( $mbct_action_url ); ?>"><?php echo esc_html( $mbct_action ); ?></a></p>
			<?php else : ?>
				<p><a target="_blank" class="button" href="https://metabox.io/custom-post-type/?utm_source=WordPress&utm_medium=link&utm_campaign=plugin"><?php esc_html_e( 'Learn More', 'mb-custom-taxonomy' ); ?></a></p>
			<?php endif; ?>
		</div>
	</div>
</div>
