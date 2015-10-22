<?php
/**
 * Controls all operations of MB Custom Taxonomy extension for registering custom taxonomy.
 *
 * @package    Meta Box
 * @subpackage MB Custom Taxonomy
 * @author     Doan Manh Duc
 * @author     Tran Ngoc Tuan Anh
 */

/**
 * Controls all operations for registering custom taxonomy.
 */
class MB_Custom_Taxonomy_Register
{
	/**
	 * Initiating
	 */
	public function __construct()
	{
		// Register taxonomies
		add_action( 'init', array( $this, 'register_post_types' ), 0 );

		// Change the output of post/bulk post updated messages
		add_filter( 'post_updated_messages', array( $this, 'updated_message' ), 10, 1 );
		add_filter( 'bulk_post_updated_messages', array( $this, 'bulk_updated_messages' ), 10, 2 );
	}

	/**
	 * Register custom post type for taxonomies
	 * @return void
	 */
	public function register_post_types()
	{
		// Register post type of the plugin 'mb-taxonomy'
		$labels = array(
			'name'               => _x( 'Taxonomies', 'Taxonomy General Name', 'mb-custom-taxonomy' ),
			'singular_name'      => _x( 'Taxonomy', 'Taxonomy Singular Name', 'mb-custom-taxonomy' ),
			'menu_name'          => __( 'Taxonomies', 'mb-custom-taxonomy' ),
			'name_admin_bar'     => __( 'Taxonomy', 'mb-custom-taxonomy' ),
			'parent_item_colon'  => __( 'Parent Taxonomy:', 'mb-custom-taxonomy' ),
			'all_items'          => __( 'All Taxonomies', 'mb-custom-taxonomy' ),
			'add_new_item'       => __( 'Add New Taxonomy', 'mb-custom-taxonomy' ),
			'add_new'            => __( 'Add New', 'mb-custom-taxonomy' ),
			'new_item'           => __( 'New Taxonomy', 'mb-custom-taxonomy' ),
			'edit_item'          => __( 'Edit Taxonomy', 'mb-custom-taxonomy' ),
			'update_item'        => __( 'Update Taxonomy', 'mb-custom-taxonomy' ),
			'view_item'          => __( 'View Taxonomy', 'mb-custom-taxonomy' ),
			'search_items'       => __( 'Search Taxonomy', 'mb-custom-taxonomy' ),
			'not_found'          => __( 'Not found', 'mb-custom-taxonomy' ),
			'not_found_in_trash' => __( 'Not found in Trash', 'mb-custom-taxonomy' ),
		);
		$args   = array(
			'label'        => __( 'Taxonomies', 'mb-custom-taxonomy' ),
			'labels'       => $labels,
			'supports'     => false,
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-exerpt-view',
			'can_export'   => true,
			'rewrite'      => false,
			'query_var'    => false,
		);
		register_post_type( 'mb-taxonomy', $args );
	}

	/**
	 * Get all registered taxonomies
	 * @return array
	 */
	public function get_taxonomies()
	{
		// This array stores all registered custom taxonomies
		$taxonomies = array();

		// Get all post where where post_type = mb-taxonomy
		$mb_taxonomies = get_posts( array(
			'posts_per_page' => - 1,
			'post_status'    => 'publish',
			'post_type'      => 'mb-taxonomy',
		) );

		foreach ( $mb_taxonomies as $taxonomy )
		{
			// Get all post meta from current post
			$post_meta = get_post_meta( $taxonomy->ID );
			// Create array that contains Labels of this current custom taxonomy
			$labels = array();
			// Create array that contains arguments of this current custom taxonomy
			$args = array();

			foreach ( $post_meta as $key => $value )
			{
				// If post meta has prefix 'label' then add it to $labels
				if ( false !== strpos( $key, 'label' ) )
				{
					$data = 1 == count( $value ) ? $value[0] : $value;

					$labels[str_replace( 'label_', '', $key )] = $data;
				}
				// If post meta has prefix 'args' then add it to $args
				elseif ( false !== strpos( $key, 'args' ) )
				{
					$data = 1 == count( $value ) ? $value[0] : $value;
					$data = is_numeric( $data ) ? ( 1 == intval( $data ) ? true : false ) : $data;

					$args[str_replace( 'args_', '', $key )] = $data;
				}
			}

			$taxonomies[] = $this->set_up_taxonomy( $labels, $args );
		}

		return $taxonomies;
	}

	/**
	 * Setup labels, arguments for a custom taxonomy
	 *
	 * @param array $labels
	 * @param array $args
	 * @return array
	 */
	public function set_up_taxonomy( $labels = array(), $args = array() )
	{
		// Default labels
		$default_labels = array(
			'menu_name'          => $labels['name'],
			'name_admin_bar'     => $labels['singular_name'],
			'add_new'            => __( 'Add New', 'mb-custom-taxonomy' ),
			'add_new_item'       => sprintf( __( 'Add New %s', 'mb-custom-taxonomy' ), $labels['singular_name'] ),
			'new_item'           => sprintf( __( 'New %s', 'mb-custom-taxonomy' ), $labels['singular_name'] ),
			'edit_item'          => sprintf( __( 'Edit %s', 'mb-custom-taxonomy' ), $labels['singular_name'] ),
			'view_item'          => sprintf( __( 'View %s', 'mb-custom-taxonomy' ), $labels['singular_name'] ),
			'update_item'        => sprintf( __( 'Update %s', 'mb-custom-taxonomy' ), $labels['singular_name'] ),
			'all_items'          => sprintf( __( 'All %s', 'mb-custom-taxonomy' ), $labels['name'] ),
			'search_items'       => sprintf( __( 'Search %s', 'mb-custom-taxonomy' ), $labels['name'] ),
			'parent_item_colon'  => sprintf( __( 'Parent %s:', 'mb-custom-taxonomy' ), $labels['name'] ),
			'not_found'          => sprintf( __( 'No %s found.', 'mb-custom-taxonomy' ), $labels['name'] ),
			'not_found_in_trash' => sprintf( __( 'No %s found in Trash.', 'mb-custom-taxonomy' ), $labels['name'] ),
		);

		$labels = wp_parse_args( $labels, $default_labels );

		// Default arguments
		$default_args = array(
			'labels'              => $labels,
			'description'         => sprintf( __( '%s GUI', 'mb-custom-taxonomy' ), $labels['name'] ),
			'public'              => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'query_var'           => true,
			'rewrite'             => array( 'slug' => $args['post_type'] ),
			'capability_type'     => 'post',
			'hierarchical'        => false,
			'menu_position'       => null,
			'menu_icon'           => 'dashicons-admin-appearance',
			'has_archive'         => true,
			'can_export'          => true,
			'show_in_nav_menus'   => true,
			'exclude_from_search' => false,
		);

		$args = wp_parse_args( $args, $default_args );
		return $args;
	}

	/**
	 * Custom post updated messages
	 *
	 * @param array $messages
	 * @return array
	 */
	public function updated_message( $messages )
	{
		$post = get_post();

		$messages['mb-taxonomy'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Taxonomy updated.', 'mb-custom-taxonomy' ),
			2  => __( 'Custom field updated.', 'mb-custom-taxonomy' ),
			3  => __( 'Custom field deleted.', 'mb-custom-taxonomy' ),
			4  => __( 'Taxonomy updated.', 'mb-custom-taxonomy' ),
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Taxonomy restored to revision from %s.', 'mb-custom-taxonomy' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => __( 'Taxonomy published.', 'mb-custom-taxonomy' ),
			7  => __( 'Taxonomy saved.', 'mb-custom-taxonomy' ),
			8  => __( 'Taxonomy submitted.', 'mb-custom-taxonomy' ),
			9  => sprintf( __( 'Taxonomy scheduled for: <strong>%s</strong>.', 'mb-custom-taxonomy' ), date_i18n( __( 'M j, Y @ G:i', 'mb-custom-taxonomy' ), strtotime( $post->post_date ) ) ),
			10 => __( 'Taxonomy draft updated.', 'mb-custom-taxonomy' ),
		);
		return $messages;
	}

	/**
	 * Custom post management WordPress messages
	 *
	 * @param array $bulk_messages
	 * @param array $bulk_counts
	 * @return array
	 */
	public function bulk_updated_messages( $bulk_messages, $bulk_counts )
	{
		$bulk_messages['mb-taxonomy'] = array(
			'updated'   => sprintf( _n( '%s taxonomy updated.', '%s taxonomies updated.', $bulk_counts['updated'], 'mb-custom-taxonomy' ), $bulk_counts['updated'] ),
			'locked'    => sprintf( _n( '%s taxonomy not updated, somebody is editing.', '%s taxonomies not updated, somebody is editing.', $bulk_counts['locked'], 'mb-custom-taxonomy' ), $bulk_counts['locked'] ),
			'deleted'   => sprintf( _n( '%s taxonomy permanently deleted.', '%s taxonomies permanently deleted.', $bulk_counts['deleted'], 'mb-custom-taxonomy' ), $bulk_counts['deleted'] ),
			'trashed'   => sprintf( _n( '%s taxonomy moved to the Trash.', '%s taxonomies moved to the Trash.', $bulk_counts['trashed'], 'mb-custom-taxonomy' ), $bulk_counts['trashed'] ),
			'untrashed' => sprintf( _n( '%s taxonomy restored from the Trash.', '%s taxonomies restored from the Trash.', $bulk_counts['untrashed'], 'mb-custom-taxonomy' ), $bulk_counts['untrashed'] ),
		);
		return $bulk_messages;
	}
}
