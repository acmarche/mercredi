<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use AcMarche\Mercredi\User\Dto\UserTuteurDto;
use AcMarche\Mercredi\User\Form\AssociateParentType;
use AcMarche\Mercredi\User\Handler\AssociationHandler;
use AcMarche\Mercredi\User\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * User controller.
 *
 * @Route("/security/associer/parent")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
class AssocierParentController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var PresenceRepository
     */
    private $presenceRepository;
    /**
     * @var TuteurRepository
     */
    private $tuteurRepository;
    /**
     * @var AssociationHandler
     */
    private $associationHandler;

    public function __construct(
        AssociationHandler $associationHandler
    ) {
        $this->associationHandler = $associationHandler;
    }

    /**
     * @Route("/{id}", name="mercredi_user_associate_parent", methods={"GET","POST"})
     */
    public function associate(Request $request, User $user)
    {
        if (!$user->isParent()) {
            $this->addFlash('danger', 'Le compte n\'a pas le rôle de parent');

            return $this->redirectToRoute('mercredi_admin_user_show', ['id' => $user->getId()]);
        }

        $dto = new UserTuteurDto($user);
        $this->associationHandler->suggestTuteur($user, $dto);

        $form = $this->createForm(AssociateParentType::class, $dto);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->associationHandler->handleAssociateParent($dto);

            return $this->redirectToRoute('mercredi_admin_user_show', ['id' => $user->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/user/associer_parent.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="mercredi_user_dissociate_parent", methods={"DELETE"})
     */
    public function dissociate(Request $request, User $user)
    {
        if ($this->isCsrfTokenValid('dissociate'.$user->getId(), $request->request->get('_token'))) {
            $tuteurId = (int)$request->request->get('tuteur');
            if (!$tuteurId) {
                $this->addFlash('danger', 'Le parent n\'a pas été trouvé');

                return $this->redirectToRoute('mercredi_admin_user_show', ['id' => $user->getId()]);
            }
            $this->associationHandler->handleDissociateParent($user, $tuteurId);
        }

        return $this->redirectToRoute('mercredi_admin_user_show', ['id' => $user->getId()]);
    }
}
