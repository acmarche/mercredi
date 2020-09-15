<?php

namespace AcMarche\Mercredi\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Message;

class MailTestCommand extends Command
{
    protected static $defaultName = 'mercredi:test-mail';
    /**
     * @var MailerInterface
     */
    private $mailer;

    public function __construct(MailerInterface $mailer, string $name = null)
    {
        parent::__construct($name);
        $this->mailer = $mailer;
    }

    protected function configure()
    {
        $this
            ->setDescription('Test envoie mail')
            ->addArgument('from', InputArgument::REQUIRED, 'Expéditeur')
            ->addArgument('to', InputArgument::REQUIRED, 'Destinataire');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $from = $input->getArgument('from');
        $to = $input->getArgument('to');

        $message = new Email();
        $message->subject('Test applicaiton mercredi');
        $message->from($from);
        $message->to($to);
        $message->text('Coucou, mail de test');

        try {
            $this->mailer->send($message);
            $io->success('Le mail a bien été envoyé.');
        } catch (TransportExceptionInterface $e) {
            $io->error('Erreur lors de l envoie: '.$e->getMessage());
        }

        return Command::SUCCESS;
    }
}
