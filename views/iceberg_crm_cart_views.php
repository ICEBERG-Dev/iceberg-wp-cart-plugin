<?php

function add_iceberg_crm_cart_menu_page() {
    add_options_page(
        'Set up your Iceberg Cart!',
        'Iceberg Cart',
        'manage_options',
        'iceberg_crm_cart',
        'iceberg_crm_cart_settings_page_cart'
    );
}
function iceberg_crm_cart_settings_page_cart() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post" style="padding: 5px 25px;background-color: rgb(232, 232, 232);border-radius: 10px;margin: 10px 0px;">
        <?php
            settings_errors('iceberg_crm_cart');
            do_settings_sections('iceberg_crm_cart');
            submit_button();
        ?>
        </form>
        <?php
            if (false){
            // if (check_token_exists_cart()){
                $handler_url_cat = plugin_dir_url( __FILE__ ) . '../admin/iceberg_crm_cart_handle_synch_cat.php';
                $handler_url_prod = plugin_dir_url( __FILE__ ) . '../admin/iceberg_crm_cart_handle_synch_prod.php';
                ?>
                 <div style="padding: 5px 25px;background-color: rgb(232, 232, 232);border-radius: 10px;margin: 10px 0px;">
                    <h2>Step 1. Synch categories from ICEBERG CRM!</h2>
                    <p>This action will rewrite some of the previously synchronized information about your categories.</p>
                    <form action="<?php echo esc_url( $handler_url_cat ); ?>" method="post">
                        <button class="button button-primary" style="margin-bottom: 20px">Synch categories now</button>
                    </form>
                </div>
                <div style="padding: 5px 25px;background-color: rgb(232, 232, 232);border-radius: 10px;margin: 10px 0px;">
                    <h2>Step 2. Synch products from ICEBERG CRM!</h2>
                    <p>This action will rewrite some of the previously synchronized information about your products.</p>
                    <form action="<?php echo esc_url( $handler_url_prod ); ?>" method="post">
                        <button class="button button-primary" style="margin-bottom: 20px">Synch products now</button>
                    </form>
                </div>
                <?php
            }
        ?>
    </div>
    <?php
}
function check_token_exists_cart() {
    global $wpdb;
    
    // Check if table exists
    $table_name = $wpdb->prefix . "iceberg_crm_cart_tokens";
    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
      return false;
    }
    
    // Check if token field exists and is not empty
    $token = $wpdb->get_var("SELECT token FROM $table_name LIMIT 1");
    if(empty($token)) {
      return false;
    }
    
    return true;
}
