<?php
/**
 * Controls all operations of MB Custom Taxonomy extension for creating / modifying custom taxonomy.
 *
 * @package    Meta Box
 * @subpackage MB Custom Taxonomy
 * @author     Tran Ngoc Tuan Anh <rilwis@gmail.com>
 */

/**
 * Controls all operations for creating / modifying custom taxonomy.
 */
class MB_Custom_Taxonomy_Edit {
	/**
	 * Used to prevent duplicated calls like revisions, manual hook to wp_insert_post, etc.
	 *
	 * @var bool
	 */
	public $saved = false;

	/**
	 * Initiating. Add hooks.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'rwmb_meta_boxes', array( $this, 'register_meta_boxes' ) );
		// Modify post information after save post.
		add_action( 'save_post_mb-taxonomy', array( $this, 'save_post' ) );
		// Add ng-controller to form.
		add_action( 'post_edit_form_tag', array( $this, 'add_ng_controller' ) );
	}

	/**
	 * Enqueue scripts and styles.
	 */
	public function enqueue_scripts() {
		if ( ! $this->is_screen() ) {
			return;
		}

		wp_register_script( 'angular', 'https://ajax.googleapis.com/ajax/libs/angularjs/1.4.2/angular.min.js', array(), '1.4.2', true );
		wp_enqueue_style( 'mb-custom-taxonomy', MB_CUSTOM_TAXONOMY_URL . 'css/style.css', array(), '1.0.0', false );
		wp_enqueue_script( 'mb-custom-taxonomy', MB_CUSTOM_TAXONOMY_URL . 'js/script.js', array(
			'jquery',
			'angular',
		), '1.0.0', false );

		$labels = array(
			'menu_name'                  => '%name%',
			'all_items'                  => __( 'All %name%', 'mb-custom-taxonomy' ),
			'edit_item'                  => __( 'Edit %singular_name%', 'mb-custom-taxonomy' ),
			'view_item'                  => __( 'View %singular_name%', 'mb-custom-taxonomy' ),
			'update_item'                => __( 'Update %singular_name%', 'mb-custom-taxonomy' ),
			'add_new_item'               => __( 'Add new %singular_name%', 'mb-custom-taxonomy' ),
			'new_item_name'              => __( 'New %singular_name%', 'mb-custom-taxonomy' ),
			'parent_item'                => __( 'Parent %singular_name%', 'mb-custom-taxonomy' ),
			'parent_item_colon'          => __( 'Parent %singular_name%:', 'mb-custom-taxonomy' ),
			'search_items'               => __( 'Search %name%', 'mb-custom-taxonomy' ),
			'popular_items'              => __( 'Popular %name%', 'mb-custom-taxonomy' ),
			'separate_items_with_commas' => __( 'Separate %name% with commas', 'mb-custom-taxonomy' ),
			'add_or_remove_items'        => __( 'Add or remove %name%', 'mb-custom-taxonomy' ),
			'choose_from_most_used'      => __( 'Choose most used %name%', 'mb-custom-taxonomy' ),
			'not_found'                  => __( 'No %name% found', 'mb-custom-taxonomy' ),
		);
		wp_localize_script( 'mb-custom-taxonomy', 'MBTaxonomyLabels', $labels );
	}

	/**
	 * Register meta boxes for add/edit mb-taxonomy page.
	 *
	 * @param array $meta_boxes Meta boxes.
	 *
	 * @return array
	 */
	public function register_meta_boxes( $meta_boxes ) {
		$label_prefix = 'label_';
		$args_prefix  = 'args_';

		$basic_fields    = array(
			array(
				'name'        => __( 'Plural name', 'mb-custom-taxonomy' ),
				'id'          => $label_prefix . 'name',
				'type'        => 'text',
				'placeholder' => __( 'General name for the taxonomy', 'mb-custom-taxonomy' ),
			),
			array(
				'name'        => __( 'Singular name', 'mb-custom-taxonomy' ),
				'id'          => $label_prefix . 'singular_name',
				'type'        => 'text',
				'placeholder' => __( 'Name for one object of this taxonomy', 'mb-custom-taxonomy' ),
			),
			array(
				'name' => __( 'Slug', 'mb-custom-taxonomy' ),
				'id'   => $args_prefix . 'taxonomy',
				'type' => 'text',
			),
		);
		$labels_fields   = array(
			array(
				'name'        => __( 'Menu name', 'mb-custom-taxonomy' ),
				'id'          => $label_prefix . 'menu_name',
				'type'        => 'text',
				'placeholder' => __( 'The menu name text', 'mb-custom-taxonomy' ),
			),
			array(
				'name'        => __( 'All items', 'mb-custom-taxonomy' ),
				'id'          => $label_prefix . 'all_items',
				'type'        => 'text',
				'placeholder' => __( 'The all items text used in the menu', 'mb-custom-taxonomy' ),
			),
			array(
				'name'        => __( 'Edit item', 'mb-custom-taxonomy' ),
				'id'          => $label_prefix . 'edit_item',
				'type'        => 'text',
				'placeholder' => __( 'The edit item text', 'mb-custom-taxonomy' ),
			),
			array(
				'name'        => __( 'View item', 'mb-custom-taxonomy' ),
				'id'          => $label_prefix . 'view_item',
				'type'        => 'text',
				'placeholder' => __( 'The view item text', 'mb-custom-taxonomy' ),
			),
			array(
				'name'        => __( 'Update item', 'mb-custom-taxonomy' ),
				'id'          => $label_prefix . 'update_item',
				'type'        => 'text',
				'placeholder' => __( 'The update item text', 'mb-custom-taxonomy' ),
			),
			array(
				'name'        => __( 'Add new item', 'mb-custom-taxonomy' ),
				'id'          => $label_prefix . 'add_new_item',
				'type'        => 'text',
				'placeholder' => __( 'The add new item text', 'mb-custom-taxonomy' ),
			),
			array(
				'name'        => __( 'New item name', 'mb-custom-taxonomy' ),
				'id'          => $label_prefix . 'new_item_name',
				'type'        => 'text',
				'placeholder' => __( 'The new item name text', 'mb-custom-taxonomy' ),
			),
			array(
				'name'        => __( 'Parent Item', 'mb-custom-taxonomy' ),
				'id'          => $label_prefix . 'parent_item',
				'type'        => 'text',
				'placeholder' => __( 'The parent item text', 'mb-custom-taxonomy' ),
			),
			array(
				'name'        => __( 'Parent Item Colon', 'mb-custom-taxonomy' ),
				'id'          => $label_prefix . 'parent_item_colon',
				'type'        => 'text',
				'placeholder' => __( 'The same as parent item, but with colon (:) in the end', 'mb-custom-taxonomy' ),
			),
			array(
				'name'        => __( 'Search items', 'mb-custom-taxonomy' ),
				'id'          => $label_prefix . 'search_items',
				'type'        => 'text',
				'placeholder' => __( 'The search items text', 'mb-custom-taxonomy' ),
			),
			array(
				'name'        => __( 'Popular items', 'mb-custom-taxonomy' ),
				'id'          => $label_prefix . 'popular_items',
				'type'        => 'text',
				'placeholder' => __( 'The popular items text', 'mb-custom-taxonomy' ),
			),
			array(
				'name'        => __( 'Separate items with commas', 'mb-custom-taxonomy' ),
				'id'          => $label_prefix . 'separate_items_with_commas',
				'type'        => 'text',
				'placeholder' => __( 'The separate items with commas text', 'mb-custom-taxonomy' ),
			),
			array(
				'name'        => __( 'Add or remove items', 'mb-custom-taxonomy' ),
				'id'          => $label_prefix . 'add_or_remove_items',
				'type'        => 'text',
				'placeholder' => __( 'The add or remove items text', 'mb-custom-taxonomy' ),
			),
			array(
				'name'        => __( 'Choose from most used', 'mb-custom-taxonomy' ),
				'id'          => $label_prefix . 'choose_from_most_used',
				'type'        => 'text',
				'placeholder' => __( 'The choose from most used text', 'mb-custom-taxonomy' ),
			),
			array(
				'name'        => __( 'Not found', 'mb-custom-taxonomy' ),
				'id'          => $label_prefix . 'not_found',
				'type'        => 'text',
				'placeholder' => __( 'The not found text', 'mb-custom-taxonomy' ),
			),
		);
		$advanced_fields = array(
			array(
				'name' => __( 'Public?', 'mb-custom-taxonomy' ),
				'id'   => $args_prefix . 'public',
				'type' => 'checkbox',
				'std'  => 1,
				'desc' => __( 'If the taxonomy should be publicly queryable.', 'mb-custom-taxonomy' ),
			),
			array(
				'name' => __( 'Show UI?', 'mb-custom-taxonomy' ),
				'id'   => $args_prefix . 'show_ui',
				'type' => 'checkbox',
				'std'  => 1,
				'desc' => __( 'Whether to generate a default UI for managing this taxonomy.', 'mb-custom-taxonomy' ),
			),
			array(
				'name' => __( 'Show in menu?', 'mb-custom-taxonomy' ),
				'id'   => $args_prefix . 'show_in_menu',
				'type' => 'checkbox',
				'std'  => 1,
				'desc' => __( 'Where to show the taxonomy in the admin menu. <code>show_ui</code> must be <code>true</code>.', 'mb-custom-taxonomy' ),
			),
			array(
				'name' => __( 'Show in nav menus?', 'mb-custom-taxonomy' ),
				'id'   => $args_prefix . 'show_in_nav_menus',
				'type' => 'checkbox',
				'std'  => 1,
				'desc' => __( 'Whether taxonomy is available for selection in navigation menus.', 'mb-custom-taxonomy' ),
			),
			array(
				'name' => __( 'Show tag cloud?', 'mb-custom-taxonomy' ),
				'id'   => $args_prefix . 'show_tagcloud',
				'type' => 'checkbox',
				'std'  => 1,
				'desc' => __( 'Whether to allow the Tag Cloud widget to use this taxonomy.', 'mb-custom-taxonomy' ),
			),
			array(
				'name' => __( 'Show in quick edit?', 'mb-custom-taxonomy' ),
				'id'   => $args_prefix . 'show_in_quick_edit',
				'type' => 'checkbox',
				'std'  => 1,
				'desc' => __( 'Whether to show the taxonomy in the quick/bulk edit panel.', 'mb-custom-taxonomy' ),
			),
			array(
				'name' => __( 'Show admin column?', 'mb-custom-taxonomy' ),
				'id'   => $args_prefix . 'show_admin_column',
				'type' => 'checkbox',
				'desc' => __( 'Whether to allow automatic creation of taxonomy columns on associated post-types table.', 'mb-custom-taxonomy' ),
			),
			array(
				'name' => __( 'Show in REST?', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'show_in_rest',
				'type' => 'checkbox',
				'desc' => __( 'Whether to include the taxonomy in the REST API.', 'mb-custom-post-type' ),
			),
			array(
				'name' => __( 'REST base', 'mb-custom-post-type' ),
				'id'   => $args_prefix . 'rest_base',
				'type' => 'checkbox',
				'desc' => __( 'To change the base url of REST API route. Default is taxonomy slug.', 'mb-custom-post-type' ),
			),
			array(
				'name' => __( 'Hierarchical?', 'mb-custom-taxonomy' ),
				'id'   => $args_prefix . 'hierarchical',
				'type' => 'checkbox',
				'desc' => __( 'Is this taxonomy hierarchical (have descendants) like categories or not hierarchical like tags.', 'mb-custom-taxonomy' ),
			),
			array(
				'name' => __( 'Query var', 'mb-custom-taxonomy' ),
				'id'   => $args_prefix . 'query_var',
				'type' => 'checkbox',
				'std'  => 1,
				'desc' => __( 'Uncheck to disable the query var, check to use the taxonomy\'s "name" as query var.', 'mb-custom-taxonomy' ),
			),
			array(
				'name' => __( 'Sort?', 'mb-custom-taxonomy' ),
				'id'   => $args_prefix . 'sort',
				'type' => 'checkbox',
				'desc' => __( 'Whether this taxonomy should remember the order in which terms are added to objects.', 'mb-custom-taxonomy' ),
			),
		);

		// Basic settings.
		$meta_boxes[] = array(
			'id'         => 'basic-settings',
			'title'      => __( 'Basic Settings', 'mb-custom-taxonomy' ),
			'post_types' => 'mb-taxonomy',
			'fields'     => array_merge(
				$basic_fields,
				array(
					array(
						'id'   => 'btn-toggle-advanced',
						'type' => 'button',
						'std'  => __( 'Advanced', 'mb-custom-taxonomy' ),
					),
				)
			),
			'validation' => array(
				'rules'    => array(
					$label_prefix . 'name'          => array(
						'required' => true,
					),
					$label_prefix . 'singular_name' => array(
						'required' => true,
					),
					$args_prefix . 'post_type'      => array(
						'required' => true,
					),
				),
				'messages' => array(
					$label_prefix . 'name'          => array(
						'required' => __( 'Plural name is required', 'mb-custom-taxonomy' ),
					),
					$label_prefix . 'singular_name' => array(
						'required' => __( 'Singular name is required', 'mb-custom-taxonomy' ),
					),
					$args_prefix . 'post_type'      => array(
						'required' => __( 'Slug is required', 'mb-custom-taxonomy' ),
					),
				),
			),
		);

		// Labels settings.
		$meta_boxes[] = array(
			'id'         => 'label-settings',
			'title'      => __( 'Labels Settings', 'mb-custom-taxonomy' ),
			'post_types' => 'mb-taxonomy',
			'fields'     => $labels_fields,
		);

		// Advanced settings.
		$meta_boxes[] = array(
			'id'         => 'advanced-settings',
			'title'      => __( 'Advanced Settings', 'mb-custom-taxonomy' ),
			'post_types' => 'mb-taxonomy',
			'fields'     => $advanced_fields,
		);

		// Post types.
		$options    = array();
		$post_types = get_post_types( '', 'objects' );
		unset( $post_types['mb-taxonomy'], $post_types['revision'], $post_types['nav_menu_item'] );
		foreach ( $post_types as $post_type => $post_type_object ) {
			$options[ $post_type ] = $post_type_object->labels->singular_name;
		}
		$meta_boxes[] = array(
			'title'      => __( 'Assign To Post Types', 'mb-custom-taxonomy' ),
			'context'    => 'side',
			'post_types' => 'mb-taxonomy',
			'priority'   => 'low',
			'fields'     => array(
				array(
					'id'      => $args_prefix . 'post_types',
					'type'    => 'checkbox_list',
					'options' => $options,
				),
			),
		);

		$fields = array_merge( $basic_fields, $labels_fields, $advanced_fields );

		// Add ng-model attribute to all fields.
		foreach ( $fields as $field ) {
			add_filter( 'rwmb_' . $field['id'] . '_html', array( $this, 'modify_field_html' ), 10, 3 );
		}

		return $meta_boxes;
	}

	/**
	 * Modify html output of field
	 *
	 * @param string $html  HTML out put of the field.
	 * @param array  $field Field parameters.
	 * @param string $meta  Meta value.
	 *
	 * @return string
	 */
	public function modify_field_html( $html, $field, $meta ) {
		// Labels.
		if ( 0 === strpos( $field['id'], 'label_' ) ) {
			$model = substr( $field['id'], 6 );
			$html  = str_replace( '>', sprintf(
				' ng-model="labels.%s" ng-init="labels.%s=\'%s\'"%s>',
				$model,
				$model,
				$meta,
				in_array( $model, array( 'name', 'singular_name' ), true ) ? ' ng-change="updateLabels()"' : ''
			), $html );
			$html  = preg_replace( '/value="(.*?)"/', 'value="{{labels.' . $model . '}}"', $html );
		} // Slug
		elseif ( 'args_taxonomy' === $field['id'] ) {
			$html = str_replace( '>', sprintf(
				' ng-model="taxonomy" ng-init="taxonomy=\'%s\'">',
				$meta
			), $html );
			$html = preg_replace( '/value="(.*?)"/', 'value="{{taxonomy}}"', $html );
		}

		return $html;
	}

	/**
	 * Modify post information and post meta after save post.
	 *
	 * @param int $post_id Post ID.
	 */
	public function save_post( $post_id ) {
		// If label_singular_name is empty or if this function is called to prevent duplicated calls like revisions, manual hook to wp_insert_post, etc.
		if ( empty( $_POST['label_singular_name'] ) || true === $this->saved ) {
			return;
		}

		$this->saved = true;

		// Update post title.
		$post = array(
			'ID'         => $post_id,
			'post_title' => $_POST['label_singular_name'],
		);

		wp_update_post( $post );

		// Flush rewrite rules after create new or edit taxonomies.
		flush_rewrite_rules();
	}

	/**
	 * Check if current screen is editing mb-taxonomy?
	 *
	 * @return boolean
	 */
	public function is_screen() {
		$screen = get_current_screen();

		return 'post' === $screen->base && 'mb-taxonomy' === $screen->post_type;
	}

	/**
	 * Add angular controller to form tag.
	 */
	public function add_ng_controller() {
		if ( $this->is_screen() ) {
			echo 'ng-controller="TaxonomyController"';
		}
	}
}
