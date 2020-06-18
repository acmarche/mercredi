<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\User\Form\UserEditType;
use AcMarche\Mercredi\User\Form\UserSearchType;
use AcMarche\Mercredi\User\Form\UserType;
use AcMarche\Mercredi\User\Message\UserCreated;
use AcMarche\Mercredi\User\Message\UserDeleted;
use AcMarche\Mercredi\User\Message\UserUpdated;
use AcMarche\Mercredi\User\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
class UserController extends AbstractController
{
    private $userRepository;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(
        UserRepository $userRepository,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Lists all User entities.
     *
     * @Route("/", name="mercredi_admin_user_index", methods={"GET","POST"})
     */
    public function index(Request $request)
    {
        $form = $this->createForm(UserSearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $nom = $data['nom'];
            $role = $data['role'];

            $users = $this->userRepository->findByNameOrRoles($nom, $role);
        } else {
            $users = $this->userRepository->findBy([], ['nom' => 'ASC']);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/user/index.html.twig',
            [
                'users' => $users,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Displays a form to create a new User utilisateur.
     *
     * @Route("/new", name="mercredi_admin_user_new", methods={"GET","POST"})
     */
    public function new(Request $request)
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $this->passwordEncoder->encodePassword($user, $form->get('plainPassword')->getData())
            );
            $this->userRepository->insert($user);
            $this->dispatchMessage(new UserCreated($user->getId()));

            return $this->redirectToRoute('mercredi_admin_user_show', ['id' => $user->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/user/new.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Finds and displays a User utilisateur.
     *
     * @Route("/{id}", name="mercredi_admin_user_show", methods={"GET"})
     */
    public function show(User $user)
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/user/show.html.twig',
            [
                'user' => $user,
            ]
        );
    }

    /**
     * Displays a form to edit an existing User utilisateur.
     *
     * @Route("/{id}/edit", name="mercredi_admin_user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user)
    {
        $editForm = $this->createForm(UserEditType::class, $user);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->userRepository->flush();
            $this->dispatchMessage(new UserUpdated($user->getId()));

            return $this->redirectToRoute('mercredi_admin_user_show', ['id' => $user->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/user/edit.html.twig',
            [
                'user' => $user,
                'form' => $editForm->createView(),
            ]
        );
    }

    /**
     * Deletes a User utilisateur.
     *
     * @Route("/{id}", name="mercredi_admin_user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $id = $user->getId();
            $this->userRepository->remove($user);
            $this->userRepository->flush();
            $this->dispatchMessage(new UserDeleted($id));
        }

        return $this->redirectToRoute('mercredi_admin_user_index');
    }
}
