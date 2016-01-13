<?php

namespace innoLCL\StatBundle\Entity;

/**
 * VotesRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class VotesRepository extends \Doctrine\ORM\EntityRepository
{
  public function findLastVoteByUser($id) {
    $qb = $this->createQueryBuilder('v')
               ->Where('v.user = :user')
               ->setParameter('user', $id)
               ->add('orderBy','v.dateVote DESC')
               ->setMaxResults(1);

    return $qb->getQuery()->getOneOrNullResult();
  }
  public function getTotalVoteCount() {
    $qb = $this->createQueryBuilder('v')
               ->select('count(v.id)');
    return $qb->getQuery()->getSingleScalarResult();
  }
  
    public function getTotalVoteJour($dateReport) 
    {
        $dateReportStart = $dateReport->format('Y-m-d') . ' 00:00:00';
        $dateReportEnd = $dateReport->format('Y-m-d') . ' 23:59:59';
        
        $qb = $this->createQueryBuilder('v');
        $qb
            ->select('COUNT(v)')
            ->where("v.dateVote >= :dateReportStart")
            ->andWhere("v.dateVote <= :dateReportEnd")
            ->setParameter('dateReportStart', $dateReportStart)
            ->setParameter('dateReportEnd', $dateReportEnd);
        
        return $qb->getQuery()->getSingleScalarResult();
    }
    
    public function getTotalVotantsCount() {
        $qb = $this->createQueryBuilder('v')
                   ->select('count(v.id)')
                ->groupBy('v.user');
        return count($qb->getQuery()->getScalarResult());
    }
    
    
}
