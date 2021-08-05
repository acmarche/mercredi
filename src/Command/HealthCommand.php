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
    private EnfantRepository $enfantRepository;
    private NotificationMailer $notifcationMailer;
    private TuteurRepository $tuteurRepository;
    private AdminEmailFactory $adminEmailFactory;

    public function __construct(
        EnfantRepository $enfantRepository,
        TuteurRepository $tuteurRepository,
        AdminEmailFactory $adminEmailFactory,
        NotificationMailer $notifcationMailer,
        string $name = null
    ) {
        parent::__construct($name);
        $this->enfantRepository = $enfantRepository;
        $this->notifcationMailer = $notifcationMailer;
        $this->tuteurRepository = $tuteurRepository;
        $this->adminEmailFactory = $adminEmailFactory;
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
            $email = $this->adminEmailFactory->sendMessagEnfantsOrphelins($enfants);
            $this->notifcationMailer->sendAsEmailNotification($email);
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
            $email = $this->adminEmailFactory->sendMessagTuteurArchived($tuteurs);
            $this->notifcationMailer->sendAsEmailNotification($email);
        }

        return Command::SUCCESS;
    }
}
