<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Spam\Repository\HistoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[IsGranted('ROLE_MERCREDI_ADMIN')]
#[Route(path: '/history')]
final class HistoryController extends AbstractController
{
    public function __construct(
        private readonly HistoryRepository $historyRepository,
    ) {}

    #[Route(path: '/', name: 'mercredi_history_index')]
    public function index(Request $request): Response
    {
        $histories = $this->historyRepository->findAll();

        return $this->render(
            '@AcMarcheMercrediAdmin/history/index.html.twig',
            [
                'histories' => $histories,
            ],
        );
    }


}
