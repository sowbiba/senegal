<?php

namespace Senegal\ToolsBundle\Controller;

use Api\Sdk\Model\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Class SecurityController
 */
class SecurityController extends Controller
{
    /**
     * @param Request $request
     *
     * @Template("SenegalToolsBundle:Security:login.html.twig")
     */
    public function loginAction(Request $request)
    {
        $session = $request->getSession();
        $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR, $session->get(SecurityContext::AUTHENTICATION_ERROR));
        
        if (!$request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return array(
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        );
    }
    
//    public function loginCheckAction()
//    {
//        $this->api = $this->get('senegal.api.service');            
//        $resp = $this->api->get("/admin/login-check");
//        echo(json_encode($resp));
//    }
}
