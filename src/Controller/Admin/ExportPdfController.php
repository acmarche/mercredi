<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Facture\Factory\FacturePdfFactoryTrait;
use AcMarche\Mercredi\Sante\Factory\SantePdfFactoryTrait;
use AcMarche\Mercredi\Sante\Handler\SanteHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController.
 *
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 * @Route("/export/pdf")
 */
final class ExportPdfController extends AbstractController
{
    /**
     * @var SantePdfFactoryTrait
     */
    private $santePdfFactory;
    /**
     * @var SanteHandler
     */
    private $santeHandler;
    /**
     * @var FacturePdfFactoryTrait
     */
    private $facturePdfFactory;

    public function __construct(SanteHandler $santeHandler, SantePdfFactoryTrait $santePdfFactory, FacturePdfFactoryTrait $facturePdfFactory)
    {
        $this->santePdfFactory = $santePdfFactory;
        $this->santeHandler = $santeHandler;
        $this->facturePdfFactory = $facturePdfFactory;
    }

    /**
     * @Route("/santefiche/{id}", name="mercredi_admin_export_sante_fiche_pdf")
     */
    public function default(Enfant $enfant): Response
    {
        $santeFiche = $this->santeHandler->init($enfant);

        return $this->santePdfFactory->santeFiche($santeFiche);
    }

    /**
     * @Route("/facture/{id}", name="mercredi_admin_export_facture_pdf")
     */
    public function facture(Facture $facture): Response
    {
        return $this->facturePdfFactory->generate($facture);
    }
}
