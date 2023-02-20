<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');
require_once '../themes/remote_params.php';


function iceberg_crm_cart_get_categories() {
    $r_cat = '{
        "status": "OK",
        "result": [
          {
            "id": 1,
            "name": "Продукты потребеления",
            "urlized": "producti-potrebleniya",
            "parent": 0,
            "descption": "1 lorem ipsum..."
          },
          {
            "id": 2,
            "name": "Молока",
            "urlized": "moloka",
            "parent": 1,
            "descption": "2 lorem ipsum..."
          }
        ]
      }';
    // global $wpdb;
    // $url = HOST.':'.PORT.'/getCategories';

    // $table_name = $wpdb->prefix . "iceberg_crm_callback_tokens";
    // $token = $wpdb->get_var("SELECT token FROM $table_name");

    // $request_data = (array(
    //     "token" => $token
    // ));

    // $ch = curl_init();
    // curl_setopt($ch, CURLOPT_URL, $url);
    // curl_setopt($ch, CURLOPT_POST, 1);
    // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_data));
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    // $response = curl_exec($ch);
    // curl_close($ch);

        
    $response = json_decode($r_cat, true);
    return $response;
}

function iceberg_crm_cart_get_products() {
    $r_prod = '{
        "status": "OK",
        "data": [
          {
            "id": 1,
            "name": "Seledka 1",
            "description": "some seledka lorem ipsum",
            "count": 100,
            "price": 1500,
            "article": "#A1239",
            "weigth": 1,
            "category": 1
          },
          {
            "id": 2,
            "name": "Seledka 2",
            "description": "some seledka 2 lorem ipsum",
            "count": 50,
            "price": 1300,
            "article": "#A1235",
            "weigth": 0.4,
            "category": 1
          },
          {
            "id": 3,
            "name": "Inner Seledka",
            "description": "some inner seledka lorem ipsum",
            "count": 10,
            "price": 500,
            "article": "#A1231",
            "weigth": 0.1,
            "category": 2
          }
        ]
      }';
    // global $wpdb;
    // $url = HOST.':'.PORT.'/getProducts';

    // $table_name = $wpdb->prefix . "iceberg_crm_callback_tokens";
    // $token = $wpdb->get_var("SELECT token FROM $table_name");

    // $request_data = (array(
    //     "token" => $token
    // ));

    // $ch = curl_init();
    // curl_setopt($ch, CURLOPT_URL, $url);
    // curl_setopt($ch, CURLOPT_POST, 1);
    // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_data));
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    // $response = curl_exec($ch);
    // curl_close($ch);


    $response = json_decode($r_prod, true);
    return $response;
}

// echo iceberg_crm_cart_get_categories();
// echo iceberg_crm_cart_get_products();
?>