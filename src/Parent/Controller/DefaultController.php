<?php

namespace AcMarche\Mercredi\Parent\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController.
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
