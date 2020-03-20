<?php

namespace Alex\MultisiteBundle\DependencyInjection\Compiler;

use Alex\MultisiteBundle\Router\Loader\AnnotatedRouteControllerLoader;
use Alex\MultisiteBundle\Router\MultisiteRouter;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass to inject site context in extended services.
 */
class InjectSiteContextPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $container
            ->getDefinition('routing.loader.annotation')
            ->setClass(AnnotatedRouteControllerLoader::class)
            ->addMethodCall('setSiteContext', array(new Reference('site_context')));
        $container->getDefinition('router.default')->setClass(MultisiteRouter::class);
        $container->getDefinition('router.default')->addMethodCall('setSiteContext', array(new Reference('site_context')));
        $container->getDefinition('router.default')->addMethodCall('setSortRoutes', array("%alex_multisite.sort_routes%"));
    }
}
