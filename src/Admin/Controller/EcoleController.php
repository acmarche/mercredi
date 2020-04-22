<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Ecole\Message\EcoleCreated;
use AcMarche\Mercredi\Ecole\Message\EcoleDeleted;
use AcMarche\Mercredi\Ecole\Message\EcoleUpdated;
use AcMarche\Mercredi\Entity\Ecole;
use AcMarche\Mercredi\Ecole\Form\EcoleType;
use AcMarche\Mercredi\Ecole\Repository\EcoleRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/ecole")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
class EcoleController extends AbstractController
{
    /**
     * @var EcoleRepository
     */
    private $ecoleRepository;

    public function __construct(EcoleRepository $ecoleRepository)
    {
        $this->ecoleRepository = $ecoleRepository;
    }

    /**
     * @Route("/", name="admin_mercredi_ecole_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/ecole/index.html.twig',
            [
                'ecoles' => $this->ecoleRepository->findAll(),
            ]
        );
    }

    /**
     * @Route("/new", name="admin_mercredi_ecole_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $ecole = new Ecole();
        $form = $this->createForm(EcoleType::class, $ecole);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->ecoleRepository->persist($ecole);
            $this->ecoleRepository->flush();

            $this->dispatchMessage(new EcoleCreated($ecole->getId()));

            return $this->redirectToRoute('admin_mercredi_ecole_show', ['id' => $ecole->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/ecole/new.html.twig',
            [
                'ecole' => $ecole,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="admin_mercredi_ecole_show", methods={"GET"})
     */
    public function show(Ecole $ecole): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/ecole/show.html.twig',
            [
                'ecole' => $ecole,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="admin_mercredi_ecole_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Ecole $ecole): Response
    {
        $form = $this->createForm(EcoleType::class, $ecole);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->ecoleRepository->flush();

            $this->dispatchMessage(new EcoleUpdated($ecole->getId()));

            return $this->redirectToRoute('admin_mercredi_ecole_show', ['id' => $ecole->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/ecole/edit.html.twig',
            [
                'ecole' => $ecole,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="admin_mercredi_ecole_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Ecole $ecole): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ecole->getId(), $request->request->get('_token'))) {
            $this->ecoleRepository->remove($ecole);
            $this->ecoleRepository->flush();
            $this->dispatchMessage(new EcoleDeleted($ecole->getId()));
        }

        return $this->redirectToRoute('admin_mercredi_ecole_index');
    }
}
