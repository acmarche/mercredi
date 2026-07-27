<?php

namespace AcMarche\Mercredi\Controller\Front;

use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Security\Token\TokenManager;
use AcMarche\Mercredi\Security\Token\TokenRepository;
use AcMarche\Mercredi\Tuteur\Form\AcceptMigrationType;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/migration')]
final class MigrationController extends AbstractController
{
    public function __construct(
        private TokenRepository $tokenRepository,
        private TuteurRepository $tuteurRepository,
        private TokenManager $tokenManager,
    ) {}

    /**
     * Page d'acceptation de la migration vers la nouvelle plateforme.
     * Le token présent dans l'url connecte automatiquement le parent.
     */
    #[Route(path: '/{value}', name: 'mercredi_front_migration', requirements: ['value' => '[0-9a-f]{40}'], methods: ['GET', 'POST'])]
    public function index(Request $request, string $value): Response
    {
        $token = $this->tokenRepository->findOneByValue($value);

        if (null === $token) {
            $this->addFlash('danger', 'Cette url n\'est pas valide');

            return $this->redirectToRoute('mercredi_front_home');
        }

        if ($this->tokenManager->isExpired($token)) {
            $this->addFlash('danger', 'Cette url a expirée');

            return $this->redirectToRoute('mercredi_front_home');
        }

        $user = $token->getUser();
        $this->autoLogin($request, $user);

        $tuteurs = $user->getTuteurs();

        if (0 === $tuteurs->count()) {
            $this->addFlash('warning', 'Aucun parent n\'est lié à votre compte');

            return $this->redirectToRoute('mercredi_front_home');
        }

        $accepted = true;
        foreach ($tuteurs as $tuteur) {
            if (!$tuteur->isAcceptMigration()) {
                $accepted = false;

                break;
            }
        }

        $form = $this->createForm(AcceptMigrationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($tuteurs as $tuteur) {
                $tuteur->setAcceptMigration(true);
            }
            $this->tuteurRepository->flush();

            $this->addFlash('success', 'Merci, votre accord a bien été enregistré');

            return $this->redirectToRoute('mercredi_front_migration', ['value' => $value]);
        }

        return $this->render(
            '@AcMarcheMercredi/front/migration/index.html.twig',
            [
                'user' => $user,
                'accepted' => $accepted,
                'form' => $form,
            ],
        );
    }

    /**
     * Connecte le parent uniquement s'il n'est pas déjà connecté sous ce compte,
     * pour ne pas régénérer la session à chaque affichage de la page.
     */
    private function autoLogin(Request $request, User $user): void
    {
        $current = $this->getUser();

        if (null !== $current && $current->getUserIdentifier() === $user->getUserIdentifier()) {
            return;
        }

        $this->tokenManager->loginUser($request, $user, 'main');
    }
}
