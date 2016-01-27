<?php

namespace Ctrl\RadBundle\Crud\Action;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeleteAction extends AbstractAction
{
    public function execute(Request $request, $id = null, array $context = array())
    {
        if ($id) {
            throw new \InvalidArgumentException('$id parameter is required');
        }

        $routes = $this->config->getRoutes();

        if (!isset($routes['delete']) || $routes['delete'] === false) {
            throw new NotFoundHttpException('CRUD route disabled');
        }

        $this->getEntityService()->remove($this->config->getActionConfig()['entity']);

        return new RedirectResponse($this->router->generate($routes['index']));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'entity_id'                 => null,
            'entity'                    => null,
        ]);
    }
}
