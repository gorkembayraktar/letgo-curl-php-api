<?php 

require 'v1/init.php';

// email işlemleri

$server = 'imap.gmail.com';
$user   = 'mail@gmail.com';
$pass   = 'password';
$port   = 993; // adjust according to server settings

$emailReaderConfig = new EmailReaderConfig(
    $server,$user,$pass,$port
);

$emailReader = new EmailReader($emailReaderConfig);


// letgo hesabında oturum işlemlerini başlatabilmemiz için mail adresimize gönderilen özel bağlantıya erişebilmemiz gerekir.


$letgoConfig = [
    "devMode" => true, // then show process messages
    "waitSecond" => 2, // Search mail every {prop} second default value 2 
    "maxSearchCount" => 15, // search mail every {prop} second and limit {prop} 
    "target_host" => "acct.letgomail.com", // search the adress
    "cookieFolder" => __DIR__."\cookie\\" // cookie bilgisinin kaydedileceği klasör  bilgisini içerir.
];

// Initiliaze işlemleri 
//$letgo = new Letgo($emailReader,$letgoConfig);
// ya da
$letgo = new Letgo($emailReader);
$letgo->config($letgoConfig);


// proxy kullanarak giriş işlemleri
/*
$proxy = "<proxy adress>";
$proxyAuth = "<proxy auth>";
$status = $letgo->login($proxy,$proxyAuth);
*/

// Login with own ip adress

// giriş bilgisine login metodundan geri dönen booleean değer ile yada isLogin ile karşılaştırma yapılır.
$status = $letgo->login();



if($letgo->isLogin()){

 
    $durum = $letgo->profile_update($letgo->auth()->data->id,[
        "public_username" => "Deneme"
        //"email" => "",
        // "city" => "",
        //"adress" => ""
    ]);

    echo $durum ? "Güncellendi" : "Güncellenemedi.";
}else{
    echo 'giriş yapılamadı';
}









