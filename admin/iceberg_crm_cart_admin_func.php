<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');


function iceberg_crm_cart_handle_token_authentication_cart() {
    if (isset($_POST['iceberg_crm_cart_token'])) {
      $token = sanitize_text_field($_POST['iceberg_crm_cart_token']);
      $response = iceberg_crm_cart_send_token_to_server_cart($token);


      if ($response == 'OK') {
        add_settings_error('iceberg_crm_cart', 'iceberg_crm_cart_message', __('Token was saved successfully.', 'iceberg_crm_cart'), 'updated');
      } elseif($response == 'wrongIdentifier') {
        $token = "";
        add_settings_error('iceberg_crm_cart', 'iceberg_crm_cart_message', __('Token already in use! Try another', 'iceberg_crm_cart'), 'iceberg_crm_cart');
      } elseif($response == 'badToken') {
        $token = "";
        add_settings_error('iceberg_crm_cart', 'iceberg_crm_cart_message', __('Incorrect token! Try another', 'iceberg_crm_cart'), 'iceberg_crm_cart');
      }
      else {
        $token = "";
        add_settings_error('iceberg_crm_cart', 'iceberg_crm_cart_message', __('Token was not saved. Server response: '.$response, 'iceberg_crm_cart'));
      }
        iceberg_crm_cart_store_token_in_database_cart($token);

    }
}

function iceberg_crm_cart_send_token_to_server_cart($token) {
    // URL of the endpoint to send the request to
    $url = ICEBERG_CRM_CART_HOST.':'.ICEBERG_CRM_CART_PORT.'/auth';

    $wphost_id = parse_url(home_url())['host'];

    // Prepare the request data
    $data = array('token' => $token, 'identifier' => $wphost_id);

    // Use WordPress built-in HTTP functions to send the request
    $response = wp_remote_post($url, array(
        'method' => 'GET',
        'timeout' => 45,
        'redirection' => 5,
        'httpversion' => '1.0',
        'blocking' => true,
        'headers' => array(),
        'body' => $data,
        'cookies' => array()
    ));

    // Check if the request was successful
    if (is_wp_error($response)) {
        // Handle error
        $error_message = $response->get_error_message();
        return 'ERROR: ' . $error_message;
    } else {
        // Return the response from the server
        return $response['body'];
    }
}

function iceberg_crm_cart_store_token_in_database_cart($token) {
    global $wpdb;
    $table_name = $wpdb->prefix . "iceberg_crm_cart_tokens";
    $data = array(
        'id'=>1,
        'token' => $token
    );
    $wpdb->replace($table_name, $data);
}

function iceberg_crm_cart_add_token_form_section_cart() {
    add_settings_section(
        'iceberg_crm_cart_section',
        __('Iceberg CRM cart Settings', 'iceberg_crm_cart'),
        'iceberg_crm_cart_section_cart',
        'iceberg_crm_cart'
    );

    add_settings_field(
        'iceberg_crm_cart_token',
        __('Token', 'iceberg_crm_cart'),
        'iceberg_crm_cart_token_cart',
        'iceberg_crm_cart',
        'iceberg_crm_cart_section'
    );

    register_setting('iceberg_crm_cart', 'iceberg_crm_cart_token');
}

function iceberg_crm_cart_token_cart() {
    global $wpdb;
    $table_name = $wpdb->prefix . "iceberg_crm_cart_tokens";
    $token = $wpdb->get_var("SELECT token FROM $table_name");
    echo '<input type="text" name="iceberg_crm_cart_token" placeholder="Enter token to check and save..." value="'.esc_attr($token).'" class="regular-text">';
}
