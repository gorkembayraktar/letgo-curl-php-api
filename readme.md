## Kendi LETGO API ile işlemler gerçekleştirelim.

    // email işlemleri 
    $server = 'imap.gmail.com';
    $user = 'mail@gmail.com';
    $pass = 'password';
    $port = 993; // 

### Configürasyon ayarlarımızı yapalım.	
    $emailConfig = new EmailReaderConfig(
		    $server,$user,$pass,$port
    );

### Mail Okuyucumuzu İnitialize edelim	
    $emailReader = new EmailReader($emailConfig);
*// letgo hesabında oturum işlemlerini başlatabilmemiz için mail adresimize gönderilen özel bağlantıya erişebilmemiz gerekir.*

    $letgoConfig = [
    "devMode" => true, // then show process messages
    "waitSecond" => 2, // Search mail every {prop} second default value 2
    "maxSearchCount" => 15, // search mail every {prop} second and limit {prop}
    "target_host" => "acct.letgomail.com", // search the adress
    "cookieFolder" => __DIR__."\cookie\\" // cookie bilgisinin kaydedileceği klasör bilgisini içerir.
    ];

### Letgo API işlemleri

    $letgo = new Letgo($emailReader,$letgoConfig);

ya da

    $letgo = new Letgo($emailReader);

    $letgo->config($letgoConfig);
    
   init edebiliriz.


####  Proxy kullanarak giriş yapmak

    $proxy = "<proxy adress>";
    $proxyAuth = "<proxy auth>";

    $status = $letgo->login($proxy,$proxyAuth);

####  Ya da kendi ip adresimiz ile 
  
   giriş bilgisine login metodundan geri dönen booleean değer ile
       yada isLogin ile karşılaştırma yapılır.

    $status = $letgo->login();


** Dİğer işlemlerden sonra deauth olabileceğinden 

    $letgo->isLogin();
ile kontrol sağlamak faydalı olacaktır.


####  Kullanıcı Bilgilerimize erişmek
    // ile giriş bilgilerini obje olarak döndürür.
    $auth = $letgo->auth();
####  Bildirimlerimze erişmek

    // bildirim objesi döndürür.

    $letgo->notifications();
  
  
#### Satılan ürünleri göster

    $letgo->product_sold($auth->data->id); // user_id props

#### Aktif listedeki ürünleri göster

    $letgo->product_selling($auth->data->id); // user_id props

#### Eklediğim favorileri göster

    $letgo->favorites($auth->data->id); // user_id props


#### Bir ürünü favorilere ekle
    $letgo->add_favorite($auth->data->id,$product_id); // @return boolean

#### Bir ürünü favorilerden kaldır`

    $letgo->remove_favorite($auth->data->id,$product_id); // @return boolean

  

#### Bir ürünü report et

    $letgo->report_product($auth->data->id,$product_id); // @return boolean

  
#### istatisik bilgilerini döndür

// görüntüleme sayısı, favori sayısı ..

    $link_adres = "https://www.letgo.com/tr-tr/i/8e9a02c0-7bfb-4142-91e3-a0029adb422b";
    $letgo->stats($link_adres);

#### Profil bilgilerini güncelle
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

  
  

#### Bir kullanıcıyı engelle

    $block_user_id = "832dc416-efdc-44ba-8e97-957c8a03";

    $letgo->user_block($letgo->auth()->data->id,$block_user_id);

  
  

#### Bir kullanıcıyı engelini kaldır

    $block_user_id = "832dc416-efdc-44ba-8e97-957c8a03"
    $letgo->user_unblock($letgo->auth()->data->id,$block_user_id);


