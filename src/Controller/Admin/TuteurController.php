<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Notification\EmailNotification;
use Symfony\Component\HttpFoundation\Response;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use AcMarche\Mercredi\Search\SearchHelper;
use AcMarche\Mercredi\Tuteur\Form\SearchTuteurType;
use AcMarche\Mercredi\Tuteur\Form\TuteurType;
use AcMarche\Mercredi\Tuteur\Message\TuteurCreated;
use AcMarche\Mercredi\Tuteur\Message\TuteurDeleted;
use AcMarche\Mercredi\Tuteur\Message\TuteurUpdated;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\NoRecipient;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tuteur")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
final class TuteurController extends AbstractController
{
    private TuteurRepository $tuteurRepository;
    private RelationRepository $relationRepository;
    private SearchHelper $searchHelper;
    private NotifierInterface $notifier;

    public function __construct(
        TuteurRepository $tuteurRepository,
        RelationRepository $relationRepository,
        NotifierInterface $notifier,
        SearchHelper $searchHelper
    ) {
        $this->tuteurRepository = $tuteurRepository;
        $this->relationRepository = $relationRepository;
        $this->searchHelper = $searchHelper;
        $this->notifier = $notifier;
    }

    /**
     * @Route("/", name="mercredi_admin_tuteur_index", methods={"GET","POST"})
     */
    public function index(Request $request): Response
    {
        $form = $this->createForm(SearchTuteurType::class);
        $form->handleRequest($request);
        $search = false;
        $tuteurs = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->searchHelper->saveSearch(SearchHelper::TUTEUR_LIST, $data);
            $search = true;
            $tuteurs = $this->tuteurRepository->search($data['nom'], $data['archived']);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/tuteur/index.html.twig',
            [
                'tuteurs' => $tuteurs,
                'form' => $form->createView(),
                'search' => $search,
            ]
        );
    }

    /**
     * @Route("/new", name="mercredi_admin_tuteur_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $tuteur = new Tuteur();
        $form = $this->createForm(TuteurType::class, $tuteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->tuteurRepository->persist($tuteur);
            $this->tuteurRepository->flush();

            $this->dispatchMessage(new TuteurCreated($tuteur->getId()));

            return $this->redirectToRoute('mercredi_admin_tuteur_show', ['id' => $tuteur->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/tuteur/new.html.twig',
            [
                'tuteur' => $tuteur,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="mercredi_admin_tuteur_show", methods={"GET"})
     */
    public function show(Tuteur $tuteur): Response
    {

        $notification = (new Notification('New Invoice', ['email']))
            ->content('You got a new invoice for 15 EUR.')
            ->importance(Notification::IMPORTANCE_HIGH)
            ->emoji('🤩');

        // The receiver of the Notification
        $recipient = new Recipient(
            'jf@marche.be',
            '+32476662615'
        );

        $norecipient = new NoRecipient();
        // Send the notification to the recipient
       // $this->notifier->send($notification, $recipient);

        $emailNotification = new EmailNotification($tuteur, 'coucou');
        $this->notifier->send($emailNotification, $recipient);

        $relations = $this->relationRepository->findByTuteur($tuteur);

        return $this->render(
            '@AcMarcheMercrediAdmin/tuteur/show.html.twig',
            [
                'tuteur' => $tuteur,
                'relations' => $relations,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="mercredi_admin_tuteur_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Tuteur $tuteur): Response
    {
        $form = $this->createForm(TuteurType::class, $tuteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->tuteurRepository->flush();

            $this->dispatchMessage(new TuteurUpdated($tuteur->getId()));

            return $this->redirectToRoute('mercredi_admin_tuteur_show', ['id' => $tuteur->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/tuteur/edit.html.twig',
            [
                'tuteur' => $tuteur,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * //todo que faire si presence.
     *
     * @Route("/{id}/delete", name="mercredi_admin_tuteur_delete", methods={"POST"})
     */
    public function delete(Request $request, Tuteur $tuteur): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tuteur->getId(), $request->request->get('_token'))) {
            $id = $tuteur->getId();
            $this->tuteurRepository->remove($tuteur);
            $this->tuteurRepository->flush();
            $this->dispatchMessage(new TuteurDeleted($id));
        }

        return $this->redirectToRoute('mercredi_admin_tuteur_index');
    }
}
