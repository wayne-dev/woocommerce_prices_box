<?php
//add_filter( 'product_type_options', 'add_numberbox_product_option' );
function add_numberbox_product_option( $product_type_options ) {
	$product_type_options['numberbox'] = array(
		'id'            => '_numberbox',
		'wrapper_class' => 'show_if_simple',
		'label'         => __( 'Numberbox', 'sadecweb' ),
		'description'   => __( '', 'sadecweb' ),
		'default'       => 'no'
	);
	return $product_type_options;
}

//add_filter( 'woocommerce_product_data_tabs', 'wtf_munberbox_product_panel_tabs' );
function wtf_munberbox_product_panel_tabs($tabs) {
    // Adds the new tab
    $tabs['numberbox'] = array(
		'label'		=> __( 'Numberbox', 'sadecweb' ),
		'target'	=> 'wtf_numberbox_options_tabs',
		'class'		=> array( 'show_if_simple' )
    );
	return $tabs;
}

//add_action( 'woocommerce_product_write_panels', 'wtf_munberbox_product_panel_content' );
function wtf_munberbox_product_panel_content() {
    // The new tab content
    global $post;
    ?>
    <div id="wtf_numberbox_options_tabs" class="panel wc-metaboxes-wrapper">
	<div class='options_group'>
	<?php
		global $wp_roles;
		$roles = $wp_roles->get_names();
		foreach ($roles as $key => $role) { ?>
			<div>
				<?php echo $role ?>
				<?php 
				woocommerce_wp_text_input( 
					array(
					'id'				=> 	'_value_single_'.$key,
					'label'				=> 	__( 'Price single', 'sadecweb' ),
					'desc_tip'			=> 	'false',
					'description'		=> 	__( '', 'sadecweb' ),
					'data_type' 		=> 	'price',
					'value'				=>	(!empty($value = get_post_meta($post->ID, '_value_single_'.$key, true))) ? $value : '',
					'custom_attributes'	=> 	array(
												'min'	=> '1',
												'step'	=> '1',
											),
					) 
				);
				woocommerce_wp_text_input( 
					array(
					'id'				=> 	'_value_double_'.$key,
					'label'				=> 	__( 'Price double', 'sadecweb' ),
					'desc_tip'			=> 	'false',
					'description'		=> 	__( '', 'sadecweb' ),
					'data_type' 		=> 	'price',
					'value'				=>	(!empty($value = get_post_meta($post->ID, '_value_double_'.$key, true))) ? $value : '',
					'custom_attributes'	=> 	array(
												'min'	=> '1',
												'step'	=> '1',
											),
					) 
				);
				?>
			</div>
		<?php
		}
	?>
	</div>
    </div>
    <?php
}

//add_action( 'woocommerce_process_product_meta_simple', 'save_giftcard_option_fields'  );
function save_giftcard_option_fields( $post_id ) {
	global $wp_roles;
	$roles = $wp_roles->get_names();
	//error_log(print_r($_POST, true));
	foreach ($roles as $key => $role) {
		if ( isset( $_POST['_value_single_'.$key] ) ) :
			update_post_meta( $post_id, '_value_single_'.$key, $_POST['_value_single_'.$key] );
		endif;
		if ( isset( $_POST['_value_double_'.$key] ) ) :
			update_post_meta( $post_id, '_value_double_'.$key, $_POST['_value_double_'.$key]);
		endif;
	}
}

add_action( 'wp_ajax_wtp_save_table_price', 'wtp_save_table_price' );
add_action( 'wp_ajax_nopriv_wtp_save_table_price', 'wtp_save_table_price' );
function wtp_save_table_price(){
	$data = $_POST['product_data'];
	foreach($data as $product){
		$product_id = $product['id'];
		foreach ($product['data'] as $meta_box) {
			update_post_meta( $product_id, $meta_box['meta'],  $meta_box['value'] );
		}
	}
}


//add_action( 'init', 'wtp_taxonomy_Role' );
function wtp_taxonomy_Role()  {
    $labels = array(
		'name'                       => _x( 'Roles', 'taxonomy general name', 'textdomain' ),
		'singular_name'              => _x( 'Role', 'taxonomy singular name', 'textdomain' ),
		'search_items'               => __( 'Search Roles', 'textdomain' ),
		'popular_items'              => __( 'Popular Roles', 'textdomain' ),
		'all_items'                  => __( 'All Roles', 'textdomain' ),
		'parent_item'                => null,
		'parent_item_colon'          => null,
		'edit_item'                  => __( 'Edit Role', 'textdomain' ),
		'update_item'                => __( 'Update Role', 'textdomain' ),
		'add_new_item'               => __( 'Add New Role', 'textdomain' ),
		'new_item_name'              => __( 'New Role Name', 'textdomain' ),
		'separate_items_with_commas' => __( 'Separate Roles with commas', 'textdomain' ),
		'add_or_remove_items'        => __( 'Add or remove Roles', 'textdomain' ),
		'choose_from_most_used'      => __( 'Choose from the most used Roles', 'textdomain' ),
		'not_found'                  => __( 'No Roles found.', 'textdomain' ),
		'menu_name'                  => __( 'Roles', 'textdomain' ),
	);
	$args = array(
		'hierarchical'          => false,
		'labels'                => $labels,
        'show_ui'               => false,
        'show_in_quick_edit'    => false,
		'show_admin_column'     => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => true,
		'rewrite'               => array( 'slug' => 'role' ),
	);
	register_taxonomy( 'role', 'product', $args );
}