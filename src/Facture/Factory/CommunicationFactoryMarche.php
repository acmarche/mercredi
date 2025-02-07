<?php

namespace AcMarche\Mercredi\Facture\Factory;

use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Facture\FactureInterface;
use AcMarche\Mercredi\Facture\Repository\FactureRepository;
use AcMarche\Mercredi\Facture\Utils\BelgianStructuredGenerator;

class CommunicationFactoryMarche implements CommunicationFactoryInterface
{
    public function __construct(
        private FactureRepository $factureRepository,
    ) {}

    /**
     * Pour la communication à faire apparaitre :.
     *
     * Raccourci de l’école + n° facture + année (année, non pas d’émission de la facture, mais du mois de garderie)
     *
     * Exemple : COM HAMP 1/2021
     *
     * Raccourcis des écoles :
     *
     * Communale de Hampteau : COMHAM
     * Communale de Hotton : COMHOT
     * Libre Bourdon : LB
     * Libre Hotton : LH
     * Libre Melreux : LM
     * Enrico Macias : EM
     *
     * La structure imposée par le standard bancaire belge est la suivante :
     *
     * +++ 000 / 0000 / 000XX +++
     *
     * Les deux derniers numéros « XX » étant des numéros de contrôle.
     *
     * Afin de faciliter la lecture de ce numéro, Smoall le compose de la manière suivante :
     *
     * +++ YYY / YNNN / NNNXX +++
     * YYYY = L’année de la date de facture
     * NNNNNN = Le numéro de la facture.
     *
     * Exemple : La communication structurée suivante +++ 201 / 8000 / 53522 +++ se rapporte donc à la facture n° 535 de l’année 2018.
     */
    public function generateForPlaine(Plaine $plaine, FactureInterface $facture): string
    {
        $communication = $plaine->getCommunication();

        if (!$communication) {
            $communication = $plaine->getSlug();
        }

        if (!$id = $facture->getId()) {
            $id = rand(1, 999);
        }

        $communication .= ' '.$facture->getNom().' '.$id;

        if ($this->checkExist($communication)) {
            $communication .= '-'.rand(1, 100);
        }

        return $communication;
    }

    public function generateForPresence(FactureInterface $facture): string
    {
        //return BelgianStructuredGenerator::generate();
        $ecoles = '';
        foreach ($facture->ecolesListing as $ecole) {
            if ($ecole->getAbreviation()) {
                $ecoles .= $ecole->getAbreviation().' ';
            } else {
                $ecoles .= $ecole->getLocalite();
            }
        }

        if (!$id = $facture->getId()) {
            $id = rand(1, 999);
        }

        //list($month, $year) = explode('-', $facture->getMois());
        $communication = $id.' - '.$facture->getMois();

        if ($this->checkExist($communication)) {
            $communication .= '-'.rand(1, 100);
        }

        return $communication;
    }

    private function checkExist(string $communication): bool
    {
        return $this->factureRepository->findByCommunication($communication) != null;
    }
}
