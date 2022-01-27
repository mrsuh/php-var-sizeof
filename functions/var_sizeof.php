<?php

if (!function_exists('var_sizeof')) {

    /**
     * @param mixed $var
     */
    function var_sizeof($var): int
    {
        return \Mrsuh\VarInfo::varSizeof($var);
    }
}
