<?php

namespace AcMarche\Mercredi\Controller\Front;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Facture\Factory\FacturePdfFactoryTrait;
use AcMarche\Mercredi\Sante\Factory\SantePdfFactoryTrait;
use AcMarche\Mercredi\Sante\Handler\SanteHandler;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/export/pdf')]
final class ExportPdfController extends AbstractController
{
    public function __construct(
        private SanteHandler $santeHandler,
        private SantePdfFactoryTrait $santePdfFactory,
        private FacturePdfFactoryTrait $facturePdfFactory,
    ) {}

    #[Route(path: '/santefiche/{uuid}', name: 'mercredi_commun_export_sante_fiche_pdf')]
    #[IsGranted('enfant_show', subject: 'enfant')]
    public function sante(Enfant $enfant): Response
    {
        $santeFiche = $this->santeHandler->init($enfant);

        return $this->santePdfFactory->santeFiche($santeFiche);
    }

    #[Route(path: '/facture/{uuid}', name: 'mercredi_commun_export_facture_pdf')]
    public function facture(Facture $facture): Response
    {
        $tuteur = $facture->getTuteur();
        $this->denyAccessUnlessGranted('tuteur_show', $tuteur);

        return $this->facturePdfFactory->generate($facture);
    }
}
