<?php
add_action('admin_menu', 'wpt_plugin_menu');
function wpt_plugin_menu() {
    add_menu_page(__('Pricing Table Products', 'sadecweb'), __('Pricing table ', 'sadecweb'), 'manage_options', 'pricing-table-product', 'wpt_plugin_options');

    add_submenu_page('pricing-table-product', __('Settings', 'sadecweb'), __('Settings', 'sadecweb'), 'manage_options', 'sub-page', 'wpt_plugin_settings');

    add_action( 'admin_init', 'register_wpt_plugin_settings' );
}

function register_wpt_plugin_settings(){
    register_setting( 'wpt_plugin_setting_group', 'wpt_role' );
}

function wpt_plugin_settings() {
    ?>
    <h2><?php _e( 'Setting Pricing Table', 'sadecweb' ); ?></h2>
    <form method="post" action="options.php">
    <?php settings_fields( 'wpt_plugin_setting_group' ); ?>
    <?php do_settings_sections( 'wpt_plugin_setting_group' ); ?>
    <table class="form-table">
        <tr valign="top"><td>Role Table Price</td></tr>
        
    <?php
    global $wp_roles;
    $roles = $wp_roles->get_names();
   if (is_array($roles)) foreach($roles as $key => $role) {
        $wpt_role = get_option('wpt_role');
        $checked = '';
        if(is_array($wpt_role) && in_array($key, $wpt_role)){
            $checked = 'checked';
        }
    ?>
        <tr valign="top"><td><label><input type="checkbox" name="wpt_role[]" value="<?php echo $key ?>" <?php echo $checked ?>/><?php echo $role ?></label></td></tr>
    <?php } ?>
    </table>
    <?php submit_button(); ?>
    </form>
<?php
}

function wpt_plugin_options() {
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
    }
    global $wp_roles;
    $roles_name = $wp_roles->get_names();
    $cats = array(16, 17, 18);
    $i = 0;
    $html_role = '';
    $html_title = '';
    $html_sub_title = '';
	$roles = get_option('wpt_role');
    if (is_array($roles)) foreach($roles as $key) {
        $html_title .= '<td colspan="2" style="text-align: center;"><h4>'.$roles_name[$key].'</h4></td>';
        $html_sub_title .= '<td class="single" style="text-align: center;"><h4>Single</h4></td>
		<td class="double" style="text-align: center;"><h4>Double</h4></td>';
        $i += 2;
    }
	?>
 <link rel='stylesheet' id='thickbox-css'  href='<?php echo WTP_PLUGIN_URL . 'css/price_box.css' ;?>' type='text/css' media='all' />
   <h2 id = 'table_price_head'><?php _e( 'Pricing Table ', 'sadecweb' ); ?></h2>
    <table id="table-prices" >
        <tbody>
        <?php 
        foreach($cats as $cat_id) : 
        $term = get_term($cat_id, 'product_cat');
        ?>
			<tr class="name-cat">
				<td><h3><?php echo $term->name ?></h3></td>
				<td colspan="<?php echo $i; ?>"></td>
			</tr>
            <tr class="name-roles">
                <td  class = "product_name"></td>
                <?php echo $html_title ; ?>
            </tr>
            <tr class="name-roles-sub">
                <td class="product_name"></td>
                <?php echo $html_sub_title ; ?>
            </tr>
            <?php
            $args_post = array(
				'posts_per_page'   => -1,
                'post_type' => 'product',
                'order' => 'ASC',
                'orderby' => 'id',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'product_cat',
                        'field' => 'term_id',
                        'terms' => $cat_id,
                    )
                )
            );
            $products = get_posts($args_post);
            if(!empty($products)) :
                foreach($products as $product) :
                ?>
                <tr class="product" data-id="<?php echo $product->ID ?>">
                    <td class = "product_name"><?php echo get_the_title($product->ID); ?></td>
                    <?php
                    if (is_array($roles)) foreach($roles as $key) {
                        echo '<td class="single"><input type = "number" min="0" step="0.1" placeholder = "N/A" name="_value_single_'.$key.'" id="_value_single_'.$key.'" value="'.get_post_meta($product->ID, '_value_single_'.$key, true).'" size="10"/></td>';
                        echo '<td class="double"><input type = "number" min="0" step="0.1" placeholder = "N/A" name="_value_double_'.$key.'" id="_value_double_'.$key.'" value="'.get_post_meta($product->ID, '_value_double_'.$key, true).'" size="10"/></td>';
                    }
	                ?>
                </tr>
            <?php 
                endforeach;
            endif; ?>
        <?php endforeach; ?>
        </tbody>
    </table>
    <button id="button-save">Save</button>
    <script>
        jQuery(function($){
            $(document).on('click', '#button-save', function(){
                var row_products = [];
                $('#table-prices .product').each(function(){
                    var _id = $(this).data('id');
                    var _value = [];
                    $(this).find('input').each(function(){
                        _value.push({'meta': $(this).attr('name'), 'value': $(this).val()});
                    });
                    row_products.push({'id': _id, 'data': _value})
                });
                var data = {'action': 'wtp_save_table_price', 'product_data': row_products};
                $.post('<?php echo admin_url( 'admin-ajax.php' ); ?>', data, function(response){
                    location.reload();
                });
            })
        });
    </script>
    <?php
}

?>