<?php

namespace AcMarche\Mercredi\Controller\Ecole;

use AcMarche\Mercredi\Ecole\Utils\EcoleUtils;
use AcMarche\Mercredi\Entity\Scolaire\Ecole;
use Symfony\Component\HttpFoundation\Response;

trait GetEcolesTrait
{
    private EcoleUtils $ecoleUtils;

    /**
     * @var Ecole[]
     */
    private iterable  $ecoles;

    /**
     * @required
     */
    public function setEcoleUtils(EcoleUtils $ecoleUtils): void
    {
        $this->ecoleUtils = $ecoleUtils;
    }

    public function hasEcoles(): ?Response
    {
        $user = $this->getUser();
        $this->ecoles = $this->ecoleUtils->getEcolesByUser($user);

        if (!$this->ecoles) {
            return $this->redirectToRoute('mercredi_ecole_nouveau');
        }

        return $this->denyAccessUnlessGranted('ecole_index', null);
    }
}
