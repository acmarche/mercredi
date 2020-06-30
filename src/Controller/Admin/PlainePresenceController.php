<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Presence;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Plaine\Dto\PlainePresencesDto;
use AcMarche\Mercredi\Plaine\Form\PlainePresenceEditType;
use AcMarche\Mercredi\Plaine\Form\PlainePresencesEditType;
use AcMarche\Mercredi\Plaine\Handler\PlainePresenceHandler;
use AcMarche\Mercredi\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Mercredi\Plaine\Repository\PlaineRepository;
use AcMarche\Mercredi\Presence\Message\PresenceUpdated;
use AcMarche\Mercredi\Presence\Utils\PresenceUtils;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use AcMarche\Mercredi\Search\Form\SearchNameType;
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
class PlainePresenceController extends AbstractController
{
    /**
     * @var PlaineRepository
     */
    private $plaineRepository;
    /**
     * @var EnfantRepository
     */
    private $enfantRepository;
    /**
     * @var PlainePresenceHandler
     */
    private $plainePresenceHandler;
    /**
     * @var RelationRepository
     */
    private $relationRepository;
    /**
     * @var PlainePresenceRepository
     */
    private $plainePresenceRepository;

    public function __construct(
        PlainePresenceHandler $plainePresenceHandler,
        PlaineRepository $plaineRepository,
        EnfantRepository $enfantRepository,
        RelationRepository $relationRepository,
        PlainePresenceRepository $plainePresenceRepository
    ) {
        $this->plaineRepository = $plaineRepository;
        $this->enfantRepository = $enfantRepository;
        $this->plainePresenceHandler = $plainePresenceHandler;
        $this->relationRepository = $relationRepository;
        $this->plainePresenceRepository = $plainePresenceRepository;
    }

    /**
     * @Route("/new/{id}", name="mercredi_admin_plaine_presence_new", methods={"GET","POST"})
     */
    public function new(Request $request, Plaine $plaine): Response
    {
        if (0 == count($plaine->getJours())) {
            $this->addFlash('danger', 'La plaine n\'a aucune date');

            return $this->redirectToRoute('mercredi_admin_plaine_show', ['id' => $plaine->getId()]);
        }

        $nom = null;
        $form = $this->createForm(SearchNameType::class, null);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $nom = $form->get('nom')->getData();
        }

        if ($nom) {
            $enfants = $this->enfantRepository->findByName($nom);
        } else {
            $enfants = $this->enfantRepository->findAllActif();
        }

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
        $this->plainePresenceHandler->handleAddEnfant($plaine, $tuteur, $enfant);

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
        $presences = $this->plainePresenceRepository->findPrecencesByPlaineAndEnfant($plaine, $enfant);

        return $this->render(
            '@AcMarcheMercrediAdmin/plaine_presence/show.html.twig',
            [
                'plaine' => $plaine,
                'enfant' => $enfant,
                'presences' => $presences,
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
            $this->plainePresenceHandler->handleEditPresence($presence);

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
        $plaineDto = new PlainePresencesDto($plaine, $enfant, $plaine->getJours());

        $presences = $this->plainePresenceHandler->findPresencesByPlaineEnfant($plaine, $enfant);
        $currentJours = PresenceUtils::extractJours($presences);
        $plaineDto->setJours($currentJours);

        $form = $this->createForm(PlainePresencesEditType::class, $plaineDto);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $new = $plaineDto->getJours();
            $tuteur = $presences[0]->getTuteur(); //todo bad

            $this->plainePresenceHandler->handleEditPresences($tuteur, $enfant, $currentJours, $new);
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
     * @Route("/{id}", name="mercredi_admin_plaine_presence_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Plaine $plaine): Response
    {
        if ($this->isCsrfTokenValid('deletePresence'.$plaine->getId(), $request->request->get('_token'))) {
            $presenceId = (int)$request->request->get('presence');
            if (!$presenceId) {
                $this->addFlash('danger', 'Référence à la présence non trouvée');

                return $this->redirectToRoute('mercredi_admin_plaine_index');
            }
            $presence = $this->plainePresenceHandler->findPresence($presenceId);
            if (!$presence) {
                $this->addFlash('danger', 'Présence non trouvée');

                return $this->redirectToRoute('mercredi_admin_plaine_index');
            }
            $enfant = $presence->getEnfant();
            $this->plainePresenceHandler->remove($presence);

            $this->addFlash('success', 'La présence à bien été supprimée');
        }

        return $this->redirectToRoute(
            'mercredi_admin_plaine_presence_show',
            ['plaine' => $plaine->getId(), 'enfant' => $enfant->getId()]
        );
    }

    /**
     * @Route("/{plaine}/{enfant}", name="mercredi_admin_plaine_presence_remove_enfant", methods={"DELETE"})
     */
    public function remove(Request $request, Plaine $plaine, Enfant $enfant): Response
    {
        if ($this->isCsrfTokenValid('remove'.$plaine->getId(), $request->request->get('_token'))) {
            $this->plainePresenceHandler->removeEnfant($plaine, $enfant);
            $this->addFlash('success','L\'enfant a été retiré de la plaine');
        }

        return $this->redirectToRoute('mercredi_admin_plaine_show', ['id'=>$plaine->getId()]);
    }
}