<?php
namespace App\Service;

use App\Repository\EquipementRepository;

class StatistiqueMetier
{
    private EquipementRepository $equipementRepository;

    public function __construct(EquipementRepository $equipementRepository)
    {
        $this->equipementRepository = $equipementRepository;
    }

    public function getEquipementStatistiques(): array
    {
        $stats = [
            'Neuf' => 0,
            'Réparé' => 0,
            'Abimé' => 0
        ];

        $equipements = $this->equipementRepository->findAll();

        foreach ($equipements as $equipement) {
            $etat = $equipement->getEtatEquipement();
            if (isset($stats[$etat])) {
                $stats[$etat]++;
            }
        }

        return $stats;
    }
}
