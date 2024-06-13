<?php

namespace Mrsuh;

class VarInfo
{
    public static ?\FFI $libc = null;

    public static function init(): void
    {
        if (self::$libc !== null) {
            return;
        }

        $platform = strtolower(\php_uname('m'));
        $operatingSystem = strtolower(\php_uname('s'));

        $libraryFilePath = sprintf(
            "%s/../library/ffi_%s_%s.%s",
            __DIR__,
            $operatingSystem,
            $platform,
            $operatingSystem === 'linux' ? 'so' : 'dylib'
        );

        if(!is_file($libraryFilePath)) {
            throw new \RuntimeException(sprintf('Unsupported system "%s(%s)"', $operatingSystem, $platform));
        }

        self::$libc = \FFI::cdef(
            "
            int var_sizeof(char *name);
            int var_class_sizeof(char *name);
            ",
            $libraryFilePath);
    }

    /**
     * @param mixed $var
     */
    public static function varSizeof($var): int
    {
        self::init();

        return (int)self::$libc->var_sizeof("var");
    }

    /**
     * @param mixed $var
     */
    public static function varClassSizeof($var): int
    {
        self::init();

        return (int)self::$libc->var_class_sizeof("var");
    }
}
