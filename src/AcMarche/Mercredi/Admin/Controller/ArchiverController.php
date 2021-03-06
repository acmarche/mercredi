<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Entity\Jour;
use AcMarche\Mercredi\Plaine\Entity\Plaine;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Archiver controller.
 *
 * @Route("/archiver")
 * @IsGranted("ROLE_MERCREDI_READ")
 */
class ArchiverController extends AbstractController
{
    /**
     * Archive a enfant.
     *
     * @Route("/enfant/{slugname}", name="enfant_archiver", methods={"GET","POST"})
     */
    public function enfant(Request $request, Enfant $enfant)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createArchiveEnfant($enfant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $label = $enfant->getArchive() ? 'désarchivé' : 'archivé';

            if ($enfant->getArchive()) {
                $enfant->setArchive(false);
            } else {
                $enfant->setArchive(true);
            }

            $em->persist($enfant);
            $em->flush();

            $this->addFlash('success', "L'enfant a bien été $label");

            return $this->redirectToRoute('enfant_show', ['slugname' => $enfant->getSlugname()]);
        }

        return $this->render('admin/archiver/enfant.html.twig', [
            'entity' => $enfant,
            'form' => $form->createView(),
        ]);
    }

    protected function createArchiveEnfant(Enfant $enfant)
    {
        $label = $enfant->getArchive() ? 'Désarchiver' : 'Archiver';

        return $this->createFormBuilder()
            ->setAction($this->generateUrl('enfant_archiver', ['slugname' => $enfant->getSlugname()]))
            ->setMethod('POST')
            ->add('submit', SubmitType::class, ['label' => $label, 'attr' => ['class' => 'btn-success']])
            ->getForm();
    }

    /**
     * Archive a jour.
     *
     * @Route("/jour/{id}", name="jour_archiver", methods={"GET","POST"})
     */
    public function jour(Request $request, Jour $jour)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createArchiveJour($jour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $label = $jour->getArchive() ? 'désarchivé' : 'archivé';

            if ($jour->getArchive()) {
                $jour->setArchive(false);
            } else {
                $jour->setArchive(true);
            }

            $em->persist($jour);
            $em->flush();

            $this->addFlash('success', "Le jour de garde a bien été $label");

            return $this->redirectToRoute('jour_show', ['id' => $jour->getId()]);
        }

        return $this->render('admin/archiver/jour.html.twig', [
            'entity' => $jour,
            'form' => $form->createView(),
        ]);
    }

    protected function createArchiveJour(Jour $jour)
    {
        $label = $jour->getArchive() ? 'Désarchiver' : 'Archiver';

        return $this->createFormBuilder()
            ->setAction($this->generateUrl('jour_archiver', ['id' => $jour->getId()]))
            ->setMethod('POST')
            ->add('submit', SubmitType::class, ['label' => $label, 'attr' => ['class' => 'btn-success']])
            ->getForm();
    }

    /**
     * Archive a plaine.
     *
     * @Route("/plaine/{slugname}", name="plaine_archiver", methods={"GET","POST"})
     */
    public function plaine(Request $request, Plaine $plaine)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createArchivePlaine($plaine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $label = $plaine->isArchive() ? 'désarchivé' : 'archivé';

            if ($plaine->isArchive()) {
                $plaine->setArchive(false);
            } else {
                $plaine->setArchive(true);
            }

            $em->persist($plaine);
            $em->flush();

            $this->addFlash('success', "La plaine a bien été $label");

            return $this->redirectToRoute('plaine_show', ['slugname' => $plaine->getSlugname()]);
        }

        return $this->render('admin/archiver/plaine.html.twig', [
            'entity' => $plaine,
            'form' => $form->createView(),
        ]);
    }

    protected function createArchivePlaine(Plaine $plaine)
    {
        $label = $plaine->isArchive() ? 'Désarchiver' : 'Archiver';

        return $this->createFormBuilder()
            ->setAction($this->generateUrl('plaine_archiver', ['slugname' => $plaine->getSlugname()]))
            ->setMethod('POST')
            ->add('submit', SubmitType::class, ['label' => $label, 'attr' => ['class' => 'btn-success']])
            ->getForm();
    }
}
