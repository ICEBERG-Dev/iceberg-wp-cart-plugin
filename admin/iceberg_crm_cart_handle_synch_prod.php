<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');
require_once './iceberg_crm_cart_get_functions.php';

function iceberg_crm_cart_find_wpID_by_crmID($crmID) {
    $args = array(
        'taxonomy' => 'product_cat',
        'meta_query' => array(
            array(
                'key' => 'crmID',
                'value' => $crmID,
            )
        ),
        'hide_empty' => false
      );
    $terms = get_terms($args);

    if (!empty($terms)) {
        return $terms[0]->term_id;
    }
    return false;
}
function iceberg_crm_cart_is_product_exists($crmID){
    // echo "HELLO FROM HELL<br>";
    $product = get_posts(array(
        'post_type' => 'product',
        'meta_query' => array(
            array(
                'key' => 'crmID',
                'value' => $crmID,
                'compare' => '='
            )
        ),
        'hide_empty' => fasle
    ));
    var_dump($product);
    die;
    if (!empty($product)) {
        return $product;
    }
    return false;
}


$products = iceberg_crm_cart_get_products();
foreach ($products['data'] as $product) {
    $product_exist = iceberg_crm_cart_is_product_exists($product["id"]);
    // var_dump($product_exist);

    if ($product_exist){
        $newproduct = array(
            'ID' => $product_exist[0]->ID,
            'post_title' => $product['name'],
            'post_content' => $product['description'],
            'post_status' => 'pending'
        );
        $updated_product_id = wp_update_post($new_product);

        update_post_meta($updated_product_id, '_stock', $product['count']);
        update_post_meta($updated_product_id, '_manage_stock', "yes");
        update_post_meta($updated_product_id, '_regular_price', $product['price']);
        update_post_meta($updated_product_id, '_price', $product['price']);
        update_post_meta($updated_product_id, '_sku', $product['article']);
        update_post_meta($updated_product_id, '_weight', $product['weigth']);
        wp_set_object_terms($updated_product_id, array((integer)iceberg_crm_cart_find_wpID_by_crmID($product['category'])), 'product_cat');
        update_post_meta($updated_product_id, 'crmID', $product['id']);
    }else{
        $new_product = array(
            'post_title' => $product['name'],
            'post_content' => $product['description'],
            'post_status' => 'pending',
            'post_type' => 'product'
        );
        $new_product_id = wp_insert_post($new_product);
        update_post_meta($new_product_id, '_stock', $product['count']);
        update_post_meta($new_product_id, '_manage_stock', "yes");
        update_post_meta($new_product_id, '_regular_price', $product['price']);
        update_post_meta($new_product_id, '_price', $product['price']);
        update_post_meta($new_product_id, '_sku', $product['article']);
        update_post_meta($new_product_id, '_weight', $product['weigth']);
        wp_set_object_terms($new_product_id, array((integer)iceberg_crm_cart_find_wpID_by_crmID($product['category'])), 'product_cat');
        update_post_meta($new_product_id, 'crmID', $product['id']);
    }

}


// header('Location: ' . admin_url('edit.php?post_status=pending&post_type=product'));
// exit;
