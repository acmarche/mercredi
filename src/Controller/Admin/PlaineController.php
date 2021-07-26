<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Plaine\Form\PlaineOpenType;
use AcMarche\Mercredi\Plaine\Handler\PlaineHandler;
use Symfony\Component\HttpFoundation\Response;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Plaine\PlaineGroupe;
use AcMarche\Mercredi\Plaine\Form\PlaineType;
use AcMarche\Mercredi\Plaine\Message\PlaineCreated;
use AcMarche\Mercredi\Plaine\Message\PlaineDeleted;
use AcMarche\Mercredi\Plaine\Message\PlaineUpdated;
use AcMarche\Mercredi\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Mercredi\Plaine\Repository\PlaineRepository;
use AcMarche\Mercredi\Scolaire\Repository\GroupeScolaireRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/plaine")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
final class PlaineController extends AbstractController
{
    private PlaineRepository $plaineRepository;
    private PlainePresenceRepository $plainePresenceRepository;
    private GroupeScolaireRepository $groupeScolaireRepository;
    private PlaineHandler $plaineHandler;

    public function __construct(
        PlaineRepository $plaineRepository,
        PlainePresenceRepository $plainePresenceRepository,
        GroupeScolaireRepository $groupeScolaireRepository,
        PlaineHandler $plaineHandler
    ) {
        $this->plaineRepository = $plaineRepository;
        $this->plainePresenceRepository = $plainePresenceRepository;
        $this->groupeScolaireRepository = $groupeScolaireRepository;
        $this->plaineHandler = $plaineHandler;
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
        foreach ($this->groupeScolaireRepository->findAllForPlaineOrderByNom() as $groupe) {
            $plaineGroupe = new PlaineGroupe($plaine, $groupe);
            $plaine->addPlaineGroupe($plaineGroupe);
        }

        $form = $this->createForm(PlaineType::class, $plaine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->plaineRepository->persist($plaine);
            $this->plaineRepository->flush();

            $this->dispatchMessage(new PlaineCreated($plaine->getId()));

            return $this->redirectToRoute('mercredi_admin_plaine_jour_edit', ['id' => $plaine->getId()]);
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
        $enfants = $this->plainePresenceRepository->findEnfantsByPlaine($plaine);

        return $this->render(
            '@AcMarcheMercrediAdmin/plaine/show.html.twig',
            [
                'plaine' => $plaine,
                'enfants' => $enfants,
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
     * @Route("/{id}/open", name="mercredi_admin_plaine_open", methods={"GET","POST"})
     */
    public function open(Request $request, Plaine $plaine): Response
    {
        $form = $this->createForm(PlaineOpenType::class, $plaine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$plaineOpen = $this->plaineHandler->handleOpeningRegistrations($plaine)) {
                $this->plaineRepository->flush();
                $this->dispatchMessage(new PlaineUpdated($plaine->getId()));
            } else {
                $this->addFlash(
                    'danger',
                    'Les inscriptions n\'ont pas pu être ouvrir car la plaine '.$plaineOpen->getNom(
                    ).' est toujours ouverte'
                );
            }

            return $this->redirectToRoute('mercredi_admin_plaine_show', ['id' => $plaine->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/plaine/open.html.twig',
            [
                'plaine' => $plaine,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}/delete", name="mercredi_admin_plaine_delete", methods={"POST"})
     */
    public function delete(Request $request, Plaine $plaine): Response
    {
        if ($this->isCsrfTokenValid('delete'.$plaine->getId(), $request->request->get('_token'))) {
            $plaineId = $plaine->getId();
            $this->plaineRepository->remove($plaine);
            $this->plaineRepository->flush();
            $this->dispatchMessage(new PlaineDeleted($plaineId));
        }

        return $this->redirectToRoute('mercredi_admin_plaine_index');
    }
}
