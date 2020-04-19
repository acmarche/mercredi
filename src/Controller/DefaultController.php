<?php

namespace AcMarche\Mercredi\Controller;

use AcMarche\Mercredi\Entity\Article;
use AcMarche\Mercredi\Repository\ArticleRepository;
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
    /**
     * @var ArticleRepository
     */
    private $articleRepository;

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
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
    public function book(Request $request, Article $article)
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
