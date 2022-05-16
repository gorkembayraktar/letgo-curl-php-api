<?php

class LetgoMessages{
    private $messages = [];
    protected function log($message){
        array_push($this->messages,[
            "date" => date("Y-m-d H:i:s"),
            "message" => $message
        ]);
    }
    protected function getMessages(){
        return $this->messages;
    }
}