<?php

namespace Senegal\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Senegal\ApiBundle\Utils\HashGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class AuthenticationController extends ApiController
{
    /**
     * @ApiDoc(
     *     section="Authentication",
     *     description="Returns user informations if the given login/password combination is valid",
     *     statusCodes={
     *         200="Returned when successful",
     *         401="Returned when login/password combination is invalid",
     *         404="Returned when login is not found"
     *     }
     * )
     *
     * @Rest\Post("/login-check")
     *
     * @Rest\RequestParam(name="login", strict=true, description="User's login")
     * @Rest\RequestParam(name="password", strict=true, description="User's password")
     *
     * @Rest\View(serializerGroups={"authentication"})
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return Response
     */
    public function loginCheckAction(ParamFetcher $paramFetcher)
    {
        try {
            $user = $this->get('senegal_user_manager')->loadUserByUsername($paramFetcher->get('login'));

            $encoder = $this->get('senegal_password_encoder');

            if ($encoder->isPasswordValid($user->getPassword(), $paramFetcher->get('password'), $user->getSalt())) {
                // if the authentification is successfull, we generate a new token for the user
//                $user->setToken(HashGenerator::generate());
//                $this->getDoctrine()->getManager()->flush();

                return $user;
            } else {
                return new Response('', Response::HTTP_UNAUTHORIZED);
            }
        } catch (UsernameNotFoundException $e) {
            return new Response('username_not_found', Response::HTTP_NOT_FOUND);
        }
    }
}
