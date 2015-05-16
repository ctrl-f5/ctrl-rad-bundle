<?php

namespace Ctrl\RadBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('enabled')
            ->add('locked')
            ->add('expired')
            ->add('credentialsExpired')
            ->add('roles', 'choice', array(
                'choices' => array(
                    'ROLE_USER'         => 'ROLE_USER',
                    'ROLE_ADMIN'        => 'ROLE_ADMIN',
                    'ROLE_SUPER_ADMIN'  => 'ROLE_SUPER_ADMIN',
                ),
                'multiple' => true,
                'required' => true,
                'attr' => array('class' => 'select2'),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ctrl\\RadBundle\\Entity\\User',
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'ctrl_rad_user';
    }
}