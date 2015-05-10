<?php

namespace Ctrl\RadBundle\Controller;

use Ctrl\RadBundle\EntityService\AbstractService;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class CrudController extends AbstractController
{
    protected $options = array(
        'label_entities'    => 'Applications',
        'label_entity'      => 'Application',
        'route_index'       => 'homepage',
    );

    /**
     * @return AbstractService
     */
    abstract protected function getEntityService();

    protected function configureIndex()
    {
        return array_merge($this->options, array(
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

        return array_merge($this->options, array(
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
        $filterActive   = false;
        $criteria       = array();
        $form           = null;
        $formView       = null;

        if ($options['filter_enabled']) {
            /** @var FormInterface $form */
            $form = $options['filter_form'];

            if ($request->query->has($form->getName())) {
                $filterActive = true;
                $form->setData($request->query->getIterator()[$form->getName()]);
                $criteria = $form->getData();
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
                    $request->get('_routeParams')
                ));
            }
        }

        return $this->render('CtrlRadBundle:crud:edit.html.twig', array(
            'entity'        => $entity,
            'form'          => $form->createView(),
            'options'       => $options,
        ));
    }
}