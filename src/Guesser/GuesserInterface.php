<?php

namespace Jenko\Sunscreen\Guesser;

use Composer\Package\PackageInterface;

interface GuesserInterface
{
    /**
     * @var string
     */
    const VENDOR_DIR = 'vendor';
    
    /**
     * @param PackageInterface $package
     *
     * @return array
     */
    public function guess(PackageInterface $package);
}