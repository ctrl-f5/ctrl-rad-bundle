<?php

namespace Ctrl\RadBundle\Crud\Action;

use Ctrl\RadBundle\Crud\Config;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditAction extends AbstractAction
{
    const SAVE_SUCCESS_RELOAD   = 'reload';
    const SAVE_SUCCESS_REDIRECT = 'redirect';

    public function execute(Request $request, $id = null, array $context = array())
    {
        $options = $this->config->getOptions();
        $routes = $this->config->getRoutes();
        $config = $options['action_config'];

        if ((!$id && $routes['create'] === false) || ($id && $routes['edit'] === false)) {
            throw new NotFoundHttpException('CRUD route disabled');
        }

        /** @var FormInterface $form */
        $form                   = $config['form'];
        $entity                 = $config['entity'];
        $context['is_create']   = false;
        $context['route']       = array(
            'route'     => $request->get('_route'),
            'params'    => $request->get('_route_params'),
        );

        if ($id) {
            $entity = $this->getEntityService()->getFinder()->firstOrNull()->find(array('id' => $id));
            if (!$entity) {
                $this->session->getFlashBag()->add('error', sprintf('%s not found', $options['entity_label']));
                return new RedirectResponse($this->router->generate($routes['index']));
            }
            $context['is_create'] = true;
        }

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $entity = $form->getData();

            if (isset($config['pre_persist'])) {
                $result = call_user_func_array($options['pre_persist'], array($entity, $context));
                if ($result instanceof Response) return $result;
            }

            $this->getEntityService()->persist($entity);

            if (isset($config['post_persist'])) {
                $result = call_user_func_array($options['post_persist'], array($entity, $context));
                if ($result instanceof Response) return $result;
            }
            $context['route']['params']['id'] = $entity->getId();

            if ($config['save_success_redirect']) {
                return new RedirectResponse($this->router->generate(
                    $routes['index']
                ));
            }

            return new RedirectResponse($this->router->generate(
                $context['route']['route'],
                $context['route']['params']
            ));
        }

        $viewVars = array_merge(array(
            'entity'        => $entity,
            'form'          => $form->createView(),
            'config'        => $this->config,
            'options'       => $this->config->getOptions(),
            'routes'        => $routes,
            'action'        => $config,
        ), $options['view_vars']);

        return $this->templating->renderResponse($config['template'], $viewVars);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'template'                  => 'CtrlRadBundle:crud:edit.html.twig',
            'template_form_elements'    => 'CtrlRadBundle:partial:_form_elements.html.twig',
            'template_form_buttons'     => 'CtrlRadBundle:partial:_form_buttons.html.twig',
            'save_success_redirect'     => self::SAVE_SUCCESS_REDIRECT,
            'post_persist'              => null,
            'pre_persist'               => null,
            'entity'                    => null,
            'entity_id'                 => null,
        ]);
        $resolver->setRequired([
            'form'
        ]);
    }
}
