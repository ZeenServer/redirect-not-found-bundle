<?php

namespace Zeen\ZeenRedirectNotFoundBundle\DependencyInjection;

use Zeen\ZeenRedirectNotFoundBundle\Observer\NotFoundSubscriberInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;


class AddDependencyCallsCompilerPass implements CompilerPassInterface
{
    const PATH_ATTR_NAME = 'path';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->processObservers($container);
        $this->processSubscribers($container);
    }

    /**
     * @param ContainerBuilder $container
     *
     * @throws LogicException
     */
    private function processObservers(ContainerBuilder $container)
    {
        $observers =  array();
        $pool = $container->getDefinition('zeen.zeenredirect_not_found.observer_pool');

        foreach ($container->findTaggedServiceIds('zeen.zeenredirectnotfound.observer') as $id => $attrs) {
            $observer= $container->getDefinition($id);
            $arguments = $observer->getArguments();

            //First argument - prefix of url
            $observers[$id] = $arguments[0];
        }

        foreach ($container->findTaggedServiceIds('zeen.zeenredirect_not_found.observer') as $id => $attrs) {
            $basePath = false;
            foreach ($attrs as $attrRow) {
                if (isset($attrRow[self::PATH_ATTR_NAME])) {
                    $basePath = $attrRow[self::PATH_ATTR_NAME];
                }
            }
            if (!$basePath) {
                throw new LogicException(sprintf('Missed required tag attribute "%s".', self::PATH_ATTR_NAME));
            }
            $observers[$id] = $basePath;
        }

        if (count($observers)) {
            $pool->addMethodCall('addObservers',  [$observers]);
        }
    }

    /**
     * @param ContainerBuilder $container
     */
    private function processSubscribers(ContainerBuilder $container)
    {
        $subscribers =  array();
        $pool = $container->getDefinition('zeen.zeenredirect_not_found.observer_pool');

        foreach ($container->findTaggedServiceIds('zeen.zeebredirect_not_found.subscriber') as $id => $attrs) {
            $subscriberDef = $container->getDefinition($id);
            $subscribers[$id] = $this->getSubscribedPaths($subscriberDef);
        }

        if (count($subscribers)) {
            $pool->addMethodCall('addSubscribers', [$subscribers]);
        }
    }

    /**
     * @param Definition $subscriberDef
     *
     * @return array
     */
    private function getSubscribedPaths(Definition $subscriberDef)
    {
        $class = $subscriberDef->getClass();

        if (!in_array('Zeen\ZeenRedirectNotFoundBundle\Observer\NotFoundSubscriberInterface', class_implements($class))) {
            throw new InvalidArgumentException(sprintf(
                'Class "%s" must implements interface "%s"',
                $class,
                'Zeen\ZeenRedirectNotFoundBundle\Observer\NotFoundSubscriberInterface'
            ));
        }
        $paths = forward_static_call([$class, 'getPaths']);
        if (!is_array($paths)) {
            throw new RuntimeException(sprintf('Can\'t retrieve paths from subscriber class "%s".', $class));
        }

        return $paths;
    }
}
