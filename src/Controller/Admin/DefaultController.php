<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Mailer\InitMailerTrait;
use AcMarche\Mercredi\Search\Form\SearchAutocompleteType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[IsGranted('ROLE_MERCREDI_ADMIN')]
final class DefaultController extends AbstractController
{
    use InitMailerTrait;

    #[Route(path: '/{test}', name: 'mercredi_admin_home')]
    public function default(Request $request, bool $test = false): Response
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

        if ($test) {
            $this->testmail();
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/default/index.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    private function testmail()
    {
        $message = new Email();
        $message->subject('Test applicaiton mercredi');
        $message->from("jf@atl-hotton.be");
        $message->to("jfsenechal@gmail.com");
        $message->text('Coucou, mail de test0');

        try {
            $this->sendMail($message);
            var_dump(123);
        } catch (TransportExceptionInterface $e) {
            var_dump($e->getMessage());
        }
    }
}
