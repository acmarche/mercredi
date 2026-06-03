<?php

namespace AcMarche\Mercredi\Controller\Api;

use AcMarche\Mercredi\Entity\Relation;
use AcMarche\Mercredi\Entity\Scolaire\AnneeScolaire;
use AcMarche\Mercredi\Entity\Scolaire\Ecole;
use AcMarche\Mercredi\Entity\Scolaire\GroupeScolaire;
use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use DateTimeInterface;
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
            'slug' => $tuteur->getSlug(),
            'nom' => $tuteur->getNom(),
            'prenom' => $tuteur->getPrenom(),
            'sexe' => $tuteur->getSexe(),
            'registre_national' => $tuteur->getRegistreNational(),
            'email' => $tuteur->getEmail(),
            'rue' => $tuteur->getRue(),
            'code_postal' => $tuteur->getCodePostal(),
            'localite' => $tuteur->getLocalite(),
            'telephone' => $tuteur->getTelephone(),
            'telephone_bureau' => $tuteur->getTelephoneBureau(),
            'gsm' => $tuteur->getGsm(),
            'nom_conjoint' => $tuteur->getNomConjoint(),
            'prenom_conjoint' => $tuteur->getPrenomConjoint(),
            'relation_conjoint' => $tuteur->getRelationConjoint(),
            'email_conjoint' => $tuteur->getEmailConjoint(),
            'telephone_conjoint' => $tuteur->getTelephoneConjoint(),
            'telephone_bureau_conjoint' => $tuteur->getTelephoneBureauConjoint(),
            'gsm_conjoint' => $tuteur->getGsmConjoint(),
            'iban' => $tuteur->getIban(),
            'facture_papier' => $tuteur->getFacturePapier(),
            'remarque' => $tuteur->getRemarque(),
            'archived' => $tuteur->isArchived(),
            'user_add' => $tuteur->getUserAdd(),
            'id_old' => $tuteur->getIdOld(),
            'created_at' => $tuteur->getCreatedAt()?->format(DateTimeInterface::ATOM),
            'updated_at' => $tuteur->getUpdatedAt()?->format(DateTimeInterface::ATOM),
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

        return [
            'id' => $enfant->getId(),
            'slug' => $enfant->getSlug(),
            'uuid' => $enfant->getUuid()?->toString(),
            'nom' => $enfant->getNom(),
            'prenom' => $enfant->getPrenom(),
            'sexe' => $enfant->getSexe(),
            'birthday' => $enfant->getBirthday()?->format('Y-m-d'),
            'age' => $enfant->getAge(),
            'registre_national' => $enfant->getRegistreNational(),
            'ordre' => $enfant->getOrdre(),
            'poids' => $enfant->getPoids(),
            'telephones' => $enfant->getTelephones(),
            'photo_name' => $enfant->getPhotoName(),
            'mime' => $enfant->getMime(),
            'photo_autorisation' => $enfant->getPhotoAutorisation(),
            'fiche_sante_is_complete' => $enfant->isFicheSanteIsComplete(),
            'is_accueil_ecole' => $enfant->isAccueilEcole(),
            'remarque' => $enfant->getRemarque(),
            'user_add' => $enfant->getUserAdd(),
            'id_old' => $enfant->getIdOld(),
            'created_at' => $enfant->getCreatedAt()?->format(DateTimeInterface::ATOM),
            'updated_at' => $enfant->getUpdatedAt()?->format(DateTimeInterface::ATOM),
            'ecole' => $this->serializeEcole($enfant->getEcole()),
            'groupe_scolaire' => $this->serializeGroupeScolaire($enfant->getGroupeScolaire()),
            'annee_scolaire' => $this->serializeAnneeScolaire($enfant->getAnneeScolaire()),
            'relation' => $relation->getType(),
        ];
    }

    private function serializeEcole(?Ecole $ecole): ?array
    {
        if (null === $ecole) {
            return null;
        }

        return [
            'id' => $ecole->getId(),
            'nom' => $ecole->getNom(),
            'abreviation' => $ecole->getAbreviation(),
        ];
    }

    private function serializeGroupeScolaire(?GroupeScolaire $groupeScolaire): ?array
    {
        if (null === $groupeScolaire) {
            return null;
        }

        return [
            'id' => $groupeScolaire->getId(),
            'nom' => $groupeScolaire->getNom(),
            'age_minimum' => $groupeScolaire->getAgeMinimum(),
            'age_maximum' => $groupeScolaire->getAgeMaximum(),
        ];
    }

    private function serializeAnneeScolaire(?AnneeScolaire $anneeScolaire): ?array
    {
        if (null === $anneeScolaire) {
            return null;
        }

        return [
            'id' => $anneeScolaire->getId(),
            'nom' => $anneeScolaire->getNom(),
            'ordre' => $anneeScolaire->getOrdre(),
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
