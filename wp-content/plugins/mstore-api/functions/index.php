<?php
define("ACTIVE_API", "https://inspireui-license.now.sh/api/v1");
define("ACTIVE_TOKEN", "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJmb28iOiJiYXIiLCJpYXQiOjE1ODY5NDQ3Mjd9.-umQIC6DuTS_0J0Jj8lcUuUYGjq9OXp3cIM-KquTWX0");

function verifyPurchaseCode ($code) {
    $website = get_home_url();
    $response = wp_remote_get( ACTIVE_API."/active?code=".$code."&website=".$website."&token=".ACTIVE_TOKEN."&isPlugin=true");
    $statusCode = wp_remote_retrieve_response_code($response);
    $success = $statusCode == 200;
    if($success){
        update_option("mstore_purchase_code", true);
        update_option("mstore_purchase_code_key", $code);
    }else{
        $body = wp_remote_retrieve_body($response);
        $body = json_decode($body, true);
        return $body["error"];
    }
    return $success;
}

function checkCurrentPurchaseCode () {
    // $code = get_option("mstore_purchase_code_key");
    // if(isset($code) && $code != false){
    //     $website = get_home_url();
    //     $response = wp_remote_get( ACTIVE_API."/active?code=".$code."&website=".$website."&token=".ACTIVE_TOKEN."&isPlugin=true");
    //     $statusCode = wp_remote_retrieve_response_code($response);
    //     update_option("mstore_purchase_code", $statusCode == 200);
    // }else{
    //     update_option("mstore_purchase_code", false);
    // }
}
?>