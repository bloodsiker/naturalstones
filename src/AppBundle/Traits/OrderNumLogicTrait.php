<?php
/**
 * Created by PhpStorm.
 * User: pretorian42
 * Date: 07.10.16
 * Time: 16:12
 */

namespace AppBundle\Traits;


use Doctrine\ORM\NoResultException;

/**
 * Trait OrderNumLogicTrait
 */
trait OrderNumLogicTrait
{
    private $reorderQuery = 'set @i:= -99;
        UPDATE %s 
        SET order_num = ( @i := @i + 100 )
        order by order_num';

    /**
     * @param $property
     * @param string $order
     * @return mixed
     */
    public function getHighestPropertyByOrder($property, $order = 'DESC')
    {
        $qb = $this->createQueryBuilder('object');
        $qb
            ->select('object.' . $property)
            ->orderBy('object.' . $property, $order)
            ->setMaxResults(1);
        try {
            return $qb->getQuery()->getSingleScalarResult();
        } catch (NoResultException $e){
            return 0;
        }
    }

    /**
     * @param $property
     * @param string $order
     * @return mixed
     */
    public function getHighestPropertyByPosition($property, $except, $order = 'DESC')
    {
        $qb = $this->createQueryBuilder('object');
        $qb
            ->select('object')
            ->andWhere(
                'object.' . $property . ' '
                . ($order != 'DESC' ? '>=' : '<=')
                . ' :property'
            )->setParameter('property', $except->getOrderNum())
            ->andWhere('object.id != :except')->setParameter('except', $except)
            ->orderBy('object.' . $property, $order)
            ->setMaxResults(1);;

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @return mixed
     */
    public function getDuplicationList()
    {
        $qb = $this->createQueryBuilder('object');
        $qb
            ->select('object')
            ->andHaving('count(object.orderNum) > 1')
            ->groupBy('object.orderNum')
            ;
        return $qb->getQuery()->getResult();
    }

    /**
     *
     */
    public function reCountOrderNum()
    {
        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare(sprintf($this->reorderQuery, $this->getClassMetadata()->getTableName()));
        $stmt->execute();
    }
}