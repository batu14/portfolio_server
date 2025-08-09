<?php

namespace App\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'app:create-upload-folders',
    description: 'Gerekli upload klasörlerini oluşturur.',
)]
class CreateUploadFoldersCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filesystem = new Filesystem();

        $dirs = [
            'public/uploads/products',
            'public/uploads/users',
            'public/uploads/temp',
        ];

        foreach ($dirs as $dir) {
            if (!$filesystem->exists($dir)) {
                $filesystem->mkdir($dir, 0775);
                $output->writeln("<info>Oluşturuldu:</info> $dir");
            } else {
                $output->writeln("<comment>Zaten var:</comment> $dir");
            }
        }

        return Command::SUCCESS;
    }
}
