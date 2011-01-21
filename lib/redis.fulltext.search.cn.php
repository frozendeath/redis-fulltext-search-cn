<?php
/*
 *
 *
 */
 
class RedisFSC{

    var $notime; //currenttime
    var $cws;   //chinese word spliter
    var $key_prefix;  //key prefix for the whole search engine
    var $key_contentid;   //key for {contentid} set
    var $key_index_prefix;  //key prefix for the index
    var $r; //redis link
    var $resultreturntype;
    
    /*
     *
     */
    
    function RedisFSC(){
        $this->nowtime = time();
        $this->keyprefix = "RFSC";
        $this->key_contentid = "{$this->keyprefix}:content:id";
        $this->key_index_prefix = "{$this->keyprefix}:index";
        $this->resultreturntype = "string";
        if(isset($redislink)){
            $this->r = $redislink;
        }else{
            $this->r = new Redis();
            $this->r->connect('127.0.0.1');
            $this->r->select(0);
        }
        //init cws here
        $this->cws = scws_new();
        $this->cws->set_charset('utf8');
        $multi = 8;
        $this->cws->set_duality(false);
        $this->cws->set_ignore(true);  //ignore punctuations?
        $this->cws->set_multi($multi);
        //end
      
    }
    
    function index($content,$postid){
        if(empty($content)){
            return true;
        }
        $this->cws->send_text($content);
        while ($res = $this->cws->get_result()){
            foreach ($res as $tmp)
            {
                if ($tmp['len'] == 1 && $tmp['word'] == "\r"){
                    continue;
                }elseif ($tmp['len'] == 1 && $tmp['word'] == "\n"){
                    continue;
                }else{
                    $this->r->zAdd("{$this->key_index_prefix}:time:{$tmp['word']}",$this->nowtime,$postid);
                }
            }
        }
        return true;
    }
    
    function search($key,$resultorder="desc"){
        if(empty($key)){
            return true;
        }
        $this->cws->send_text($key);
        $keyarray = array();
        while ($res = $this->cws->get_result()){
            foreach ($res as $tmp)
            {
                if ($tmp['len'] == 1 && $tmp['word'] == "\r"){
                    continue;
                }elseif ($tmp['len'] == 1 && $tmp['word'] == "\n"){
                    continue;
                }else{
                    $keyarray[] = "{$this->key_index_prefix}:time:{$tmp['word']}";
                }
            }
        }
        $randomkey = rand(0,9999);
        $this->cws->close();
        $tmpkeyname = "{$this->keyprefix}:tmpkey:{$this->nowtime}{$randomkey}";
        $this->r->zInter($tmpkeyname ,$keyarray);
        $data = $this->r->sort($tmpkeyname,array('get'=>"#",'sort'=>$resultorder));
        return $this->resultreturntype=="string"?join(",",$data):$data;
    }
}
?>