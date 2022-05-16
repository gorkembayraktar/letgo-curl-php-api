<?php

class Http{
    private $_response;
    
    private $_proxy,$_proxyauth;
  
    public function __construct(){
      $this->_response = new HttpResponse();
    } 
  
    public function proxy($proxy){
      $this->_proxy = trim($proxy);
      return $this;
    }
    public function proxyauth($auth){
      $this->_proxyauth = $auth;
      return $this;
    }
  
    public function get(string $url,array $headers = []){
  
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36 OPR/71.0.3770.456');
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
      curl_setopt($ch, CURLOPT_TIMEOUT, 5);
      curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,1);
  
      if(!empty($this->_proxy)){
        curl_setopt($ch, CURLOPT_PROXY, $this->_proxy);
        curl_setopt($ch, CURLOPT_TIMEOUT, 200);
      }
      if(!empty($this->_proxyauth)){
        curl_setopt($ch, CURLOPT_TIMEOUT, 200);
        curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->_proxyauth);
      }
  
      curl_setopt($ch,CURLOPT_AUTOREFERER,1);
  
      curl_setopt($ch, CURLOPT_HEADER, 1);
      
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      $response = curl_exec($ch);
      $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
      curl_close($ch);
  
      $this->_response->setStatus($httpcode);
      $this->_response->setHeader( substr($response, 0, $header_size) );
      $this->_response->setBody(substr($response, $header_size));
  
      return $this->response();
  
    }
    public function put(string $url,array $headers = []){
  
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_PUT, 1);
      curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36 OPR/71.0.3770.456');
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
      curl_setopt($ch, CURLOPT_TIMEOUT, 5);
      curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,1);
  
      if(!empty($this->_proxy)){
        curl_setopt($ch, CURLOPT_PROXY, $this->_proxy);
        curl_setopt($ch, CURLOPT_TIMEOUT, 200);
      }
      if(!empty($this->_proxyauth)){
        curl_setopt($ch, CURLOPT_TIMEOUT, 200);
        curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->_proxyauth);
      }
  
      curl_setopt($ch,CURLOPT_AUTOREFERER,1);
  
      curl_setopt($ch, CURLOPT_HEADER, 1);
      
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      $response = curl_exec($ch);
      $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
      curl_close($ch);
  
      $this->_response->setStatus($httpcode);
      $this->_response->setHeader( substr($response, 0, $header_size) );
      $this->_response->setBody(substr($response, $header_size));
  
      return $this->response();
  
    }
   
    public function delete(string $url,array $headers = [],$post = null){
  
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");

      if($post){
        curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
      }

      curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36 OPR/71.0.3770.456');
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
      curl_setopt($ch, CURLOPT_TIMEOUT, 5);
      curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,1);
  
      curl_setopt($ch,CURLOPT_AUTOREFERER,1);
  
      if(!empty($this->_proxy)){
        curl_setopt($ch, CURLOPT_PROXY, $this->_proxy);
        curl_setopt($ch, CURLOPT_TIMEOUT, 200);
      }
      if(!empty($this->_proxyauth)){
        curl_setopt($ch, CURLOPT_TIMEOUT, 200);
        curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->_proxyauth);
      }
  
      curl_setopt($ch, CURLOPT_HEADER, 1);
      
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      $response = curl_exec($ch);
      $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
      curl_close($ch);
  
      $this->_response->setStatus($httpcode);
      $this->_response->setHeader( substr($response, 0, $header_size) );
      $this->_response->setBody(substr($response, $header_size));
  
      return $this->response();
  
    }
    public function postJSON(string $url,string $stringJson, $headers = []){
        //{"data":{"type":"authentication","attributes":{"provider":"letgo-passwordless","credentials":"yeni bir kullanıcı:KaQsEIEPPemYdQ==.ed1ektnzEvquJ9jM2Wmu7h278PDk+i2L0iWcUf8kQPyCjn4UpA=="}}}
        $ch = curl_init();
  
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$stringJson);
        
  
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,1);
  
        if(!empty($this->_proxy)){
          curl_setopt($ch, CURLOPT_PROXY, $this->_proxy);
          curl_setopt($ch, CURLOPT_TIMEOUT, 200);
        }
        if(!empty($this->_proxyauth)){
          curl_setopt($ch, CURLOPT_TIMEOUT, 200);
          curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->_proxyauth);
        }
    
        curl_setopt($ch,CURLOPT_AUTOREFERER,1);
  
        curl_setopt($ch,CURLOPT_USERAGENT,"PostmanRuntime/7.26.8");
    
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array_merge($headers,
        array(
            'Content-Type:application/vnd.api+json;version=2',
            'letgo-installation:51fd38e2-46ae-49f6-828f-1eb52c0c2d99',
            'origin:https://www.letgo.com',
            'user-agent:Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.4844.84 Safari/537.36 OPR/85.0.4341.75'
          )));
  
      
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close ($ch);
          $this->_response->setStatus($httpcode);
          $this->_response->setHeader( substr($response, 0, $header_size) );
          $this->_response->setBody(substr($response, $header_size));
        
          return $this->response();
        
   }
   public function patch(string $url,string $stringJson, $headers = []){
    //{"data":{"type":"authentication","attributes":{"provider":"letgo-passwordless","credentials":"yeni bir kullanıcı:KaQsEIEPPemYdQ==.ed1ektnzEvquoKsRfDCtOjY+POqETYfnLD1rJgWbI1kMKoS9fWGKNLjsGXCFwHuzkovpwKOZ3G6PoAUVWDSuvmfL6oAPP32fFMrGIkHGF4RdTfKmTQBovbP8sSRBnVpQgCTXHFGQjRq4oHonI+fEwkTqgFHYziO8X7FjUMZVKzRecHKfyPnBrK96HaSYEkW4wKN2B72PJ/VqjIeJuRglN0/HuDgNE5bQBXGETX9pV1yKpiw9f6Ep14wh9zO3jhGVCavWWzhjNodKlRHGjSVLkLRs+RLM3bjns8cX8GMVw+Y97gZkwq2sFioRn+rKqOt70z1LghNyC+rOIMzlxFmOXg==.HZBJJeRuBd2vTSlriLy6uJwbGFjLjC3RdtWOzwqlNRQVO3igApbv9WhFkfvSu8MQJ8sKO/DGmTAFIrMr5UlT1UO87jwzXISds4W4jEyFAKu/dcyQ6q3I3Z55t2It6zZzBZME8P/zzERnsTYheeUnFRBIJhfSJU18hvi2zLIYW4TddbbNodHsyNVmwDvbTZCnaJ6H5nLQPTjd7T2e63PLrTYLAtiC/H5DVUllBpVcqesJ0HHR1ITPcT73Y5L+4AIDim6FMtCLuzYb4kauxH0A7EVS/xpq78ILfagbshYpXzYVWRnYJ9jM2Wmu7h278PDk+i2L0iWcUf8kQPyCjn4UpA=="}}}
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
    curl_setopt($ch, CURLOPT_POSTFIELDS,$stringJson);
    

    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,1);

    if(!empty($this->_proxy)){
      curl_setopt($ch, CURLOPT_PROXY, $this->_proxy);
      curl_setopt($ch, CURLOPT_TIMEOUT, 200);
    }
    if(!empty($this->_proxyauth)){
      curl_setopt($ch, CURLOPT_TIMEOUT, 200);
      curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->_proxyauth);
    }

    curl_setopt($ch,CURLOPT_AUTOREFERER,1);

    curl_setopt($ch,CURLOPT_USERAGENT,"PostmanRuntime/7.26.8");

    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array_merge($headers,
    array(
        'Content-Type:application/vnd.api+json;version=2',
        'letgo-installation:51fd38e2-46ae-49f6-828f-1eb52c0c2d99',
        'origin:https://www.letgo.com',
        'user-agent:Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.4844.84 Safari/537.36 OPR/85.0.4341.75'
      )));

  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    curl_close ($ch);
      $this->_response->setStatus($httpcode);
      $this->_response->setHeader( substr($response, 0, $header_size) );
      $this->_response->setBody(substr($response, $header_size));
    
      return $this->response();
    
}
    protected function response(){
      return $this->_response;
    }
  
  }