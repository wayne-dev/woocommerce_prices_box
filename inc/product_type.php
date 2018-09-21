<?php

/**
 * Register the custom product type after init
 */
function register_table_pricing_product_type() {
	/**
	 * This should be in its own separate file.
	 */
	class WC_Product_table_pricing extends WC_Product {
		public function __construct( $product ) {
			$this->product_type = 'table_pricing';
			parent::__construct( $product );
		}
	}
}
//add_action( 'plugins_loaded', 'register_table_pricing_product_type' );
/**
 * Add to product type drop down.
 */
function add_table_pricing_product( $types ){
	// Key should be exactly the same as in the class
	$types[ 'table_pricing' ] = __( 'Table pricing' );
	return $types;
}
//add_filter( 'product_type_selector', 'add_table_pricing_product' );
/**
 * Show pricing fields for table_pricing product.
 */
function table_pricing_custom_js() {
	if ( 'product' != get_post_type() ) :
		return;
	endif;
	?><script type='text/javascript'>
		jQuery( document ).ready( function() {
			jQuery( '.options_group.pricing' ).addClass( 'show_if_table_pricing' ).show();
		});
	</script><?php
}
//add_action( 'admin_footer', 'table_pricing_custom_js' );
/**
 * Add a custom product tab.
 */
function custom_product_tabs( $tabs) {
	$tabs['rental'] = array(
		'label'		=> __( 'Rental', 'woocommerce' ),
		'target'	=> 'pricing_options',
		'class'		=> array( 'show_if_table_pricing', 'show_if_variable_rental'  ),
	);
	return $tabs;
}
//add_filter( 'woocommerce_product_data_tabs', 'custom_product_tabs' );
/**
 * Contents of the rental options product tab.
 */
function pricing_options_product_tab_content() {
	global $post;
	$_pricing_type 		= get_post_meta( $post->ID, '_pricing_type' ,true)	;
	$_pricing_width 	= get_post_meta( $post->ID, '_pricing_width' ,true)	;
	$_pricing_height 	= get_post_meta( $post->ID, '_pricing_height' ,true)	;
		?>
		<?php
			/*woocommerce_wp_checkbox( array(
				'id' 		=> '_enable_renta_option',
				'label' 	=> __( 'Enable rental option X', 'woocommerce' ),
			) );*/
			woocommerce_wp_checkbox( array(
				'label' 	=> __( 'Enable pricing', 'woocommerce' ),
				'id'			=> 'pricing_type',
				'cbvalue' 			=> 'yes',
				'value' 			=> $_pricing_type,
			) );
		?>
		<div id = 'pricing_table_control'>
		<?php
			woocommerce_wp_text_input( array(
				'label' 	=> __( '<b>Width (ft)</b> : from', 'woocommerce' ),
				'id'			=> 'min_width',
				'name'			=> '_pricing_width[0]',
				'wrapper_class' 	=> 'cols_2',
				'value' 	=> $_pricing_width[0],
				'type' 	=> 'number',
				'custom_attributes' 	=> array(
					'max' => 100,
					'min' => 1,
					'step' => 1 ,
					),
			) );
			woocommerce_wp_text_input( array(
				'label' 	=> __( 'to', 'woocommerce' ),
				'id'			=> 'max_width',
				'name'			=> '_pricing_width[1]',
				'wrapper_class' 	=> 'cols_2',
				'type' 	=> 'number',
				'value' 	=> $_pricing_width[1],
				'custom_attributes' 	=> array(
					'max' => 100,
					'min' => 2,
					'step' => 1 ,
					),
			) );
			woocommerce_wp_text_input( array(
				'label' 	=> __( '<b>Height (ft)</b> : from', 'woocommerce' ),
				'id'			=> 'min_height',
				'name'			=> '_pricing_height[0]',
				'wrapper_class' 	=> 'cols_2',
				'type' 	=> 'number',
				'value' 	=> $_pricing_height[0],
				'custom_attributes' 	=> array(
					'max' => 100,
					'min' => 1,
					'step' => 1 ,
					),
			) );
			woocommerce_wp_text_input( array(
				'label' 	=> __( 'to', 'woocommerce' ),
				'id'			=> 'max_width',
				'name'			=> '_pricing_height[1]',
				'wrapper_class' 	=> 'cols_2',
				'type' 	=> 'number',
				'value' 	=> $_pricing_height[1],
				'custom_attributes' 	=> array(
					'max' => 100,
					'min' => 2,
					'step' => 1 ,
					),
			) );
			
		?>
		</div>
		<?php if(get_post_meta( $post->ID, '_pricing_type' ,true) == 'yes' ) { ?>
		<style>
		p.form-field._regular_price_field,
		p.form-field._sale_price_field
		{display:none;}
		</style>
		<?php }else{ ?>
		<style>
		#pricing_table_control {display:none;}
		</style>
		<?php } ?>
<style>
.woocommerce_options_panel p.form-field.cols_2 {
    width: 70px;
    display: inline-block;
    padding: 5px 20px 5px 162px!important;
}
</style>
		<script type="text/javascript">
		jQuery(function($){
			console.log("ssssssssss");
			$(document).on('change','#pricing_type',function(){
				if(this.checked) {
					$('#pricing_table_control').show();	
					$('p.form-field._sale_price_field').hide();	
					$('p.form-field._regular_price_field').hide();	
				}else{
					$('#pricing_table_control').hide();	
					$('p.form-field._sale_price_field').show();	
					$('p.form-field._regular_price_field').show();	
				}
			});
		});
		</script>
		<?php
}
add_action( 'woocommerce_product_options_pricing', 'pricing_options_product_tab_content' );
/**
 * Save the custom fields.
 */
function save_pricing_option_field( $post_id ) {
	if ( isset( $_POST['_pricing_width'] ) ) {
		update_post_meta( $post_id, '_pricing_width', $_POST['_pricing_width'] );
	}
	if ( isset( $_POST['_pricing_height'] ) ) {
		update_post_meta( $post_id, '_pricing_height', $_POST['_pricing_height'] );
	}
		update_post_meta( $post_id, '_pricing_type', $_POST['pricing_type'] );
	if ( isset( $_POST['pricing_type'] ) ){
		update_post_meta( $post_id, '_regular_price', 0 );
		update_post_meta( $post_id, '_price', 0 );
	}
}
add_action( 'woocommerce_process_product_meta', 'save_pricing_option_field'  );
//add_action( 'woocommerce_process_product_meta_variable_rental', 'save_pricing_option_field'  );
/**
 * Hide Attributes data panel.
 */
function hide_attributes_data_panel( $tabs) {
	$tabs['attribute']['class'][] = 'hide_if_table_pricing hide_if_variable_rental';
	return $tabs;
}
//add_filter( 'woocommerce_product_data_tabs', 'hide_attributes_data_panel' );
//add_action( 'woocommerce_single_product_summary', 'table_pricing_template', 60 );
function table_pricing_template () {
	global $product;
	if ( 'table_pricing' == $product->get_type() ) {
		$template_path = WTP_PLUGIN_PATH . '/templates/';
		// Load the template
		wc_get_template( 'single-product/add-to-cart/table_pricing.php',
			'',
			'',
			trailingslashit( $template_path ) );
	}
}