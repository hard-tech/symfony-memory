<?php

namespace App\Command;

use App\Entity\Theme;
use App\Service\PixabayService;
use App\Enum\ThemeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:fetch-themes',
    description: 'Fetch themes from Pixabay and store them in the database',
)]
class FetchThemesCommand extends Command
{
    private $pixabayService;
    private $entityManager;

    public function __construct(PixabayService $pixabayService, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->pixabayService = $pixabayService;
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('query', InputArgument::REQUIRED, 'The search query for images and sounds')
            ->addArgument('count', InputArgument::OPTIONAL, 'Number of items to fetch', 10);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $query = $input->getArgument('query');
        $count = $input->getArgument('count');

        $images = $this->pixabayService->searchImages($query, $count);
        $sounds = $this->pixabayService->searchSounds($query, $count);

        foreach ($images as $image) {
            $theme = new Theme();
            $theme->setUrl($image['webformatURL']);
            $theme->setName($image['tags']);
            $theme->setType(ThemeType::IMAGE);
            $this->entityManager->persist($theme);
        }

        foreach ($sounds as $sound) {
            $theme = new Theme();
            $theme->setUrl($sound['videos']['tiny']['url']);
            $theme->setName($sound['tags']);
            $theme->setType(ThemeType::VIDEO);
            $this->entityManager->persist($theme);
        }

        $this->entityManager->flush();

        $output->writeln(sprintf('Fetched and stored %d themes', count($images) + count($sounds)));

        return Command::SUCCESS;
    }
}
