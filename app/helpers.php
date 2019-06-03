<?php

if (!function_exists('str_limit_reverse')) {
    function str_limit_reverse($str, $limit = 100, $prepend = '...') {
        $width = mb_strwidth($str);
        $start = $width <= 30 ? 0 : $width - 30;
        return $prepend . mb_strcut($str, $start, 30);
    }
}