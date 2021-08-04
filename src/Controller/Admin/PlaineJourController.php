<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Plaine\PlaineJour;
use AcMarche\Mercredi\Plaine\Form\PlaineJourType;
use AcMarche\Mercredi\Plaine\Handler\PlaineHandler;
use AcMarche\Mercredi\Plaine\Message\PlaineDeleted;
use AcMarche\Mercredi\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Mercredi\Plaine\Repository\PlaineRepository;
use AcMarche\Mercredi\Scolaire\Grouping\GroupingInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/plaine_jour")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
final class PlaineJourController extends AbstractController
{
    private PlaineRepository $plaineRepository;
    private EnfantRepository $enfantRepository;
    private PlaineHandler $plaineHandler;
    private PlainePresenceRepository $plainePresenceRepository;
    private GroupingInterface $grouping;

    public function __construct(
        PlaineRepository $plaineRepository,
        EnfantRepository $enfantRepository,
        PlaineHandler $plaineHandler,
        PlainePresenceRepository $plainePresenceRepository,
        GroupingInterface $grouping
    ) {
        $this->plaineRepository = $plaineRepository;
        $this->enfantRepository = $enfantRepository;
        $this->plaineHandler = $plaineHandler;
        $this->plainePresenceRepository = $plainePresenceRepository;
        $this->grouping = $grouping;
    }

    /**
     * @Route("/edit/{id}", name="mercredi_admin_plaine_jour_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Plaine $plaine): Response
    {
        $this->plaineHandler->initJours($plaine);

        $form = $this->createForm(PlaineJourType::class, $plaine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $jours = $form->get('jours')->getData();
            $this->plaineHandler->handleEditJours($plaine, $jours);

            $this->addFlash('success', 'les dates ont bien été enregistrées');

            return $this->redirectToRoute('mercredi_admin_plaine_show', ['id' => $plaine->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/plaine_jour/edit.html.twig',
            [
                'plaine' => $plaine,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="mercredi_admin_plaine_jour_show", methods={"GET"})
     */
    public function show(PlaineJour $plaineJour): Response
    {
        $plaine = $plaineJour->getPlaine();
        $jour = $plaineJour->getJour();
        $enfants = $this->plainePresenceRepository->findEnfantsByJour($jour);
        $data = $this->grouping->groupEnfantsForPlaine($plaine, $enfants);

        return $this->render(
            '@AcMarcheMercrediAdmin/plaine_jour/show.html.twig',
            [
                'jour' => $plaineJour->getJour(),
                'plaine' => $plaine,
                'datas' => $data,
                'plaineJour' => $plaineJour,
            ]
        );
    }

    /**
     * @Route("/{id}/delete", name="mercredi_admin_plaine_jour_delete", methods={"POST"})
     */
    public function delete(Request $request, Plaine $plaine): Response
    {
        if ($this->isCsrfTokenValid('delete'.$plaine->getId(), $request->request->get('_token'))) {
            if (count($this->enfantRepository->findBy(['plaine' => $plaine])) > 0) {
                $this->addFlash('danger', 'La plaine contient des enfants et ne peut être supprimée');

                return $this->redirectToRoute('mercredi_admin_plaine_show', ['id' => $plaine->getId()]);
            }
            $plaineId = $plaine->getId();
            $this->plaineRepository->remove($plaine);
            $this->plaineRepository->flush();
            $this->dispatchMessage(new PlaineDeleted($plaineId));
        }

        return $this->redirectToRoute('mercredi_admin_plaine_index');
    }
}
