<?php

namespace Api\SdkBundle\Security\Handler;

use Api\Sdk\Bridge\LegacyBridge;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class LoginSuccessHandler
 */
class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{

    /**
     * @param LegacyBridge $bridge
     */
    public function __construct()
    {
    }

    /**
     * @param Request        $request
     * @param TokenInterface $token
     *
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
//        $this->bridge->permissiveTransaction(function () use ($token) {
//            $user = \sfGuardUserPeer::retrieveByPK($token->getUser()->getId());
//            \sfContext::getInstance()->getUser()->signIn($user);
//        });

        return new RedirectResponse($request->getSession()->get('_security.profideo.target_path', '/'));
    }
}
