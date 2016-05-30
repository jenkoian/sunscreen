<?php

namespace <namespace>;

use <interfaceFqn>;
use <namespace>\<interfaceName> as Local<interfaceName>;

final class <className> implements Local<interfaceName>
{
    /**
     * @var <interfaceName>
     */
    private $<interfaceProperty>;

    /**
     * @var <interfaceName> $<interfaceProperty>
     */
    public function __construct(<interfaceName> $<interfaceProperty>)
    {
        $this-><interfaceProperty> = $<interfaceProperty>;
    }
    <methods>
}
