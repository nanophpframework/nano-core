<?php

namespace Nano\Foundation;

use Nano\DependencyInjection\Contracts\ContainerInterface;
use Nano\Foundation\Contracts\ProviderInterface;

abstract class ServiceProvider implements ProviderInterface
{
    public function __construct(
        protected ContainerInterface $app,
    )
    {
        //
    }
}
