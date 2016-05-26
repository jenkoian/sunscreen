<?php

namespace Jenko\Sunscreen\Processor;

interface ProcessorInterface
{
    /**
     * @param bool $asString
     *
     * @return string|bool
     */
    public function generate($asString = false);
}
