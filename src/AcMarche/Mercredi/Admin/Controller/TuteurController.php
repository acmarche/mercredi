<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Entity\EnfantTuteur;
use AcMarche\Mercredi\Admin\Entity\Paiement;
use AcMarche\Mercredi\Admin\Entity\Presence;
use AcMarche\Mercredi\Admin\Entity\Tuteur;
use AcMarche\Mercredi\Admin\Events\TuteurEvent;
use AcMarche\Mercredi\Admin\Form\Search\SearchTuteurType;
use AcMarche\Mercredi\Admin\Form\Tuteur\TuteurSetEnfantType;
use AcMarche\Mercredi\Admin\Form\Tuteur\TuteurType;
use AcMarche\Mercredi\Admin\Repository\TuteurRepository;
use AcMarche\Mercredi\Admin\Service\FacturePlaine;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Tuteur controller.
 *
 * @Route("/tuteur")
 * @IsGranted("ROLE_MERCREDI_READ")
 */
class TuteurController extends AbstractController
{
    /**
     * @var TuteurRepository
     */
    private $tuteurRepository;
    /**
     * @var FacturePlaine
     */
    private $facturePlaine;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(
        TuteurRepository $tuteurRepository,
        FacturePlaine $facturePlaine,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->tuteurRepository = $tuteurRepository;
        $this->facturePlaine = $facturePlaine;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Lists all Tuteur entities.
     *
     * @Route("/", name="tuteur", methods={"GET"})
     * @Route("/all/{all}", name="tuteur_all")
     */
    public function index(Request $request, $all = false)
    {
        $session = $request->getSession();

        $data = [];
        $search = false;
        $key = 'tuteur_search';

        if ($session->has($key)) {
            $data = unserialize($session->get($key));
            $search = true;
        }

        $search_form = $this->createForm(
            SearchTuteurType::class,
            $data,
            [
                'action' => $this->generateUrl('tuteur'),
                'method' => 'GET',
            ]
        );

        $search_form->handleRequest($request);

        if ($search_form->isSubmitted() && $search_form->isValid()) {
            $data = $search_form->getData();
            $search = true;
            if ($search_form->get('raz')->isClicked()) {
                $session->remove($key);
                $this->addFlash('success', 'La recherche a bien ??t?? r??initialis??e.');

                return $this->redirectToRoute('tuteur');
            }
        }

        $tuteurs = [];

        if ($search) {
            $session->set($key, serialize($data));
            $tuteurs = $this->tuteurRepository->quickSearch($data);
        } elseif ($all) {
            $tuteurs = $this->tuteurRepository->quickSearch([]);
        }

        $year = date('Y') + 1;
        $years = range(2015, $year);

        return $this->render(
            'admin/tuteur/index.html.twig',
            [
                'search_form' => $search_form->createView(),
                'tuteurs' => $tuteurs,
                'years' => $years,
                'search' => $search,
                'all' => $all,
            ]
        );
    }

    /**
     * Displays a form to create a new Tuteur entity.
     *
     * @Route("/new", name="tuteur_new", methods={"GET","POST"})
     * @Route("/new/{id}", name="tuteur_new_with_enfant", methods={"GET","POST"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     */
    public function new(Request $request, Enfant $enfant = null)
    {
        $em = $this->getDoctrine()->getManager();
        $tuteur = new Tuteur();

        if ($enfant) {
            $tuteur->setEnfant($enfant);
        }

        $form = $this->createForm(TuteurType::class, $tuteur)
            ->add('submit', SubmitType::class, ['label' => 'Create']);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $tuteur->setUserAdd($user);

            $enfant = $tuteur->getEnfant();
            if ($enfant) {
                $enfant_tuteur = new EnfantTuteur();
                $enfant_tuteur->setEnfant($enfant);
                $enfant_tuteur->setTuteur($tuteur);
                $em->persist($enfant_tuteur);

                $tuteur->addEnfant($enfant_tuteur);
            }

            $em->persist($tuteur);
            $em->flush();

            $this->addFlash('success', 'Le parent a bien ??t?? ajout??');

            if ($enfant) {
                return $this->redirectToRoute('enfant_show', ['slugname' => $enfant->getSlugname()]);
            }

            return $this->redirectToRoute('tuteur_show', ['slugname' => $tuteur->getSlugname()]);
        }

        return $this->render(
            'admin/tuteur/new.html.twig',
            [
                'tuteur' => $tuteur,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Finds and displays a Tuteur entity.
     *
     * @Route("/{slugname}", name="tuteur_show", methods={"GET"})
     */
    public function show(Tuteur $tuteur)
    {
        $enfant_tuteur = new EnfantTuteur();
        $enfant_tuteur->setTuteur($tuteur);

        $form_detach = $this->createDetachForm($tuteur->getId());
        $deleteForm = $this->createDeleteForm($tuteur->getId());
        $form_attach = $this->createAttachForm($enfant_tuteur);

        $tuteurEnfants = $this->facturePlaine->traitement($tuteur);
        $year = date('Y') + 1;
        $years = range(2015, $year);

        $this->eventDispatcher->dispatch(new TuteurEvent($tuteur), TuteurEvent::TUTEUR_EDIT);

        return $this->render(
            'admin/tuteur/show.html.twig',
            [
                'tuteur' => $tuteur,
                'years' => $years,
                'tuteurenfants' => $tuteurEnfants,
                'delete_form' => $deleteForm->createView(),
                'form_detach' => $form_detach->createView(),
                'form_attach' => $form_attach->createView(),
            ]
        );
    }

    /**
     * Creates a form to detach a Enfant entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDetachForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('enfant_detach', ['id' => $id]))
            ->setMethod('POST')
            ->add('tuteur_enfant_id', HiddenType::class)
            ->add('submit', SubmitType::class, ['label' => 'D??tacher', 'attr' => ['class' => 'btn-warning']])
            ->getForm();
    }

    /**
     * Creates a form to delete a Tuteur entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('tuteur_delete', ['id' => $id]))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, ['label' => 'Delete', 'attr' => ['class' => 'btn btn-danger']])
            ->getForm();
    }

    /**
     * Creates a form to create a Enfant entity.
     *
     * @param Enfant $entity The entity
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createAttachForm(EnfantTuteur $enfant_tuteur)
    {
        $tuteur = $enfant_tuteur->getTuteur();
        $form = $this->createForm(
            TuteurSetEnfantType::class,
            $enfant_tuteur,
            [
                'action' => $this->generateUrl('enfant_attach', ['id' => $tuteur->getId()]),
                'method' => 'POST',
            ]
        );

        $form->add(
            'submit',
            SubmitType::class,
            ['label' => 'D??finir comme enfant', 'attr' => ['class' => 'btn-primary']]
        );

        return $form;
    }

    /**
     * Displays a form to edit an existing Tuteur entity.
     *
     * @Route("/{slugname}/edit", name="tuteur_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     */
    public function edit(Request $request, Tuteur $tuteur)
    {
        $editForm = $form = $this->createForm(TuteurType::class, $tuteur)
            ->add('submit', SubmitType::class, ['label' => 'Update']);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('success', 'Le parent a bien ??t?? mis ?? jour');

            return $this->redirectToRoute('tuteur_show', ['slugname' => $tuteur->getSlugname()]);
        }

        $this->eventDispatcher->dispatch(new TuteurEvent($tuteur), TuteurEvent::TUTEUR_EDIT);

        return $this->render(
            'admin/tuteur/edit.html.twig',
            [
                'tuteur' => $tuteur,
                'edit_form' => $editForm->createView(),
            ]
        );
    }

    /**
     * Deletes a Tuteur entity.
     *
     * @Route("/{id}", name="tuteur_delete", methods={"DELETE"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     */
    public function delete(Request $request, Tuteur $tuteur)
    {
        $form = $this->createDeleteForm($tuteur->getId());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($tuteur);
            $em->flush();

            $this->addFlash('success', 'Le parent a bien ??t?? supprim??');
        }

        return $this->redirectToRoute('tuteur');
    }

    /**
     * Detacher un enfant de son tuteur.
     *
     * @Route("/detach/{id}", name="enfant_detach", methods={"POST"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     */
    public function detach(Request $request, Tuteur $tuteur)
    {
        $form = $this->createDetachForm($tuteur->getId());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $tuteur_enfant_id = isset($data['tuteur_enfant_id']) ? $data['tuteur_enfant_id'] : 0;

            $em = $this->getDoctrine()->getManager();
            $enfant_tuteur = $em->getRepository(EnfantTuteur::class)->find($tuteur_enfant_id);

            if (!$enfant_tuteur) {
                throw $this->createNotFoundException("La relation n'a pas ??t?? trouv??e.");
            }

            $tuteur = $enfant_tuteur->getTuteur();
            /**
             * je retire les presences lies.
             */
            $presences = $em->getRepository(Presence::class)->getByEnfantTuteur($enfant_tuteur);

            foreach ($presences as $presence) {
                $em->remove($presence);
            }
            /**
             * je retire les paiements lies.
             */
            $paiements = $em->getRepository(Paiement::class)->getByEnfantTuteur($enfant_tuteur);
            foreach ($paiements as $paiement) {
                $em->remove($paiement);
            }

            $em->remove($enfant_tuteur);

            $em->flush();

            $this->addFlash('success', "L'enfant a bien ??t?? d??tach??");

            return $this->redirectToRoute('tuteur_show', ['slugname' => $tuteur->getSlugname()]);
        }

        return $this->render(
            'admin/tuteur/detacher.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Attach un enfant a un tuteur.
     *
     * @Route("/attach/{id}", name="enfant_attach", methods={"POST"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     */
    public function Attach(Request $request, Tuteur $tuteur)
    {
        $em = $this->getDoctrine()->getManager();

        $enfant_tuteur = new EnfantTuteur();

        $enfant_tuteur->setTuteur($tuteur);

        $form_attach = $this->createAttachForm($enfant_tuteur);

        $form_attach->handleRequest($request);

        if ($form_attach->isValid()) {
            $enfant = $enfant_tuteur->getEnfant();
            if (!$enfant) {
                $this->addFlash('danger', "L'enfant s??lectionn?? n'a pas ??t?? trouv??");
            } else {
                $em->persist($enfant_tuteur);

                $em->flush();

                $this->addFlash('success', "L'enfant a bien ??t?? associ??");
            }

            return $this->redirectToRoute('tuteur_show', ['slugname' => $tuteur->getSlugname()]);
        }

        $form_detach = $this->createDetachForm($tuteur->getId());

        $tuteurs = $fratries = [];

        return $this->render(
            'admin/tuteur/add_enfant.html.twig',
            [
                'tuteur' => $tuteur,
                'tuteurs' => $tuteurs,
                'fratries' => $fratries,
                'form' => $form_detach->createView(),
                'form_attach' => $form_attach->createView(),
            ]
        );
    }
}
