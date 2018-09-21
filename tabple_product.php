<?php
/*
  Plugin Name: WP Table Product
  Plugin URI: https://sadecweb.com/
  Description: 
  Version: 1.0
  Author: Sadecweb
  Author URI: https://www.sadecweb.com/
  Text Domain: sadecweb
  Domain Path: /languages
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define WC_PLUGIN_FILE.
if ( ! defined( 'WTP_PLUGIN_PATH' ) ) {
    define( 'WTP_PLUGIN_PATH', plugin_dir_path( __FILE__ ));
    define( 'WTP_PLUGIN_URL', plugin_dir_url( __FILE__ ));
}

require_once("inc/admin_page.php");
require_once("inc/woocommerce.php");
require_once("inc/product_type.php");
require_once("inc/functions.php");
require_once("inc/frontend.php");
