<?php

namespace Api\SdkBundle\Security\Handler;

use Api\Sdk\Bridge\LegacyBridge;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class LoginSuccessHandler
 */
class LoginSuccessHandler extends DefaultAuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(HttpUtils $httpUtils, array $options)
    {
        $options = array_merge(array(
            'default_target_path'            => '/admin',
            'login_path'                     => '/admin/login',
        ), $options);

        parent::__construct($httpUtils, $options);
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

        return parent::onAuthenticationSuccess($request, $token);
    }
}
