<?php

namespace App\Factory\StatusChain;

use App\Factory\StatusChain\Helper\PropertyHelper;
use ReflectionClass;

class StatusChainFactory
{
    public const NAMESPACE = 'App\Entity\Classes\StatusDTO';

    public function makeChainClasses(): string
    {
        $namespace = self::NAMESPACE;
        $result = <<<EOF
//
// Auto-generated, DO NOT EDIT. 
// To rebuild this file use this command: php bin/console cache:clear
//

namespace App\Entity\Classes\StatusDTO\Compiled;

EOF;

        $statusFactoryMethods = [];
        $alreadyUse = [];

        $classes = $this->getStatusClasses();
        foreach ($classes as $className) {

            $result .= <<<EOF
use {$namespace}\\{$className};

EOF;
            $chainInterfaces = [];
            foreach ($this->generatePropChain($className) as $pair) {

                [$curr, $next] = $pair;
                $thisInterface = $className . '_' . $curr->name;
                $chainInterface = $className . '_' . $next->name;
                $type = (string)$curr->type;
                $use = '';
                if ($this->checkStandartType($type) === false) {
                    if(!isset($alreadyUse[$type])) {
                        $alreadyUse[$type] = 1;
                        $use = 'use ' . $type . ';';
                    }
                    $type = explode('\\', $type);
                    $type = $type[count($type) - 1];
                }

                $result .= <<<EOF
{$use}
interface {$thisInterface} {
    public function set{$curr->nameUc}({$curr->nullModifier}{$type} \${$curr->name}): {$chainInterface};
}

EOF;

                $chainInterfaces[] = $thisInterface;
                $chainInterfaces[] = $chainInterface;
            }

            if (!count($chainInterfaces)) {
                continue;
            }

            $chainInterfaces = array_values(array_unique($chainInterfaces));
            $chainInterfacesStr = implode(',' . PHP_EOL . '    ', array_unique($chainInterfaces));
            $startInterface = $chainInterfaces[0];
            $finalizeInterface = $chainInterfaces[count($chainInterfaces) - 1];

            $result .= <<<EOF
interface {$finalizeInterface} {
    public function finalize(): {$className};
}
class {$className}Factory implements
    {$chainInterfacesStr}
{
    private {$className} \$value;
    public function __construct() {
        \$this->value = new {$className}();
    }
    public function finalize(): {$className} {
        return \$this->value;
    }
    public function start(): {$startInterface} {
        return \$this;
    }

EOF;

            foreach ($this->generatePropChain($className) as $pair) {
                [$curr, $next] = $pair;
                $chainInterface = $className . '_' . $next->name;

                $type = $curr->type;
                if ($this->checkStandartType($type) === false) {
                    $type = explode('\\', $type);
                    $type = $type[count($type) - 1];
                }

                $result .= <<<EOF
    public function set{$curr->nameUc}({$curr->nullModifier}{$type} \${$curr->name}): {$chainInterface} {
        \$this->value->{$curr->name} = \${$curr->name};
        return \$this;
    }

EOF;
            }

            $result .= <<<EOF
}

// --------------------------------------------------------------------------------------------


EOF;
            $statusFactoryMethods[] = <<<EOF
    function make{$className}(): {$startInterface} {
        return (new {$className}Factory())->start();
    }

EOF;
        }

        $statusFactoryMethodsStr = implode("", $statusFactoryMethods);
        $result .= <<<EOF
class StatusFactory
{
{$statusFactoryMethodsStr}
}
EOF;

        return $result;
    }

    private function generatePropChain($className)
    {
        $reflection = new ReflectionClass(self::NAMESPACE . '\\' . $className);
        $properties = $reflection->getProperties();
        foreach ($properties as $key => $property) {
            if ($property->name === 'currentStatus') {
                continue;
            }
            $firstProp = new PropertyHelper();
            $firstProp->name = $property->name;
            $firstProp->nameUc = ucfirst($property->name);
            $firstProp->type = $property->getType();
            $firstProp->nullModifier = $property->getType()->allowsNull() ? '?' : '';

            $secondProp = new PropertyHelper();
            $isLast = ($key + 1) == count($properties);
            if (!$isLast) {
                $nextProperty = $properties[$key + 1];
                $secondProp->name = $nextProperty->name;
                $secondProp->nameUc = ucfirst($nextProperty->name);
                $secondProp->type = $nextProperty->getType();
            } else {
                $secondProp->name = 'finalize';
                $secondProp->nameUc = 'Finalize';
                $secondProp->type = null;
            }
            yield [$firstProp, $secondProp];
        }
    }

    private function getStatusClasses(): array
    {
        $dir = __DIR__ . '/../../Entity/Classes/StatusDTO/';
        $files = $this->scandir($dir);

        $classes = array_map(function ($file) {
            return str_replace('.php', '', $file);
        }, $files);

        return $classes;
    }

    private function scandir($dir)
    {
        $files = scandir($dir);
        foreach ($files as $key => $file) {
            if ($file == '.' || $file == '..' || $file == 'BaseStatusDTO.php' || is_dir($dir . $file)) {
                unset($files[$key]);
            }
        }
        return array_values($files);
    }

    private function checkStandartType($type)
    {
        return in_array($type, ['int','float','null','string','bool','array','object']);
    }
}