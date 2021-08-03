<?php

namespace AcMarche\Mercredi\Command;

use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Notification\Mailer\NotificationMailer;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HealthCommand extends Command
{
    protected static $defaultName = 'mercredi:health';
    private EnfantRepository $enfantRepository;
    private NotificationMailer $notifcationMailer;
    private TuteurRepository $tuteurRepository;

    public function __construct(
        EnfantRepository $enfantRepository,
        TuteurRepository $tuteurRepository,
        NotificationMailer $notifcationMailer,
        string $name = null
    ) {
        parent::__construct($name);
        $this->enfantRepository = $enfantRepository;
        $this->notifcationMailer = $notifcationMailer;
        $this->tuteurRepository = $tuteurRepository;
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

        $tuteurs = [];
        foreach ($this->tuteurRepository->findAllActif() as $tuteur) {
            $relations = $tuteur->getRelations();
            if (count($relations) === 0) {
                continue;
            }
            $count = 0;
            foreach ($tuteur->getRelations() as $relation) {
                if ($relation->getEnfant()->isArchived()) {
                    $count++;
                }
            }
            if ($count === count($relations)) {
                $tuteurs[] = $tuteur;
                $tuteur->setArchived(true);
            }
        }
      //  $this->tuteurRepository->flush();
        if (count($enfants) > 0) {
            $this->notifcationMailer->sendMessagTuteurArchived($tuteurs);
        }

        return Command::SUCCESS;
    }
}
