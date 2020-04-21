<?php

namespace AcMarche\Mercredi\Front\Controller;

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
     * @Route("/", name="mercredi_home")
     */
    public function default(Request $request)
    {
        return $this->render(
            '@AcMarcheMercredi/default/index.html.twig',
            [
            ]
        );
    }

    /**
     * @Route("/books/{id}", name="mercredi_push")
     */
    public function book(Request $request, Enfant $article)
    {
        if (isset($request->request->statuts)) {
            $data = json_decode($request->request->statuts);
            $article->setStatus($data);
            $this->articleRepository->flush();
        }


        return $this->render(
            'publish/book.html.twig',
            [
                'article' => $article,
            ]
        );
    }

    /**
     * @Route("/", name="publish")
     */
    public function index()
    {
        return $this->render(
            'publish/index.html.twig',
            [
                'controller_name' => 'PublishController',
            ]
        );
    }
}
