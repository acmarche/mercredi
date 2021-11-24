<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Contrat\Plaine\PlaineCalculatorInterface;
use AcMarche\Mercredi\Contrat\Plaine\PlaineHandlerInterface;
use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Presence\Presence;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Plaine\Dto\PlainePresencesDto;
use AcMarche\Mercredi\Plaine\Form\PlainePresenceEditType;
use AcMarche\Mercredi\Plaine\Form\PlainePresencesEditType;
use AcMarche\Mercredi\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Mercredi\Presence\Message\PresenceUpdated;
use AcMarche\Mercredi\Presence\Utils\PresenceUtils;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use AcMarche\Mercredi\Search\Form\SearchNameType;
use AcMarche\Mercredi\Utils\SortUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/plaine/presence")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
final class PlainePresenceController extends AbstractController
{
    private EnfantRepository $enfantRepository;
    private PlaineHandlerInterface $plaineHandler;
    private RelationRepository $relationRepository;
    private PlaineCalculatorInterface $plaineCalculator;
    private PlainePresenceRepository $plainePresenceRepository;

    public function __construct(
        PlaineHandlerInterface $plaineHandler,
        EnfantRepository $enfantRepository,
        RelationRepository $relationRepository,
        PlainePresenceRepository $plainePresenceRepository,
        PlaineCalculatorInterface $plaineCalculator
    ) {
        $this->enfantRepository = $enfantRepository;
        $this->plaineHandler = $plaineHandler;
        $this->relationRepository = $relationRepository;
        $this->plaineCalculator = $plaineCalculator;
        $this->plainePresenceRepository = $plainePresenceRepository;
    }

    /**
     * @Route("/new/{id}", name="mercredi_admin_plaine_presence_new", methods={"GET","POST"})
     */
    public function new(Request $request, Plaine $plaine): Response
    {
        if (0 === count($plaine->getJours())) {
            $this->addFlash('danger', 'La plaine n\'a aucune date');

            return $this->redirectToRoute('mercredi_admin_plaine_show', ['id' => $plaine->getId()]);
        }

        $nom = null;
        $form = $this->createForm(SearchNameType::class, null);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $nom = $form->get('nom')->getData();
        }

        $enfants = $nom ? $this->enfantRepository->findByName($nom) : $this->enfantRepository->findAllActif();

        return $this->render(
            '@AcMarcheMercrediAdmin/plaine_presence/new.html.twig',
            [
                'enfants' => $enfants,
                'plaine' => $plaine,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/select/tuteur/{plaine}/{enfant}", name="mercredi_admin_plaine_presence_select_tuteur", methods={"GET","POST"})
     *
     * @Entity("plaine", expr="repository.find(plaine)")
     * @Entity("enfant", expr="repository.find(enfant)")
     */
    public function selectTuteur(Plaine $plaine, Enfant $enfant): Response
    {
        $tuteurs = $this->relationRepository->findTuteursByEnfant($enfant);
        if (1 === count($tuteurs)) {
            $tuteur = $tuteurs[0];

            return $this->redirectToRoute(
                'mercredi_admin_plaine_presence_confirmation',
                [
                    'plaine' => $plaine->getId(),
                    'tuteur' => $tuteur->getId(),
                    'enfant' => $enfant->getId(),
                ]
            );
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/plaine_presence/select_tuteur.html.twig',
            [
                'enfant' => $enfant,
                'plaine' => $plaine,
                'tuteurs' => $tuteurs,
            ]
        );
    }

    /**
     * @Route("/confirmation/{plaine}/{tuteur}/{enfant}", name="mercredi_admin_plaine_presence_confirmation", methods={"GET","POST"})
     *
     * @Entity("tuteur", expr="repository.find(tuteur)")
     * @Entity("plaine", expr="repository.find(plaine)")
     * @Entity("enfant", expr="repository.find(enfant)")
     */
    public function confirmation(Plaine $plaine, Tuteur $tuteur, Enfant $enfant): Response
    {
        $this->plaineHandler->handleAddEnfant($plaine, $tuteur, $enfant);

        $this->addFlash('success', "L'enfant a bien été inscrit");

        return $this->redirectToRoute(
            'mercredi_admin_plaine_presence_show',
            [
                'plaine' => $plaine->getId(),
                'enfant' => $enfant->getId(),
            ]
        );
    }

    /**
     * @Route("/{plaine}/{enfant}", name="mercredi_admin_plaine_presence_show", methods={"GET"})
     */
    public function show(Plaine $plaine, Enfant $enfant): Response
    {
        $presences = $this->plainePresenceRepository->findByPlaineAndEnfant($plaine, $enfant);
        $presences = SortUtils::sortPresences($presences);
        $cout = $this->plaineCalculator->calculate($plaine, $presences);

        return $this->render(
            '@AcMarcheMercrediAdmin/plaine_presence/show.html.twig',
            [
                'plaine' => $plaine,
                'enfant' => $enfant,
                'presences' => $presences,
                'cout' => $cout,
            ]
        );
    }

    /**
     * @Route("/{plaine}/{presence}/edit", name="mercredi_admin_plaine_presence_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Plaine $plaine, Presence $presence): Response
    {
        $enfant = $presence->getEnfant();
        $form = $this->createForm(PlainePresenceEditType::class, $presence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->plainePresenceRepository->flush();

            $this->dispatchMessage(new PresenceUpdated($presence->getId()));

            return $this->redirectToRoute(
                'mercredi_admin_plaine_presence_show',
                [
                    'plaine' => $plaine->getId(),
                    'enfant' => $enfant->getId(),
                ]
            );
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/plaine_presence/edit.html.twig',
            [
                'plaine' => $plaine,
                'presence' => $presence,
                'enfant' => $enfant,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{plaine}/{enfant}/jours", name="mercredi_admin_plaine_presence_jours", methods={"GET","POST"})
     */
    public function jours(Request $request, Plaine $plaine, Enfant $enfant): Response
    {
        $jours = $plaine->getJours();
        $plainePresencesDto = new PlainePresencesDto($plaine, $enfant, $jours);

        $presences = $this->plainePresenceRepository->findByPlaineAndEnfant($plaine, $enfant);
        $currentJours = PresenceUtils::extractJours($presences);
        $plainePresencesDto->setJours($currentJours);

        $form = $this->createForm(PlainePresencesEditType::class, $plainePresencesDto);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $new = $plainePresencesDto->getJours();
            if (0 === count($presences)) {
                $tuteurs = $this->relationRepository->findTuteursByEnfant($enfant);
                $tuteur = $tuteurs[0];
            } else {
                //todo bad
                $tuteur = $presences[0]->getTuteur();
            }

            $this->plaineHandler->handleEditPresences($tuteur, $enfant, $currentJours, $new);
            $this->addFlash('success', 'Les présences ont bien été modifiées');

            return $this->redirectToRoute(
                'mercredi_admin_plaine_presence_show',
                [
                    'plaine' => $plaine->getId(),
                    'enfant' => $enfant->getId(),
                ]
            );
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/plaine_presence/edit_presences.html.twig',
            [
                'plaine' => $plaine,
                'enfant' => $enfant,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}/delete", name="mercredi_admin_plaine_presence_delete", methods={"POST"})
     */
    public function delete(Request $request, Plaine $plaine): Response
    {
        if ($this->isCsrfTokenValid('deletePresence'.$plaine->getId(), $request->request->get('_token'))) {
            $presenceId = (int)$request->request->get('presence');
            if (0 === $presenceId) {
                $this->addFlash('danger', 'Référence à la présence non trouvée');

                return $this->redirectToRoute('mercredi_admin_plaine_index');
            }
            $presence = $this->plainePresenceRepository->find($presenceId);
            if (null === $presence) {
                $this->addFlash('danger', 'Présence non trouvée');

                return $this->redirectToRoute('mercredi_admin_plaine_index');
            }
            $enfant = $presence->getEnfant();
            $this->plainePresenceRepository->remove($presence);
            $this->plainePresenceRepository->flush();

            $this->addFlash('success', 'La présence à bien été supprimée');
        }

        return $this->redirectToRoute(
            'mercredi_admin_plaine_presence_show',
            ['plaine' => $plaine->getId(), 'enfant' => $enfant->getId()]
        );
    }

    /**
     * @Route("/{plaine}/{enfant}", name="mercredi_admin_plaine_presence_remove_enfant", methods={"POST"})
     */
    public function remove(Request $request, Plaine $plaine, Enfant $enfant): Response
    {
        if ($this->isCsrfTokenValid('remove'.$plaine->getId(), $request->request->get('_token'))) {
            $this->plaineHandler->removeEnfant($plaine, $enfant);
            $this->addFlash('success', 'L\'enfant a été retiré de la plaine');
        }

        return $this->redirectToRoute('mercredi_admin_plaine_show', ['id' => $plaine->getId()]);
    }
}
