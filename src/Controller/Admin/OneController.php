<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Accueil\Repository\AccueilRepository;
use AcMarche\Mercredi\Pdf\PdfDownloaderTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route(path: '/one')]
#[IsGranted(data: 'ROLE_MERCREDI_ADMIN')]
final class OneController extends AbstractController
{
    use PdfDownloaderTrait;

    public function __construct(
        private AccueilRepository $accueilRepository
    ) {
    }

    #[Route(path: '/', name: 'mercredi_admin_one')]
    public function default(): Response
    {
        return $this->render(
            '@AcMarcheMercredi/admin/one/index.html.Twig',
            [
            ]
        );
    }
}
