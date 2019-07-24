<?php

if (! function_exists('str_limit_reverse')) {
    function str_limit_reverse($str, $limit = 100, $prepend = '...')
    {
        $width = mb_strwidth($str);
        $start = $width <= $limit ? 0 : $width - $limit;

        return $prepend.mb_strcut($str, $start, $limit);
    }
}
