<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Page\Repository\PageRepository;
use AcMarche\Mercredi\Sante\Repository\SanteQuestionRepository;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController.
 *
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 * @Route("/ajax")
 */
final class AjaxController extends AbstractController
{
    private EnfantRepository $enfantRepository;
    private TuteurRepository $tuteurRepository;
    private SanteQuestionRepository $santeQuestionRepository;
    private PageRepository $pageRepository;

    public function __construct(
        EnfantRepository $enfantRepository,
        TuteurRepository $tuteurRepository,
        SanteQuestionRepository $santeQuestionRepository,
        PageRepository $pageRepository
    ) {
        $this->enfantRepository = $enfantRepository;
        $this->tuteurRepository = $tuteurRepository;
        $this->santeQuestionRepository = $santeQuestionRepository;
        $this->pageRepository = $pageRepository;
    }

    /**
     * not use
     * @Route("/tuteurs", name="mercredi_admin_ajax_tuteurs")
     */
    public function tuteurs(Request $request): Response
    {
        $keyword = $request->get('q');
        $tuteurs = [];
        if ($keyword) {
            $tuteurs = $this->tuteurRepository->search($keyword);
        }

        return $this->render('@AcMarcheMercredi/commun/tuteur/_list.html.twig', ['tuteurs' => $tuteurs]);
    }

    /**
     * @Route("/enfants/link", name="mercredi_admin_ajax_enfants", methods={"GET"})
     */
    public function enfants(Request $request): Response
    {
        $keyword = $request->get('q');
        $enfants = [];
        if ($keyword) {
            $enfants = $this->enfantRepository->findByName($keyword, true, 10);
        }

        return $this->render('@AcMarcheMercredi/commun/enfant/_list.html.twig', ['enfants' => $enfants]);
    }

    /**
     * @Route("/enfants/nolink", name="mercredi_admin_ajax_enfants_no_link", methods={"GET"})
     */
    public function enfantsNoLink(Request $request): Response
    {
        $keyword = $request->get('q');
        $enfants = [];
        if ($keyword) {
            $enfants = $this->enfantRepository->findByName($keyword, true, 10);
        }

        return $this->render('@AcMarcheMercredi/commun/enfant/_list_not_link.html.twig', ['enfants' => $enfants]);
    }

    /**
     * not use
     * @Route("/plaine/date", name="mercredi_admin_ajax_plaine_new_date")
     */
    public function plaineDate(Request $request): Response
    {
        $index = $request->get('index', 0);

        return $this->render('@AcMarcheMercrediAdmin/plaine/_new_line.html.twig', ['index' => $index]);
    }

    /**
     * @Route("/q/sort/", name="mercredi_admin_ajax_question_sort", methods={"POST"})
     */
    public function trierQuestion(Request $request): Response
    {
        //    $isAjax = $request->isXmlHttpRequest();
        //    if ($isAjax) {
        //
        $data = json_decode($request->getContent());
        $questions = $data->questions;
        foreach ($questions as $position => $questionId) {
            $question = $this->santeQuestionRepository->find($questionId);
            if ($question) {
                $question->setDisplayOrder($position);
            }
        }
        $this->santeQuestionRepository->flush();

        return $this->json('<div class="alert alert-success">Tri enregistré</div>');
    }

    /**
     * @Route("/q/sort/{id}", name="mercredi_admin_ajax_page_sort", methods={"POST", "PATCH"})
     */
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
}
