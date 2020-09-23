<?php

namespace AcMarche\Mercredi\Command;

use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Notification\Mailer\NotificationMailer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HealthCommand extends Command
{
    protected static $defaultName = 'mercredi:health';
    /**
     * @var EnfantRepository
     */
    private $enfantRepository;
    /**
     * @var NotificationMailer
     */
    private $notifcationMailer;

    public function __construct(
        EnfantRepository $enfantRepository,
        NotificationMailer $notifcationMailer,
        string $name = null
    ) {
        parent::__construct($name);
        $this->enfantRepository = $enfantRepository;
        $this->notifcationMailer = $notifcationMailer;
    }

    protected function configure()
    {
        $this
            ->setDescription('Vérifie l\'intégrité des données');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $enfants = $this->enfantRepository->findOrphelins();
        if (count($enfants) > 0) {
            $this->notifcationMailer->sendMessagEnfantsOrphelins($enfants);
        }

        return Command::SUCCESS;
    }
}
