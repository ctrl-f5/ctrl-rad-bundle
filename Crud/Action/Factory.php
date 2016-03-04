<?php

namespace Ctrl\RadBundle\Crud\Action;

use Ctrl\RadBundle\Crud\Config;
use Ctrl\RadBundle\Crud\ConfigBuilder;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Factory implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param string $type
     * @param Config|ConfigBuilder $config
     * @return AbstractAction
     */
    public function create($config)
    {
        if ($config instanceof ConfigBuilder) $config = $config->buildConfig();

        $class = $config->getActionClass();
        if (!is_subclass_of($class, AbstractAction::class)) {
            throw new \InvalidArgumentException(sprintf('class %s does not extend %s', $class, AbstractAction::class));
        }

        return new $class(
            $config,
            $this->container->get('router'),
            $this->container->get('templating')
        );
    }
}
