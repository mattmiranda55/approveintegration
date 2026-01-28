<?php

use Illuminate\Support\Facades\Log;

if (! function_exists('dd2')) {
   function dd2($value) {
       $data = $value;
       if(is_array($data) || is_object($data)){
           $data = json_decode(json_encode($data));
       }
       Log::info(print_r($data,true));
   }
}