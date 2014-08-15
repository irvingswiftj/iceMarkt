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
}