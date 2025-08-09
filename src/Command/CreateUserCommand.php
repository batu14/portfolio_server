<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:create-user',
    description: 'Yeni bir kullanıcı oluşturur.',
)]
class CreateUserCommand extends Command
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = new User();
        $user->setUsername('admin');
        $user->setPassword('$2y$13$$2y$13$$2y$13$kXIidScqSM8mHthuKuLCQO3KczRypp3jLdAJjlxJC5MXG0COzNhTi'); // hash'li şifre
        $user->setRoles(['ROLE_ADMIN']);

        $this->em->persist($user);
        $this->em->flush();

        $output->writeln('Kullanıcı başarıyla oluşturuldu.');
        return Command::SUCCESS;
    }
}
