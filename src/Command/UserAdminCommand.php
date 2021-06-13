<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use UnexpectedValueException;

#[AsCommand(
    name: 'user-admin',
    description: 'Administer user accounts',
    aliases: ['useradmin', 'admin']
)]
class UserAdminCommand extends Command
{
    private InputInterface $input;
    private OutputInterface $output;
    private const OPTIONS
        = [
            'Exit',
            'List Users',
            'Create User',
            'Edit User',
            'Delete User',
        ];

    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator
    ) {
        parent::__construct();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $io = new SymfonyStyle($input, $output);

        $io->title('KuKu\'s User Admin');

        $this->input = $input;
        $this->output = $output;

        $this->showMenu();

        return Command::SUCCESS;
    }

    private function showMenu(): void {
        $io = new SymfonyStyle($this->input, $this->output);

        $users = $this->entityManager->getRepository(User::class)->findAll();

        $io->text(
            sprintf(
                '<fg=cyan>There are %d users in the database.</>',
                count($users)
            )
        );

        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            'Please select an option (defaults to exit)',
            self::OPTIONS,
            0
        );
        $question->setErrorMessage('Choice %s is invalid.');

        $answer = $helper->ask($this->input, $this->output, $question);
        $this->output->writeln($answer);

        try {
            switch ($answer) {
                case 'List Users':
                    $this->renderUsersTable($users);
                    break;
                case 'Create User':
                    $this->createUser();
                    $io->success('User created');
                    break;
                case 'Edit User':
                    $io->text('Edit not implemented yet :(');
                    break;
                case 'Delete User':
                    $this->deleteUser();
                    $io->success('User has been removed');
                    break;
                case 'Exit':
                    $io->text('have Fun =;)');
                    return;
                default:
                    throw new UnexpectedValueException(
                        'Unknown answer: '.$answer
                    );
            }
        } catch (Exception $exception) {
            $io->error($exception->getMessage());
        }
        $this->showMenu();
    }

    private function renderUsersTable(array $users): void {
        $table = new Table($this->output);
        $table->setHeaders(['ID', 'Identifier', 'Role']);

        /* @type User $user */
        foreach ($users as $user) {
            $table->addRow(
                [
                    $user->getId(),
                    $user->getUserIdentifier(),
                    implode(", ", $user->getRoles()),
                ]
            );
        }
        $table->render();
    }

    private function askIdentifier(): string {
        $io = new SymfonyStyle($this->input, $this->output);
        do {
            $identifier = $this->getHelper('question')->ask(
                $this->input,
                $this->output,
                new Question('Identifier: ')
            );
            if (!$identifier) {
                $io->warning('Identifier required :(');
            }
        } while ($identifier === null);

        return $identifier;
    }

    private function askRole()
    {
        return $this->getHelper('question')->ask(
            $this->input,
            $this->output,
            (new ChoiceQuestion(
                'User role',
                array_values(User::ROLES)
            ))
                ->setErrorMessage('Choice %s is invalid.')
        );
    }

    private function createUser(): void {
        $identifier = $this->askIdentifier();
        $role = $this->askRole();

        $user = (new User())
            ->setUserIdentifier($identifier)
            ->setRole($role);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    private function deleteUser(): void
    {
        $id = $this->getHelper('question')->ask(
            $this->input,
            $this->output,
            new Question('User ID to delete: ')
        );
        $user = $this->entityManager->getRepository(User::class)->findOneBy(
            ['id' => $id]
        );

        if (!$user) {
            throw new UnexpectedValueException('User not found!');
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }
}
