<?php

namespace Aldaflux\AldafluxMatomoBundle\DependencyInjection;


use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\Config\FileLocator;

class AldafluxMatomoExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container) : void 
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        

                $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
                $loader->load('services.yml');
                
                if (isset($config['default']))
                {
                    $container->setParameter( 'aldaflux_matomo.site', $config['default']['site'] );
                    $container->setParameter( 'aldaflux_matomo.token_auth', $config['default']['token_auth'] );
                }
                
        
        
    }
}
