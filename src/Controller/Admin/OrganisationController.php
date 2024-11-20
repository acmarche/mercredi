<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Entity\Organisation;
use AcMarche\Mercredi\Organisation\Form\OrganisationType;
use AcMarche\Mercredi\Organisation\Message\OrganisationCreated;
use AcMarche\Mercredi\Organisation\Message\OrganisationDeleted;
use AcMarche\Mercredi\Organisation\Message\OrganisationUpdated;
use AcMarche\Mercredi\Organisation\Repository\OrganisationRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/organisation')]
#[IsGranted('ROLE_MERCREDI_ADMIN')]
final class OrganisationController extends AbstractController
{
    public function __construct(
        private OrganisationRepository $organisationRepository,
        private MessageBusInterface $dispatcher,
    ) {}

    #[Route(path: '/', name: 'mercredi_admin_organisation_index', methods: ['GET'])]
    public function index(): Response
    {
        if (null !== ($organisation = $this->organisationRepository->getOrganisation())) {
            return $this->redirectToRoute('mercredi_admin_organisation_show', [
                'id' => $organisation->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/organisation/index.html.twig',
            [
                'organisation' => $organisation,
            ],
        );
    }

    #[Route(path: '/new', name: 'mercredi_admin_organisation_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        if (null !== ($organisation = $this->organisationRepository->getOrganisation())) {
            $this->addFlash('danger', 'Une seule organisation peut être enregistrée');

            return $this->redirectToRoute('mercredi_admin_organisation_show', [
                'id' => $organisation->getId(),
            ]);
        }
        $organisation = new Organisation();
        $form = $this->createForm(OrganisationType::class, $organisation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->organisationRepository->persist($organisation);
            $this->organisationRepository->flush();

            $this->dispatcher->dispatch(new OrganisationCreated($organisation->getId()));

            return $this->redirectToRoute('mercredi_admin_organisation_show', [
                'id' => $organisation->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/organisation/new.html.twig',
            [
                'organisation' => $organisation,
                'form' => $form->createView(),
            ],
        );
    }

    #[Route(path: '/{id}', name: 'mercredi_admin_organisation_show', methods: ['GET'])]
    public function show(Organisation $organisation): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/organisation/show.html.twig',
            [
                'organisation' => $organisation,
            ],
        );
    }

    #[Route(path: '/{id}/edit', name: 'mercredi_admin_organisation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Organisation $organisation): Response
    {
        $form = $this->createForm(OrganisationType::class, $organisation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->organisationRepository->flush();

            $this->dispatcher->dispatch(new OrganisationUpdated($organisation->getId()));

            return $this->redirectToRoute('mercredi_admin_organisation_show', [
                'id' => $organisation->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/organisation/edit.html.twig',
            [
                'organisation' => $organisation,
                'form' => $form->createView(),
            ],
        );
    }

    #[Route(path: '/{id}/delete', name: 'mercredi_admin_organisation_delete', methods: ['POST'])]
    public function delete(Request $request, Organisation $organisation): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$organisation->getId(), $request->request->get('_token'))) {
            $id = $organisation->getId();
            $this->organisationRepository->remove($organisation);
            $this->organisationRepository->flush();
            $this->dispatcher->dispatch(new OrganisationDeleted($id));
        }

        return $this->redirectToRoute('mercredi_admin_organisation_index');
    }
}
