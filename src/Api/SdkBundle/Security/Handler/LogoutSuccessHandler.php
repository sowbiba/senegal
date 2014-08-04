<?php

namespace Api\SdkBundle\Security\Handler;

use Api\Sdk\Bridge\LegacyBridge;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

/**
 * Class LogoutSuccessHandler
 */
class LogoutSuccessHandler implements LogoutSuccessHandlerInterface
{
    protected $bridge;

    /**
     * @param LegacyBridge $bridge
     */
    public function __construct(LegacyBridge $bridge)
    {
        $this->bridge = $bridge;
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function onLogoutSuccess(Request $request)
    {
        $this->bridge->permissiveTransaction(function () {
            \sfContext::getInstance()->getUser()->signOut();
        });

        return new RedirectResponse($request->getSession()->get('_security.profideo.target_path', '/'));
    }
}
