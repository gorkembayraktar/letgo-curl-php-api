<?php

class LetgoAPI{

    public static $uri = "https://www.letgo.com/tr-tr";

    public static $register_uri = "https://bouncer.letgo.com/api/authentication?include=user,user_accounts"; // POST
    

    public static $login_uri = "https://bouncer.letgo.com/api/passwordless-issue";

    public static $notifications =  "https://notifications.letgo.com/api/notifications";

    public static $PROFILE_STATUS  = [
        "selling" => "selling,expired",
        "sold" => "sold"
    ];

    public static function profile_uri($user_id,$status = null){
        return "https://search-products-pwa.letgo.com/api/users/$user_id/products?status=$status&offset=0&country_code=TR&num_results=21";
    }
    public static function favorites($userid){
        return "https://api-pwa.letgo.com/api/users/$userid/favorites/products?offset=0&num_results=21";
    }
    public static function add_favorite($my_user_id, $product_id){
        //put
        return "https://api-pwa.letgo.com/api/users/$my_user_id/favorites/products/$product_id";
    }
    public static function report_product($userid,$product_id){
        // put 
        return "https://api-pwa.letgo.com/api/users/$userid/reports/products/$product_id";
    }
    public static function profile_update($user_id){
        // patch
        return "https://bouncer.letgo.com/api/users/$user_id";
    }

    public static function user_block($own_user_id){
        return "https://bouncer.letgo.com/api/users/$own_user_id/links";
    }

    public static function user_block_json($user_id,$isBlock = true){
       $json = [
            "data" => [
                "type" => "user_links",
                "attributes" => [
                    "link_name" => "blocked",
                    "user_id" => "$user_id"
                ]
            ]
        ];
        if( ! $isBlock)
            $json["data"]["id"] = $user_id.":blocked";
        return json_encode($json);
    }

    public static function register_json($username,$auth_code){
        $auth_code = LetgoAPI::link_decode($auth_code);
        return json_encode([
            "data" => [
                "type" => "authentication",
                "attributes" => [
                    "provider" =>  "letgo-passwordless",
                    "credentials" => "$username:$auth_code"
                ]
            ]
        ]);

        
    }
    
    public static function login_json($mail_adres){
        return json_encode([
            "data" => [
                "type" => "passwordless_issue",
                "attributes" => [
                    "email"=>"$mail_adres"
                    ]   
            ]
        ]);
    }
    public static function profile_update_json($user_id,$attributes = array()){
        return json_encode([
           "data" => [
               "type"=>"users",
               "id" => "$user_id",
               "attributes" => $attributes
           ]
        ]);
    }
    public static function install_auth_cookie($user_id,$token){
        return 'install_auth='.urlencode('j:{"id":"'.$user_id.'","token":"'.$token.'","locale":"tr-TR"}').";";
    }
    public static function user_auth_cookie($token){
        return 'user_auth='.urlencode('{"token":"'.$token.'","rememberMe":true}').";";
    }

    // linkler encode şekilde geliyo bunun düzenleyip post ile göndermek gerekir.
    public static function link_decode($link){
        return urldecode($link);
    }
}
