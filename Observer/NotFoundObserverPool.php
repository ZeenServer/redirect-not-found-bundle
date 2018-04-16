<?php

namespace Zeen\ZeenRedirectNotFoundBundle\Observer;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class NotFoundObserverPool extends ContainerAware
{
    /**
     * @var array
     */
    protected $observersByPath = array();

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    /**
     * @param array $observers
     */
    public function addObservers($observers)
    {
        foreach ($observers as $serviceId => $path)
        {
            $this->observersByPath[$path][] = $serviceId;
        }
    }

    /**
     * @param array $subscribers
     */
    public function addSubscribers(array $subscribers)
    {
        foreach ($subscribers as $serviceId => $paths) {
            foreach ($paths as $path) {
                $this->observersByPath[$path][] = $serviceId;
            }
        }
    }

    /**
     * @param string $uri
     *
     * @return string|null
     */
    public function findRedirect($uri)
    {
        foreach ($this->observersByPath as $path => $serviceIds) {
            if (substr($uri, 0, strlen($path)) == $path) {
                return $this->findRedirectInServices($uri, $serviceIds);
            }
        }

        return null;
    }

    /**
     * @param string $uri
     * @param array  $serviceIds
     *
     * @return string|null
     */
    public function findRedirectInServices ($uri, array $serviceIds)
    {
        foreach ($serviceIds as $serviceId) {
            $redirectUri =  $this->container->get($serviceId)->findRedirect($uri);
            if (!is_null($redirectUri)) {
                return $redirectUri;
            }
        }

       return null;
    }

}