<?php
// src/UserBundle/Controller/RegistrationController.php

namespace  innoLCL\AllUserBundle\Controller;

use FOS\UserBundle\Controller\RegistrationController as BaseController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;


class RegistrationController extends BaseController
{    
    public function registerAction() 
    {
        
        $request = $this->container->get('request');
        $templating = $this->container->get('templating');
        
        $form = $this->container->get('fos_user.registration.form');
        $formHandler = $this->container->get('fos_user.registration.form.handler');
        $confirmationEnabled = $this->container->getParameter('fos_user.registration.confirmation.enabled');
        
        $process = $formHandler->process($confirmationEnabled);
        if ($process) {
            $user = $form->getData();
            
            $user->setRegisteredAt(new \DateTime());
            $this->container->get('fos_user.user_manager')->updateUser($user);

            $authUser = false;
            if ($confirmationEnabled) {
                $this->container->get('session')->set('fos_user_send_confirmation_email/email', $user->getEmail());
                $route = 'fos_user_registration_check_email';
            } else {
                $authUser = true;
                $route = 'fos_user_registration_confirmed';
            }

            $this->setFlash('fos_user_success', 'registration.flash.user_created');
            $url = $this->container->get('router')->generate($route);
            $response = new RedirectResponse($url);

            if ($authUser) {
                $this->authenticateUser($user, $response);
            }

            if($request->isXmlHttpRequest()) {
                return new JsonResponse( array('message' => 'Ok',
                            'popin' => $templating->render('innoLCLAllUserBundle:Registration:popin_success_raw.html.twig', array('user'=>$user)))
                            ,200);
            }else{
                return $response;
            }
        }

        if($request->isXmlHttpRequest()) {
            return new JsonResponse( array('message' => 'Erreur de validation',
                        'form' => $templating->render('innoLCLAllUserBundle:Registration:registerraw.html.twig',
                        array(
                            'form' => $form->createView(),
                        ))), 400);
        }else{
            return $this->container->get('templating')->renderResponse('innoLCLAllUserBundle:Registration:register.html.'.$this->getEngine(), array(
                'form' => $form->createView(),
            ));
        }
    }
    
    /**
     * Tell the user to check his email provider
     */
    public function checkEmailAction()
    {
        $email = $this->container->get('session')->get('fos_user_send_confirmation_email/email');
        $this->container->get('session')->remove('fos_user_send_confirmation_email/email');
        $user = $this->container->get('fos_user.user_manager')->findUserByEmail($email);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with email "%s" does not exist', $email));
        }
        
        $url = $this->container->get('router')->generate('innolcl_front_homepage');
        $response = new RedirectResponse($url);
        return $response;
        
        //return $this->container->get('templating')->renderResponse('FOSUserBundle:Registration:checkEmail.html.'.$this->getEngine(), array(
         //   'user' => $user,
        //));
        
    }
    
    /**
     * Receive the confirmation token from user email provider, login the user
     */
    public function confirmAction($token)
    {
        $user = $this->container->get('fos_user.user_manager')->findUserByConfirmationToken($token);

        if (null === $user) {
            //throw new NotFoundHttpException(sprintf('The user with confirmation token "%s" does not exist', $token));
            // pas d'utilisateur ayant ce token
            // ou token déjà validé et utilisateur abusant du lien de confirmation pour revenir à chaque fois.
            $route = $this->container->get('router')->generate('innolcl_front_homepage');
            return new RedirectResponse($route);
        }

        $user->setConfirmationToken(null);
        $user->setEnabled(true);
        $user->setLastLogin(new \DateTime());

        $this->container->get('fos_user.user_manager')->updateUser($user);
        $response = new RedirectResponse($this->container->get('router')->generate('fos_user_registration_confirmed'));
        $this->authenticateUser($user, $response);

        return $response;
    }
}
