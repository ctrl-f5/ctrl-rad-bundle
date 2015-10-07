<?php

namespace Ctrl\RadBundle\Controller;

use Ctrl\RadBundle\Crud\Action\EditAction;
use Ctrl\RadBundle\Crud\Action\IndexAction;
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
            ->setRoute('create', 'ctrl_rad_user_edit')
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
        $builder = $this->getCrudConfigBuilder(IndexAction::class);
        $builder
            ->setFilterForm($this->createFormBuilder(null, array('csrf_protection' => false))
                ->add('username')
                ->add('email')
                ->add('enabled', 'checkbox')
                ->add('locked', 'checkbox')
                ->getForm()
            )
                ->setTable(
                    $builder->createTable()
                        ->setColumns(array(
                            'id'        => '#',
                            'username'  => 'Username',
                            'email'     => 'Email',
                            'enabled'   => 'Enabled',
                            'locked'    => 'Locked',
                        ))->addAction(array(
                            'label'         => 'Edit',
                            'icon'          => 'edit',
                            'class'         => 'primary',
                            'route'         => 'ctrl_rad_user_edit',
                            'route_params'  => function ($data) { return ['id' => $data->getId()]; },
                        ))
                );

        return $this->buildCrud($builder)->execute($request);
    }

    /**
     * @Route("/edit/{id}", name="ctrl_rad_user_edit")
     * @param Request $request
     * @param int|null $id
     * @return Response
     */
    public function editAction(Request $request, $id = null)
    {
        $builder = $this->getCrudConfigBuilder(EditAction::class);

        $builder
            ->setEntityId($id)
            ->setForm($this->createForm(new UserEditType(), $builder->getEntity(), [
                'role_choices' => array_merge([
                    'ROLE_USER'             => 'USER',
                    'ROLE_ADMIN'            => 'ADMIN',
                    'ROLE_SUPER_ADMIN'      => 'SUPER_ADMIN',
                ], $this->get('doctrine')->getRepository('CtrlRadBundle:User')->getKnownRoles(true))
            ]))
        ;

        return $this->buildCrud($builder)->execute($request);
    }
}
