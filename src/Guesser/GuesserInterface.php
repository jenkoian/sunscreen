<?php

namespace Jenko\Sunscreen\Guesser;

use Composer\Package\PackageInterface;

interface GuesserInterface
{
    /**
     * @param PackageInterface $package
     *
     * @return array
     */
    public function guess(PackageInterface $package);
}