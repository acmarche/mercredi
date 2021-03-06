<?php

namespace AcMarche\Mercredi\Plaine\Controller;

use AcMarche\Mercredi\Plaine\Entity\Plaine;
use AcMarche\Mercredi\Plaine\Entity\PlaineEnfant;
use AcMarche\Mercredi\Plaine\Entity\PlaineJour;
use AcMarche\Mercredi\Plaine\Entity\PlainePresence;
use AcMarche\Mercredi\Plaine\Form\PlaineJourType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * PlaineJour controller.
 *
 * @Route("/plainejour")
 * @IsGranted("ROLE_MERCREDI_READ")
 */
class PlaineJourController extends AbstractController
{
    /**
     * Lists all PlaineJour entities.
     *
     * @Route("/", name="plainejour", methods={"GET"})
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository(PlaineJour::class)->findAll();

        return $this->render(
            'plaine/plaine_jour/index.html.twig',
            [
                'entities' => $entities,
            ]
        );
    }

    /**
     * Displays a form to create a new PlaineJour entity.
     *
     * @Route("/new/{id}", name="plainejour_new", methods={"GET","POST"})
     *
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     */
    public function new(Request $request, Plaine $plaine)
    {
        $plaineJour = new PlaineJour();
        $plaineJour->setPlaine($plaine);

        $form = $this->createForm(PlaineJourType::class, $plaineJour)
            ->add('Create', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $data = $form->getData();
            $jour = $data->getDateJour();

            $plaine = $plaineJour->getPlaine();

            if ($em->getRepository(PlaineJour::class)->findOneBy(['date_jour' => $jour])) {
                $this->addFlash('danger', 'Cette date existe d??j?? dans la plaine');

                return $this->redirectToRoute('plaine_show', ['slugname' => $plaine->getSlugname()]);
            }

            $user = $this->getUser();

            /**
             * lorsque j'ajoute une date a une plaine
             * je dois ajouter cette date a tous les inscrits.
             */
            $plaine_enfants = $em->getRepository(PlaineEnfant::class)->search(['plaine_id' => $plaine->getId()]);

            foreach ($plaine_enfants as $plaineEnfant) {
                /**
                 * si qu' un tuteur pour l'enfant je l'ajoute d'office.
                 */
                $enfant = $plaineEnfant->getEnfant();
                $tuteur = false;
                if ($enfant) {
                    $enfant_tuteur = $enfant->getTuteurs();
                    if (1 == count($enfant_tuteur)) {
                        $tuteur = $enfant_tuteur[0]->getTuteur();
                    }
                }

                $presence = new PlainePresence();
                $presence->setPlaineEnfant($plaineEnfant);
                $presence->setJour($plaineJour);
                $presence->setUserAdd($user);
                if ($tuteur) {
                    $presence->setTuteur($tuteur);
                }

                $em->persist($presence);
            }

            $em->persist($plaineJour);
            $em->flush();

            $this->addFlash('success', 'La date a bien ??t?? ajout??e');

            return $this->redirectToRoute('plaine_show', ['slugname' => $plaine->getSlugname()]);
        }

        return $this->render(
            'plaine/plaine_jour/new.html.twig',
            [
                'entity' => $plaineJour,
                'plaine' => $plaine,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Finds and displays a PlaineJour entity.
     *
     * @Route("/{id}", name="plainejour_show", methods={"GET"})
     */
    public function show(PlaineJour $plaineJour)
    {
        $em = $this->getDoctrine()->getManager();

        $plaine = $plaineJour->getPlaine();
        $plaine_id = $plaine->getId();

        $args = ['plaine_id' => $plaine_id];
        $plaine_enfants = $em->getRepository(PlaineEnfant::class)->search($args);
        $peIds = [];
        foreach ($plaine_enfants as $plaine_enfant) {
            $peIds[] = $plaine_enfant->getId();
        }

        $enfants = $em->getRepository(PlainePresence::class)->getEnfantsByPlaineAndByJour($peIds, $plaineJour->getId());

        $petits = $moyens = $grands = [];

        foreach ($enfants as $enfant) {
            $annee_scolaire = $enfant->getAnneeScolaire();

            if (in_array($annee_scolaire, ['PM', '1M', '2M'])) {
                $petits[] = $enfant;
            } elseif (in_array($annee_scolaire, ['3M', '1P', '2P'])) {
                $moyens[] = $enfant;
            } else {
                $grands[] = $enfant;
            }
        }

        $deleteForm = $this->createDeleteForm($plaineJour->getId());

        return $this->render(
            'plaine/plaine_jour/show.html.twig',
            [
                'entity' => $plaineJour,
                'plaine' => $plaine,
                'petits' => $petits,
                'moyens' => $moyens,
                'grands' => $grands,
                'delete_form' => $deleteForm->createView(),
            ]
        );
    }

    /**
     * Creates a form to delete a PlaineJour entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('plainejour_delete', ['id' => $id]))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, ['label' => 'Delete', 'attr' => ['class' => 'btn-danger']])
            ->getForm();
    }

    /**
     * Deletes a PlaineJour entity.
     *
     * @Route("/{id}", name="plainejour_delete", methods={"DELETE"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     */
    public function delete(Request $request, PlaineJour $plaineJour)
    {
        $form = $this->createDeleteForm($plaineJour->getId());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $plaine = $plaineJour->getPlaine();
            $em->remove($plaineJour);
            $em->flush();

            $this->addFlash('success', 'La date a bien ??t?? supprim??e');

            return $this->redirectToRoute('plaine_show', ['slugname' => $plaine->getSlugname()]);
        }

        return $this->redirectToRoute('plainejour');
    }
}
