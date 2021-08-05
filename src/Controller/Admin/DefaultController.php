<?php

namespace AcMarche\Mercredi\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController.
 *
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
final class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="mercredi_admin_home")
     */
    public function default(): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/default/index.html.twig',
            [
            ]
        );
    }
}
