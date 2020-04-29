<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Jour\Message\JourCreated;
use AcMarche\Mercredi\Jour\Message\JourDeleted;
use AcMarche\Mercredi\Jour\Message\JourUpdated;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Jour\Form\JourType;
use AcMarche\Mercredi\Jour\Repository\JourRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/jour")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
class JourController extends AbstractController
{
    /**
     * @var JourRepository
     */
    private $jourRepository;

    public function __construct(JourRepository $jourRepository)
    {
        $this->jourRepository = $jourRepository;
    }

    /**
     * @Route("/", name="mercredi_admin_jour_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/jour/index.html.twig',
            [
                'jours' => $this->jourRepository->findActifs(),
            ]
        );
    }

    /**
     * @Route("/new", name="mercredi_admin_jour_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $jour = new Jour();
        $form = $this->createForm(JourType::class, $jour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->jourRepository->persist($jour);
            $this->jourRepository->flush();

            $this->dispatchMessage(new JourCreated($jour->getId()));

            return $this->redirectToRoute('mercredi_admin_jour_show', ['id' => $jour->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/jour/new.html.twig',
            [
                'jour' => $jour,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="mercredi_admin_jour_show", methods={"GET"})
     */
    public function show(Jour $jour): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/jour/show.html.twig',
            [
                'jour' => $jour,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="mercredi_admin_jour_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Jour $jour): Response
    {
        $form = $this->createForm(JourType::class, $jour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->jourRepository->flush();

            $this->dispatchMessage(new JourUpdated($jour->getId()));

            return $this->redirectToRoute('mercredi_admin_jour_show', ['id' => $jour->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/jour/edit.html.twig',
            [
                'jour' => $jour,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="mercredi_admin_jour_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Jour $jour): Response
    {
        if ($this->isCsrfTokenValid('delete'.$jour->getId(), $request->request->get('_token'))) {
            $jourId = $jour->getId();
            $this->jourRepository->remove($jour);
            $this->jourRepository->flush();
            $this->dispatchMessage(new JourDeleted($jourId));
        }

        return $this->redirectToRoute('mercredi_admin_jour_index');
    }
}
