<?php

namespace Ctrl\RadBundle\Controller;

use Ctrl\RadBundle\Entity\User;
use Ctrl\RadBundle\EntityService\AbstractService;
use Ctrl\RadBundle\Form\UserEditType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @package Ctrl\RadBundle\Controller
 * @Route("/admin/user")
 */
class UserController extends CrudController
{
    /**
     * @return AbstractService
     */
    protected function getEntityService()
    {
        return $this->get('ctrl_rad.entity.user');
    }

    /**
     * @param array $options
     * @return array
     */
    protected function configureCrud(array $options = array())
    {
        return parent::configureCrud(array(
            'label_entity'          => 'User',
            'route_prefix'          => 'ctrl_rad_',
            'templates'             => array(
                'filter_elements'   => 'CtrlRadBundle:user:_filter.html.twig',
            )
        ));
    }

    /**
     * @param array $options
     * @return array
     */
    protected function configureIndex(array $options = array())
    {
        return parent::configureIndex(array(
            'filter_form'       => $this->createFormBuilder(null, array('csrf_protection' => false))
                ->add('username')
                ->add('email')
                ->add('enabled', 'checkbox')
                ->add('locked', 'checkbox')
                ->getForm(),
            'columns'           => array(
                'id'        => '#',
                'username'  => 'Username',
                'email'     => 'Email',
                'enabled'   => 'Enabled',
                'locked'    => 'Locked',
            ),
            'actions'           => array(
                $this->configureButton(array(
                    'label'         => 'Edit',
                    'icon'          => 'edit',
                    'class'         => 'primary',
                    'route_name'    => 'ctrl_rad_user_edit'
                )),
            ),
        ));
    }

    protected function configureEdit($id = null, array $options = array())
    {
        $config = parent::configureEdit($id);
        $config['form'] = $this->createForm(new UserEditType(), $config['entity']);

        return $config;
    }

    /**
     * @Route("", name="ctrl_rad_user_index")
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        return parent::indexAction($request);
    }

    /**
     * @Route("/edit/{id}", name="ctrl_rad_user_edit")
     * @param Request $request
     * @param int|null $id
     * @return Response
     */
    public function editAction(Request $request, $id = null)
    {
        return parent::editAction($request, $id);
    }
}