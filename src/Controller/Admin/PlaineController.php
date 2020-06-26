<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Plaine\Form\PlaineType;
use AcMarche\Mercredi\Plaine\Message\PlaineCreated;
use AcMarche\Mercredi\Plaine\Message\PlaineDeleted;
use AcMarche\Mercredi\Plaine\Message\PlaineUpdated;
use AcMarche\Mercredi\Plaine\Repository\PlaineRepository;
use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\Plaine;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/plaine")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
class PlaineController extends AbstractController
{
    /**
     * @var PlaineRepository
     */
    private $plaineRepository;
    /**
     * @var EnfantRepository
     */
    private $enfantRepository;

    public function __construct(PlaineRepository $plaineRepository, EnfantRepository $enfantRepository)
    {
        $this->plaineRepository = $plaineRepository;
        $this->enfantRepository = $enfantRepository;
    }

    /**
     * @Route("/", name="mercredi_admin_plaine_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/plaine/index.html.twig',
            [
                'plaines' => $this->plaineRepository->findAll(),
            ]
        );
    }

    /**
     * @Route("/new", name="mercredi_admin_plaine_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $plaine = new Plaine();
        $form = $this->createForm(PlaineType::class, $plaine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->plaineRepository->persist($plaine);
            $this->plaineRepository->flush();

            $this->dispatchMessage(new PlaineCreated($plaine->getId()));

            return $this->redirectToRoute('mercredi_admin_plaine_show', ['id' => $plaine->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/plaine/new.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="mercredi_admin_plaine_show", methods={"GET"})
     */
    public function show(Plaine $plaine): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/plaine/show.html.twig',
            [
                'plaine' => $plaine,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="mercredi_admin_plaine_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Plaine $plaine): Response
    {
        $form = $this->createForm(PlaineType::class, $plaine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->plaineRepository->flush();

            $this->dispatchMessage(new PlaineUpdated($plaine->getId()));

            return $this->redirectToRoute('mercredi_admin_plaine_show', ['id' => $plaine->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/plaine/edit.html.twig',
            [
                'plaine' => $plaine,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="mercredi_admin_plaine_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Plaine $plaine): Response
    {
        if ($this->isCsrfTokenValid('delete'.$plaine->getId(), $request->request->get('_token'))) {
            if (count($this->enfantRepository->findBy(['plaine' => $plaine])) > 0) {
                $this->addFlash('danger', 'La plaine contient des enfants et ne peut être supprimée');

                return $this->redirectToRoute('mercredi_admin_plaine_show', ['id' => $plaine->getId()]);
            }
            $plaineId = $plaine->getId();
            $this->plaineRepository->remove($plaine);
            $this->plaineRepository->flush();
            $this->dispatchMessage(new PlaineDeleted($plaineId));
        }

        return $this->redirectToRoute('mercredi_admin_plaine_index');
    }
}
