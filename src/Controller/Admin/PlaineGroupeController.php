<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Plaine\PlaineGroupe;
use AcMarche\Mercredi\Plaine\Form\PlaineGroupeEditType;
use AcMarche\Mercredi\Plaine\Form\PlaineGroupesType;
use AcMarche\Mercredi\Plaine\Repository\PlaineGroupeRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/plaine_groupe')]
#[IsGranted('ROLE_MERCREDI_ADMIN')]
final class PlaineGroupeController extends AbstractController
{
    public function __construct(
        private PlaineGroupeRepository $plaineGroupeRepository,
    ) {}

    #[Route(path: '/index/{id}', name: 'mercredi_admin_plaine_groupe_index', methods: ['GET', 'POST'])]
    public function index(Request $request, Plaine $plaine): Response
    {
        $groupesPlaine = $this->plaineGroupeRepository->findByPlaine($plaine);
        $groupesScolaire = array_map(function ($groupePlaine) {
            return $groupePlaine->getGroupeScolaire();
        }, $groupesPlaine);

        $plaine->groupesScolaire = $groupesScolaire;

        $form = $this->createForm(PlaineGroupesType::class, $plaine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($plaine->groupesScolaire as $groupeScolaire) {
                if (!$this->plaineGroupeRepository->findByPlaineAndGroupeScolaire($plaine, $groupeScolaire)) {
                    $groupePlaine = new PlaineGroupe($plaine, $groupeScolaire);
                    $this->plaineGroupeRepository->persist($groupePlaine);
                }
            }

            foreach (array_diff($groupesScolaire, $plaine->groupesScolaire) as $groupeScolaire) {
                $plaineGroupe = $this->plaineGroupeRepository->findByPlaineAndGroupeScolaire($plaine, $groupeScolaire);
                if ($plaineGroupe) {
                    $this->plaineGroupeRepository->remove($plaineGroupe);
                }
            }

            $this->plaineGroupeRepository->flush();
            $this->addFlash('success', 'les groupes ont bien été enregistrés');

            return $this->redirectToRoute('mercredi_admin_plaine_show', [
                'id' => $plaine->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/plaine_groupe/index.html.twig',
            [
                'plaine' => $plaine,
                'form' => $form,
            ],
        );
    }

    #[Route(path: '/edit/{id}', name: 'mercredi_admin_plaine_groupe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PlaineGroupe $plaineGroupe): Response
    {
        $plaine = $plaineGroupe->getPlaine();
        $form = $this->createForm(PlaineGroupeEditType::class, $plaineGroupe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->plaineGroupeRepository->flush();
            $this->addFlash('success', 'le groupe été enregistré');

            return $this->redirectToRoute('mercredi_admin_plaine_show', [
                'id' => $plaine->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/plaine_groupe/edit.html.twig',
            [
                'plaine' => $plaine,
                'plaine_groupe' => $plaineGroupe,
                'form' => $form,
            ],
        );
    }
}
