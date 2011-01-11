/*
 *
 *
 */
 
class RedisFSC{

    var $notime; //currenttime
    var $this->cws;   //chinese word spliter
    var $key_prefix;  //key prefix for the whole search engine
    var $key_contentid;   //key for {contentid} set
    var $key_index_prefix;  //key prefix for the index
    var $r; //redis link
    var $resultreturntype;
    
    /*
     *
     */
    
    function RedisFSC($redislink){
        $this->nowtime = time();
        $this->keyprefix = "RFSC";
        $this->key_contentid = "{$this->keyprefix}:content:id";
        $this->key_index_prefix = "{$this->keyprefix}:index";
        $this->resultreturntype = "string";
        if($redislink){
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
    
    function index($content){
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
                    $r->zAdd("{$this->key_index_prefix}:time:{$tmp['word']}",$nowtime,$postid);
                }
            }
        }
        return true;
    }
    
    function search($key,$resultorder="desc"){
        if(empty($key)){
            return true;
        }
        $this->cws->send_text($keyword);
        $keyarray = array();
        while ($res = $this->cws->get_result()){
            foreach ($res as $tmp)
            {
                if ($tmp['len'] == 1 && $tmp['word'] == "\r"){
                    continue;
                }elseif ($tmp['len'] == 1 && $tmp['word'] == "\n"){
                    continue;
                }else{
                    $keyarray[] = "index:time:{$tmp['word']}";
                }
            }
        }
        $randomkey = rand(0,9999);
        $this->cws->close();
        $tmpkeyname = "{$this->keyprefix}:tmpkey:{$nowtime}{$randomkey}";
        $r->zInter($tmpkeyname ,$keyarray);
        $data = $r->sort($tmpkeyname,array('get'=>"post:*",'sort'=>$resultorder));
        return $this->resultreturntype=="string"?join(",",$data):$data;
    }

}