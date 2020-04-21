<?php

namespace AcMarche\Mercredi\Front\Controller;

use AcMarche\Mercredi\Entity\Enfant;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MessageController
 * @package AcMarche\Mercredi\Controller
 * @IsGranted("ROLE_ADMINISTRATOR")
 */
class MessageController extends AbstractController
{
    /**
     * @param MessageBusInterface $bus
     * @Route("/message", name="message_home")
     * @return Response
     */
    public function index(MessageBusInterface $bus)
    {
        // will cause the SmsNotificationHandler to be called
        //  $bus->dispatch(new SmsNotification('Look! I created a message!'));

        $enfant = new Enfant();
        $enfant->setNom('Sénéchal');
        $enfant->setPrenom('Chloé');

        // or use the shortcut
        $this->dispatchMessage(new SmsNotification('Look! I created a message!'));
        //    $this->dispatchMessage(new FlashNotification('Super flash notification!'));
        $this->dispatchMessage(new EnfantCreated($enfant));
        $this->dispatchMessage(new NewUserWelcomeEmail(44));

        return $this->render(
            '@AcMarcheMercrediFront/default/index.html.twig',
            [

            ]
        );
    }


}
