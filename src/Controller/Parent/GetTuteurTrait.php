<?php

namespace AcMarche\Mercredi\Controller\Parent;

use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Tuteur\Utils\TuteurUtils;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\Attribute\Required;

trait GetTuteurTrait
{
    private TuteurUtils $tuteurUtils;
    private ?Tuteur $tuteur = null;
    private RequestStack $requestStack;

    #[Required]
    public function setTuteurUtils(TuteurUtils $tuteurUtils): void
    {
        $this->tuteurUtils = $tuteurUtils;
    }

    public function hasTuteur(): ?Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('mercredi_parent_nouveau');
        }

        if (!$this->tuteur = $this->tuteurUtils->getTuteurByUser($user)) {
            return $this->redirectToRoute('mercredi_parent_nouveau');
        }

        return null;
    }
}
