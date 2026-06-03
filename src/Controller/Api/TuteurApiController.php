<?php

namespace AcMarche\Mercredi\Controller\Api;

use AcMarche\Mercredi\Entity\Relation;
use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/api')]
final class TuteurApiController
{
    public function __construct(
        private readonly TuteurRepository $tuteurRepository,
        #[Autowire(env: 'MERCREDI_API_TOKEN')]
        private readonly string $apiToken,
    ) {
    }

    /**
     * Liste des tuteurs non archivés avec leurs enfants et comptes utilisateurs liés.
     *
     * Authentification : en-tête `Authorization: Bearer <MERCREDI_API_TOKEN>`.
     */
    #[Route(path: '/tuteurs', name: 'mercredi_api_tuteurs', methods: ['GET'])]
    public function tuteurs(Request $request): JsonResponse
    {
        if (($error = $this->checkToken($request)) instanceof JsonResponse) {
            return $error;
        }

        $tuteurs = $this->tuteurRepository->findNotArchivedWithEnfantsAndUsers();

        $data = array_map(
            fn(Tuteur $tuteur): array => $this->serializeTuteur($tuteur),
            $tuteurs
        );

        return new JsonResponse([
            'count' => \count($data),
            'data' => $data,
        ]);
    }

    private function checkToken(Request $request): ?JsonResponse
    {
        $header = $request->headers->get('Authorization', '');

        if (!preg_match('/^Bearer\s+(.+)$/i', $header, $matches)) {
            return new JsonResponse(
                ['error' => 'Missing or malformed Authorization Bearer token'],
                Response::HTTP_UNAUTHORIZED,
                ['WWW-Authenticate' => 'Bearer']
            );
        }

        if ('' === $this->apiToken || !hash_equals($this->apiToken, $matches[1])) {
            return new JsonResponse(
                ['error' => 'Invalid API token'],
                Response::HTTP_UNAUTHORIZED,
                ['WWW-Authenticate' => 'Bearer']
            );
        }

        return null;
    }

    private function serializeTuteur(Tuteur $tuteur): array
    {
        return [
            'id' => $tuteur->getId(),
            'nom' => $tuteur->getNom(),
            'prenom' => $tuteur->getPrenom(),
            'email' => $tuteur->getEmail(),
            'email_conjoint' => $tuteur->getEmailConjoint(),
            'telephone' => $tuteur->getTelephone(),
            'gsm' => $tuteur->getGsm(),
            'rue' => $tuteur->getRue(),
            'code_postal' => $tuteur->getCodePostal(),
            'localite' => $tuteur->getLocalite(),
            'enfants' => array_map(
                fn(Relation $relation): array => $this->serializeEnfant($relation),
                $tuteur->getRelations()->toArray()
            ),
            'users' => array_map(
                fn(User $user): array => $this->serializeUser($user),
                $tuteur->getUsers()->toArray()
            ),
        ];
    }

    private function serializeEnfant(Relation $relation): array
    {
        $enfant = $relation->getEnfant();

        if (null === $enfant) {
            return [];
        }

        $birthday = $enfant->getBirthday();

        return [
            'id' => $enfant->getId(),
            'nom' => $enfant->getNom(),
            'prenom' => $enfant->getPrenom(),
            'birthday' => $birthday?->format('Y-m-d'),
            'age' => $enfant->getAge(),
            'relation' => $relation->getType(),
        ];
    }

    private function serializeUser(User $user): array
    {
        return [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'enabled' => $user->isEnabled(),
        ];
    }
}
