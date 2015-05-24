<?php

namespace Ctrl\RadBundle\Controller;

use Ctrl\Common\EntityService\AbstractDoctrineService;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class CrudController extends AbstractController
{
    protected $crudOptions = array();

    protected $defaultCrudOptions = array(
        'label_entity'          => 'Entity',
        'templates'             => array(),
        'view_vars'             => array()
    );

    /**
     * @return AbstractDoctrineService
     */
    abstract protected function getEntityService();

    protected function getCrudOptions()
    {
        if (empty($this->crudOptions)) {
            $config = $this->get('service_container')->getParameter('ctrl_rad.config');
            $config['templates'];

            $this->crudOptions = $this->defaultCrudOptions;
            $this->crudOptions['templates'] = array_replace_recursive(
                $config['templates'],
                $this->defaultCrudOptions['templates']
            );
        }

        return $this->crudOptions;
    }

    /**
     * @param array $options
     * @return array
     */
    protected function configureCrud(array $options = array())
    {
        if (isset($options['label_entity'])) {
            $routePrefix = isset($options['route_prefix']) ? $options['route_prefix']: '';

            $entityNameCanonical = str_replace(' ', '_', strtolower($options['label_entity']));
            if (!isset($options['label_entities'])) $options['label_entities']  = $options['label_entity'] . 's';
            if (!isset($options['route_index']))    $options['route_index']     = $routePrefix . $entityNameCanonical . '_index';
            if (!isset($options['route_edit']))     $options['route_edit']      = $routePrefix . $entityNameCanonical . '_edit';
            if (!isset($options['route_create']))   $options['route_create']    = $routePrefix . $entityNameCanonical . '_edit';
        }

        return array_replace_recursive(
            $this->getCrudOptions(),
            $options
        );
    }

    /**
     * @param array $options
     * @return array
     */
    protected function configureIndex(array $options = array())
    {
        if (!isset($options['filter_enabled'])) {
            $options['filter_enabled'] = (isset($options['filter_form'])) && $options['filter_form'];
        }

        return array_merge(
            $this->configureCrud(),
            array(
                'filter_enabled'    => false,
                'filter_form'       => null,
                'sort'              => array(),
                'columns'           => array(
                    'id' => '#',
                ),
                'actions'           => array(),
            ),
            $options
        );
    }

    /**
     * @param null|int $id
     * @param array $options
     * @return array
     */
    protected function configureEdit($id = null, array $options = array())
    {
        $entity = null;
        if ($id) {
            $entity = $this->getEntityService()->findOne(array('id' => $id));
        }

        return array_merge(
            $this->configureCrud(),
            array(
                'form'      => null,
                'id'        => $id,
                'entity'    => $entity,
            ),
            $options
        );
    }

    /**
     * @param array $options
     * @return array
     */
    protected function configureButton(array $options)
    {
        return array_merge(
            array(
                'type'      => 'a',
                'label'     => 'action',
                'icon'      => null,
                'class'     => 'default',
                'route'     => function ($entity) use ($options) {
                    return $this->generateUrl($options['route_name'], array('id' => $entity->getId()));
                }
            ),
            $options
        );
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $options        = $this->configureIndex();

        if ($options['route_index'] === false) {
            throw $this->createNotFoundException('CRUD route disabled');
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
                $form->submit((array)$request->query->getIterator()['form']);
                $criteria = $this->createFilterCriteria($form);
            }

            $formView = $form->createView();
        }

        $paginator = $this->getEntityService()->getFinder()->paginate()->find($criteria, $options['sort']);

        return $this->render($options['templates']['crud_index'], array(
            'paginator'     => $paginator,
            'filterActive'  => $filterActive,
            'options'       => $options,
            'form'          => $formView,
        ));
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id = null)
    {
        $options        = $this->configureEdit($id);

        if ((!$id && $options['route_create'] === false) || ($id && $options['route_edit'] === false)) {
            throw $this->createNotFoundException('CRUD route disabled');
        }

        /** @var FormInterface $form */
        $form           = $options['form'];
        $formView       = null;
        $entity         = $options['entity'];

        if ($id) {
            $entity = $this->getEntityService()->getFinder()->firstOrNull()->find(array('id' => $id));
            if (!$entity) {
                $this->addFlash('error', sprintf('%s not found', $options['label_entity']));
                return $this->redirect($this->generateUrl($options['route_index']));
            }
        }

        if ($request->isMethod('POST')) {
            if ($form->handleRequest($request)->isValid()) {

                $entity = $form->getData();
                $this->crudPrePersist($entity);
                $this->getEntityService()->persist($entity);

                $routeParams = $request->get('_route_params');
                $routeParams['id'] = $entity->getId();
                return $this->redirect($this->generateUrl(
                    $request->get('_route'),
                    $routeParams
                ));
            }
        }

        $viewVars = array_merge(array(
            'entity'        => $entity,
            'form'          => $form->createView(),
            'options'       => $options,
        ), $options['view_vars']);

        return $this->render($options['templates']['crud_edit'], $viewVars);
    }

    protected function crudPrePersist($entity) {}

    protected function createFilterCriteria(FormInterface $form)
    {
        $criteria = array();

        /** @var FormInterface $child */
        foreach ($form as $child) {
            $field = $child->getName();
            $fieldPath = str_replace('_', '.', $field);
            switch ($child->getConfig()->getType()->getName()) {
                case 'text':
                    $criteria[$fieldPath . ' LIKE :'.$field] = '%' . $child->getData() . '%';
                    break;
                case 'checkbox':
                    if ($child->getData()) {
                        $criteria[$fieldPath] = true;
                    }
                    break;
                default:
                    $criteria[$fieldPath] = $child->getData();
            }
        }

        return $criteria;
    }
}