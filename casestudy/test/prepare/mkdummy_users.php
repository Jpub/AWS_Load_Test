<?php
date_default_timezone_set("UTC");

define("USER_NUM", 100000);

$time = date("Y-m-d H:i:s");
for($i=1; $i<=USER_NUM; $i++){
    echo "${i}\tNAME_${i}\t${time}\t${time}\n";
}