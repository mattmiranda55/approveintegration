<?php

use Illuminate\Support\Facades\Log;

if (!function_exists('dd2')) {
   function dd2($value) {
       $data = $value;
       if(is_array($data) || is_object($data)){
           $data = json_decode(json_encode($data));
       }
       Log::info(print_r($data,true));
   }
}

if (!function_exists('parse_product_cart')) {
    function parse_product_cart($selectors) {
        $product = $selectors->product;
        $cart = $selectors->cart;
        $template = View::make('integration-templates.m-product-cart-integration', compact(['product', 'cart']));
        return $template->render();
    }
}

if (!function_exists('parse_product_cart_gallery')) {
    function parse_product_cart_gallery($selectors) {
        $product = $selectors->product;
        $cart = $selectors->cart;
        $gallery = $selectors->gallery;
        $template = View::make('integration-templates.m-product-cart-gallery-integration', compact(['product', 'cart', 'gallery']));
        return $template->render();
    }
}