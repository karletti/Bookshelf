<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class BookRepository extends EntityRepository
{
    public function SQLQuery()
    {
        $sql = '
            SELECT b.id, b.name, COUNT(ba.author_id) as author_count
            FROM book as b
            INNER JOIN book_author as ba ON b.id = ba.book_id
            GROUP BY b.id
            HAVING COUNT(ba.author_id) > 1
        ';

        $conn = $this->getEntityManager()->getConnection();

        $query = $conn->prepare($sql);

        $query->execute();

        return $query->fetchAll();
    }

    public function DQLQuery()
    {
        $query =$this->getEntityManager()->createQuery('
            SELECT b.id, b.name, count(ba.id) as author_count
            FROM AppBundle\Entity\Book b
            JOIN b.authors ba
            GROUP BY b.id
            HAVING COUNT(ba.id) > 1
        ');

        return $query->execute();
    }

    public function DoctrineQuery()
    {
        $qb = $this->createQueryBuilder('b');

        $query = $qb->select(array('b.id', 'b.name', 'count(ba.id) as author_count'))
            ->join('b.authors', 'ba')
            ->groupBy('b.id')
            ->having($qb->expr()->gte($qb->expr()->count('ba.id'), 2))
        ->getQuery();

        return $query->execute();
    }

    public function findByFilter($data){

        $qb = $this->createQueryBuilder('b');

        foreach ($data as $key => $value){
            if ($value !== null && $key !== 'authors'){
                if (is_string($value)){
                    $qb->andWhere($qb->expr()->like('b.'.$key, '\'%'.$value).'%\'');
                } else {
                    $qb->andWhere($qb->expr()->eq('b.'.$key, $value));
                }
            } elseif ($key === 'authors' && !$value->isEmpty()){
                $qb->leftJoin('b.authors', 'ba');
                foreach ($value->getValues() as $value) {
                    $qb->andWhere($qb->expr()->eq('ba.id', $value->getId()));
                    //$qb->orWhere($qb->expr()->eq('ba.id', $value->getId()));
                }
            }
        }
        
        return $qb->getQuery()->execute();
    }
}