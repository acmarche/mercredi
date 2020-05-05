<?php

namespace AcMarche\Mercredi\Admin\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 * @package AcMarche\Mercredi\Controller
 *
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 * @Route("/message")
 */
class MessageController extends AbstractController
{
    public function __construct()
    {
    }

    /**
     * @Route("/jour", name="mercredi_message_new_jour")
     */
    public function default(Request $request): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/default/index.html.twig',
            [
            ]
        );
    }

    /**
     * @Route("/groupe", name="mercredi_message_new_groupescolaire")
     */
    public function groupeScolaire(Request $request): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/default/index.html.twig',
            [
            ]
        );
    }

}
