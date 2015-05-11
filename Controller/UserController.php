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

    protected function configureCrud()
    {
        $config = parent::configureCrud();
        return array_merge(
            $config,
            array(
                'label_entities'    => 'Users',
                'label_entity'      => 'User',
                'route_index'       => 'ctrl_rad_user_index',
                'route_edit'        => 'ctrl_rad_user_edit',
                'route_create'      => 'ctrl_rad_user_edit',
                'templates'             => array_merge($config['templates'], array(
                    'filter_elements'   => 'CtrlRadBundle:user:_form_elements.html.twig',
                ))
            )
        );
    }

    /**
     * @return array
     */
    protected function configureIndex()
    {
        return array_merge(parent::configureIndex(), array(
            'filter_enabled'    => true,
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
                array(
                    'label'     => 'Edit',
                    'icon'      => 'edit',
                    'class'     => 'primary',
                    'route'     => function (User $user) {
                        return $this->generateUrl('ctrl_rad_user_edit', array('id' => $user->getId()));
                    }
                ),
            ),
        ));
    }

    protected function configureEdit($id = null)
    {
        $config = parent::configureEdit($id);

        return array_merge($config, array(
            'form' => $this->createForm(new UserEditType(), $config['entity'])
        ));
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