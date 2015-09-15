<?php

namespace Senegal\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Senegal\ApiBundle\Utils\HashGenerator;
use Symfony\Component\ExpressionLanguage\Token;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use FOS\RestBundle\View\View as FOSView;

/**
 * AuthenticationController is a RESTful controller managing user authentication.
 */
class AuthenticationController extends ApiController
{
    /**
     * Checks if the combination of login and password is valid:
     * - if true, it generate a new token, save it in database and returns an array
     * which contains user data.
     *
     * @example
     * [
     *      'email'     => 'email@profideo.com',
     *      'firstname' => 'Firstname',
     *      'id'        => 1,
     *      'name'      => 'NAME',
     *      'roles'     => [
     *          'id'   => 1,
     *          'name' => 'role',
     *      ],
     *      'token'     => 'aRandomlyGeneratedToken',
     *      'username'  => 'username',
     * ]
     *
     * - if false, it returns:
     *      - a "401 Unauthorized" HTTP error if the combination login/password is not valid.
     *      - a "404 Not Found" HTTP error if the username is invalid.
     *
     * @ApiDoc(
     *     section="Authentication",
     *     description="Returns user data if the given login/password combination is valid",
     *     statusCodes={
     *         200="Returned when successful",
     *         401="Returned when username/password combination is invalid",
     *         404="Returned when username is not found"
     *     }
     * )
     *
     * @Rest\Post("/login-check")
     *
     * @Rest\RequestParam(name="environment", default="api", requirements="(back|api)+", strict=false, description="In which environment ?")
     * @Rest\RequestParam(name="serializerGroup", default="authentication", requirements="(authentication)+", strict=false, description="The serializer group")
     *
     * @Rest\RequestParam(name="login", description="User's login")
     * @Rest\RequestParam(name="password", description="User's password")
     *
     * @Rest\View()
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return Response
     */
    public function loginCheckAction(ParamFetcher $paramFetcher)
    {
        try {
            $userManager = $this->get('senegal_user_manager');
            $password = $paramFetcher->get('password');
            $login = $paramFetcher->get('login');

            $user = $userManager->loadUserByUsername($login);

            $encoder = $this->get('senegal_password_encoder');

            if ($encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt())) {
                // If the authentification is successfull, we generate a new token for the user.
                $userManager->generateToken($user);

                $token = new AnonymousToken($user->getToken(), $user);
                $this->get('security.token_storage')->setToken($token);

                return $this->view($user)
                    ->setSerializationContext(
                        SerializationContext::create()
                            ->setGroups(['authentication'])
                    );
            } else {
                return new Response('', Response::HTTP_UNAUTHORIZED);
            }
        } catch (UsernameNotFoundException $e) {
            return new Response('username_not_found', Response::HTTP_NOT_FOUND);
        }
    }
}
