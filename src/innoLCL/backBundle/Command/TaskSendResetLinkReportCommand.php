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

class TaskSendResetLinkReportCommand extends ContainerAwareCommand
{

    private $pathExport = 'web/';
    private $pathFile = 'online/report/';
    private $filename;
    
    protected function configure()
    {
        $this
            ->setName('task:sendResetLinkReport')
            ->setDescription('Task command that send a full report of all reset link')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<comment>Running Task command...</comment>');

        try {
            $em = $this->getContainer()->get('doctrine.orm.entity_manager');
            
            $users = $em->getRepository('innoLCL\AllUserBundle\Entity\User')->getAllWithResetToken();
            
            if(count($users)>0){
            
                $this->pathExport .= $this->pathFile;
                if(!is_dir($this->pathExport)){
                    mkdir($this->pathExport, 0777, true);
                }
                $today = new \DateTime();
                $this->filename = $today->format('Ymd-His').'.log';
                $this->pathExport = $this->pathExport.$this->filename;
                if(file_exists($this->pathExport)){
                    unlink($this->pathExport);
                }

                $htmlReport = '';

                if (($handle = fopen($this->pathExport, "w")) != FALSE) { 

                    foreach($users as $user){

                        $lien = 'https://challengedelinnovation-10ans.lcl.fr/resetting/reset/'.$user->getConfirmationToken();

                        $newLine = $user->getEmail()."\n".$lien."\n\n";

                        $htmlReport.=$user->getLastname()." ".$user->getFirstname()."<br>".$user->getEmail()."<br><a href='".$lien."'>".$lien."</a><br><br>";

                        fwrite($handle,$newLine);
                    }

                    fclose($handle);

                    $to = "support_lcl_challenge@freetouch.fr";
                    //$to = "julien@freetouch.fr";
                    if (!$this->getContainer()->get('mail_to_user')->sendReportResetPassword($to, $htmlReport)) {
                        throw $this->createNotFoundException('Unable to send ReportResetPassword mail.');
                    }

                }else{
                    $this->output->writeln('<error>Creation '.$this->pathFile.' impossible</error>'); 
                }

            }else{
                $this->output->writeln('<info>aucune demande</info>'); 
            }
            
        } catch (\Exception $e) {
            $output->writeln('<error>ERROR</error>'.$e->getMessage());
        }
        
        $output->writeln('<comment>Task done!</comment>');
    }
}
