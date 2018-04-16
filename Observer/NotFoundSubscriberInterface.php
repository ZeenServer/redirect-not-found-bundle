<?php

namespace Zeen\ZeenRedirectNotFoundBundle\Observer;

interface NotFoundSubscriberInterface extends NotFoundObserverInterface
{
    /**
     * @return array
     */
    public static function getPaths();
}