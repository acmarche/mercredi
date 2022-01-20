<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Animateur\Form\AnimateurType;
use AcMarche\Mercredi\Animateur\Form\SearchAnimateurType;
use AcMarche\Mercredi\Animateur\Message\AnimateurCreated;
use AcMarche\Mercredi\Animateur\Message\AnimateurDeleted;
use AcMarche\Mercredi\Animateur\Message\AnimateurUpdated;
use AcMarche\Mercredi\Animateur\Repository\AnimateurRepository;
use AcMarche\Mercredi\Entity\Animateur;
use AcMarche\Mercredi\Search\SearchHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/animateur")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
final class AnimateurController extends AbstractController
{
    private AnimateurRepository $animateurRepository;
    private SearchHelper $searchHelper;

    public function __construct(
        AnimateurRepository $animateurRepository,
        SearchHelper $searchHelper,
        private EventDispatcherInterface $dispatcher
    ) {
        $this->animateurRepository = $animateurRepository;
        $this->searchHelper = $searchHelper;
    }

    /**
     * @Route("/", name="mercredi_admin_animateur_index", methods={"GET", "POST"})
     */
    public function index(Request $request): Response
    {
        $form = $this->createForm(SearchAnimateurType::class);
        $form->handleRequest($request);
        $search = false;
        $animateurs = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->searchHelper->saveSearch(SearchHelper::TUTEUR_LIST, $data);
            $search = true;
            $animateurs = $this->animateurRepository->search($data['nom']);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/animateur/index.html.twig',
            [
                'animateurs' => $animateurs,
                'form' => $form->createView(),
                'search' => $search,
            ]
        );
    }

    /**
     * @Route("/new", name="mercredi_admin_animateur_new", methods={"GET", "POST"})
     */
    public function new(Request $request): Response
    {
        $animateur = new Animateur();
        $form = $this->createForm(AnimateurType::class, $animateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->animateurRepository->persist($animateur);
            $this->animateurRepository->flush();

            $this->dispatcher->dispatch(new AnimateurCreated($animateur->getId()));

            return $this->redirectToRoute('mercredi_admin_animateur_show', ['id' => $animateur->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/animateur/new.html.twig',
            [
                'animateur' => $animateur,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="mercredi_admin_animateur_show", methods={"GET"})
     */
    public function show(Animateur $animateur): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/animateur/show.html.twig',
            [
                'animateur' => $animateur,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="mercredi_admin_animateur_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Animateur $animateur): Response
    {
        $form = $this->createForm(AnimateurType::class, $animateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->animateurRepository->flush();

            $this->dispatcher->dispatch(new AnimateurUpdated($animateur->getId()));

            return $this->redirectToRoute('mercredi_admin_animateur_show', ['id' => $animateur->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/animateur/edit.html.twig',
            [
                'animateur' => $animateur,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * //todo que faire si presence.
     *
     * @Route("/{id}/delete", name="mercredi_admin_animateur_delete", methods={"POST"})
     */
    public function delete(Request $request, Animateur $animateur): Response
    {
        if ($this->isCsrfTokenValid('delete'.$animateur->getId(), $request->request->get('_token'))) {
            $id = $animateur->getId();
            $this->animateurRepository->remove($animateur);
            $this->animateurRepository->flush();
            $this->dispatcher->dispatch(new AnimateurDeleted($id));
        }

        return $this->redirectToRoute('mercredi_admin_animateur_index');
    }
}
