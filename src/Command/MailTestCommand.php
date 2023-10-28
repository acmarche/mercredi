<?php

namespace AcMarche\Mercredi\Command;

use AcMarche\Mercredi\Mailer\InitMailerTrait;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Email;

#[AsCommand(
    name: 'mercredi:test-mail',
    description: 'Test envoie mail'
)]
class MailTestCommand extends Command
{
    use InitMailerTrait;

    protected function configure(): void
    {
        $this
            ->addArgument('to', InputArgument::REQUIRED, 'Destinataire');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $to = $input->getArgument('to');

        $message = new Email();
        $message->subject('Test applicaiton mercredi');
        $message->from($this->getEmailSenderAddress());
        $message->to($to);
        $message->text('Coucou, mail de test');

        try {
            $this->sendMail($message);
            $io->success('Le mail a bien été envoyé.');
        } catch (TransportExceptionInterface $e) {
            $io->error('Erreur lors de l envoie: '.$e->getMessage());
        }

        return Command::SUCCESS;
    }
}
