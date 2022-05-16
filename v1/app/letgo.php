<?php

class Letgo extends LetgoMessages{

    // Geliştirici modunda
    private $devMode = true;

    private $target_host = "acct.letgomail.com"; 
    // özellikler
    private $repeatCount = 15;
    private $waitSecond = 2;

    // request bilgileri.
    private $http;

    // http authriztion bölgelerinde gerekli
    private $Bearer,$Cookie;


    // giriş durumuna göre
    private $loginState = false;
    private $AUTH;

    private $emailReader;
    function __construct(EmailReader $emailReader,$config = null){
        $this->emailReader = $emailReader;
        
      
        if($config){
            $this->config($config);
        }

        $this->http = new Http();
        $this->Cookie = $this->Cookie();
        $this->Bearer = $this->BearerCreate();

    }
    function auth(){
        return $this->AUTH;
    }
    function login($proxy = null,$proxyAuth = null){



        if(FileHelper::exists(FileHelper::token($this->emailReader->config->user))){
          
            $data = FileHelper::getTxt(FileHelper::token($this->emailReader->config->user));

            $json = json_decode($data);

            $this->Bearer = $json->data->attributes->auth_token;

            if(($favorites = $this->favorites($json->data->id)) === false){
                FileHelper::remove_token($this->emailReader->config->user);
               return false;
            }
            $this->AUTH =  $json;

            $this->loginState = true;
            return $this->loginState;
        }

        $isSend = $this->loginPost($proxy,$proxyAuth);
        if(!$isSend):
            $this->loginState = false;
            $this->log("Mail gönderilemedi.");
            return false;
        endif;
        $this->loginState = $this->process();

        return $this->loginState;
    }
    function isLogin(){
        return $this->loginState;
    }
    function flow(){
        $this->loginState  = $this->begin();

        if($this->loginState){
            $this->log( "giriş başarılı");
        }else{
            $this->log( "error : giriş başarısız!");
            FileHelper::remove_token($this->emailReader->config->user);
      
            $this->flow();
        }
    }
    function begin(){
        if(FileHelper::exists(FileHelper::token($this->emailReader->config->user))){
            $data = FileHelper::getTxt(FileHelper::token($this->emailReader->config->user));

            $json = json_decode($data);

            $this->Bearer = $json->data->attributes->auth_token;

            if(($favorites = $this->favorites($json->data->id)) === false){
               return false;
            }
            $this->AUTH =  $json;
            //die($data);
        }else{
            $isSend = $this->loginPost();
            if(!$isSend):
                $this->log("Mail gönderilemedi.");
                return false;
            endif;

            return $this->process();
           
        }
        return true;
    }
    function Cookie(){
        $response = $this->http->get(LetgoAPI::$uri,array('cookie: '.$this->Cookie));
        return $response->getCookie();

    }
  
    // GİRİŞ İSTEĞİNDE BULUNUR.
    private function loginPost($proxy = null,$proxyAuth = null):bool{
            $http = $this->http;
            if($proxy != null){
                $http->proxy($proxy);
            }
            if($proxyAuth != null){
                $http->proxyauth($proxyAuth);
            }

            $response = $http->postJSON(
            LetgoAPI::$login_uri,
            LetgoAPI::login_json($this->emailReader->config->user),
            array(
                "Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.127 Safari/537.36 OPR/86.0.4363.59",
                "authorization:Bearer ".$this->Bearer,
                "letgo-installation:6b3c193e-29cc-48d3-95f5-5250381ad2ed",
            )
            );
          
        return $response->status() == 204;
    }
    private function BearerCreate(){
        // öncesinde cookie bilgisini al
        $response = $this->http->get(
            LetgoAPI::$uri,
            array(
                "User-Agent: PostmanRuntime/7.26.8",
                'Cookie:'.$this->Cookie));
        $body = $response->body();
        $re = '/"token":"([^"]*)"/m';

        preg_match($re, $body, $match);

        $this->Cookie = $response->getCookie();

        if(!$match){
            $this->log('Baerer kod üretilemedi , cookie kontrol ediniz.');
        }
       
        return !$match ? "" : $match[1];
    }

    private function process(){

        $find = $this->find();

        if($find == 0){
            $this->log("İlgili mail bulunamadı.");
            return false;
        }

  
        return $this->analyz($find);

    }
    

    private function isLetgoMail($host,$mail_adres):bool{
        // mail adresi ve host eşitliği yap.
        // gönderilen mail hedef mail ile eşleşiyor mu 
        return $host == $this->target_host;
    }

    private function find(){
        $i = $this->repeatCount;
        while($i > 0){
            $item = $this->emailReader->last();
            $host = $item['header']->from[0]->host; 
         
            if(!$this->isLetgoMail($host,$item['header']->toaddress)){
                sleep($this->waitSecond);
            }else{
                // görüldü işaretle.
                $this->emailReader->seen($item['index']);

                return $item;
            }
            $i--;

        }
        return 0;
    }


    private function analyz($item){
  
        $kayit = "letgo'ya_kaydolmak_i"; // eğer bunu içeriyorsa ilgili parçaçık.
        $giris = "letgo'ya_giri";
      
        if( strpos($item['header']->subject,$kayit) !== false){

            $auth_token = $this->mail_auth_token($item['body'],LetgoState::$REGISTER);


            // $kayıt işlemleri
            $name = NameCreator::name();
          
            $result = $this->http->postJSON(
                LetgoAPI::$register_uri,
                LetgoAPI::register_json($name,$auth_token),
                array(
                    "authorization:Bearer ".$this->Bearer
                )
            );

            if($result->status() == 201){

                // işlemler başarılı
                $filename = md5($this->emailReader->config->user).".txt";
                FileHelper::saveTxt($filename,$result->body());
                $this->AUTH = json_decode($result->body());
                $this->log( 'hesap oluşturuldu ve kaydedildi. '.$filename);

            }else{
                $this->log($result->body());
                $this->log('İşlem başarısız oldu kod : '.$result->status());
            }




        }else if(strpos($item['header']->subject,$giris) !== false){

            $auth_token = $this->mail_auth_token($item['body'],LetgoState::$LOGIN);
         

            $result = $this->http->postJSON(
                LetgoAPI::$register_uri,
                LetgoAPI::register_json("username",$auth_token),
                array(
                    "authorization:Bearer ".$this->Bearer
                )
            );

            if($result->status() == 201){

                // işlemler başarılı
                $filename = md5($this->emailReader->config->user).".txt";
                FileHelper::saveTxt($filename,$result->body());
                $this->AUTH = json_decode($result->body());
                $this->log('hesap oluşturuldu ve kaydedildi. '.$filename);

            }else{
                $this->log($result->body());
                $this->log('İşlem başarısız oldu kod : '.$result->status());
            }



           
        }else{
            return false;
        }

        return true;
        
    }
    private function mail_auth_token($data, $state){
        $data = quoted_printable_decode ($data);

        $re = '/'.$state.'=([^&]*)/m';
        //$data =  preg_replace('~[\r\n]+~', '', $data);
        preg_match($re,$data,$match);
        if(!$match) die('token alınamadı');

        return $match[1] ?? "";
    }

    function notifications(){
        $response = $this->http->get(
            LetgoAPI::$notifications, 
            array(
            "authorization:Bearer ".$this->Bearer
            )
        );
       
    }

    function product_sold($userid){
        $cookie = "Cookie:".$this->Cookie;
        $response = $this->http->get(
            LetgoAPI::profile_uri($userid,LetgoAPI::$PROFILE_STATUS["sold"]), 
            array(
                $cookie,
                 "User-Agent: PostmanRuntime/7.26.8"
               )
        );

        if($response->status() == 200){
            return json_decode($response->body());
        }
        return null;

    }
    function product_selling($userid){
        $uri = LetgoAPI::profile_uri($userid,LetgoAPI::$PROFILE_STATUS["selling"]);
       
        $cookie = "Cookie:".$this->Cookie;
        
        // user agent kontrolu yapılmış m111111qqqqq böle işin
        $response = $this->http->get(
            $uri, 
            array(
             $cookie,
              "User-Agent: PostmanRuntime/7.26.8"
            )
        );
        if($response->status() == 200){
            return json_decode($response->body());
        }
        return null;
    }
    function favorites($userid){
        if(!$this->auth()) return false;
        // auth kodu olanlarda görüntülenembilir

        $uri = LetgoAPI::favorites($userid);

        $cookie = "Cookie:".$this->Cookie;
        
        // user agent kontrolu yapılmış m111111qqqqq böle işin
        $response = $this->http->get(
            $uri, 
            array(
             $cookie,
              "User-Agent: PostmanRuntime/7.26.8",
              "authorization:Bearer ".$this->Bearer
            )
        );

       
        if($response->status() == 200){
            return json_decode($response->body());
        }else if($response->status() === 401){
            //Expired authorization token.
            FileHelper::remove_token($this->emailReader->config->user);
        }
        return false;
    }
    function add_favorite($userid,$product_id){
        if(!$this->auth()) return false;
        $uri = LetgoAPI::add_favorite($userid,$product_id);

        $cookie = "Cookie:".$this->Cookie;
        
        // user agent kontrolu yapılmış m111111qqqqq böle işin
        $response = $this->http->put(
            $uri, 
            array(
             $cookie,
              "User-Agent: PostmanRuntime/7.26.8",
              "authorization:Bearer ".$this->Bearer
            )
        );

        $this->log($response->header());
        $this->log($response->body());
       
        return $response->status() == 204;
    }
    function remove_favorite($userid,$product_id){
        if(!$this->auth()) return false;
        $uri = LetgoAPI::add_favorite($userid,$product_id);

        $cookie = "Cookie:".$this->Cookie;
        
  
        $response = $this->http->delete(
            $uri, 
            array(
             $cookie,
              "User-Agent: PostmanRuntime/7.26.8",
              "authorization:Bearer ".$this->Bearer
            )
        );

        $this->log($response->header());
        $this->log($response->body());
       
        return $response->status() == 204;
    }
    function report_product($userid,$product_id){
        if(!$this->auth()) return false;
        $uri = LetgoAPI::report_product($userid,$product_id);

        $cookie = "Cookie:".$this->Cookie;
    
        $response = $this->http->put(
            $uri, 
            array(
             $cookie,
              "User-Agent: PostmanRuntime/7.26.8",
              "authorization:Bearer ".$this->Bearer
            )
        );

        $this->log($response->header());
        $this->log($response->body());
       
        return $response->status() == 204;
    }

    function profile_update($userid,$attributes){
        if(!$this->auth()) return false;

        $uri = LetgoAPI::profile_update($userid);

        $cookie = "Cookie:".$this->Cookie;
    
        $response = $this->http->patch(
            $uri, 
            LetgoAPI::profile_update_json($this->emailReader->config->user,$attributes),
            array(
             $cookie,
              "User-Agent: PostmanRuntime/7.26.8",
              "authorization:Bearer ".$this->Bearer
            )
        );
        return $response->status() == 200;
    }
    function user_block($own_user_id,$blocked_user_id){
        if(!$this->auth()) return false;

        $uri = LetgoAPI::profile_block($own_user_id);

        $cookie = "Cookie:".$this->Cookie;
    
        $response = $this->http->postJSON(
            $uri, 
            LetgoAPI::profile_block_json($blocked_user_id),
            array(
             $cookie,
              "User-Agent: PostmanRuntime/7.26.8",
              "authorization:Bearer ".$this->Bearer
            )
        );
        return $response->status() == 204;
    }
    function user_unblock($own_user_id,$blocked_user_id){
        if(!$this->auth()) return false;

        $uri = LetgoAPI::profile_block($own_user_id);

        $cookie = "Cookie:".$this->Cookie;
    
        $response = $this->http->delete(
            $uri, 
            array(
             $cookie,
              "User-Agent: PostmanRuntime/7.26.8",
              "authorization:Bearer ".$this->Bearer
            ),
            LetgoAPI::profile_block_json($blocked_user_id,false)
        );
        return $response->status() == 204;
    }
    function stats($product_uri){
        $re = '/"stats":(.*}}),"originalPrice"/m';
        $result = $this->http->get($product_uri);

        preg_match($re,$result->body(),$match);
        if($match && $match[1]){
            return json_decode($match[1]);
        }
        return null;
    }
    function devMode($status){
        $this->devMode = $status;
        return $this;
    }
    public function config($config){
        if(isset($config['cookieFolder'])){
            FileHelper::folder($config['cookieFolder']);
        }
        $keys = [
            "devMode" => "devMode",
            "waitSecond" => "waitSecond", 
            "maxSearchCount" => "repeatCount", 
            "target_host" => "target_host", 
        ];
        foreach($config as $key => $value){
            if(isset($keys[$key])){
                $this->{$keys[$key]} = $value;
            }
        }
    }
    public function __destruct(){
        if( $this->devMode ){
            print_r($this->getMessages());
        }
    }
}
