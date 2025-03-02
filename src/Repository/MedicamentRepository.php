<?php

namespace App\Repository;

use App\Entity\Medicament;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Medicament>
 */
class MedicamentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Medicament::class);
    }
    // src/Repository/MedicamentRepository.php

    public function searchAndGroupByFournisseur(?string $search = null, ?string $type = null)
    {
        $qb = $this->createQueryBuilder('m')
            ->leftJoin('m.fournisseur', 'f')
            ->addSelect('f')
            ->orderBy('f.NomFournisseur', 'ASC');

        if ($search) {
            $qb->andWhere('m.NomMedicament LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        if ($type) {
            $qb->andWhere('m.Type = :type')
                ->setParameter('type', $type);
        }

        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return Medicament[] Returns an array of Medicament objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Medicament
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
