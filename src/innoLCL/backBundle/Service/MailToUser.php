<?php

namespace innoLCL\backBundle\Service;

use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Routing\RouterInterface;
use Assetic\Exception\Exception;


class MailToUser {

    protected $mailer;
    protected $router;
    protected $templating;
    protected $app_front_url;
    protected $kernel;
    private $from = "support_lcl_challenge@freetouch.fr";
    private $reply = "support_lcl_challenge@freetouch.fr";
    private $name = "Challenge de l'innovation LCL";

    public function __construct($mailer, EngineInterface $templating, RouterInterface $router, $app_front_url, $kernel) {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->templating = $templating;
        $this->app_front_url = $app_front_url;
        $this->kernel = $kernel;
    }
    
    public function sendEmailIdeeFront($to, $typeMail){
        if($typeMail == "improveIdea"){
            return $this->sendIdeaImproved($to);
        }elseif($typeMail == "modifyIdea"){
            return $this->sendIdeaModified($to);
        }else{      
            return $this->sendIdeaNew($to);
        }
    }
       
    public function sendEmailValider($to){
        $view = null;
        $view = $this->templating->render('innoLCLbackBundle:Mailing:valider.html.twig', array());
        if (!$view)
            return false;
        
        // sujet
        $subject = "[Challenge de l'innovation LCL] idée validée";
        
        return $this->sendMail($subject, $view, $to);
    }
    
    public function sendEmailPeutEtre($to){
        $view = null;
        $view = $this->templating->render('innoLCLbackBundle:Mailing:peut-etre.html.twig', array());
        if (!$view)
            return false;
        
        // sujet
        $subject = "[Challenge de l'innovation LCL] idée à améliorer";
        
        return $this->sendMail($subject, $view, $to);
    }
    
    public function sendEmailRefuser($to, $motif){
        $view = null;
        $view = $this->templating->render('innoLCLbackBundle:Mailing:refuser.html.twig', array());
        if (!$view)
            return false;
        
        // sujet
        $subject = "[Challenge de l'innovation LCL] idée refusée";
        
        // variables dynamiques
        $view = str_replace('#MOTIF#',$motif, $view);
        
        return $this->sendMail($subject, $view, $to);
    }
    
    public function sendReportResetPassword($to, $htmlContent){
        $view = null;
        $view = $this->templating->render('innoLCLbackBundle:Mailing:reportResetPassword.html.twig', array());
        if (!$view)
            return false;
        
        // sujet
        $subject = "[Challenge de l'innovation LCL] Demande reset Mot de passe"; 
        
        // variables dynamiques
        $view = str_replace('#MESSAGE#',$htmlContent, $view);
        
        return $this->sendMail($subject, $view, $to);
    }
    

    private function sendMail($subject, $view, $to){
                
        $view = $this->createOnlineVersion($view);
        
        // pour utiliser la fonction php mail à la place du smtp
        //$transport = \Swift_MailTransport::newInstance();
        //$this->mailer = \Swift_Mailer::newInstance($transport);
        
        $mail = \Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom($this->from, $this->name)
                ->setBody($view)
                ->setReplyTo($this->reply, $this->name)
                ->setContentType('text/html');
        
        try {
            $mail->setTo($to);
            $this->mailer->send($mail);
        } catch (Exception $exc) {
            return false;
        }
        
        // on double les envois
        
        // on substitue lcl.fr par lcl.com pour les adresses concernées afin de passer par les boites externes.                
        if(strpos($to, '@lcl.fr') !== false){
            $to = str_replace('@lcl.fr', '@lcl.com', $to);       
            try {
                $mail->setTo($to);
                $this->mailer->send($mail);
            } catch (Exception $exc) {
                return false;
            }
        }

        return true;
    }
    
    
    
    
    /* PRIVATE */
    
    private function sendIdeaImproved($to){        
        $view = null;
        $view = $this->templating->render('innoLCLbackBundle:Mailing:front_ameliorer.html.twig', array());
        if (!$view)
            return false;
        
        // sujet
        $subject = "[Challenge de l'innovation LCL] idée modifiée";
        
        return $this->sendMail($subject, $view, $to);
    }
    
    private function sendIdeaModified($to){        
        $view = null;
        $view = $this->templating->render('innoLCLbackBundle:Mailing:front_modifier.html.twig', array());
        if (!$view)
            return false;
        
        // sujet
        $subject = "[Challenge de l'innovation LCL] idée modifiée";
        
        return $this->sendMail($subject, $view, $to);
    }
    
    private function sendIdeaNew($to){        
        $view = null;        
        $view = $this->templating->render('innoLCLbackBundle:Mailing:front_nouveau.html.twig', array());
        if (!$view)
            return false;
        
        // sujet
        $subject = "[Challenge de l'innovation LCL] idée enregistrée";
        
        return $this->sendMail($subject, $view, $to);
    }
    
    private function createOnlineVersion($view){
        
        $filename = md5(uniqid(null, true).date("YmdHis"));
        $filenameWithExt = $filename.".dat";
        
        // variables dynamiques
        $lien_version_online = $this->app_front_url.$this->router->generate('front_online_version', array('type' => 'mail', 'hash' => $filename));
        $newView = str_replace('#LIEN_ONLINE#',$lien_version_online, $view);        
        
        // traitement du fichier justificatif
        $dir=$this->kernel->getRootDir()."/../web/".$this->kernel->getContainer()->getParameter('online_mail_dir');

        if (!is_dir($dir)) { mkdir($dir); }
        
        file_put_contents($dir.$filenameWithExt, $newView);
        
        return $newView;
    }
}
