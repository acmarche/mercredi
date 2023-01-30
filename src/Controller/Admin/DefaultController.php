<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Search\Form\SearchNameType;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[IsGranted('ROLE_MERCREDI_ADMIN')]
final class DefaultController extends AbstractController
{
    #[Route(path: '/', name: 'mercredi_admin_home')]
    public function default(): Response
    {
        $form = $this->createForm(SearchNameType::class);

        return $this->render(
            '@AcMarcheMercrediAdmin/default/index.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
