<?php

if (!function_exists('truncate_string')) {
    /**
     * Truncate a string to a specified length and append ellipsis if necessary.
     *
     * @param string $string The string to truncate.
     * @param int $length The maximum length of the string.
     * @return string The truncated string.
     */
    function truncate_string($string, $length = 100) {
        return (strlen($string) > $length)
            ? substr($string, 0, $length) . '...'
            : $string;
    }
}

// You can add more helper functions below as needed
