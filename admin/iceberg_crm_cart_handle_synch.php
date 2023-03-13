<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');
require_once './iceberg_crm_cart_get_functions.php';


$cats_mirr = [];

function iceberg_crm_cart_find_wpID_by_crmID($crmID) {
    global $cats_mirr;
    $array = $cats_mirr;
    $crmIDs = array_column($array, 'crmID');
    $index = array_search($crmID, $crmIDs);

    if ($index === false) {
      return "";
    } else {
      return $array[$index]['wpID'];
    }
}

function iceberg_crm_cart_get_term_actual_id_by_crm_id($array, $id){
    foreach ($array as $data) {
        if ($data['id'] === $id) {
            $term = get_term_by('name', $data['name'], 'product_cat');
            $term_id = $term->term_id;
            return $term_id;
        }
    }
    return 0;
}

function iceberg_crm_cart_is_product_exists($crm_id){
    $args = array(
        'post_type' => 'product',
        'meta_query' => array(
            array(
                'key' => 'crmID',
                'value' => $crm_id,
                'compare' => '='
            )
        )
    );
    $products = get_posts($args);

    if (count($products) > 0) {
        return $products;
    }
    return false;
}

if ($_POST['type'] === "cat"){
$categories = iceberg_crm_cart_get_categories();
foreach ($categories['result'] as $category) {
    global $cats_mirr;


    if ( $category['parent'] != 0){ $category['parent'] = iceberg_crm_cart_get_term_actual_id_by_crm_id($categories["result"], $category['parent']); }

  $term_exists = term_exists($category['name'], 'product_cat');

  if ($term_exists) {
    $term_actual_id = $term_exists["term_id"];
    $cats_mirr[] = [
        "crmID"=>$category['id'],
        "wpID"=>$term_exists["term_id"]
    ];
    $term = get_term($term_actual_id, 'product_cat');

    wp_update_term($term->term_id, 'product_cat', array(
      'slug' => $category['urlized'],
      'parent' => $category['parent'],
      'description' => $category['description'],
    ));

  } else {
    $insert_res = wp_insert_term($category['name'], 'product_cat', array(
      'slug' => $category['urlized'],
      'parent' => $category['parent'],
      'description' => $category['description'],
    ));

    $cats_mirr = [
        "crmID"=>$category['id'],
        "wpID"=>$insert_res["term_id"]
    ];
  }


}
}

if ($_POST['type'] === "prod"){
$products = iceberg_crm_cart_get_products();
foreach ($products['data'] as $product) {
    global $cats_mirr;


    if (iceberg_crm_cart_is_product_exists($product["id"])){

    }else{
        $new_product = array(
            'post_title' => $product['name'],
            'post_content' => $product['description'],
            'post_status' => 'pending',
            'post_type' => 'product'
        );

        $product_id = wp_insert_post($new_product);


        update_post_meta($product_id, '_stock', $product['count']);
        update_post_meta($product_id, '_manage_stock', "yes");
        update_post_meta($product_id, '_regular_price', $product['price']);
        update_post_meta($product_id, '_price', $product['price']);
        update_post_meta($product_id, '_sku', $product['article']);
        update_post_meta($product_id, '_weight', $product['weigth']);
        wp_set_object_terms($product_id, (int)find_wpID_by_crmID($product['category']), 'product_cat', true);
        update_post_meta($product_id, 'crmID', $product['id']);
    }

}
}



// header('Location: ' . admin_url('edit.php?post_status=pending&post_type=product'));
// exit;
?>
