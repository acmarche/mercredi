<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Entity\Message;
use AcMarche\Mercredi\Mailer\InitMailerTrait;
use AcMarche\Mercredi\Message\Form\MessageTestType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController.
 *
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
final class DefaultController extends AbstractController
{
    use InitMailerTrait;

    /**
     * @Route("/", name="mercredi_admin_home")
     */
    public function default(): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/default/index.html.twig',
            [
            ]
        );
    }

    /**
     * @Route("/test", name="mercredi_admin_test_mail")
     */
    public function testMail(Request $request): Response
    {
        $message = new Message();
        $message->setFrom('contact@atl-hotton.be');

        $form = $this->createForm(MessageTestType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $email = new Email();
            $email->from($data->getFrom());
            $email->to($data->getTo());
            $email->subject($data->getSujet());
            $email->text($data->getTexte());

            try {
                $this->sendMail($email);
                $this->addFlash('success', 'Le mail a bien été envoyé.');
            } catch (TransportExceptionInterface $e) {
                $this->addFlash('danger', 'Erreur lors de l envoie: '.$e->getMessage());
            }
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/default/test.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
