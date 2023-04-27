<?php

namespace AcMarche\Mercredi\Controller\Ecole;

use AcMarche\Mercredi\Ecole\Utils\EcoleUtils;
use AcMarche\Mercredi\Entity\Scolaire\Ecole;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\Attribute\Required;

trait GetEcolesTrait
{
    private EcoleUtils $ecoleUtils;

    /**
     * @var Ecole[]|Collection
     */
    private iterable  $ecoles;

    #[Required]
    public function setEcoleUtils(EcoleUtils $ecoleUtils): void
    {
        $this->ecoleUtils = $ecoleUtils;
    }

    public function hasEcoles(): ?Response
    {
        $user = $this->getUser();
        $this->ecoles = $this->ecoleUtils->getEcolesByUser($user);

        if (! $this->ecoles) {
            return $this->redirectToRoute('mercredi_ecole_nouveau');
        }

        return $this->denyAccessUnlessGranted('ecole_index', null);
    }
}
