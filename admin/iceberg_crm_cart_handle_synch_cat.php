<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');
require_once './iceberg_crm_cart_get_functions.php';



function iceberg_crm_cart_update_wc_category_by_crmID($term_id, $crmID, $name, $slug, $parent_id, $description) {
      $category_id = $term_id;
      $update_args = array(
          'name' => $name,
          'slug' => $slug,
          'parent' => $parent_id,
          'description' => $description
      );
      wp_update_term($category_id, 'product_cat', $update_args);
      update_term_meta($category_id, 'crmID', $crmID);
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

function iceberg_crm_cart_create_wc_category($name, $crmID, $slug, $parent, $description) {
  $args = array(
    'taxonomy' => 'product_cat',
    'hide_empty' => false,
    'meta_query' => array(
        array(
            'key' => 'crmID',
            'value' => $crmID
        )
    ),
  );

  $existing_category = get_terms($args);
  if (!empty($existing_category)) {
    return $existing_category[0]->term_id;
  }

  $category = array(
    'name' => $name,
    'description' => $description,
    'slug' => $slug,
    'parent' => $parent
  );

  $category_id = wp_insert_term($name, 'product_cat', $category);
  if (!is_wp_error($category_id)) {
    add_term_meta($category_id['term_id'], 'crmID', $crmID, true);
  }

  return $category_id;
}


$categories = iceberg_crm_cart_get_categories();
foreach ($categories['result'] as $category) {
  if ( $category['parent'] != 0){ $category['parent'] = iceberg_crm_cart_get_term_actual_id_by_crm_id($categories["result"], $category['parent']); }

  $args = array(
    'taxonomy' => 'product_cat',
    'meta_query' => array(
        array(
            'key' => 'crmID',
            'value' => $category['id'],
        )
    ),
    'hide_empty' => false,
  );
  $terms = get_terms($args);
  if (!empty($terms)) {
      iceberg_crm_cart_update_wc_category_by_crmID($terms[0]->term_id, $category['id'], $category['name'], $category['urlized'], $category['parent'], $category['description']);
  } else {
      iceberg_crm_cart_create_wc_category($category['name'], $category['id'], $category['urlized'], $category['parent'], $category['description']);
  }
}

header('Location: ' . admin_url('edit-tags.php?taxonomy=product_cat&post_type=product'));
exit;
