<?php

namespace Ctrl\RadBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('email')
            ->add('enabled', CheckboxType::class)
            ->add('locked')
            ->add('expired')
            ->add('credentialsExpired')
            ->add('roles', ChoiceType::class, array(
                'choices' => $options['role_choices'],
                'multiple' => true,
                'required' => true,
                'attr' => array('class' => 'select2'),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'    => 'Ctrl\\RadBundle\\Entity\\User',
            'role_choices'  => [
                'ROLE_USER'         => 'USER',
                'ROLE_ADMIN'        => 'ADMIN',
                'ROLE_SUPER_ADMIN'  => 'SUPER_ADMIN',
            ]
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
