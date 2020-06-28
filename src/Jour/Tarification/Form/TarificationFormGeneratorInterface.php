<?php


namespace AcMarche\Mercredi\Jour\Tarification\Form;


use AcMarche\Mercredi\Entity\Jour;
use Symfony\Component\Form\FormInterface;

interface TarificationFormGeneratorInterface
{
    public function generate(Jour $jour): FormInterface;

    public function generateTarifsHtml(Jour $jour): string;
}
