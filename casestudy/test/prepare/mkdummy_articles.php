<?php

date_default_timezone_set("UTC");

define("USER_NUM", 100000);
define("ARTICLE_NUM", 3650000);

$article_id=0;
$time = date("Y-m-d H:i:s");

while(true){
    for($user_id=1; $user_id<=USER_NUM; $user_id++){
        $article_id ++;
        if($article_id > ARTICLE_NUM){
            break 2;
        }
        $time = date("Y-m-d H:i:s", $article_id);
        echo "${article_id}\t${user_id}\tTITLE_${article_id}\tCONTENT_${article_id}\t1000\t${time}\t${time}\n";
    }
}