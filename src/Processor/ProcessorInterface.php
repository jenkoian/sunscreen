<?php

namespace Jenko\Sunscreen\Processor;

interface ProcessorInterface
{
    /**
     * @param bool $asString
     *
     * @return string|bool
     */
    public function process($asString = false);
}
