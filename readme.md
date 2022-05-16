<?php 


// email işlemleri

$server = 'imap.gmail.com';
$user   = 'mail@gmail.com';
$pass   = 'password';
$port   = 993; // adjust according to server settings

$emailConfig = new EmailReaderConfig(
    $server,$user,$pass,$port
);

$emailReader = new EmailReader($emailConfig);


// letgo hesabında oturum işlemlerini başlatabilmemiz için mail adresimize gönderilen özel bağlantıya erişebilmemiz gerekir.


$letgoConfig = [
    "devMode" => true, // then show process messages
    "waitSecond" => 2, // Search mail every {prop} second default value 2 
    "maxSearchCount" => 15, // search mail every {prop} second and limit {prop} 
    "target_host" => "acct.letgomail.com", // search the adress
    "cookieFolder" => __DIR__."\cookie\\" // cookie bilgisinin kaydedileceği klasör  bilgisini içerir.
];

// Initiliaze işlemleri 
$letgo = new Letgo($emailReader,$letgoConfig);
// ya da
$letgo = new Letgo($emailReader);
$letgo->config($letgoConfig);


// proxy kullanarak giriş işlemleri

$proxy = "<proxy adress>";
$proxyAuth = "<proxy auth>";
$status = $letgo->login($proxy,$proxyAuth);

// Login with own ip adress

// giriş bilgisine login metodundan geri dönen booleean değer ile yada isLogin ile karşılaştırma yapılır.
$status = $letgo->login();

$letgo->isLogin();

// ile giriş bilgilerini obje olarak döndürür.
$auth = $letgo->auth();


// bildirimleri döndürür
$letgo->notifications();




// satılan ürünleri göster

$letgo->product_sold($auth->data->id); // user_id props

// aktif listedeki ürünleri göster

$letgo->product_selling($auth->data->id); // user_id props



// eklediğim favorileri göster

$letgo->favorites($auth->data->id); // user_id props


// bir ürünü favorilere ekle

$letgo->add_favorite($auth->data->id,$product_id); // @return boolean


// bir ürünü favorilerden kaldır
$letgo->remove_favorite($auth->data->id,$product_id); // @return boolean

// bir ürünü report et
$letgo->report_product($auth->data->id,$product_id); // @return boolean


// istatisik bilgilerini döndür 
// görüntüleme sayısı, favori sayısı ..
$link_adres = "https://www.letgo.com/tr-tr/i/8e9a02c0-7bfb-4142-91e3-a0029adb422b";

$letgo->stats($link_adres);



if($letgo->isLogin()){


    // Bilgilerimi güncelle

    $letgo->profile_update($letgo->auth()->data->id,[
        "public_username" => "Adı soyadı"
        //"email" => "",
        // "city" => "",
        //"adress" => ""
    ]);

}else{
    echo 'giriş yapılamadı';
}


// Bir kullanıcıyı engelle

$block_user_id = "832dc416-efdc-44ba-8e97-957c8a03"

$letgo->user_block($letgo->auth()->data->id,$block_user_id);


// Bir kullanıcının engelini kaldır

$block_user_id = "832dc416-efdc-44ba-8e97-957c8a03"

$letgo->user_unblock($letgo->auth()->data->id,$block_user_id);








