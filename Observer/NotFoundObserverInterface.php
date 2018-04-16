<?php

namespace Zeen\ZeenRedirectNotFoundBundle\Observer;

interface NotFoundObserverInterface
{
    /**
     * @param string       $uri
     *
     * @return string|null
     */
    public function findRedirect($uri);
}
