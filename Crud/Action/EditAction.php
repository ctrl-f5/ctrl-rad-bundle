<?php

namespace Ctrl\RadBundle\Crud\Action;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EditAction extends AbstractAction
{
    public function execute(Request $request, $id = null, array $context = array())
    {
        $options = $this->config->getOptions();
        $routes = $this->config->getRoutes();

        if ((!$id && $routes['route_create'] === false) || ($id && $routes['route_edit'] === false)) {
            throw new NotFoundHttpException('CRUD route disabled');
        }

        /** @var FormInterface $form */
        $form                   = $options['form'];
        $formView               = null;
        $entity                 = $options['entity'];
        $context['is_create']   = false;
        $context['route']       = array(
            'route'     => $request->get('_route'),
            'params'    => $request->get('_route_params'),
        );

        if ($id) {
            $entity = $this->getEntityService()->getFinder()->firstOrNull()->find(array('id' => $id));
            if (!$entity) {
                $this->session->getFlashBag()->add('error', sprintf('%s not found', $options['entity_label']));
                return new RedirectResponse($this->router->generate($routes['route_index']));
            }
            $context['is_create'] = true;
        }

        if ($request->isMethod('POST')) {
            if ($form->handleRequest($request)->isValid()) {

                $entity = $form->getData();

                if (isset($options['pre_persist'])) {
                    $result = call_user_func_array($options['pre_persist'], array($entity, $context));
                    if ($result instanceof Response) return $result;
                }

                $this->getEntityService()->persist($entity);

                if (isset($options['post_persist'])) {
                    $result = call_user_func_array($options['post_persist'], array($entity, $context));
                    if ($result instanceof Response) return $result;
                }
                $context['route']['params']['id'] = $entity->getId();

                return new RedirectResponse($this->router->generate(
                    $context['route']['route'],
                    $context['route']['params']
                ));
            }
        }

        $viewVars = array_merge(array(
            'entity'        => $entity,
            'form'          => $form->createView(),
            'config'        => $this->config,
        ), $options['view_vars']);

        return $this->templating->renderResponse($this->config->getTemplateConfig()['crud_edit'], $viewVars);
    }
}
