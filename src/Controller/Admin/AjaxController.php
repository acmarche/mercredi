<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Page\Repository\PageRepository;
use AcMarche\Mercredi\Sante\Repository\SanteQuestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[IsGranted('ROLE_MERCREDI_ADMIN')]
#[Route(path: '/ajax')]
final class AjaxController extends AbstractController
{
    public function __construct(
        private EnfantRepository $enfantRepository,
        private SanteQuestionRepository $santeQuestionRepository,
        private PageRepository $pageRepository,
    ) {}

    #[Route(path: '/enfants/link', name: 'mercredi_admin_autocomplete_enfants', methods: ['GET'])]
    public function enfants(Request $request): JsonResponse
    {
        $query = $request->query->get('query');
        $enfants = [];
        if ($query) {
            $enfants = $this->enfantRepository->findByName($query, true, 20);
        }
        $results = ['results' => []];
        foreach ($enfants as $enfant) {
            $results['results'][] = ['value' => $enfant->getId(), 'text' => $enfant->getNom().' '.$enfant->getPrenom()];
        }
        $results['next_page'] = null;

        return $this->json($results);
    }

    #[Route(path: '/q/sort/', name: 'mercredi_admin_ajax_question_sort', methods: ['POST'])]
    public function trierQuestion(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), null, 512, JSON_THROW_ON_ERROR);
        $questions = $data->questions;
        foreach ($questions as $position => $questionId) {
            $question = $this->santeQuestionRepository->find($questionId);
            if (null !== $question) {
                $question->setDisplayOrder($position);
            }
        }
        $this->santeQuestionRepository->flush();

        return $this->json('<div class="alert alert-success">Tri enregistré</div>');
    }

    #[Route(path: '/q/sort/{id}', name: 'mercredi_admin_ajax_page_sort', methods: ['POST', 'PATCH'])]
    public function trierPage(Request $request, int $id): Response
    {
        $isAjax = $request->isXmlHttpRequest();
        if ($isAjax) {
            $position = $request->request->get('position');
            if (($page = $this->pageRepository->find($id)) !== null) {
                $page->setPosition($position);
                $this->pageRepository->flush();
            }

            return new Response('<div class="alert alert-success">Tri enregistré '.$position.'</div>');
        }

        return new Response('<div class="alert alert-danger">Faill</div>');
    }

    #[Route(path: '/plaine/date', name: 'mercredi_admin_ajax_plaine_new_date')]
    public function plaineDate(Request $request): Response
    {
        $index = $request->get('index', 0);

        return $this->render('@AcMarcheMercrediAdmin/plaine/_new_line.html.twig', [
            'index' => $index,
        ]);
    }
}
