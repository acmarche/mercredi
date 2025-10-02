<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Animateur\Repository\AnimateurRepository;
use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\User\Dto\AssociateUserAnimateurDto;
use AcMarche\Mercredi\User\Form\AssociateAnimateurType;
use AcMarche\Mercredi\User\Handler\AssociationAnimateurHandler;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/security/associer/animateur')]
#[IsGranted('ROLE_MERCREDI_ADMIN')]
final class AssocierAnimateurController extends AbstractController
{
    public function __construct(
        private AssociationAnimateurHandler $associationAnimateurHandler,
        private AnimateurRepository $animateurRepository,
    ) {}

    #[Route(path: '/{id}', name: 'mercredi_user_associate_animateur', methods: ['GET', 'POST'])]
    public function associate(Request $request, User $user): Response
    {
        if (!$user->isAnimateur()) {
            $this->addFlash('danger', 'Le compte n\'a pas le rôle de animateur');

            return $this->redirectToRoute('mercredi_admin_user_show', [
                'id' => $user->getId(),
            ]);
        }
        $associateUserAnimateurDto = new AssociateUserAnimateurDto($user);
        $form = $this->createForm(AssociateAnimateurType::class, $associateUserAnimateurDto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->associationAnimateurHandler->handleAssociateAnimateur($associateUserAnimateurDto);
            } catch (\Exception $exception) {
                $this->addFlash('danger', $exception->getMessage());
            }

            return $this->redirectToRoute('mercredi_admin_user_show', [
                'id' => $user->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/user/associer_animateur.html.twig',
            [
                'user' => $user,
                'form' => $form,
            ],
        );
    }

    #[Route(path: '/{id}', name: 'mercredi_user_dissociate_animateur', methods: ['POST'])]
    public function dissociate(Request $request, User $user): RedirectResponse
    {
        if ($this->isCsrfTokenValid('dissociate'.$user->getId(), $request->request->get('_token'))) {
            $animateurId = (int)$request->request->get('animateur');
            if (0 === $animateurId) {
                $this->addFlash('danger', 'L\'animateur n\'a pas été trouvé');

                return $this->redirectToRoute('mercredi_admin_user_show', [
                    'id' => $user->getId(),
                ]);
            }

            $animateur = $this->animateurRepository->find($animateurId);
            $this->associationAnimateurHandler->handleDissociateAnimateur($user, $animateur);
        }

        return $this->redirectToRoute('mercredi_admin_user_show', [
            'id' => $user->getId(),
        ]);
    }
}
