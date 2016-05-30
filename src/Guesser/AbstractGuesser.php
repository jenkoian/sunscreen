<?php

namespace Jenko\Sunscreen\Guesser;

use Composer\Package\PackageInterface;

abstract class AbstractGuesser implements GuesserInterface
{
    /**
     * @var string
     */
    protected $vendorDir;

    /**
     * @param string $vendorDir
     */
    public function __construct($vendorDir)
    {
        $this->vendorDir = $vendorDir;
    }

    /**
     * {@inheritdoc}
     */
    abstract function guess(PackageInterface $package);
}