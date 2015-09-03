<?php

namespace Ctrl\RadBundle\Crud\Action;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class IndexAction extends AbstractAction
{
    public function execute(Request $request)
    {
        $options = $this->config->getOptions();

        if ($this->config->getRouteConfig()['route_index'] === false) {
            throw new NotFoundHttpException('CRUD route disabled');
        }

        $filterActive   = false;
        $criteria       = array();
        $form           = null;
        $formView       = null;

        if ($options['filter_enabled']) {
            /** @var FormInterface $form */
            $form = $options['filter_form'];

            if ($request->query->has($form->getName())) {
                $filterActive = true;
                $form->submit((array)$request->query->getIterator()[$form->getName()]);
                $criteria = $this->createFilterCriteria($form);
            }

            $formView = $form->createView();
        }

        $paginator = $this->getEntityService()->getFinder()->paginate()->find($criteria);
        $paginator->configureFromRequestParams($request->query->all());

        return $this->templating->renderResponse($this->config->getTemplateConfig()['crud_index'], array(
            'paginator'     => $paginator,
            'filterActive'  => $filterActive,
            'config'        => $this->config,
            'form'          => $formView,
        ));
    }
}
