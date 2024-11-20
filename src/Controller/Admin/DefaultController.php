<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Mailer\InitMailerTrait;
use AcMarche\Mercredi\Search\Form\SearchAutocompleteType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[IsGranted('ROLE_MERCREDI_ADMIN')]
final class DefaultController extends AbstractController
{
    use InitMailerTrait;

    #[Route(path: '/', name: 'mercredi_admin_home')]
    public function default(Request $request): Response
    {
        $form = $this->createForm(SearchAutocompleteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $enfant = $data['nom'];
            if (!$enfant) {
                $this->addFlash('danger', 'Enfant non trouvé');
            } else {
                return $this->redirectToRoute('mercredi_admin_enfant_show', ['id' => $enfant->getId()]);
            }
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/default/index.html.twig',
            [
                'form' => $form->createView(),
            ],
        );
    }

    #[Route(path: '/mailtest')]
    public function mailTest(): Response
    {
        $email = (new TemplatedEmail())
            ->from($this->getEmailSenderAddress())
            ->to('jf@marche.be')
            ->subject('Coucou')
            ->text('super ovh pour les mails');

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            var_dump($e->getMessage());
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/default/test.html.twig',
            [

            ],
        );
    }
}
