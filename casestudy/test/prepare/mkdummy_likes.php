<?php
date_default_timezone_set("UTC");

define("USER_NUM",      100000);
define("ARTICLE_NUM",  3650000);
define("LIKE_NUM",    36500000);

$article_id=0;
$time = date("Y-m-d H:i:s");

for($like=1; $like<=LIKE_NUM; $like++){
    $time       = date("Y-m-d H:i:s", $like);
    $article_id = rand(1, ARTICLE_NUM);
    $user_id    = rand(1, USER_NUM);
    echo "${like}\t${article_id}\t${user_id}\t${time}\t${time}\n";
}
