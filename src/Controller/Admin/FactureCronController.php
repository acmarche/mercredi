<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Entity\Facture\FactureCron;
use AcMarche\Mercredi\Facture\Form\FactureCronType;
use AcMarche\Mercredi\Facture\Repository\FactureCronRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[IsGranted('ROLE_MERCREDI_ADMIN')]
#[Route(path: '/cron/admin')]
final class FactureCronController extends AbstractController
{
    public function __construct(
        private FactureCronRepository $factureCronRepository,
    ) {}

    #[Route(path: '/', name: 'mercredi_admin_facture_cron_index', methods: ['GET', 'POST'])]
    public function index(): Response
    {
        $crons = $this->factureCronRepository->findAllOrdered();

        return $this->render(
            '@AcMarcheMercrediAdmin/facture_cron/index.html.twig',
            [
                'crons' => $crons,
            ],
        );
    }

    #[Route(path: '/{id}/show', name: 'mercredi_admin_facture_cron_show', methods: ['GET'])]
    public function show(FactureCron $factureCron): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/facture_cron/show.html.twig',
            [
                'factureCron' => $factureCron,
            ],
        );
    }

    #[Route(path: '/{id}/edit', name: 'mercredi_admin_facture_cron_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, FactureCron $factureCron): Response
    {
        $form = $this->createForm(FactureCronType::class, $factureCron);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->factureCronRepository->flush();
            $this->addFlash('success', 'Sauvegardé');

            return $this->redirectToRoute('mercredi_admin_facture_cron_show', ['id' => $factureCron->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture_cron/edit.html.twig',
            [
                'factureCron' => $factureCron,
                'form' => $form,
            ],
        );
    }

    #[Route(path: '/{id}/delete', name: 'mercredi_admin_facture_cron_delete', methods: ['POST'])]
    public function delete(Request $request, FactureCron $factureCron): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$factureCron->getId(), $request->request->get('_token'))) {
            $this->factureCronRepository->remove($factureCron);
            $this->factureCronRepository->flush();
        }

        return $this->redirectToRoute('mercredi_admin_facture_cron_index', [

        ]);
    }


}
