# PHP var_sizeof()

Function for getting size of any PHP variable in bytes.<br>
It must be more accurate tool to calculate total size of PHP variable than `memory_get_usage()`, but it has [restrictions](#warning-restrictions).

### How it works
`var_sizeof()` with `var_class_sizeof()` uses FFI to access internal structures of PHP variables.<br>
It calculates the size of internal structures such as `zval`, `_zend_array`, `_zend_object`, etc., as well as additional allocated memory for them.<br>
It doesn't take into calculate the memory of handlers/functions/etc.

### Requirements
* PHP >= 7.4 (with FFI)
* Linux(x86_64) / Darwin(x86_64)

### How to install
```bash
composer require mrsuh/php-var-sizeof
```

### Functions
```php
int var_sizeof(mixed $var);
```

```php
int var_class_sizeof(mixed $var);
```

## Usage

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

$int = 1;
printf("variable \$int size: %d bytes\n", var_sizeof($int));

$array = array_fill(0, 100, $a);
printf("variable \$array size: %d bytes\n", var_sizeof($array));

$object = new \stdClass();
printf("variable \$object size: %d bytes\n", var_sizeof($object));
printf("class \$object size: %d bytes\n", var_class_sizeof($object));
```

### var_sizeof vs memory_get_usage

PHP 8.1.2 Linux(x86_64)

| type                                               | var_sizeof(bytes) | memory_get_usage(bytes) |
|----------------------------------------------------|-------------------|-------------------------|
| NULL                                               | 16                | 0                       |
| boolean(true)                                      | 16                | 0                       |
| integer(1)                                         | 16                | 0                       |
| double(1.5)                                        | 16                | 0                       |
| string("hello world")                              | 27                | 40                      |
| resource                                           | 48                | 416                     |
| callable                                           | 72                | 384                     |
| array(count: 0, list: true)                        | 336               | 0                       |
| array(count: 100, list: true)                      | 2,128             | 8,248                   |
| array(count: 1,000, list: true)                    | 16,464            | 36,920                  |
| array(count: 10,000, list: true)                   | 262,224           | 528,440                 |
| array(count: 100, list: false)                     | 5,192             | 8,248                   |
| array(count: 1,000, list: false)                   | 41,032            | 41,016                  |
| array(count: 10,000, list: false)                  | 655,432           | 655,416                 |
| EmptyClass{}                                       | 72                | 40                      |
| ClassWithArray{"array(count: 0, list: true)"}      | 408               | 56                      |
| ClassWithArray{"array(count: 100, list: true)"}    | 2,200             | 8,304                   |
| ClassWithArray{"array(count: 1,000, list: true)"}  | 16,536            | 36,976                  |
| ClassWithArray{"array(count: 10,000, list: true)"} | 262,296           | 528,496                 |
| ClassWithObject{"EmptyClass{}"}                    | 144               | 96                      |
| ArrayIterator{"array(count: 100, list: true)"}     | 2,264             | 8,376                   |
| ArrayIterator{"array(count: 100, list: false)"}    | 5,328             | 40,376                  |

| type                                               | var_class_sizeof(bytes) | var_sizeof(bytes) | memory_get_usage(bytes) |
|----------------------------------------------------|-------------------------|-------------------|-------------------------|
| EmptyClass{}                                       | 1,362                   | 72                | 40                      |
| ClassWithArray{"array(count: 0, list: true)"}      | 1,494                   | 408               | 56                      |
| ClassWithArray{"array(count: 100, list: true)"}    | 1,494                   | 2,200             | 8,304                   |
| ClassWithArray{"array(count: 1,000, list: true)"}  | 1,494                   | 16,536            | 36,976                  |
| ClassWithArray{"array(count: 10,000, list: true)"} | 1,494                   | 262,296           | 528,496                 |
| ClassWithObject{"EmptyClass{}"}                    | 1,495                   | 144               | 96                      |
| ArrayIterator{"array(count: 100, list: true)"}     | 2,437                   | 2,264             | 8,376                   |
| ArrayIterator{"array(count: 100, list: false)"}    | 2,437                   | 5,328             | 40,376                  |


### :warning: Restrictions
* works correctly only with userland objects and SPL \ArrayIterator
* doesn't work correctly with complicated structures like extensions/resources/callables/functions
* to calculate total size of an object you need to use `var_sizeof()` with `var_class_sizeof()`

## For contributors

### How to reproduce a table of numbers above
```bash
git clone git@github.com:mrsuh/php-var-sizeof.git && cd php-var-sizeof
composer install
docker build -t image-php-var-sizeof .
docker run -it --rm --name my-running-script -v "$PWD":/app image-php-var-sizeof php bin/render-table.php
```

### How to compile library
```bash
cd php-src
./buildconf
./configure
cd ..
make DEBUG=1
```
