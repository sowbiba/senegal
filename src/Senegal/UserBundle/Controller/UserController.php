<?php

namespace Senegal\UserBundle\Controller;

use Api\SdkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Request;
//use Api\SdkBundle\Entity\User;
use Api\Sdk\Model\User;

class UserController extends Controller
{
    public function indexAction()
    {
        return $this->render('SenegalUserBundle:User:documents.html.twig', array('documents' => array()));
    }
    
    public function editAction(Request $request, $id)
    {
        $user = $this->getSdk("user")->getById((int)$id);
        $oldPassword = $user->getPassword();
        
        $form     = $this->get('form.factory')->createNamed('', 'user', $user, array(
            'validation_groups' => array('Default', 'create')
        ));

        if ('POST' == $request->getMethod()) {
             $form->handleRequest($request);

             if ($form->isValid()) {
                 $newPassword = $user->getPassword();
                 
                 if($oldPassword != $newPassword) {
                    $encoderFactory = $this->get('security.encoder_factory');
                    $passwordEncoder = $encoderFactory->getEncoder($user);
                    $password = $passwordEncoder->encodePassword($newPassword, $user->getSalt());
                    $user->setPassword($password);
                 }
                 
                 // Check integrity and save user's data
                $isValid = $this->getSdk("user")->updateValues($user);

                if ($isValid) {
                    $this->get('session')->getFlashBag()->add('success', sprintf('Les modifications de l\'utilisateur %s ont bien été sauvegardées.', $user->getFirstname()));
                    return $this->redirect($this->generateUrl('senegal_user_edit', array('id' => $user->getId())));
                }
                
             }
        }
        
        return $this->render('SenegalUserBundle:User:edit.html.twig', 
                array('form' => $form->createView())
                );
    }
    
    public function deleteAction($id)
    {
        $api = $this->get('senegal.api.service');
        $user = json_decode($api->get("/user/" . $id));
        return $this->render('SenegalUserBundle:User:edit.html.twig', array('user' => $user));
    }
    
    public function usersAction()
    {
        //$api = $this->get('senegal.api.service');
        //$users = json_decode($api->get("/all/users"));
        $users = $this->getSdk("user")->getAllUsers();
        return $this->render('SenegalUserBundle:User:users.html.twig', array('users' => $users));
    }
    
    private function convertUserToArray($user)
    {
        $userArray = array();

        if(is_object($user)) {
            $userArray["id"]        = isset($user->id) ? $user->id : null;
            $userArray["username"]  = isset($user->username) ? $user->username : null;
            $userArray["roles"]     = isset($user->roles) ? $user->roles : null;
            $userArray["firstname"] = isset($user->firstname) ? $user->firstname : null;
            $userArray["lastname"]  = isset($user->lastname) ? $user->lastname : null;
            $userArray["email"]     = isset($user->email) ? $user->email : null;
            $userArray["salt"]      = isset($user->salt) ? $user->salt : null;
            $userArray["password"]  = isset($user->password) ? $user->password : null;
            $userArray["active"]    = isset($user->active) ? $user->active : false;
        }

        return $userArray;
    }
}
