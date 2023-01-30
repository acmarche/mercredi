<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Accueil\Repository\AccueilRepository;
use AcMarche\Mercredi\Pdf\PdfDownloaderTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/one')]
#[IsGranted('ROLE_MERCREDI_ADMIN')]
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

    #[Route('/one', name: 'app_one')]
    public function index(): Response
    {
        return $this->render('one/index.html.twig', [
            'controller_name' => 'OneController',
        ]);
    }
}
