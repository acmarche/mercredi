<?php


namespace AcMarche\Mercredi\Facture\Factory;


use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Facture\Repository\FactureRepository;

class CommunicationFactory
{
    private FactureRepository $factureRepository;

    public function __construct(FactureRepository $factureRepository)
    {
        $this->factureRepository = $factureRepository;
    }

    /**
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
    public function generate(Facture $facture): string
    {
        list($month, $year) = explode('-', $facture->getMois());
        $digits = 4;
        $factureId = str_pad(rand(0, pow(10, $digits) - 1), $digits, '0', STR_PAD_LEFT);
        //4(Y) +2(m) + 4(id) + 2(last)
        $numbers = $year.$month.$factureId;
        $numbers .= $this->getModulo($numbers);
        $communication = substr($numbers, 0, 3).'/'.substr($numbers, 3, 4).'/'.substr($numbers, 7, 5);
        if ($this->factureRepository->findOneBy(['communication' => $communication])) {
            return $this->generate($facture);
        }

        return $communication;
    }

    /**
     * Si le résultat du modulo est égal à 0, le chiffre 97 sera utilisé.
     * Si le résultat du modulo est plus grand que 0, le résultat du modulo sera utilisé.
     * Par exemple :
     *
     * Numéro de facture avec un zéro ajouté =
     *
     * Invoice ID with added zero = 1809426 + 0 = 18094260
     * 18094260 % 97 = 74
     *
     * +++001/8094/26074+++
     * @param string $numeros
     * @return int
     */
    private function getModulo(string $numeros): int
    {
        //$r = fmod($numeros, 97);
        $r = $numeros / 97;//retourne entier
        if ($r == 0) {
            return 97;
        }

        return $r;
    }

    public function generatePlaine(Plaine $plaine): string
    {
        return $plaine->getSlug();
    }


}
