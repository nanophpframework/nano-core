<?php

namespace Nano\Http\Contracts;

interface KernelInterface
{
    public function handle(RequestInterface $request):self;
    public function send(): JsonResponseInterface;
}
