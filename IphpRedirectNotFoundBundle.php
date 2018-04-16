<?php

namespace Zeen\ZeenRedirectNotFoundBundle;

use Zeen\ZeenRedirectNotFoundBundle\DependencyInjection\AddDependencyCallsCompilerPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;

class ZeenRedirectNotFoundBundle extends Bundle
{

    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddDependencyCallsCompilerPass());

    }
}
