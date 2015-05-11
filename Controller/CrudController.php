<?php

namespace Ctrl\RadBundle\Controller;

use Ctrl\RadBundle\EntityService\AbstractService;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class CrudController extends AbstractController
{
    protected $options = array(
        'label_entities'        => 'Entities',
        'label_entity'          => 'Entity',
        'route_index'           => 'homepage',
        'route_edit'            => false,
        'route_create'          => false,
        'templates'             => array(
            'index_table'       => 'CtrlRadBundle:partial:_table.html.twig',
            'form_elements'     => 'CtrlRadBundle:partial:_form_elements.html.twig',
            'form_buttons'      => 'CtrlRadBundle:partial:_form_buttons.html.twig',
            'filter_elements'   => 'CtrlRadBundle:partial:_form_elements.html.twig',
        )
    );

    /**
     * @return AbstractService
     */
    abstract protected function getEntityService();

    protected function configureCrud()
    {
        return $this->options;
    }

    protected function configureIndex()
    {
        return array_merge($this->configureCrud(), array(
            'filter_enabled'    => false,
            'filter_form'       => null,
            'columns'           => array(
                'id' => '#',
            ),
            'actions'           => array(),
        ));
    }

    protected function configureEdit($id = null)
    {
        $entity = null;
        if ($id) {
            $entity = $this->getEntityService()->findOne(array('id' => $id));
        }

        return array_merge($this->configureCrud(), array(
            'form'      => null,
            'entity'    => $entity,
        ));
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

        $paginator = $this->getEntityService()->paginate()->findAll($criteria);

        return $this->render('CtrlRadBundle:crud:index.html.twig', array(
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
            $entity = $this->getEntityService()->findOne(array('id' => $id));
            if (!$entity) {
                $this->addFlash('error', sprintf('Application not found'));
                return $this->redirect($this->generateUrl($options['route_index']));
            }
        }

        if ($request->isMethod('POST')) {
            if ($form->handleRequest($request)->isValid()) {
                $this->getEntityService()->persist($form->getData());

                return $this->redirect($this->generateUrl(
                    $request->get('_route'),
                    $request->get('_route_params')
                ));
            }
        }

        return $this->render('CtrlRadBundle:crud:edit.html.twig', array(
            'entity'        => $entity,
            'form'          => $form->createView(),
            'options'       => $options,
        ));
    }

    protected function createFilterCriteria(FormInterface $form)
    {
        $criteria = array();

        /** @var FormInterface $child */
        foreach ($form as $child) {
            $field = $child->getName();
            switch ($child->getConfig()->getType()->getName()) {
                case 'text':
                    $criteria[$field . ' LIKE :'.$field] = '%' . $child->getData() . '%';
                    break;
                case 'checkbox':
                    if ($child->getData()) {
                        $criteria[$field] = true;
                    }
                    break;
                default:
                    $criteria[$field] = $child->getData();
            }
        }

        return $criteria;
    }
}