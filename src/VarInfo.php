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

        if (\php_uname('m') !== 'x86_64') {
            throw new \RuntimeException('Unsupported machine type');
        }

        switch (\php_uname('s')) {
            case 'Linux':
                $libraryFileName = 'ffi_linux.so';
                break;
            case 'Darwin':
                $libraryFileName = 'ffi_darwin.dylib';
                break;
            default:
                throw new \RuntimeException('Unsupported operating system');
        }

        self::$libc = \FFI::cdef(
            "
            int var_sizeof(char *name);
            int var_class_sizeof(char *name);
            ",
            __DIR__ . "/../library/" . $libraryFileName);
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
