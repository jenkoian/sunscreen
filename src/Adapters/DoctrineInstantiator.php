<?php

namespace Jenko\Sunscreen;

use Doctrine\Instantiator\InstantiatorInterface;
use Jenko\Sunscreen\InstantiatorInterface as LocalInstantiatorInterface;

final class DoctrineInstantiator implements LocalInstantiatorInterface
{
    /**
     * @var InstantiatorInterface
     */
     private $instantiatorInterface;

     /**
      * @var InstantiatorInterface $instantiatorInterface
      */
     public function __construct(InstantiatorInterface $instantiatorInterface)
     {
        $this->instantiatorInterface = $instantiatorInterface;
     }

     
    /**
     * {@inheritdoc}
     */
    public function instantiate($className)
    {
        return $this->instantiatorInterface->instantiate($className);
    }

}
