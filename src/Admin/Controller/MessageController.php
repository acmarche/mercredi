<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Message\Form\SearchMessageType;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Presence\Utils\PresenceUtils;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use AcMarche\Mercredi\Relation\Utils\RelationUtils;
use AcMarche\Mercredi\Search\SearchHelper;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use AcMarche\Mercredi\Tuteur\Utils\TuteurUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 * @package AcMarche\Mercredi\Controller
 *
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 * @Route("/message")
 */
class MessageController extends AbstractController
{
    /**
     * @var PresenceRepository
     */
    private $presenceRepository;
    /**
     * @var TuteurRepository
     */
    private $tuteurRepository;
    /**
     * @var RelationRepository
     */
    private $relationRepository;
    /**
     * @var SearchHelper
     */
    private $searchHelper;

    public function __construct(
        PresenceRepository $presenceRepository,
        TuteurRepository $tuteurRepository,
        RelationRepository $relationRepository,
        SearchHelper $searchHelper
    ) {
        $this->presenceRepository = $presenceRepository;
        $this->tuteurRepository = $tuteurRepository;
        $this->relationRepository = $relationRepository;
        $this->searchHelper = $searchHelper;
    }

    /**
     * @Route("/", name="mercredi_message")
     */
    public function index(Request $request): Response
    {
        $tuteurs = [];
        $form = $this->createForm(SearchMessageType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $ecole = $data['ecole'];
            $jour = $data['jour'];

            if ($jour && $ecole) {
                $this->addFlash('danger', 'Un seul critère de recherche');
                $this->redirectToRoute('mercredi_message');
            }

            if ($jour) {
                $presences = $this->presenceRepository->findByDay($jour);
                $tuteurs = PresenceUtils::extractTuteurs($presences);
            }

            if ($ecole) {
                $relations = $enfants = $this->relationRepository->findByEcole($ecole);
                $tuteurs = RelationUtils::extractTuteurs($relations);
            }
        } else {
            $relations = $this->relationRepository->findTuteursActifs();
            $tuteurs = RelationUtils::extractTuteurs($relations);
        }

        $emails = TuteurUtils::getEmails($tuteurs);
        $tuteursWithOutEmails = TuteurUtils::filterTuteursWithOutEmail($tuteurs);

        $this->searchHelper->saveSearch(SearchHelper::MESSAGE_INDEX, $emails);

        return $this->render(
            '@AcMarcheMercrediAdmin/message/index.html.twig',
            [
                'form' => $form->createView(),
                'emails' => $emails,
                'tuteurs' => $tuteursWithOutEmails,
            ]
        );
    }

    /**
     * @Route("/jour/{id}", name="mercredi_message_new_jour")
     */
    public function default(Request $request, Jour $jour): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/default/index.html.twig',
            [
            ]
        );
    }

    /**
     * @Route("/groupe", name="mercredi_message_new_groupescolaire")
     */
    public function groupeScolaire(Request $request): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/default/index.html.twig',
            [
            ]
        );
    }

    /**
     * @Route("/new", name="mercredi_message_new")
     */
    public function new(Request $request): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/default/index.html.twig',
            [
            ]
        );
    }


}
