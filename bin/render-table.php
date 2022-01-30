<?php

require_once __DIR__ . '/../vendor/autoload.php';

function sdump($var): string
{
    switch (true) {
        case is_null($var):
        case is_resource($var):
            return sprintf("%s", gettype($var));
        case is_callable($var):
            return sprintf("callable");
        case is_bool($var):
            return sprintf("%s(%s)", gettype($var), $var ? 'true' : 'false');
        case is_integer($var):
        case is_double($var):
            return sprintf("%s(%s)", gettype($var), $var);
        case is_string($var):
            return sprintf("%s(\"%s\")", gettype($var), $var);
        case is_array($var):

            $isList = array_keys($var) === range(0, count($var) - 1) || empty($var);

            return sprintf("%s(count: %s, list: %s)", gettype($var), number_format(count($var)), $isList ? 'true' : 'false');

        case is_object($var):
            $reflection = new ReflectionObject($var);
            $properties = [];
            foreach ($reflection->getProperties() as $reflectionProperty) {
                $properties[] = sdump($reflectionProperty->getValue($var));
            }

            if($var instanceof \ArrayIterator) {
                $properties[] = sdump((array)$var);
            }

            return sprintf("%s%s", get_class($var), str_replace(['[', ']'], ['{', '}'], json_encode($properties)));
    }

    return '';
}

class EmptyClass
{
}

class ClassWithArray
{
    public array $array;

    public function __construct(array $array)
    {
        $this->array = $array;
    }
}

class ClassWithObject
{
    public EmptyClass $container;

    public function __construct()
    {
        $this->container = new EmptyClass();
    }
}

$tableCommonBuilder = new \MaddHatter\MarkdownTable\Builder();
$tableCommonBuilder->headers(['type', 'var_sizeof(bytes)', 'memory_get_usage(bytes)']);

$tableObjectBuilder = new \MaddHatter\MarkdownTable\Builder();
$tableObjectBuilder->headers(['type', 'var_class_sizeof(bytes)', 'var_sizeof(bytes)', 'memory_get_usage(bytes)']);

function addCommonRow(\MaddHatter\MarkdownTable\Builder $tableBuilder, $var, int $memory)
{
    $tableBuilder->row([sdump($var), number_format(var_sizeof($var)), number_format($memory)]);
}

function addObjectRow(\MaddHatter\MarkdownTable\Builder $tableBuilder, $var, int $memory)
{
    $tableBuilder->row([sdump($var), number_format(var_class_sizeof($var)), number_format(var_sizeof($var)), number_format($memory)]);
}

$memory      = memory_get_usage();
$null        = null;
$memoryUsage = memory_get_usage() - $memory;
addCommonRow($tableCommonBuilder, $null, $memoryUsage);

$memory      = memory_get_usage();
$bool        = true;
$memoryUsage = memory_get_usage() - $memory;
addCommonRow($tableCommonBuilder, $bool, $memoryUsage);

$memory      = memory_get_usage();
$integer     = 1;
$memoryUsage = memory_get_usage() - $memory;
addCommonRow($tableCommonBuilder, $integer, $memoryUsage);

$memory      = memory_get_usage();
$double      = 1.5;
$memoryUsage = memory_get_usage() - $memory;
addCommonRow($tableCommonBuilder, $double, $memoryUsage);

$memory      = memory_get_usage();
$string      = str_replace('{name}', 'world', 'hello {name}');
$memoryUsage = memory_get_usage() - $memory;
addCommonRow($tableCommonBuilder, $string, $memoryUsage);

$memory      = memory_get_usage();
$resource    = fopen('php://memory', 'r');
$memoryUsage = memory_get_usage() - $memory;
addCommonRow($tableCommonBuilder, $resource, $memoryUsage);

$memory      = memory_get_usage();
$callable    = function () {
};
$memoryUsage = memory_get_usage() - $memory;
addCommonRow($tableCommonBuilder, $callable, $memoryUsage);

$memory      = memory_get_usage();
$list0       = [];
$memoryUsage = memory_get_usage() - $memory;
addCommonRow($tableCommonBuilder, $list0, $memoryUsage);

$memory      = memory_get_usage();
$list100     = array_fill(0, 100, null);
$memoryUsage = memory_get_usage() - $memory;
addCommonRow($tableCommonBuilder, $list100, $memoryUsage);

$memory      = memory_get_usage();
$list1000    = array_fill(0, 1000, null);
$memoryUsage = memory_get_usage() - $memory;
addCommonRow($tableCommonBuilder, $list1000, $memoryUsage);

$memory      = memory_get_usage();
$list10000   = array_fill(0, 10000, null);
$memoryUsage = memory_get_usage() - $memory;
addCommonRow($tableCommonBuilder, $list10000, $memoryUsage);

$memory            = memory_get_usage();
$array100          = array_fill(0, 99, null);
$array100['index'] = null;
$memoryUsage       = memory_get_usage() - $memory;
addCommonRow($tableCommonBuilder, $array100, $memoryUsage);

$memory             = memory_get_usage();
$array1000          = array_fill(0, 999, null);
$array1000['index'] = null;
$memoryUsage        = memory_get_usage() - $memory;
addCommonRow($tableCommonBuilder, $array1000, $memoryUsage);

$memory              = memory_get_usage();
$array10000          = array_fill(0, 9999, null);
$array10000['index'] = null;
$memoryUsage         = memory_get_usage() - $memory;
addCommonRow($tableCommonBuilder, $array10000, $memoryUsage);

$memory      = memory_get_usage();
$emptyClass  = new EmptyClass();
$memoryUsage = memory_get_usage() - $memory;
addCommonRow($tableCommonBuilder, $emptyClass, $memoryUsage);
addObjectRow($tableObjectBuilder, $emptyClass, $memoryUsage);

$memory          = memory_get_usage();
$classWithArray0 = new ClassWithArray([]);
$memoryUsage     = memory_get_usage() - $memory;
addCommonRow($tableCommonBuilder, $classWithArray0, $memoryUsage);
addObjectRow($tableObjectBuilder, $classWithArray0, $memoryUsage);

$memory            = memory_get_usage();
$classWithArray100 = new ClassWithArray(array_fill(0, 100, null));
$memoryUsage       = memory_get_usage() - $memory;
addCommonRow($tableCommonBuilder, $classWithArray100, $memoryUsage);
addObjectRow($tableObjectBuilder, $classWithArray100, $memoryUsage);

$memory             = memory_get_usage();
$classWithArray1000 = new ClassWithArray(array_fill(0, 1000, null));
$memoryUsage        = memory_get_usage() - $memory;
addCommonRow($tableCommonBuilder, $classWithArray1000, $memoryUsage);
addObjectRow($tableObjectBuilder, $classWithArray1000, $memoryUsage);

$memory              = memory_get_usage();
$classWithArray10000 = new ClassWithArray(array_fill(0, 10000, null));
$memoryUsage         = memory_get_usage() - $memory;
addCommonRow($tableCommonBuilder, $classWithArray10000, $memoryUsage);
addObjectRow($tableObjectBuilder, $classWithArray10000, $memoryUsage);

$memory          = memory_get_usage();
$classWithObject = new ClassWithObject();
$memoryUsage     = memory_get_usage() - $memory;
addCommonRow($tableCommonBuilder, $classWithObject, $memoryUsage);
addObjectRow($tableObjectBuilder, $classWithObject, $memoryUsage);

$memory          = memory_get_usage();
$listIterator = new \ArrayIterator();
for($i = 0;$i < 100; $i++) {
    $listIterator[] = null;
}
$memoryUsage     = memory_get_usage() - $memory;
addCommonRow($tableCommonBuilder, $listIterator, $memoryUsage);
addObjectRow($tableObjectBuilder, $listIterator, $memoryUsage);

$memory          = memory_get_usage();
$arrayIterator = new \ArrayIterator();
for($i = 0;$i < 100; $i++) {
    $arrayIterator[sprintf('index%d', $i)] = null;
}
$memoryUsage     = memory_get_usage() - $memory;
addCommonRow($tableCommonBuilder, $arrayIterator, $memoryUsage);
addObjectRow($tableObjectBuilder, $arrayIterator, $memoryUsage);

echo sprintf("PHP %s %s(%s)\n\n", phpversion(), php_uname('s'), php_uname('m'));
echo $tableCommonBuilder->render();
echo "\n";
echo $tableObjectBuilder->render();
