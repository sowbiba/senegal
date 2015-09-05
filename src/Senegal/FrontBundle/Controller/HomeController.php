<?php

namespace Senegal\FrontBundle\Controller;


use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class HomeController extends FrontController
{
    /**
     * @Route("/", name="senegal_front_home")
     *
     * @param  Request  $request
     * @return Response
     */
    public function homeAction(Request $request)
    {
        return $this->render("SenegalFrontBundle:Home:index.html.twig", [

        ]);
    }

} 