<?php


class EmailReaderConfig{
    // email login credentials
    public $server = '';
    public $user   = '';
    public $pass   = '';
    public $port   = 993; // adjust according to server settings

    public function __construct($server,$user,$pass,$port){
        $this->server = $server;
        $this->user = $user;
        $this->pass = $pass;
        $this->port = $port;
    }
}


class EmailReader {

    // imap server connection
    public $conn;

    // inbox storage and inbox message count
    private $inbox;
    private $msg_cnt;

    public $config;
   

    // connect to the server and get the inbox emails
    function __construct(EmailReaderConfig $config) {

        $this->config = $config;


        $this->connect();

        $this->inbox();
    }

    // close the server connection
    function close() {
      
        imap_close($this->conn);
    }

    // open the server connection
    // the imap_open function parameters will need to be changed for the particular server
    // these are laid out to connect to a Dreamhost IMAP server
    function connect() {
        $this->conn = imap_open('{'.$this->config->server.':'.$this->config->port.'/imap/ssl/novalidate-cert}INBOX', $this->config->user, $this->config->pass);
    }
    function reconnect(){
        $this->close();
        $this->connect();
    }

    // move the message to a new folder
    function move($msg_index, $folder='INBOX.Processed') {
        // move on server
        imap_mail_move($this->conn, $msg_index, $folder);
        imap_expunge($this->conn);

        // re-read the inbox
        $this->inbox();
    }

    // get a specific message (1 = first email, 2 = second email, etc.)
    function get($msg_index=NULL) {
        if (count($this->inbox) <= 0) {
            return array();
        }
        elseif ( ! is_null($msg_index) && isset($this->inbox[$msg_index])) {
            return $this->inbox[$msg_index];
        }

        return $this->inbox[0];
    }

    // read the inbox
    function inbox($limit = 3) {
        // last $limit
        $this->msg_cnt = imap_num_msg($this->conn);

    
        
        $in = array();
        for($i = ($this->msg_cnt - $limit); $i <= $this->msg_cnt; $i++) {
            $in[] = $this->item($i);
        }

        $this->inbox = $in;
    }
    function item($i){
        if($i < 1 || $i > $this->msg_cnt) return array(); // böyle bir kayıt yok.
        return array(
            'index'     => $i,
            'header'    => imap_headerinfo($this->conn, $i),
            'body'      => imap_utf8(imap_body($this->conn, $i)),
            'structure' => imap_fetchstructure($this->conn, $i)
        );
    }
    function seen($id){
   
        $durum =  imap_setflag_full($this->conn,$id, "\\Seen \\Flagged");
        return (bool)($durum);
    }
    function last(){
        $lastItem = $this->item($this->msg_cnt);
        $this->reconnect();
        $this->msg_cnt = imap_num_msg($this->conn);
        return $lastItem;
    }


    function getAll(){
        return $this->inbox;
    }


    function __destruct(){
        $this->close();
    }

}