<?php

namespace AcMarche\Mercredi\Admin\Command;

use AcMarche\Mercredi\Security\Entity\Group;
use AcMarche\Mercredi\Security\Entity\User;
use AcMarche\Mercredi\Security\Repository\GroupRepository;
use AcMarche\Mercredi\Security\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class InitUserCommand extends Command
{
    /**
     * @var GroupRepository
     */
    private $groupRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(
        GroupRepository $groupRepository,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        parent::__construct();
        $this->groupRepository = $groupRepository;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function configure()
    {
        $this
            ->setName('mercredi:inituser')
            ->setDescription('Encode les groupes et l\'utilisateur admin')
            ->addArgument('test', InputArgument::OPTIONAL, 'For phpunit');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->createDefaultAccount($output);
    }

    protected function createDefaultAccount(
        OutputInterface $output
    ) {
        $groupAdmin = $this->groupRepository->findOneBy(['name' => 'MERCREDI_ADMIN']);
        $groupRead = $this->groupRepository->findOneBy(['name' => 'ROLE_MERCREDI_READ']);
        $groupParent = $this->groupRepository->findOneBy(['name' => 'MERCREDI_PARENT']);
        $groupEcole = $this->groupRepository->findOneBy(['name' => 'MERCREDI_ECOLE']);
        $groupAnimateur = $this->groupRepository->findOneBy(['name' => 'MERCREDI_ANIMATEUR']);

        $adminUser = $this->userRepository->findOneBy(['username' => 'admin']);
        $readUser = $this->userRepository->findOneBy(['username' => 'read']);
        $ecoleUser = $this->userRepository->findOneBy(['username' => 'ecole']);
        $animateurUser = $this->userRepository->findOneBy(['username' => 'animateur']);
        $parentUser = $this->userRepository->findOneBy(['username' => 'parent']);

        $password = 'admin1234';

        if (!$groupAdmin) {
            $groupAdmin = new Group('MERCREDI_ADMIN');
            $groupAdmin->addRole('ROLE_MERCREDI_ADMIN');
            $groupAdmin->addRole('ROLE_MERCREDI_READ');
            $this->entityManager->persist($groupAdmin);
            $this->entityManager->flush();
            $output->writeln('Groupe MERCREDI_ADMIN cr????');
        }

        if (!$groupParent) {
            $groupParent = new Group('MERCREDI_PARENT');
            $groupParent->addRole('ROLE_MERCREDI_PARENT');
            $this->entityManager->persist($groupParent);
            $this->entityManager->flush();
            $output->writeln('Groupe MERCREDI_PARENT cr????');
        }

        if (!$groupAnimateur) {
            $groupAnimateur = new Group('MERCREDI_ANIMATEUR');
            $groupAnimateur->addRole('ROLE_MERCREDI_ANIMATEUR');
            $this->entityManager->persist($groupAnimateur);
            $this->entityManager->flush();
            $output->writeln('Groupe MERCREDI_ANIMATEUR cr????');
        }

        if (!$groupEcole) {
            $groupEcole = new Group('MERCREDI_ECOLE');
            $groupEcole->addRole('ROLE_MERCREDI_ECOLE');
            $this->entityManager->persist($groupEcole);
            $this->entityManager->flush();
            $output->writeln('Groupe MERCREDI_ECOLE cr????');
        }

        if (!$groupRead) {
            $groupRead = new Group('MERCREDI_READ');
            $groupRead->addRole('ROLE_MERCREDI_READ');
            $this->entityManager->persist($groupRead);
            $this->entityManager->flush();
            $output->writeln('Groupe MERCREDI_READ cr????');
        }

        if (!$adminUser) {
            $adminUser = new User();
            $adminUser->setUsername('admin');
            $adminUser->setNom('Admin');
            $adminUser->setPrenom('Zeze');
            $adminUser->setPlainPassword('admin');
            $adminUser->setEnabled(1);
            $adminUser->setEmail('jf@marche.be');
            $adminUser->addGroup($groupAdmin);
            $adminUser->setPassword($this->passwordEncoder->encodePassword($adminUser, $password));
            $this->entityManager->persist($adminUser);
            $output->writeln("L'utilisateur admin a bien ??t?? cr????");
        }

        if (!$parentUser) {
            $parentUser = new User();
            $parentUser->setUsername('pmichel');
            $parentUser->setNom('Mich');
            $parentUser->setPrenom('Phili');
            $parentUser->setPlainPassword('admin');
            $parentUser->setEnabled(1);
            $parentUser->setEmail('webmaster@marche.be');
            $parentUser->addGroup($groupParent);
            $parentUser->setPassword($this->passwordEncoder->encodePassword($parentUser, $password));
            $this->entityManager->persist($parentUser);
            $output->writeln("L'utilisateur pmi a bien ??t?? cr????");
        }

        if (!$animateurUser) {
            $animateurUser = new User();
            $animateurUser->setUsername('animateur');
            $animateurUser->setNom('Vermoesen');
            $animateurUser->setPrenom('John');
            $animateurUser->setPlainPassword('animateur');
            $animateurUser->setEnabled(1);
            $animateurUser->setEmail('animateur@marche.be');
            $animateurUser->addGroup($groupAnimateur);
            $animateurUser->setPassword($this->passwordEncoder->encodePassword($animateurUser, $password));
            $this->entityManager->persist($animateurUser);
            $output->writeln("L'utilisateur animateur a bien ??t?? cr????");
        }

        if (!$ecoleUser) {
            $ecoleUser = new User();
            $ecoleUser->setUsername('ecole');
            $ecoleUser->setNom('Ecole');
            $ecoleUser->setPrenom('Aye');
            $ecoleUser->setPlainPassword('ecole');
            $ecoleUser->setEnabled(1);
            $ecoleUser->setEmail('ecole@marche.be');
            $ecoleUser->addGroup($groupEcole);
            $ecoleUser->setPassword($this->passwordEncoder->encodePassword($ecoleUser, $password));
            $this->entityManager->persist($ecoleUser);
            $output->writeln("L'utilisateur ecole a bien ??t?? cr????");
        }

        if (!$readUser) {
            $readUser = new User();
            $readUser->setUsername('read');
            $readUser->setNom('Lecteur');
            $readUser->setPrenom('Ipod');
            $readUser->setPlainPassword('read');
            $readUser->setEnabled(1);
            $readUser->setEmail('read@marche.be');
            $readUser->addGroup($groupRead);
            $readUser->setPassword($this->passwordEncoder->encodePassword($readUser, $password));
            $this->entityManager->persist($readUser);
        }

        //if (!$ecoleUser->hasGroup($groupEcole)) {
        $ecoleUser->addGroup($groupEcole);
        $this->entityManager->persist($groupEcole);
        $output->writeln("L'utilisateur porte a ??t?? ajout?? dans le groupe commerce");
        // }

        // if (!$animateurUser->hasGroup($groupAnimateur)) {
        $animateurUser->addGroup($groupAnimateur);
        $this->entityManager->persist($animateurUser);
        $output->writeln("L'utilisateur animateur a ??t?? ajout?? dans le groupe animateur");
        //   }

        //   if (!$parentUser->hasGroup($groupParent)) {
        $parentUser->addGroup($groupParent);
        $this->entityManager->persist($parentUser);
        $output->writeln("L'utilisateur parent a ??t?? ajout?? dans le groupe parent");
        //   }

        //   if (!$adminUser->hasGroup($groupAdmin)) {
        $adminUser->addGroup($groupAdmin);
        $this->entityManager->persist($adminUser);
        $output->writeln("L'utilisateur admin a ??t?? ajout?? dans le groupe admin");
        //  }

        $this->entityManager->flush();
    }
}
