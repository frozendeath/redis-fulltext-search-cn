<?php
require_once('../lib/redis.fulltext.search.cn.php');
$rs = new RedisFSC();
$rs->index("测试下下",rand(0,9999));
echo $rs->search("测试");
?>