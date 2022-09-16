<?php

namespace AcMarche\Mercredi\Controller\Parent;

use AcMarche\Mercredi\Contrat\Plaine\PlaineHandlerInterface;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Facture\Repository\FactureRepository;
use AcMarche\Mercredi\Plaine\Dto\PlainePresencesDto;
use AcMarche\Mercredi\Plaine\Form\PlainePresencesEditType;
use AcMarche\Mercredi\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Mercredi\Presence\Utils\PresenceUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Sodium\add;

class PlainePresenceController extends AbstractController
{
    use GetTuteurTrait;

    public function __construct(
        private PlainePresenceRepository $plainePresenceRepository,
        private PlaineHandlerInterface $plaineHandler,
        private FactureRepository $factureRepository
    ) {
    }

    #[Route(path: '/{plaine}/{uuid}/edit', name: 'mercredi_parent_plaine_presence_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Plaine $plaine,
        Enfant $enfant
    ): Response {

        if (($hasTuteur = $this->hasTuteur()) !== null) {
            return $hasTuteur;
        }

        if ($this->plaineHandler->isRegistrationFinalized($plaine, $this->tuteur)) {
            if ($this->factureRepository->findByTuteurAndPlaine($this->tuteur, $plaine)) {
                $this->addFlash('danger', 'Les présences ne peuvent plus être modifiées si une facture a été générée');

                return $this->redirectToRoute(
                    'mercredi_parent_plaine_show',
                    [
                        'id' => $plaine->getId(),
                    ]
                );
            }
        }

        $jours = $plaine->getJours();
        $plainePresencesDto = new PlainePresencesDto($plaine, $enfant, $jours);
        $presences = $this->plainePresenceRepository->findByPlaineAndEnfant($plaine, $enfant);
        $currentJours = PresenceUtils::extractJours($presences);
        $plainePresencesDto->setJours($currentJours);
        $form = $this->createForm(PlainePresencesEditType::class, $plainePresencesDto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $new = $plainePresencesDto->getJours();
            $this->plaineHandler->handleEditPresences($this->tuteur, $enfant, $currentJours, $new);
            $this->addFlash('success', 'Les présences ont bien été modifiées');

            return $this->redirectToRoute(
                'mercredi_parent_plaine_show',
                [
                    'id' => $plaine->getId(),
                ]
            );
        }

        return $this->render(
            '@AcMarcheMercrediParent/plaine/presences_edit.html.twig',
            [
                'plaine' => $plaine,
                'enfant' => $enfant,
                'form' => $form->createView(),
            ]
        );
    }
}