<?php

namespace AcMarche\Mercredi\Controller\Parent;

use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Tuteur\Utils\TuteurUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

trait GetTuteurTrait
{
    /**
     * @var TuteurUtils
     *
     */
    private $tuteurUtils;

    /**
     * @param TuteurUtils $tuteurUtils
     * @required
     */
    public function setTuteurUtils(TuteurUtils $tuteurUtils)
    {
        $this->tuteurUtils = $tuteurUtils;
    }

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

        if (!$this->tuteur) {
            return $this->redirectToRoute('mercredi_parent_nouveau');
        }

        return $this->denyAccessUnlessGranted('tuteur_show', $this->tuteur);
    }
}
