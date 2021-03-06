<?php
/**
 * @copyright  Copyright (c) 2009-2014 Steven TITREN - www.webaki.com
 * @package    Webaki\UserBundle\Redirection
 * @author     Steven Titren <contact@webaki.com>
 */

namespace innoLCL\AllUserBundle\Redirection;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

class AfterLogoutRedirection implements LogoutSuccessHandlerInterface
{
    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    /**
     * @var \Symfony\Component\Security\Core\SecurityContextInterface
     */
    private $security;

    /**
     * @param SecurityContextInterface $security
     */
    public function __construct(RouterInterface $router, SecurityContextInterface $security)
    {
        $this->router = $router;
        $this->security = $security;
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function onLogoutSuccess(Request $request)
    {
		//definition d'une route par défaut pour supprimer les else
		$response = new RedirectResponse($this->router->generate('innolcl_front_homepage'));
		
		//GetToken retourne null si inexistant
		$token = $this->security->getToken();
		if($token !== null) {
			$user = $this->security->getToken()->getUser();
			if($user){
				// Get list of roles for current user
				$roles = $user->getRoles();
				//Si admin retourne a l'auth admin
				if (in_array('ROLE_MODERATEUR', $roles, true) || in_array('ROLE_VALIDATEUR', $roles, true) || in_array('ROLE_LECTEUR', $roles, true) || in_array('ROLE_SELECTIONNEUR', $roles, true) || in_array('ROLE_STATS', $roles, true))
				{
					$response = new RedirectResponse($this->router->generate('fos_admin_user_security_login'));
				}
			}
		}
        return $response;
    }
} 
