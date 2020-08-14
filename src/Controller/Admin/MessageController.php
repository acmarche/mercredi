<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Message\Factory\MessageFactory;
use AcMarche\Mercredi\Message\Form\MessageType;
use AcMarche\Mercredi\Message\Form\SearchMessageType;
use AcMarche\Mercredi\Message\Handler\MessageHandler;
use AcMarche\Mercredi\Presence\Handler\PresenceHandler;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Presence\Utils\PresenceUtils;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use AcMarche\Mercredi\Relation\Utils\RelationUtils;
use AcMarche\Mercredi\Search\SearchHelper;
use AcMarche\Mercredi\Tuteur\Utils\TuteurUtils;
use function count;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController.
 *
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 * @Route("/message")
 */
final class MessageController extends AbstractController
{
    /**
     * @var string
     */
    private const FORM = 'form';
    /**
     * @var string
     */
    private const EMAILS = 'emails';
    /**
     * @var string
     */
    private const TUTEURS = 'tuteurs';
    /**
     * @var string
     */
    private const SUCCESS = 'success';
    /**
     * @var PresenceRepository
     */
    private $presenceRepository;
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
    /**
     * @var PresenceHandler
     */
    private $presenceHandler;

    public function __construct(
        PresenceRepository $presenceRepository,
        RelationRepository $relationRepository,
        SearchHelper $searchHelper,
        TuteurUtils $tuteurUtils,
        MessageFactory $messageFactory,
        MessageHandler $messageHandler,
        PresenceHandler $presenceHandler
    ) {
        $this->presenceRepository = $presenceRepository;
        $this->relationRepository = $relationRepository;
        $this->searchHelper = $searchHelper;
        $this->tuteurUtils = $tuteurUtils;
        $this->messageFactory = $messageFactory;
        $this->messageHandler = $messageHandler;
        $this->presenceHandler = $presenceHandler;
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

            if (! $jour && ! $ecole) {
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
                self::FORM => $form->createView(),
                self::EMAILS => $emails,
                self::TUTEURS => $tuteursWithOutEmails,
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

        $form = $form = $this->createForm(MessageType::class, $message);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->messageHandler->handle($message);

            $this->addFlash(self::SUCCESS, 'Le message a bien été envoyé');

            return $this->redirectToRoute('mercredi_message_index');
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/message/new.html.twig',
            [
                'emailuser' => $this->getUser()->getEmail(),
                self::FORM => $form->createView(),
                self::EMAILS => $emails,
                self::TUTEURS => [],
            ]
        );
    }

    /**
     * @Route("/groupe/{groupe}", name="mercredi_message_new_groupescolaire")
     */
    public function groupeScolaire(Request $request, string $groupe): Response
    {
        $args = $this->searchHelper->getArgs(SearchHelper::PRESENCE_LIST);
        if (count($args) < 1) {
            return $this->redirectToRoute('mercredi_admin_presence_index');
        }

        $jour = $args['jour'];
        $ecole = $args['ecole'];

        $data = $this->presenceHandler->handleForGrouping($jour, $ecole, false);
        $enfants = $data[$groupe] ?? [];

        $tuteurs = $this->tuteurUtils->getTuteursByEnfants($enfants);
        $emails = $this->tuteurUtils->getEmails($tuteurs);

        $message = $this->messageFactory->createInstance();
        $message->setDestinataires($emails);

        $form = $form = $this->createForm(MessageType::class, $message);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->messageHandler->handle($message);

            $this->addFlash(self::SUCCESS, 'Le message a bien été envoyé');

            return $this->redirectToRoute('mercredi_admin_presence_index');
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/message/new.html.twig',
            [
                'emailuser' => $this->getUser()->getEmail(),
                self::FORM => $form->createView(),
                self::EMAILS => $emails,
                self::TUTEURS => [],
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

            $this->addFlash(self::SUCCESS, 'Le message a bien été envoyé');

            $this->searchHelper->deleteSearch(SearchHelper::MESSAGE_INDEX);

            return $this->redirectToRoute('mercredi_message_index');
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/message/new.html.twig',
            [
                self::EMAILS => $emails,
                self::FORM => $form->createView(),
            ]
        );
    }
}
