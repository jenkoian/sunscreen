<?php

namespace Jenko\Sunscreen;

use Composer\Installer\PackageEvent;

interface SunscreenInterface
{
    /**
     * @param PackageEvent $event
     * @return mixed
     */
    public static function postPackageInstall(PackageEvent $event);
}
