<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ExceptionController extends Controller
{
    public function showExceptionAction(Request $request)
    {
        return $this->render('default/exception.html.twig');
    }
}
