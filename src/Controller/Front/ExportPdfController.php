<?php

namespace AcMarche\Mercredi\Controller\Front;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Facture\Creance;
use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Facture\Factory\FacturePdfFactoryTrait;
use AcMarche\Mercredi\Sante\Factory\SantePdfFactoryTrait;
use AcMarche\Mercredi\Sante\Handler\SanteHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 * @Route("/export/pdf")
 */
final class ExportPdfController extends AbstractController
{
    private SantePdfFactoryTrait $santePdfFactory;
    private SanteHandler $santeHandler;
    private FacturePdfFactoryTrait $facturePdfFactory;

    public function __construct(
        SanteHandler $santeHandler,
        SantePdfFactoryTrait $santePdfFactory,
        FacturePdfFactoryTrait $facturePdfFactory
    ) {
        $this->santePdfFactory = $santePdfFactory;
        $this->santeHandler = $santeHandler;
        $this->facturePdfFactory = $facturePdfFactory;
    }

    /**
     * @Route("/santefiche/{uuid}", name="mercredi_commun_export_sante_fiche_pdf")
     * @IsGranted("enfant_show", subject="enfant")
     */
    public function sante(Enfant $enfant): Response
    {
        $santeFiche = $this->santeHandler->init($enfant);

        return $this->santePdfFactory->santeFiche($santeFiche);
    }

    /**
     * @Route("/facture/{uuid}", name="mercredi_commun_export_facture_pdf")
     */
    public function facture(Facture $facture): Response
    {
        $tuteur = $facture->getTuteur();
        $this->denyAccessUnlessGranted('tuteur_show', $tuteur);

        return $this->facturePdfFactory->generate($facture);
    }

    /**
     * @Route("/creance/{uuid}", name="mercredi_commun_export_creance_pdf")
     */
    public function creance(Creance $creance): Response
    {
        return new Response('todo');
        $tuteur = $creance->getTuteur();
        $this->denyAccessUnlessGranted('tuteur_show', $tuteur);

        return $this->facturePdfFactory->generate($creance);
    }
}
