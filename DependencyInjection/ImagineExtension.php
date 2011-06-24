<?php

namespace Bundle\ImagineBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator,
    Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\Definition,
    Symfony\Component\DependencyInjection\Reference,
    Symfony\Component\DependencyInjection\Container,
    Symfony\Component\DependencyInjection\Parameter,
    Symfony\Component\DependencyInjection\Loader\XmlFileLoader,
    Symfony\Component\HttpKernel\DependencyInjection\Extension;

class ImagineExtension extends Extension
{
    protected $resources = array(
        'imagine' => 'imagine.xml'
    );

    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load($this->resources['imagine']);

        foreach ($configs as $processorName => $config)
        {
            $commands = isset ($configs['commands']) ? $configs['commands'] : array();
            $processDef = new Definition('Imagine\Processor');
            foreach ($commands as $command)
            {
                if ( ! isset ($command['name'])) {
                    throw new \LogicException('Command doesn\'t have a name, check your app configuration');
                }
                $processDef->addMethodCall($command['name'], (array)$command['arguments']);
            }
            $container->setDefinition('imagine.processor.' . $processorName, $processDef);
        }
    }

    /**
     * Returns the namespace to be used for this extension (XML namespace).
     *
     * @return string The XML namespace
     */
    public function getNamespace()
    {
        return 'http://www.symfony-project.org/schema/dic/symfony';
    }

    /**
     * @return string
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__ . '/../Resources/config/';
    }

    /**
     * Returns the recommended alias to use in XML.
     *
     * This alias is also the mandatory prefix to use when using YAML.
     *
     * @return string The alias
     */
    public function getAlias()
    {
        return 'imagine';
    }
}
