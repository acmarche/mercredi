<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Message\Factory\MessageFactory;
use AcMarche\Mercredi\Message\Form\MessageType;
use AcMarche\Mercredi\Message\Form\SearchMessageType;
use AcMarche\Mercredi\Message\Handler\MessageHandler;
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
    /**
     * @var TuteurUtils
     */
    private $tuteurUtils;
    /**
     * @var MessageFactory
     */
    private $messageFactory;
    /**
     * @var MessageHandler
     */
    private $messageHandler;

    public function __construct(
        PresenceRepository $presenceRepository,
        TuteurRepository $tuteurRepository,
        RelationRepository $relationRepository,
        SearchHelper $searchHelper,
        TuteurUtils $tuteurUtils,
        MessageFactory $messageFactory,
        MessageHandler $messageHandler
    ) {
        $this->presenceRepository = $presenceRepository;
        $this->tuteurRepository = $tuteurRepository;
        $this->relationRepository = $relationRepository;
        $this->searchHelper = $searchHelper;
        $this->tuteurUtils = $tuteurUtils;
        $this->messageFactory = $messageFactory;
        $this->messageHandler = $messageHandler;
    }

    /**
     * @Route("/", name="mercredi_message_index")
     */
    public function index(Request $request): Response
    {
        $form = $this->createForm(SearchMessageType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $ecole = $data['ecole'];
            $jour = $data['jour'];
            $tuteurs = [[]];

            if ($jour) {
                $presences = $this->presenceRepository->findByDay($jour);
                $tuteurs[] = PresenceUtils::extractTuteurs($presences);
            }

            if ($ecole) {
                $relations = $enfants = $this->relationRepository->findByEcole($ecole);
                $tuteurs[] = RelationUtils::extractTuteurs($relations);
            }

            if (!$jour && !$ecole) {
                $relations = $this->relationRepository->findTuteursActifs();
                $tuteurs[] = RelationUtils::extractTuteurs($relations);
            }

            $tuteurs = array_merge(...$tuteurs);
        } else {
            $relations = $this->relationRepository->findTuteursActifs();
            $tuteurs = RelationUtils::extractTuteurs($relations);
        }

        $emails = $this->tuteurUtils->getEmails($tuteurs);
        $tuteursWithOutEmails = $this->tuteurUtils->filterTuteursWithOutEmail($tuteurs);

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
        $emails = $this->searchHelper->getArgs(SearchHelper::MESSAGE_INDEX);

        $message = $this->messageFactory->createInstance();
        $message->setDestinataires($emails);

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->messageHandler->handle($message);

            $this->addFlash('success', 'Le message a bien été envoyé');

            $this->searchHelper->deleteSearch(SearchHelper::MESSAGE_INDEX);

            return $this->redirectToRoute('mercredi_message_index');
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/message/new.html.twig',
            [
                'emails' => $emails,
                'form' => $form->createView(),
            ]
        );
    }


}
