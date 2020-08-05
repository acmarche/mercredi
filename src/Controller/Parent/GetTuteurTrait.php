<?php

namespace AcMarche\Mercredi\Controller\Parent;

use AcMarche\Mercredi\Entity\Tuteur;
use Symfony\Component\HttpFoundation\Response;

trait GetTuteurTrait
{
    /**
     * @var Tuteur
     */
    private $tuteur;

    /*  public function __construct(TuteurUtils $tuteurUtils, Security $security, UrlGeneratorInterface $urlGenerator)
      {
          $this->tuteurUtils = $tuteurUtils;
          $this->security = $security;
          $this->urlGenerator = $urlGenerator;
      }

      /**
       * @required
       *
      public function setSecurity(Security $security)
      {
          $this->security = $security;
      }*/

    public function hasTuteur(): ?Response
    {
        $user = $this->getUser();
        $this->tuteur = $this->tuteurUtils->getTuteurByUser($user);

        if (! $this->tuteur) {
            return $this->redirectToRoute('mercredi_parent_nouveau');
        }

        return $this->denyAccessUnlessGranted('tuteur_show', $this->tuteur);
    }
}
