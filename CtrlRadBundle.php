<?php

namespace Ctrl\RadBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class CtrlRadBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
