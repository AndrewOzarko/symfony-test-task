<?php

namespace App\Command;

use App\Entity\User;
use App\Entity\Group;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(
    name: 'app:user-group',
    description: 'Manage users and groups via CLI'
)]
class UserGroupCommand extends Command
{
    protected static $defaultName = 'app:user-group';

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setDescription('Manage users and groups via CLI')
            ->addArgument('action', InputArgument::REQUIRED, 'Action to perform: add-user, edit-user, delete-user, add-group, edit-group, delete-group, report')
            ->addOption('id', 'i', InputOption::VALUE_OPTIONAL, 'ID of user or group')
            ->addOption('user-name', null, InputOption::VALUE_OPTIONAL, 'Name of user')
            ->addOption('user-email', null, InputOption::VALUE_OPTIONAL, 'Email of user')
            ->addOption('group-name', null, InputOption::VALUE_OPTIONAL, 'Name of group');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $action = $input->getArgument('action');

        switch ($action) {
            case 'add-user':
                return $this->executeAddUser($input, $output);
            case 'edit-user':
                return $this->executeEditUser($input, $output);
            case 'delete-user':
                return $this->executeDeleteUser($input, $output);
            case 'add-group':
                return $this->executeAddGroup($input, $output);
            case 'edit-group':
                return $this->executeEditGroup($input, $output);
            case 'delete-group':
                return $this->executeDeleteGroup($input, $output);
            case 'report':
                return $this->executeReport($input, $output);
            default:
                $output->writeln("Unknown action: $action");
                return Command::FAILURE;
        }
    }

    private function executeAddUser(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getOption('user-name');
        $email = $input->getOption('user-email');

        $user = new User();
        $user->setName($name);
        $user->setEmail($email);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln("User added with ID: " . $user->getId());

        return Command::SUCCESS;
    }

    private function executeEditUser(InputInterface $input, OutputInterface $output): int
    {
        $id = $input->getOption('id');
        $name = $input->getOption('user-name');
        $email = $input->getOption('user-email');

        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            $output->writeln("User with ID $id not found");
            return Command::FAILURE;
        }

        $user->setName($name);
        $user->setEmail($email);

        $this->entityManager->flush();

        $output->writeln("User with ID $id updated");

        return Command::SUCCESS;
    }

    private function executeDeleteUser(InputInterface $input, OutputInterface $output): int
    {
        $id = $input->getOption('id');

        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            $output->writeln("User with ID $id not found");
            return Command::FAILURE;
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        $output->writeln("User with ID $id deleted");

        return Command::SUCCESS;
    }

    private function executeAddGroup(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getOption('group-name');

        $group = new Group();
        $group->setName($name);

        $this->entityManager->persist($group);
        $this->entityManager->flush();

        $output->writeln("Group added with ID: " . $group->getId());

        return Command::SUCCESS;
    }

    private function executeEditGroup(InputInterface $input, OutputInterface $output): int
    {
        $id = $input->getOption('id');
        $name = $input->getOption('group-name');

        $group = $this->entityManager->getRepository(Group::class)->find($id);

        if (!$group) {
            $output->writeln("Group with ID $id not found");
            return Command::FAILURE;
        }

        $group->setName($name);

        $this->entityManager->flush();

        $output->writeln("Group with ID $id updated");

        return Command::SUCCESS;
    }

    private function executeDeleteGroup(InputInterface $input, OutputInterface $output): int
    {
        $id = $input->getOption('id');

        $group = $this->entityManager->getRepository(Group::class)->find($id);

        if (!$group) {
            $output->writeln("Group with ID $id not found");
            return Command::FAILURE;
        }

        $this->entityManager->remove($group);
        $this->entityManager->flush();

        $output->writeln("Group with ID $id deleted");

        return Command::SUCCESS;
    }

    private function executeReport(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("Generating report...");

        $groups = $this->entityManager->getRepository(Group::class)->findAll();

        foreach ($groups as $group) {
            $output->writeln("Group: " . $group->getName());

            foreach ($group->getUsers() as $user) {
                $output->writeln("- User: " . $user->getName() . " (Email: " . $user->getEmail() . ")");
            }
        }

        return Command::SUCCESS;
    }
}