<?php

namespace Api\Bundle\AuthenticationBundle\Controller;

use Api\Sdk\Model\User;
use Api\SdkBundle\Security\Encoder\LegacyPasswordEncoder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class AuthController extends Controller
{
    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Returns user informations if the given login/password combination is valid",
     *   statusCodes={
     *         200="Returned when successful",
     *         404="Returned when login/password combination is invalid"
     *  },
     *  requirements={
     *      {
     *          "name"="login",
     *          "dataType"="string",
     *          "description"="user login"
     *      },
     *      {
     *          "name"="password",
     *          "dataType"="string",
     *          "description"="user password"
     *      }
     *  }
     * )
     * @param  Request      $request
     * @return JsonResponse
     */
    public function AdminLogin_CheckAction(Request $request)
    {
        try {
            /** @var User $user */
            $user = $this->get('sdk_user_provider')->loadUserByUsername($request->get('login'));
        } catch (UsernameNotFoundException $e) {
            return new JsonResponse(array($e->getMessage()), Response::HTTP_NOT_FOUND);
        }
        /** @var LegacyPasswordEncoder $encoder */
        $encoder = $this->get("api_password_encoder");

        //return json_encode("TATA");
        return new Response($this->get('jms_serializer')->serialize($user, "json"), Response::HTTP_OK, array('Content-Type' => 'application/json'));
        
        if ($encoder->isPasswordValid($user->getPassword(), $request->get('password'), $user->getSalt())) {
            error_log("AHH");
            return new Response($this->get('jms_serializer')->serialize($user, "json"), Response::HTTP_OK, array('Content-Type' => 'application/json'));
        } else {
            return new JsonResponse(array(), Response::HTTP_NOT_FOUND);
        }
    }
}
