<?php

/**
 * Description of TaskCommand
 *
 * @author pinacolada
 */
namespace innoLCL\backBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TaskSendStatsReportCommand extends ContainerAwareCommand
{

    private $pathExport = 'web/';
    private $pathFile = 'online/report/';
    private $filename;
    
    protected function configure()
    {
        $this
            ->setName('task:sendStatsReport')
            ->setDescription('Task command that send a full report of all stats')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<comment>Running Task command...</comment>');

        try {
            $em = $this->getContainer()->get('doctrine.orm.entity_manager');
            
            $today = new \DateTime();
            $yesterday = new \DateTime();
            $yesterday->modify('-1 day');
            
            $htmlReport = '';
            $htmlReport.= '<strong>Rapport du '.$yesterday->format('d m Y').'</strong><br>';
            $htmlReport.= '<e>généré le '.$today->format('d m Y').' à '.$today->format('His').'</em><br>';
            
            $htmlReport.= '<br><strong>Résumé de la journée</strong><br>';
            
            $total = $em->getRepository('innoLCL\AllUserBundle\Entity\User')->countNouvellesInscriptions($yesterday);
            $htmlReport.= "Nouvelles inscriptions : ".$total.'<br>';
            
            $total = $em->getRepository('innoLCL\AllUserBundle\Entity\User')->countConnexions($yesterday);
            $htmlReport.= "Connexions : ".$total.'<br>';
            
            $total = $em->getRepository('innoLCL\StatBundle\Entity\Votes')->getTotalVoteJour($yesterday);
            $htmlReport.= "Votes : ".$total.'<br>';
            
            
            $htmlReport.= '<br><strong>Résumé global</strong><br>';
            
            $total = $em->getRepository('innoLCL\AllUserBundle\Entity\User')->countInscriptions();
            $htmlReport.= "Total inscriptions : ".$total.'<br>';
            
            $total = $em->getRepository('innoLCL\StatBundle\Entity\Votes')->getTotalVoteCount();
            $htmlReport.= "Total votes : ".$total.'<br>';
            
            $total = $em->getRepository('innoLCL\StatBundle\Entity\Votes')->getTotalVotantsCount();
            $htmlReport.= "Total votants : ".$total.'<br>';
            
            $stats = $em->getRepository('innoLCL\bothIdeaBundle\Entity\IdeaLaureat')->findBy([], ['nbVotes' => 'DESC']);
            $htmlReport.= "<br><strong>Classement idées : </strong><br>";
            foreach($stats as $stat){
                $htmlReport.= $stat->getTitre()." : ".$stat->getNbVotes().'<br>';
            }
            
            $stats = $em->getRepository('innoLCL\StatBundle\Entity\VideoStat')->findAll();
            $htmlReport.= "<br><strong>Vidéos vues : </strong><br>";
            foreach($stats as $stat){
                $htmlReport.= $stat->getVideoname()." : ".$stat->getCounter().'<br>';
            }
            
            $to = "support_lcl_challenge@freetouch.fr";
            //$to = "julien@freetouch.fr";
            if (!$this->getContainer()->get('mail_to_user')->sendReportStats($to, $htmlReport)) {
                throw $this->createNotFoundException('Unable to send ReportStats mail.');
            }
            
        } catch (\Exception $e) {
            $output->writeln('<error>ERROR</error>'.$e->getMessage());
        }
        
        $output->writeln('<comment>Task done!</comment>');
    }
}
