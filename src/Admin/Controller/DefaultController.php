<?php

namespace AcMarche\Mercredi\Admin\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 * @package AcMarche\Mercredi\Controller
 *
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
class DefaultController extends AbstractController
{
    public function __construct()
    {
    }

    /**
     * @Route("/", name="mercredi_admin_home")
     */
    public function default(Request $request)
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/default/index.html.twig',
            [
            ]
        );
    }


}
