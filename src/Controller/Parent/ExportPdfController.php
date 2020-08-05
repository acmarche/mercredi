<?php

namespace AcMarche\Mercredi\Controller\Parent;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Sante\Factory\SantePdfFactory;
use AcMarche\Mercredi\Sante\Handler\SanteHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController.
 *
 * @IsGranted("ROLE_MERCREDI_PARENT")
 * @Route("/export/pdf")
 */
class ExportPdfController extends AbstractController
{
    use GetTuteurTrait;

    /**
     * @var SantePdfFactory
     */
    private $santePdfFactory;
    /**
     * @var SanteHandler
     */
    private $santeHandler;

    public function __construct(SanteHandler $santeHandler, SantePdfFactory $santePdfFactory)
    {
        $this->santePdfFactory = $santePdfFactory;
        $this->santeHandler = $santeHandler;
    }

    /**
     * @Route("/santefiche/{uuid}", name="mercredi_parent_export_sante_fiche_pdf", methods={"GET"})
     * @IsGranted("enfant_show", subject="enfant")
     */
    public function default(Enfant $enfant): Response
    {
        $santeFiche = $this->santeHandler->init($enfant);

        return $this->santePdfFactory->santeFiche($santeFiche);
    }
}
