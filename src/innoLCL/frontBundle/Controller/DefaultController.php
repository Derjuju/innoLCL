<?php

namespace innoLCL\frontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DefaultController extends Controller
{
    public function indexAction(Request $request) // /accueil
    {
        $securityContext = $this->get('security.context');
        $currentTime = time();
        $phase2Time = strtotime($this->container->getParameter('phase.phase2'));
        $phase3Time = strtotime($this->container->getParameter('phase.phase3'));
        $phase4Time = strtotime($this->container->getParameter('phase.phase4'));
        $phase4finTime = strtotime($this->container->getParameter('phase.phase4fin'));
        $phase5Time = strtotime($this->container->getParameter('phase.phase5'));

        if($currentTime < $phase2Time) { //PHASE 1
            if($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
                $route = $this->container->get('router')->generate('innolcl_front_landing_proposal');
                return new RedirectResponse($route);
            }
        }
        elseif($currentTime < $phase3Time) { //PHASE 2
            $route = $this->container->get('router')->generate('innolcl_front_landing_selection');
        }
        elseif($currentTime < $phase4Time) { //PHASE 3
            $route = $this->container->get('router')->generate('innolcl_front_landing_laureat');
        }
        elseif($currentTime <= $phase4finTime) { //PHASE 4
            if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
                $route = $this->container->get('router')->generate('innolcl_front_landing_vote');
            }
        }
        elseif($currentTime <= $phase5Time) { //PHASE 4 FIN
            $route = $this->container->get('router')->generate('innolcl_front_landing_vote_end');            
        }
        else { //PHASE 5
            $route = $this->container->get('router')->generate('innolcl_front_landing_results');
        }

        if(isset($route)) {
            return new RedirectResponse($route);
        }

      return $this->render('innoLCLfrontBundle:Default:index.html.twig');
    }
    
    /**
     * Tell the user his account is now confirmed
     */
    public function confirmedAction(Request $request)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        //if (!is_object($user) || !$user instanceof UserInterface) {
        /*if (!is_object($user)) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }*/
        if (is_object($user)) {
            return $this->homeConnected($request,true);
        }else{
            // pas d'utilisateur ayant ce token
            // ou token déjà validé et utilisateur abusant du lien de confirmation pour revenir à chaque fois.
            $route = $this->container->get('router')->generate('innolcl_front_homepage');
            return new RedirectResponse($route);
        }
    }
    
    public function proposalAction(Request $request) // phase 1
    {
        $currentTime = time();
        $phase2Time = strtotime($this->container->getParameter('phase.phase2'));
        if($currentTime > $phase2Time) {
            return $this->indexAction($request);
        }
        return $this->homeConnected($request, false);
    }
    
    private function homeConnected(Request $request, $registerConfirmed)
    {
        $repositoryIdea = $this->getDoctrine()->getManager()->getRepository('innoLCL\bothIdeaBundle\Entity\Idea');
        
        $user = $this->get('security.context')->getToken()->getUser();
        $userId = $user->getId();

        $twig = array('authorid' => $userId,
                      'displayCTA' => 0,
                      'displayForm' => 0,
                      'FormIdeaID' => 0,
                      'idealist' => array()); //valeur par défault pour twig

         $twig['urladmin'] = $this->get('router')->generate('innolcl_moderateur_list_idea',array('page' => 1));
        
        
        //Verification du role => admin ejecté ou limité ? Si les admin ne peuvent pas participer ? ou alors incapacité d'afficher le CTA
        
        if($repositoryIdea->getCountIdeaOfUser($userId) > 0) //Si l'user déjà des idées => affiche la liste d'idée
        {
            $twig['idealist'] = $repositoryIdea->findBy(array('author' => $userId),array('postedon' => 'desc'));
        }
        
        //Defini si le CTA et le formulaire sera affiché / disponible
        if($user->getVideoseenon() != null)
        {
            $twig['displayForm'] = 1;
            //Defini si le form pour soumettre une nouvelle idée est affiché (les cas d'idea 'peut-etre' et 'valider' s'affiche directement grâce au twig)
            if($repositoryIdea->isUserLastIdeaRefused($userId) || $repositoryIdea->getCountIdeaOfUser($userId) == 0) // Si dernière idée réfusé ou pas d'idée déposé par cet user
            {
                $twig['displayCTA'] = 1;
                $twig['FormIdeaID'] = 0;
            }
            else 
            {
                $twig['displayCTA'] = 0;
                $twig['FormIdeaID'] = $repositoryIdea->getLastIdeaIdOfUser($userId);
            }
        }
        
        $twig['registerConfirmed'] = $registerConfirmed;
        
          return $this->render('innoLCLfrontBundle:Default:proposal.html.twig',$twig);
    }
    
    
    
    
    public function SelectionAction(Request $request) // phase 2
    {
		$currentTime = time();
		$phase2Time = strtotime($this->container->getParameter('phase.phase2'));
		$phase3Time = strtotime($this->container->getParameter('phase.phase3'));
		if(!($currentTime > $phase2Time && $currentTime < $phase3Time)) {			
			$route = $this->container->get('router')->generate('innolcl_front_landing_proposal');
			return new RedirectResponse($route);
		}
         return $this->render('innoLCLfrontBundle:Default:selection.html.twig');
    }
    
    public function laureatAction(Request $request) // phase 3
    {
		$currentTime = time();
		$phase3Time = strtotime($this->container->getParameter('phase.phase3'));
		$phase4Time = strtotime($this->container->getParameter('phase.phase4'));
		if(!($currentTime > $phase3Time && $currentTime < $phase4Time)) {			
			$route = $this->container->get('router')->generate('innolcl_front_landing_proposal');
			return new RedirectResponse($route);
		}
         return $this->render('innoLCLfrontBundle:Default:laureats.html.twig');
    }

    public function voteAction(Request $request, \DateTime $dateCurrent = null) // phase 4
    {
        //redirige vers l'accueil si non connecté
        $securityContext = $this->get('security.context');
        if(!($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED'))) {
            $route = $this->container->get('router')->generate('innolcl_front_landing_proposal');
            return new RedirectResponse($route);
        }

        //redirige vers l'accueil si date hors phase
        $currentTime = time();
        $phase4Time = strtotime($this->container->getParameter('phase.phase4'));
        $phase4finTime = strtotime($this->container->getParameter('phase.phase4fin'));
        if(!($currentTime > $phase4Time && $currentTime < $phase4finTime)) {
            $route = $this->container->get('router')->generate('innolcl_front_landing_proposal');
            return new RedirectResponse($route);
        }

        $em = $this->getDoctrine()->getManager();
        $repoIdeaLaureat = $em->getRepository('innoLCL\bothIdeaBundle\Entity\IdeaLaureat');
        $repoVote = $em->getRepository('innoLCL\StatBundle\Entity\Votes');
        $user = $securityContext->getToken()->getUser();

        $unorderedIdeaLaureats = $repoIdeaLaureat->findBy(array(),
                                                        array('nomAuthor' => 'ASC'));
        //Recupère le DateTime initial pour test si non défini la set à la date voulu, (en prod jeudi 14/01/2016 à 9:00)
        $dateInitialVoteOrder = \DateTime::createFromFormat("Y-m-d H:i:s", "2016-01-14 09:00:00");
        if(!$dateCurrent) { $dateCurrent = new \DateTime();}

        //DateTime diff renvoi les jours sans prendre en compte les heures. Pour ne pas modifier les datetime pour s'adapter, on calcule içi le nombre de tranche complete de 24h
        //permet aussi les valeurs negatives, pour afficher correctement la veille et le matin de la release avant 9h00
        $timeElapsed = $dateCurrent->getTimestamp() - $dateInitialVoteOrder->getTimestamp();
        $completeDayElapsed = (int) floor($timeElapsed/86400);

        //Calcul du nb de dimanche dans l'intervalle de temps
        $nbDimanche = intval($completeDayElapsed / 7) + ($dateInitialVoteOrder->format('N') + $completeDayElapsed % 7 >= 7);

        //Suppression des dimanche et ajustement de l'offset à la taille du tableau
        $offsetSplit = ($completeDayElapsed-$nbDimanche) % count($unorderedIdeaLaureats);

        //Découpe la liste en deux et la reconstruit dans l'ordre voulu
        $debutOrderedIdeaLaureats = array_slice($unorderedIdeaLaureats, $offsetSplit);
        $finOrderedIdeaLaureats = array_slice($unorderedIdeaLaureats, 0, $offsetSplit);
        $orderedIdeaLaureats = array_merge($debutOrderedIdeaLaureats,$finOrderedIdeaLaureats);

        $twig['IdeaLaureats'] = $orderedIdeaLaureats;

        $lastVote = $repoVote->findLastVoteByUser($user);
        //Si déjà voté pour cet user et vote date d'aujourd'hui
        if($lastVote && strtotime(date("Y-m-d")) == $lastVote->getDateVote()->getTimestamp()) {
            $twig['todayvote'] = $lastVote->getIdeaLaureat()->getId();
        }
        else {
            $twig['todayvote'] = 0;
        }

        return $this->render('innoLCLfrontBundle:Default:phase4.html.twig',$twig);
    }

    public function votetesteurAction(Request $request, $Y,$M,$d,$h,$m,$s) {
        $dateInitialVoteOrder = \DateTime::createFromFormat("Y-m-d H:i:s", $Y.'-'.$M.'-'.$d.' '.$h.':'.$m.':'.$s);
        return $this->voteAction($request, $dateInitialVoteOrder);
    }

    
    public function endvoteAction(Request $request){
        return $this->render('innoLCLfrontBundle:Default:phase4fin.html.twig');
    }
    
    public function  resultsAction(Request $request) // phase 5
    {
        //Renvoi si date ne correspond pas à la phase
        $phase5Time = strtotime($this->container->getParameter('phase.phase5'));
        if(!(time() > $phase5Time)) {
            $route = $this->container->get('router')->generate('innolcl_front_landing_proposal');
            return new RedirectResponse($route);
        }

        //Recuperation de la liste des idées par Nb de votes descendant
        $em = $this->getDoctrine()->getManager();
        $repoIdeaLaureat = $em->getRepository('innoLCL\bothIdeaBundle\Entity\IdeaLaureat');
        $twig['IdeaLaureats'] = $repoIdeaLaureat->findBy(array(),
                                                        array('nbVotes' => 'DESC',
                                                        'prenomAuthor' => 'ASC'));

        return $this->render('innoLCLfrontBundle:Default:results.html.twig',$twig);
    }
}
