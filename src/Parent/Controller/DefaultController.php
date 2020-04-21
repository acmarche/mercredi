<?php

namespace AcMarche\Mercredi\Parent\Controller;

use AcMarche\Mercredi\Entity\Enfant;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 * @package AcMarche\Mercredi\Controller
 *
 * IsGranted("ROLE_ADMINISTRATOR")
 */
class DefaultController extends AbstractController
{


    public function __construct()
    {

    }


    /**
     * @Route("/", name="mercredi_parent_home")
     */
    public function default(Request $request)
    {
        return $this->render(
            '@AcMarcheMercrediParent/default/index.html.twig',
            [
            ]
        );
    }

}
