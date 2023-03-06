<?php
/*
Plugin Name: Iceberg CRM Cart
Description: Hey there! This is the ICEBERG crm reference!
Contributors: iceberg group
Author: Iceberg Group
Author URI: https://iceberg-crm.ru
Version: 0.1.0
*/

require_once plugin_dir_path( __FILE__ ) . 'themes/remote_params.php';
require_once plugin_dir_path( __FILE__ ) . 'admin/iceberg_crm_cart_admin_func.php';
require_once plugin_dir_path( __FILE__ ) . 'views/iceberg_crm_cart_views.php';

function iceberg_crm_cart_install() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'iceberg_crm_cart_tokens';
    $charset_collate = $wpdb->get_charset_collate();
 
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $sql = "CREATE TABLE $table_name (
          id mediumint(9) NOT NULL AUTO_INCREMENT,
          token text NOT NULL,
          PRIMARY KEY  (id)
        ) $charset_collate;";
 
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }
    
    
}
function iceberg_crm_cart_uninstall() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'iceberg_crm_cart_tokens';
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}

function iceberg_crm_cart_check_woocommerce_active() {
    if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
        add_action( 'admin_notices', 'iceberg_crm_cart_woocommerce_required_admin_notice' );
        deactivate_plugins( 'iceberg-crm-cart/iceberg-crm-cart.php' );
    }
}
function iceberg_crm_cart_woocommerce_required_admin_notice() {
    echo '<div class="notice notice-error is-dismissible">
        <p>WooCommerce plugin is required for the <b>Iceberg CRM Cart</b> plugin to work. Please install and activate WooCommerce.</p>
    </div>';
}


function iceberg_crm_cart_send_order_products_to_server($order_id) {
	global $wpdb;
    $table_name = $wpdb->prefix . 'iceberg_crm_cart_tokens';
    $token = $wpdb->get_var("SELECT token FROM $table_name");
	
    $order = wc_get_order($order_id);
    $order_products = $order->get_items();

    $data = array();
    foreach($order_products as $order_product) {
        $product = $order_product->get_product();
        $crmID = get_post_meta($product->get_id(), 'crmID', true);
        $data[] = array(
            'id' => $crmID,
            'name' => $product->get_name(),
            'price' => $product->get_price(),
            'count' => $order_product->get_quantity(),
        );
    }
	
	$data_to_send = [
		'token'=>$token,
		'data'=>[
			'clientData'=>[
				'phone' => (int)preg_replace('/\D/', '', $order->get_billing_phone()),
				'name' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
				'address' => $order->get_billing_address_1() . ' ' . $order->get_billing_address_2(),
			    'comment' => $order->get_customer_note(),
			],
			'orderData'=>[
				'products'=>$data,
			],
		],
	];
	
	
    $url = HOST.':'.PORT;
    $response = wp_remote_post($url.'/sendForm', array(
        'method' => 'POST',
        'timeout' => 45,
        'redirection' => 5,
        'httpversion' => '1.0',
        'blocking' => true,
        'headers' => array('Content-Type' => 'application/json'),
        'body' => json_encode($data_to_send, JSON_UNESCAPED_UNICODE),
        'cookies' => array()
    ));

    if (is_wp_error($response)) {
        // error
    } else {
        // Success
    }
}

add_action('woocommerce_thankyou', 'iceberg_crm_cart_send_order_products_to_server', 10, 1);
add_action( 'admin_init', 'iceberg_crm_cart_check_woocommerce_active' ); 
add_action('admin_menu', 'add_iceberg_crm_cart_menu_page');
add_action('admin_init', 'add_token_form_section_cart');
add_action('admin_init', 'handle_token_authentication_cart');
register_activation_hook( __FILE__, 'iceberg_crm_cart_install' );
register_deactivation_hook( __FILE__, 'iceberg_crm_cart_uninstall' );
