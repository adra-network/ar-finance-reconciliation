<?php

if(!function_exists('trailing_zeros')) {
    function trailing_zeros($number) {
        return number_format((float)$number, 2, '.', '');
    }
}