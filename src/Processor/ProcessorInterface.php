<?php

namespace Jenko\Sunscreen\Processor;

interface ProcessorInterface
{
    /**
     * @param bool $asString
     *
     * @return mixed
     */
    public function generate($asString = false);
}