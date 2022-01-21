<?php

namespace AcMarche\Mercredi\Command;

use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Mailer\Factory\AdminEmailFactory;
use AcMarche\Mercredi\Mailer\NotificationMailer;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HealthCommand extends Command
{
    protected static $defaultName = 'mercredi:health';

    public function __construct(
        private EnfantRepository $enfantRepository,
        private TuteurRepository $tuteurRepository,
        private AdminEmailFactory $adminEmailFactory,
        private NotificationMailer $notifcationMailer,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Vérifie l\'intégrité des données');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $enfants = $this->enfantRepository->findOrphelins();
        if ([] !== $enfants) {
            $message = $this->adminEmailFactory->messagEnfantsOrphelins($enfants);
            $this->notifcationMailer->sendAsEmailNotification($message);
        }

        $tuteurs = [];
        foreach ($this->tuteurRepository->findAllActif() as $tuteur) {
            $relations = $tuteur->getRelations();
            if (0 === \count($relations)) {
                continue;
            }
            $count = 0;
            foreach ($tuteur->getRelations() as $relation) {
                if ($relation->getEnfant()->isArchived()) {
                    ++$count;
                }
            }
            if ($count === \count($relations)) {
                $tuteurs[] = $tuteur;
                $tuteur->setArchived(true);
            }
        }
        //  $this->tuteurRepository->flush();
        if ([] !== $enfants) {
            $message = $this->adminEmailFactory->messageTuteurArchived($tuteurs);
            $this->notifcationMailer->sendAsEmailNotification($message);
        }

        return Command::SUCCESS;
    }
}
