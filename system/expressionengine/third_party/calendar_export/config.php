<?php

define('CALENDAR_EXPORT_VERSION', '0.1');
define('CALENDAR_EXPORT_DOCS', '');

/**
 * < EE 2.6.0 backward compat
 */
if (!function_exists('ee')) {
    function ee()
    {
        static $EE;
        if (! $EE) {
            $EE = get_instance();
        }

        return $EE;
    }
}