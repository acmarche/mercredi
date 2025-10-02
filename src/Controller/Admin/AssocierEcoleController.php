<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Ecole\Repository\EcoleRepository;
use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\User\Dto\AssociateUserEcoleDto;
use AcMarche\Mercredi\User\Form\AssociateEcoleType;
use AcMarche\Mercredi\User\Handler\AssociationEcoleHandler;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/security/associer/ecole')]
#[IsGranted('ROLE_MERCREDI_ADMIN')]
final class AssocierEcoleController extends AbstractController
{
    public function __construct(
        private AssociationEcoleHandler $associationEcoleHandler,
        private EcoleRepository $ecoleRepository,
    ) {}

    #[Route(path: '/{id}', name: 'mercredi_user_associate_ecole', methods: ['GET', 'POST'])]
    public function associate(Request $request, User $user): Response
    {
        if (!$user->isEcole()) {
            $this->addFlash('danger', 'Le compte n\'a pas le rôle de école');

            return $this->redirectToRoute('mercredi_admin_user_show', [
                'id' => $user->getId(),
            ]);
        }
        $associateUserEcoleDto = new AssociateUserEcoleDto($user);
        $form = $this->createForm(AssociateEcoleType::class, $associateUserEcoleDto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->associationEcoleHandler->handleAssociateEcole($associateUserEcoleDto);

            return $this->redirectToRoute('mercredi_admin_user_show', [
                'id' => $user->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/user/associer_ecole.html.twig',
            [
                'user' => $user,
                'form' => $form,
            ],
        );
    }

    #[Route(path: '/{id}', name: 'mercredi_user_dissociate_ecole', methods: ['POST'])]
    public function dissociate(Request $request, User $user): RedirectResponse
    {
        if ($this->isCsrfTokenValid('dissociate'.$user->getId(), $request->request->get('_token'))) {
            $ecoleId = (int)$request->request->get('tuteur');
            if (0 === $ecoleId) {
                $this->addFlash('danger', 'L\'école n\'a pas été trouvée');

                return $this->redirectToRoute('mercredi_admin_user_show', [
                    'id' => $user->getId(),
                ]);
            }

            $ecole = $this->ecoleRepository->find($ecoleId);
            $this->associationEcoleHandler->handleDissociateEcole($user, $ecole);
        }

        return $this->redirectToRoute('mercredi_admin_user_show', [
            'id' => $user->getId(),
        ]);
    }
}
