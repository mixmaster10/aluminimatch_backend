<?php

if (!function_exists('success_response')) {
    function success_response($message, $data=null) {
        $response = [
            "error" => false,
            "message" => $message
        ];
        if($data){
            $response['data'] = $data;
        }
        return response()->json($response,200);
    }
}


if (!function_exists('error_response')) {
    function error_response($message) {
        $response = [
            "error" => true,
            "message" => $message
        ];
        return response()->json($response,200);
    }
}