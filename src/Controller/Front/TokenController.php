<?php

namespace AcMarche\Mercredi\Controller\Front;

use AcMarche\Mercredi\Entity\Security\Token;
use AcMarche\Mercredi\Security\Token\TokenManager;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TokenController extends AbstractController
{
    public function __construct(private TokenManager $tokenManager)
    {
    }

    #[Route(path: '/token/', name: 'mercredi_token_generate_for_all')]
    #[IsGranted('ROLE_MERCREDI_ADMIN')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(EmptyType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->tokenManager->createForAllUsers();
            $this->addFlash('success', 'Token créé pour tout le monde');
        }

        return $this->render(
            '@AcMarcheMercredi/user/token.html.twig',
            [
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/token/{value}', name: 'mercredi_security_autologin')]
    public function show(
        Request $request,
        #[MapEntity(expr: 'repository.findOneByValue(value)')] Token $token
    ): RedirectResponse {
        if ($this->tokenManager->isExpired($token)) {
            $this->addFlash('error', 'Cette url a expirée');

            return $this->redirectToRoute('mercredi_front_home');
        }

        $user = $token->getUser();
        $this->tokenManager->loginUser($request, $user, 'main');

        return $this->redirectToRoute('mercredi_front_profile_redirect');
    }
}
