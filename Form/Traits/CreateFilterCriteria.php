<?php

namespace Ctrl\RadBundle\Form\Traits;

use Symfony\Component\Form\FormInterface;

trait CreateFilterCriteria
{
    /**
     * @param FormInterface $form
     * @return array
     */
    protected function createFilterCriteria(FormInterface $form)
    {
        $criteria = array();

        /** @var FormInterface $child */
        foreach ($form as $child) {
            $field = $child->getName();
            $fieldPath = str_replace('_', '.', $field);
            switch ($child->getConfig()->getType()->getName()) {
                case 'text':
                    if ($child->getData()) {
                        $criteria[$fieldPath . ' LIKE :' . $field] = '%' . $child->getData() . '%';
                    }
                    break;
                case 'checkbox':
                    if ($child->getData()) {
                        $criteria[$fieldPath] = true;
                    }
                    break;
                default:
                    if ($child->getData()) {
                        $criteria[$fieldPath] = $child->getData();
                    }
            }
        }

        return $criteria;
    }
}
