<?php

namespace App\Repository;

use App\Entity\ParametresViteaux;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ParametresViteauxRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ParametresViteaux::class);
    }

    /**
     * Recherche les paramètres vitaux avec des filtres dynamiques
     */
    public function findByFilters(?Utilisateur $user, array $filters): array
    {
        $qb = $this->createQueryBuilder('p');

        

        foreach ($filters as $key => $value) {
            if (!empty($value)) {
                // Handle each specific filter key dynamically
                switch ($key) {
                    case 'name':
                        // Filter by user's name
                        $qb->join('p.user', 'u')
                           ->andWhere('LOWER(u.nom) LIKE LOWER(:name)')
                           ->setParameter('name', "%$value%");
                        break;
                    
                    case 'date':
                        // Ensure the date is in the correct format and handle the filter
                        $date = \DateTime::createFromFormat('Y-m-d', $value);
                        if ($date) {
                            $qb->andWhere('DATE(p.createdAt) = :date')
                               ->setParameter('date', $date->format('Y-m-d'));
                        }
                        break;
                    
                    case 'fc': // Example for specific fields like 'fc'
                    case 'fr':
                    case 'ecg':
                    case 'gad':
                    case 'tas':
                    case 'tad':
                    case 'spo2':
                    case 'gsc':
                        // Filter by specific parameter values
                        $qb->andWhere("p.$key = :$key")
                           ->setParameter($key, $value);
                        break;

                    default:
                        // Handle default case for additional filters that don't fall into a specific case
                        $qb->andWhere("p.$key = :$key")
                           ->setParameter($key, $value);
                        break;
                }
            }
        }

        return $qb->getQuery()->getResult();
    }
}
