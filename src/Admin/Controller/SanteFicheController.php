<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Sante\Form\SanteFicheType;
use AcMarche\Mercredi\Sante\Handler\SanteHandler;
use AcMarche\Mercredi\Sante\Message\SanteFicheDeleted;
use AcMarche\Mercredi\Sante\Message\SanteFicheUpdated;
use AcMarche\Mercredi\Entity\Sante\SanteFiche;
use AcMarche\Mercredi\Sante\Repository\SanteFicheRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/santeFiche")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
class SanteFicheController extends AbstractController
{
    /**
     * @var SanteFicheRepository
     */
    private $santeFicheRepository;
    /**
     * @var SanteHandler
     */
    private $santeHandler;

    public function __construct(SanteFicheRepository $santeFicheRepository, SanteHandler $santeHandler)
    {
        $this->santeFicheRepository = $santeFicheRepository;
        $this->santeHandler = $santeHandler;
    }

    /**
     * @Route("/{id}", name="mercredi_admin_sante_fiche_show", methods={"GET"})
     */
    public function show(Enfant $enfant): Response
    {
        $santeFiche = $this->santeFicheRepository->findByEnfant($enfant);

        if (!$santeFiche) {
            $this->addFlash('warning', 'Cette enfant n\'a pas encore de fiche santé');

            return $this->redirectToRoute('mercredi_admin_sante_fiche_edit', ['id' => $enfant->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/sante_fiche/show.html.twig',
            [
                'enfant' => $enfant,
                'sante_fiche' => $santeFiche,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="mercredi_admin_sante_fiche_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Enfant $enfant): Response
    {
        $santeFiche = $this->santeHandler->init($enfant);

        $form = $this->createForm(SanteFicheType::class, $santeFiche);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $questions = $form->getData()->getQuestions();
            $this->santeHandler->handle($santeFiche, $questions);

            $this->dispatchMessage(new SanteFicheUpdated($santeFiche->getId()));

            return $this->redirectToRoute('mercredi_admin_sante_fiche_show', ['id' => $enfant->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/sante_fiche/edit.html.twig',
            [
                'sante_fiche' => $santeFiche,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="mercredi_admin_sante_fiche_delete", methods={"DELETE"})
     */
    public function delete(Request $request, SanteFiche $santeFiche): Response
    {
        if ($this->isCsrfTokenValid('delete'.$santeFiche->getId(), $request->request->get('_token'))) {
            $id = $santeFiche->getId();
            $enfant = $santeFiche->getEnfant();
            $this->santeFicheRepository->remove($santeFiche);
            $this->santeFicheRepository->flush();
            $this->dispatchMessage(new SanteFicheDeleted($id));
        }

        return $this->redirectToRoute('mercredi_admin_enfant_show', ['id' => $enfant->getId()]);
    }
}
