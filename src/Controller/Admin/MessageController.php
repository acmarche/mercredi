<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Contrat\Presence\PresenceHandlerInterface;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Message;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Scolaire\GroupeScolaire;
use AcMarche\Mercredi\Message\Factory\MessageFactory;
use AcMarche\Mercredi\Message\Form\MessagePlaineType;
use AcMarche\Mercredi\Message\Form\MessageType;
use AcMarche\Mercredi\Message\Form\SearchMessageType;
use AcMarche\Mercredi\Message\Handler\MessageHandler;
use AcMarche\Mercredi\Message\Repository\MessageRepository;
use AcMarche\Mercredi\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Presence\Utils\PresenceUtils;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use AcMarche\Mercredi\Relation\Utils\RelationUtils;
use AcMarche\Mercredi\Search\SearchHelper;
use AcMarche\Mercredi\Tuteur\Utils\TuteurUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function count;

/**
 * Class DefaultController.
 *
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 * @Route("/message")
 */
final class MessageController extends AbstractController
{
    private PresenceRepository $presenceRepository;
    private RelationRepository $relationRepository;
    private SearchHelper $searchHelper;
    private TuteurUtils $tuteurUtils;
    private MessageFactory $messageFactory;
    private MessageHandler $messageHandler;
    private PresenceHandlerInterface $presenceHandler;
    private MessageRepository $messageRepository;
    private PlainePresenceRepository $plainePresenceRepository;

    public function __construct(
        PresenceRepository $presenceRepository,
        PlainePresenceRepository $plainePresenceRepository,
        RelationRepository $relationRepository,
        MessageRepository $messageRepository,
        SearchHelper $searchHelper,
        TuteurUtils $tuteurUtils,
        MessageFactory $messageFactory,
        MessageHandler $messageHandler,
        PresenceHandlerInterface $presenceHandler
    ) {
        $this->presenceRepository = $presenceRepository;
        $this->relationRepository = $relationRepository;
        $this->searchHelper = $searchHelper;
        $this->tuteurUtils = $tuteurUtils;
        $this->messageFactory = $messageFactory;
        $this->messageHandler = $messageHandler;
        $this->presenceHandler = $presenceHandler;
        $this->messageRepository = $messageRepository;
        $this->plainePresenceRepository = $plainePresenceRepository;
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
            $plaine = $data['plaine'];
            $tuteurs = [[]];

            if ($jour) {
                $presences = $this->presenceRepository->findByDay($jour);
                $tuteurs[] = PresenceUtils::extractTuteurs($presences);
            }

            if ($ecole) {
                $relations = $this->relationRepository->findByEcole($ecole);
                $tuteurs[] = RelationUtils::extractTuteurs($relations);
            }

            if ($plaine) {
                $presences = $this->plainePresenceRepository->findByPlaine($plaine);
                $tuteurs[] = PresenceUtils::extractTuteurs($presences);
            }

            if (!$jour && !$ecole && !$plaine) {
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
    public function fromJour(Request $request, Jour $jour): Response
    {
        $presences = $this->presenceRepository->findByDay($jour);

        $tuteurs = PresenceUtils::extractTuteurs($presences);
        $emails = $this->tuteurUtils->getEmails($tuteurs);

        $message = $this->messageFactory->createInstance();
        $message->setDestinataires($emails);

        $form = $this->createForm(MessageType::class, $message);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->messageHandler->handle($message);

            $this->addFlash('success', 'Le message a bien été envoyé');

            return $this->redirectToRoute('mercredi_message_index');
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/message/new.html.twig',
            [
                'emailuser' => $this->getUser()->getEmail(),
                'form' => $form->createView(),
                'emails' => $emails,
                'jour' => $jour,
                'tuteurs' => [],
            ]
        );
    }

    /**
     * @Route("/groupe/{id}", name="mercredi_message_new_groupescolaire")
     */
    public function fromGroupeScolaire(Request $request, GroupeScolaire $groupeScolaire): Response
    {
        $args = $this->searchHelper->getArgs(SearchHelper::PRESENCE_LIST);
        if (count($args) < 1) {
            $this->addFlash('danger', 'Aucun critère de recherche encodé');

            return $this->redirectToRoute('mercredi_admin_presence_index');
        }

        $jour = $args['jour'];
        $ecole = $args['ecole'];

        $data = $this->presenceHandler->searchAndGrouping($jour, $ecole, false);
        $enfants = $data[$groupeScolaire->getId()]['enfants'] ?? [];

        $tuteurs = $this->tuteurUtils->getTuteursByEnfants($enfants);
        $emails = $this->tuteurUtils->getEmails($tuteurs);

        $message = $this->messageFactory->createInstance();
        $message->setDestinataires($emails);

        $form = $this->createForm(MessageType::class, $message);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->messageHandler->handle($message);

            $this->addFlash('success', 'Le message a bien été envoyé');

            return $this->redirectToRoute('mercredi_admin_presence_index');
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/message/new.html.twig',
            [
                'emailuser' => $this->getUser()->getEmail(),
                'form' => $form->createView(),
                'emails' => $emails,
                'tuteurs' => [],
            ]
        );
    }

    /**
     * @Route("/plaine/{id}", name="mercredi_message_new_plaine")
     */
    public function fromPlaine(Request $request, Plaine $plaine): Response
    {
        $presences = $this->plainePresenceRepository->findByPlaine($plaine);

        $tuteurs = PresenceUtils::extractTuteurs($presences);
        $emails = $this->tuteurUtils->getEmails($tuteurs);

        $message = $this->messageFactory->createInstance();
        $message->setDestinataires($emails);

        $form = $this->createForm(MessagePlaineType::class, $message);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $attachCourrier = (bool)$form->get('attachCourriers')->getData();
            $this->messageHandler->handleFromPlaine($plaine, $message, $attachCourrier);

            $this->addFlash('success', 'Le message a bien été envoyé');

            return $this->redirectToRoute('mercredi_message_index');
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/message/new_from_plaine.html.twig',
            [
                'emailuser' => $this->getUser()->getEmail(),
                'form' => $form->createView(),
                'emails' => $emails,
                'plaine' => $plaine,
                'tuteurs' => [],
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

    /**
     * @Route("archive", name="mercredi_message_archive")
     */
    public function archive()
    {
        $messages = $this->messageRepository->findall();

        return $this->render(
            '@AcMarcheMercredi/admin/message/archive.html.twig',
            [
                'messages' => $messages,
            ]
        );
    }

    /**
     * @Route("/show/{id}", name="mercredi_message_show", methods={"GET"})
     */
    public function show(Message $message)
    {
        return $this->render(
            '@AcMarcheMercredi/admin/message/show.html.twig',
            [
                'message' => $message,
            ]
        );
    }
}
