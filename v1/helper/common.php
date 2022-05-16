<?php



function location_url($id = 1){
  $uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
  return 'http://' . $_SERVER['HTTP_HOST'] . $uri_parts[0] ."?id=".$id;
}

function go($url){
  header("refresh:5;url= $url");
  die;
}