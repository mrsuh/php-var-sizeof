<?php

if (!function_exists('var_class_sizeof')) {

    /**
     * @param mixed $var
     */
    function var_class_sizeof($var): int
    {
        return \Mrsuh\VarInfo::varClassSizeof($var);
    }
}
