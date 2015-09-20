<?php

namespace Ctrl\RadBundle\Crud\Action;

use Ctrl\Common\EntityService\ServiceInterface;
use Ctrl\RadBundle\Crud\Config;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface as Templating;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Router;

abstract class AbstractAction
{
    const ACTION_INDEX  = 'action_index';
    const ACTION_CREATE = 'action_create';
    const ACTION_EDIT   = 'action_edit';

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Templating
     */
    protected $templating;

    /**
     * @param Config $config
     * @param Router $router
     * @param Templating $templating
     */
    public function __construct(Config $config, Router $router, Templating $templating)
    {
        $optionsResolver = new OptionsResolver();
        $this->configureOptions($optionsResolver);
        $config->resolveCrudActionConfig($optionsResolver);

        $this->config       = $config;
        $this->router       = $router;
        $this->templating   = $templating;
    }

    abstract public function execute(Request $request);

    /**
     * @return ServiceInterface
     */
    protected function getEntityService()
    {
        return $this->config->getOptions()['entity_service'];
    }

    /**
     * @param FormInterface $form
     * @return array
     */
    protected function createFilterCriteria(FormInterface $form)
    {
        $criteria = array();

        /** @var FormInterface $child */
        foreach ($form as $child) {
            $field = $child->getName();
            $fieldPath = str_replace('_', '.', $field);
            switch ($child->getConfig()->getType()->getName()) {
                case 'text':
                    if ($child->getData()) {
                        $criteria[$fieldPath . ' LIKE :' . $field] = '%' . $child->getData() . '%';
                    }
                    break;
                case 'checkbox':
                    if ($child->getData()) {
                        $criteria[$fieldPath] = true;
                    }
                    break;
                default:
                    if ($child->getData()) {
                        $criteria[$fieldPath] = $child->getData();
                    }
            }
        }

        return $criteria;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([
            'template'
        ]);
    }
}
