<?php
function the_truncated_excerpt($excerpt, $count){
$raw_str = strip_tags($excerpt);
$trim_str = rtrim($raw_str);
$the_str =  mb_strimwidth($trim_str, 0, $count, "...");

return $the_str;
}
