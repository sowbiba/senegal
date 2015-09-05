<?php

namespace Senegal\FrontBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/account")
 */
class AccountController  extends FrontController
{
    /**
     * @Route("/create", name="senegal_front_create_account")
     *
     * @param  Request  $request
     * @return Response
     */
    public function createAction(Request $request)
    {
        $form = $this->get('senegal.front.account_creation.form');

        return $this->render("SenegalFrontBundle:Account:create.html.twig", [
            'form' => $form->createView()
        ]);
    }

} 