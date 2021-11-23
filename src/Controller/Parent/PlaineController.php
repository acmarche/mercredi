<?php

namespace AcMarche\Mercredi\Controller\Parent;

use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Facture\Handler\FacturePlaineHandler;
use AcMarche\Mercredi\Mailer\Factory\AdminEmailFactory;
use AcMarche\Mercredi\Mailer\Factory\FactureEmailFactory;
use AcMarche\Mercredi\Mailer\NotificationMailer;
use AcMarche\Mercredi\Plaine\Form\PlaineConfirmationType;
use AcMarche\Mercredi\Plaine\Form\SelectEnfantType;
use AcMarche\Mercredi\Plaine\Handler\PlainePresenceHandler;
use AcMarche\Mercredi\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Mercredi\Plaine\Repository\PlaineRepository;
use AcMarche\Mercredi\Presence\Utils\PresenceUtils;
use AcMarche\Mercredi\Relation\Utils\RelationUtils;
use AcMarche\Mercredi\Sante\Handler\SanteHandler;
use AcMarche\Mercredi\Sante\Utils\SanteChecker;
use AcMarche\Mercredi\Tuteur\Utils\TuteurUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/plaine")
 */
final class PlaineController extends AbstractController
{
    use GetTuteurTrait;

    private PlaineRepository $plaineRepository;
    private RelationUtils $relationUtils;
    private PlainePresenceHandler $plainePresenceHandler;
    private SanteHandler $santeHandler;
    private SanteChecker $santeChecker;
    private PlainePresenceRepository $plainePresenceRepository;
    private FacturePlaineHandler $facturePlaineHandler;
    private FactureEmailFactory $factureEmailFactory;
    private NotificationMailer $notificationMailer;
    private AdminEmailFactory $adminEmailFactory;

    public function __construct(
        PlaineRepository $plaineRepository,
        RelationUtils $relationUtils,
        PlainePresenceHandler $plainePresenceHandler,
        SanteHandler $santeHandler,
        SanteChecker $santeChecker,
        PlainePresenceRepository $plainePresenceRepository,
        FacturePlaineHandler $facturePlaineHandler,
        FactureEmailFactory $factureEmailFactory,
        NotificationMailer $notificationMailer,
        AdminEmailFactory $adminEmailFactory
    ) {
        $this->plaineRepository = $plaineRepository;
        $this->relationUtils = $relationUtils;
        $this->plainePresenceHandler = $plainePresenceHandler;
        $this->santeHandler = $santeHandler;
        $this->santeChecker = $santeChecker;
        $this->plainePresenceRepository = $plainePresenceRepository;
        $this->facturePlaineHandler = $facturePlaineHandler;
        $this->factureEmailFactory = $factureEmailFactory;
        $this->notificationMailer = $notificationMailer;
        $this->adminEmailFactory = $adminEmailFactory;
    }

    /**
     * @Route("/open", name="mercredi_parent_plaine_open")
     * @IsGranted("ROLE_MERCREDI_PARENT")
     */
    public function open(): Response
    {
        $plaine = $this->plaineRepository->findPlaineOpen();

        return $this->render(
            '@AcMarcheMercrediParent/plaine/_open.html.twig',
            [
                'plaine' => $plaine,
            ]
        );
    }

    /**
     * @Route("/{id}/show", name="mercredi_parent_plaine_show")
     * @IsGranted("ROLE_MERCREDI_PARENT")
     */
    public function show(Plaine $plaine): Response
    {
        if (($hasTuteur = $this->hasTuteur()) !== null) {
            return $hasTuteur;
        }

        $inscriptions = $this->plainePresenceRepository->findByPlaineAndTuteur($plaine, $this->tuteur);
        $enfantsInscrits = PresenceUtils::extractEnfants($inscriptions);
        $enfants = $this->relationUtils->findEnfantsByTuteur($this->tuteur);

        $resteEnfant = count($enfantsInscrits) !== count($enfants);

        return $this->render(
            '@AcMarcheMercrediParent/plaine/show.html.twig',
            [
                'plaine' => $plaine,
                'enfants' => $enfantsInscrits,
                'inscriptions' => $inscriptions,
                'resteEnfants' => $resteEnfant,
            ]
        );
    }

    /**
     * Etape 1 select enfant.
     *
     * @Route("/select/enfant", name="mercredi_parent_plaine_select_enfant", methods={"GET","POST"})
     */
    public function selectEnfant(Request $request): Response
    {
        if (($hasTuteur = $this->hasTuteur()) !== null) {
            return $hasTuteur;
        }

        $enfants = $this->relationUtils->findEnfantsByTuteur($this->tuteur);
        $form = $this->createForm(SelectEnfantType::class, null, ['enfants' => $enfants]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plaine = $this->plaineRepository->findPlaineOpen();
            $enfantsSelected = $form->get('enfants')->getData();
            foreach ($enfantsSelected as $enfant) {

                $santeFiche = $this->santeHandler->init($enfant);

                if (!$this->santeChecker->isComplete($santeFiche)) {
                    $this->addFlash('danger', 'La fiche santé de '.$enfant.' doit être complétée');

                    continue;
                }

                if (null !== $plaine) {
                    $this->plainePresenceHandler->handleAddEnfant($plaine, $this->tuteur, $enfant);
                    $this->addFlash('success', $enfant.' a bien été inscrits à la plaine');
                }
            }

            return $this->redirectToRoute(
                'mercredi_parent_plaine_presence_confirmation',
                [

                ]
            );
        }

        return $this->render(
            '@AcMarcheMercrediParent/plaine/select_enfant.html.twig',
            [
                'enfants' => $enfants,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/confirmation", name="mercredi_parent_plaine_presence_confirmation", methods={"GET","POST"})
     */
    public function confirmation(Request $request): Response
    {
        if (($hasTuteur = $this->hasTuteur()) !== null) {
            return $hasTuteur;
        }

        $plaine = $this->plaineRepository->findPlaineOpen();

        $enfants = $this->plainePresenceRepository->findEnfantsByPlaineAndTuteur($plaine, $this->tuteur);

        $form = $this->createForm(PlaineConfirmationType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $inscriptions = $this->plainePresenceRepository->findByPlaineAndTuteur($plaine, $this->tuteur);
            foreach ($inscriptions as $inscription) {
                $inscription->setConfirmed(true);
            }
            $this->plainePresenceRepository->flush();

            $facture = $this->facturePlaineHandler->newInstance($plaine, $this->tuteur);
            $this->plainePresenceRepository->persist($facture);
            $this->plainePresenceRepository->flush();

            $this->facturePlaineHandler->handleManually($facture, $plaine);

            $emails = TuteurUtils::getEmailsOfOneTuteur($this->tuteur);
            if (count($emails) < 1) {
                $error = 'Pas de mail pour la facture plaine: '.$facture->getId();
                $this->addFlash('danger', $error);
                $message = $this->adminEmailFactory->messageAlert("Erreur envoie facture", $error);
                $this->notificationMailer->sendAsEmailNotification($message);
            }

            $from = $this->factureEmailFactory->getEmailAddressOrganisation();
            $message = $this->factureEmailFactory->messageFacture($from, 'Inscription à la plaine', 'Coucou');
            $this->factureEmailFactory->setTos($message, $emails);
            $this->factureEmailFactory->attachFactureOnTheFly($facture, $message);

            $this->factureEmailFactory->setTos($message, $emails);

            try {
                $this->notificationMailer->sendMail($message);
            } catch (TransportExceptionInterface $e) {
                $error = 'Facture plaine num '.$facture->getId().' '.$e->getMessage();
                $message = $this->adminEmailFactory->messageAlert("Erreur envoie facture plaine", $error);
                $this->notificationMailer->sendAsEmailNotification($message);
            }

            $this->notificationMailer->sendAsEmailNotification($message);
            $facture->setEnvoyeA(join(',',$emails));
            $facture->setEnvoyeLe(new \DateTime());
            $this->addFlash('success', 'La facture a bien été envoyée');
            $this->plainePresenceRepository->flush();

            return $this->redirectToRoute('mercredi_parent_plaine_show', ['id' => $plaine->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediParent/plaine/confirmation.twig',
            [
                'plaine' => $plaine,
                'enfants' => $enfants,
                'form' => $form->createView(),
            ]
        );
    }
}
