<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Plaine\Form\PlaineJoursType;
use AcMarche\Mercredi\Plaine\Handler\PlaineAdminHandler;
use AcMarche\Mercredi\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Mercredi\Scolaire\Grouping\GroupingInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/plaine_jour')]
#[IsGranted(data: 'ROLE_MERCREDI_ADMIN')]
final class PlaineJourController extends AbstractController
{
    public function __construct(
        private PlaineAdminHandler $plaineAdminHandler,
        private PlainePresenceRepository $plainePresenceRepository,
        private GroupingInterface $grouping
    ) {
    }

    #[Route(path: '/edit/{id}', name: 'mercredi_admin_plaine_jour_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Plaine $plaine): Response
    {
        $this->plaineAdminHandler->initJours($plaine);
        $form = $this->createForm(PlaineJoursType::class, $plaine);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $jours = $form->get('jours')->getData();
            $this->plaineAdminHandler->handleEditJours($plaine, $jours);

            $this->addFlash('success', 'les dates ont bien été enregistrées');

            return $this->redirectToRoute('mercredi_admin_plaine_show', [
                'id' => $plaine->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/plaine_jour/edit.html.twig',
            [
                'plaine' => $plaine,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'mercredi_admin_plaine_jour_show', methods: ['GET'])]
    public function show(Jour $jour): Response
    {
        $plaine = $jour->getPlaine();
        $enfants = $this->plainePresenceRepository->findEnfantsByJour($jour, $plaine);
        $data = $this->grouping->groupEnfantsForPlaine($plaine, $enfants);

        return $this->render(
            '@AcMarcheMercrediAdmin/plaine_jour/show.html.twig',
            [
                'jour' => $jour,
                'plaine' => $plaine,
                'datas' => $data,
            ]
        );
    }
}
