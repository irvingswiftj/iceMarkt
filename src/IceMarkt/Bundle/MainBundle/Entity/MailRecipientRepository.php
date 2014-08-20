<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 22/07/2014
 * Time: 10:08
 */

namespace IceMarkt\Bundle\MainBundle\Entity;


use Doctrine\ORM\EntityRepository;

class MailRecipientRepository extends EntityRepository
{
    public function bulkAdd()
    {

    }


    /**
     * Overriding the default find all so that disable MailRecipients are excluded
     *
     * @param array|null $orderBy
     * @param Integer|null $limit
     * @param Integer|null $offset
     * @return array
     */
    public function findAll($orderBy = null, $limit = null, $offset = null)
    {
        return $this->findBy(
            array(
                'enabled' => true
            ),
            $orderBy,
            $limit,
            $offset
        );
    }

    /**
     * Method to get count of enabled rows in recipients table
     *
     * TODO: cache this result
     *
     * @return Integer
     */
    public function fetchCount()
    {

        $qb = $this->createQueryBuilder('id')
        ->select('COUNT(id)')
        ->where('id.enabled = :enabled')
        ->setParameter('enabled', true);

        $count = $qb->getQuery()->getSingleScalarResult();

        return $count;
    }
}