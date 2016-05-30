<?php

namespace Jenko\Sunscreen;

interface InstantiatorInterface
{
    
    /**
     * @param string $className
     *
     * @return object
     *
     * @throws \Doctrine\Instantiator\Exception\ExceptionInterface
     */
    public function instantiate($className);

}
