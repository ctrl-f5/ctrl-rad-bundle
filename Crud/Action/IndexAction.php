<?php

namespace Ctrl\RadBundle\Crud\Action;

use Ctrl\Common\Tools\Doctrine\Paginator;
use Ctrl\RadBundle\Crud\Config;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IndexAction extends AbstractAction
{
    public function execute(Request $request)
    {
        $options = $this->config->getOptions();
        $routes = $this->config->getRoutes();
        $config = $this->config->getActionConfig();

        if ($routes['index'] === false) {
            throw new NotFoundHttpException('CRUD route disabled');
        }

        $filterActive   = false;
        $criteria       = $config['default_criteria'];
        $form           = null;
        $formView       = null;

        if ($config['filter_enabled']) {
            /** @var FormInterface $form */
            $form = $config['filter_form'];

            if ($request->query->has($form->getName())) {
                $filterActive = true;
                $form->submit((array)$request->query->getIterator()[$form->getName()]);
                $criteria = $this->createFilterCriteria($form);
            }

            $formView = $form->createView();
        }

        $queryBuilder = $this->getEntityService()->getFinder()->queryBuilder()->find(
            $criteria, $config['sort']
        );
        if (is_callable($config['query_builder'])) {
            $config['query_builder']($queryBuilder);
        }
        $paginator = new Paginator($queryBuilder);
        $paginator->configureFromRequestParams($request->query->all());

        $config['table']->setData($paginator);

        return $this->templating->renderResponse($config['template'], array(
            'table'         => $config['table'],
            'filterActive'  => $filterActive,
            'form'          => $formView,
            'config'        => $this->config,
            'options'       => $options,
            'routes'        => $routes,
            'action'        => $config,
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults(array(
            'template'              => 'CtrlRadBundle:crud:index.html.twig',
            'template_filter_form'  => 'CtrlRadBundle:partial:_form_elements.html.twig',
            'filter_form'           => null,
            'filter_enabled'        => false,
            'table'                 => null,
            'default_criteria'      => array(),
            'query_builder'         => null,
            'sort'                  => array(),
        ));
    }
}
