<?php

class NameCreator{

    private $names = [
        "Ali","Ahmet","Mehmet","Ayşe","Fatma","Hakan","Caner","Arya","Beyza"
    ];
    private $surnames = [
        "Bakır","Bayrak","Sönmez","Karaca","Katip","Bal","Saraç"
    ];

    private static $_instance;

    public static function get(){
        if(!NameCreator::$_instance){
            NameCreator::$_instance = new NameCreator();
        }
        return NameCreator::$_instance;
    }

    public static function name(){
        $that = NameCreator::get();
        return $that->names[rand(0,count($that->names))] . " " . $that->surnames[rand(0,count($that->surnames))];
    }
}