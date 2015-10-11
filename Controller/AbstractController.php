<?php

namespace Ctrl\RadBundle\Controller;

use Ctrl\Common\EntityService\ServiceProviderInterface;
use Ctrl\Common\Traits\SymfonyPaginationRequestReader;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

abstract class AbstractController extends Controller implements ServiceProviderInterface
{
    use SymfonyPaginationRequestReader;
}
