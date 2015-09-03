<?php

namespace Ctrl\RadBundle\Crud\Action;

use Ctrl\RadBundle\Crud\Config;
use Ctrl\RadBundle\Crud\ConfigBuilder;
use Symfony\Component\DependencyInjection\ContainerAware;

class Factory extends ContainerAware
{
    /**
     * @param string $type
     * @param Config|ConfigBuilder $config
     * @return AbstractAction
     */
    public function create($type, $config)
    {
        if ($config instanceof ConfigBuilder) $config = $config->buildConfig();

        $options = $config->getOptions();
        if (!isset($options[$type])) {
            throw new \InvalidArgumentException(sprintf('unknown $type given: %s', $type));
        }

        $class = $options[$type];
        if (!is_subclass_of($class, AbstractAction::class)) {
            throw new \InvalidArgumentException(sprintf('class %s does not extend %s', $class, AbstractAction::class));
        }

        return new $class(
            $config,
            $this->container->get('router'),
            $this->container->get('session'),
            $this->container->get('templating')
        );
    }
}
