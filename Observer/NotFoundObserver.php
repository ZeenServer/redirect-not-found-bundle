<?php

namespace Zeen\ZeenRedirectNotFoundBundle\Observer;

abstract class NotFoundObserver implements NotFoundObserverInterface
{
    protected $basePath;

    public function __construct($basePath)
    {
        $this->basePath = $basePath;
    }
}