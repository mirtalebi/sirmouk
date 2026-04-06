<?php

namespace App\Common;

class SnapFoodUtils 
{

    public static function getSnappfoodCustomerData($url) {
        $orderHash = basename($url);
        // The dynamic URL based on the order hash provided in your curl
        $url = "https://snappfood.ir/vms/v1/order/{$orderHash}/courier-assistant";

        $ch = curl_init($url);

        // Set the headers exactly as seen in your network trace
        $headers = [
            "accept: application/json, text/plain, */*",
            "accept-language: en-US,en;q=0.9",
            "origin: https://dakhl-ordering.snappfood.ir",
            "referer: https://dakhl-ordering.snappfood.ir/",
            "user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0"
        ];

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // Optional: Follow redirects if necessary
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $response = curl_exec($ch);
        $error = curl_error($ch);

        if ($error) {
            return null;
        }

        $result = json_decode($response, true);

        // Check if the response was successful and contains customer data
        if (isset($result['status']) && $result['status'] === true) {
            $result['data']['customer']['phoneNumber'] = str_replace(' ', '', $result['data']['customer']['phoneNumber']);
            return $result['data']['customer'];
        }

        return null;
    }


}