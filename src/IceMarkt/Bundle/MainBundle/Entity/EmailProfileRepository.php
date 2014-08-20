<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 22/07/2014
 * Time: 10:08
 */

namespace IceMarkt\Bundle\MainBundle\Entity;


use Doctrine\ORM\EntityRepository;

class EmailProfileRepository extends EntityRepository
{

    /**
     * Overriding the default find all so that pagination can easily be included
     *
     * @param array|null $orderBy
     * @param Integer|null $limit
     * @param Integer|null $offset
     * @return array
     */
    public function findAll($orderBy = null, $limit = null, $offset = null)
    {
        return $this->findBy(
            array(),
            $orderBy,
            $limit,
            $offset
        );
    }

    /**
     * Method to return an array suitable for being used as choices in a drop down
     *
     * @param array|null $orderBy
     *
     * @return array
     */
    public function getChoicesArray($orderBy = null)
    {
        $choices = $this->findAll();

        $result = array();

        foreach ($choices as $choice) {
            $result[$choice->getId()] = $choice->getName();
        }

        return $result;
    }

    /**
     * Method to get count of enabled rows in email_profiles table
     *
     * TODO: cache this result
     *
     * @return Integer
     */
    public function fetchCount()
    {

        $qb = $this->createQueryBuilder('id')
            ->select('COUNT(id)');

        $count = $qb->getQuery()->getSingleScalarResult();

        return $count;
    }
}