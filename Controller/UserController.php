<?php

namespace Ctrl\RadBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Ctrl\Common\EntityService\DoctrineEntityServiceProviderInterface;
use Ctrl\Common\EntityService\ServiceInterface;
use Ctrl\Common\Traits\SymfonyPaginationRequestReader;
use Ctrl\RadBundle\Crud\ConfigBuilder;
use Ctrl\RadBundle\Crud\Crud;
use Ctrl\RadBundle\Form\UserEditType;

/**
 * @package Ctrl\RadBundle\Controller
 * @Route("/admin/user")
 */
class UserController extends Controller implements DoctrineEntityServiceProviderInterface
{
    use SymfonyPaginationRequestReader;
    use Crud;

    /**
     * @return ServiceInterface
     */
    public function getEntityService()
    {
        return $this->get('ctrl_rad.entity.user');
    }

    public function configureCrudBuilder(ConfigBuilder $builder)
    {
        $builder
            ->setEntityService($this->getEntityService())
            ->setEntityLabel('User')
            ->setRoutePrefix('ctrl_rad_')
        ;
        return $builder;
    }

    /**
     * @Route("", name="ctrl_rad_user_index")
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $builder = $this->getCrudConfigBuilder()
            ->setTemplate('filter_elements', 'CtrlRadBundle:user:_filter.html.twig')
            ->setFilterForm($this->createFormBuilder(null, array('csrf_protection' => false))
                ->add('username')
                ->add('email')
                ->add('enabled', 'checkbox')
                ->add('locked', 'checkbox')
                ->getForm())
            ->setColumns(array(
                'id'        => '#',
                'username'  => 'Username',
                'email'     => 'Email',
                'enabled'   => 'Enabled',
                'locked'    => 'Locked',
            ))
            ->setActions(array(
                $this->configureButton(array(
                    'label'         => 'Edit',
                    'icon'          => 'edit',
                    'class'         => 'primary',
                    'route_name'    => 'ctrl_rad_user_edit'
                )),
            ))
        ;

        return $this->buildCrudIndex($builder)->execute($request);
    }

    /**
     * @Route("/edit/{id}", name="ctrl_rad_user_edit")
     * @Route("/edit/{id}", name="ctrl_rad_user_create")
     * @param Request $request
     * @param int|null $id
     * @param array|null $options
     * @return Response
     */
    public function editAction(Request $request, $id = null, $options = array())
    {
        $builder = $this->getCrudConfigBuilder();

        $builder
            ->setEntityId($id)
            ->setForm($this->createForm(new UserEditType(), $builder->getEntity()))
        ;

        return $this->buildCrudEdit($builder)->execute($request);
    }
}
