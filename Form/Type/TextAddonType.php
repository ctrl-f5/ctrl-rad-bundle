<?php

namespace Ctrl\RadBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TextAddonType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'addon_prefix' => false,
            'addon_suffix' => false,
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $view->vars['addon_prefix'] = $options['addon_prefix'];
        $view->vars['addon_suffix'] = $options['addon_suffix'];
    }

    public function getParent()
    {
        return TextType::class;
    }
}
