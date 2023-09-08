<?php

if (!function_exists('dateTimeNow')) {
    function dateTimeNow()
    {
        return date("Y-m-d H:i:s");
        // 2023-06-15 11:53:08.368
        // "2023-06-15 15:05:26" hasil dd datetimenow
    }
}
