<?php

namespace App\EventListener;

use App\Factory\StatusChain\StatusChainFactory;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

final class CacheWarmer implements CacheWarmerInterface
{
    public const DIR_CLASSES = 'Entity/Classes/StatusDTO/Compiled';
    public const FILENAME = 'StatusFactory.php';

    public function warmUp($cacheDirectory)
    {
        $classesData = (new StatusChainFactory())->makeChainClasses();

        $directory = __DIR__ . '/../' . self::DIR_CLASSES;

        (new Filesystem())->dumpFile(
            $directory . '/' . self::FILENAME,
            "<?php\r\n" . $classesData
        );

        return [];
    }

    public function isOptional()
    {
        return false;
    }
}
