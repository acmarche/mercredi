<?php

namespace AcMarche\Mercredi\Controller\Parent;

use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Tuteur\Utils\TuteurUtils;
use Symfony\Component\HttpFoundation\Response;

trait GetTuteurTrait
{
    private TuteurUtils $tuteurUtils;
    private ?Tuteur $tuteur;

    /**
     * @required
     */
    public function setTuteurUtils(TuteurUtils $tuteurUtils): void
    {
        $this->tuteurUtils = $tuteurUtils;
    }

    public function hasTuteur(): ?Response
    {
        $user = $this->getUser();

        if (!$this->tuteur = $this->tuteurUtils->getTuteurByUser($user)) {
            return $this->redirectToRoute('mercredi_parent_nouveau');
        }

        return $this->denyAccessUnlessGranted('tuteur_show', $this->tuteur);
    }
}
